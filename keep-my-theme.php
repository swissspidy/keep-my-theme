<?php
/**
 * Keep My Theme.
 *
 * Keep My Theme will display posts with the theme that was active when they were published.
 *
 * @package   Keep_My_Theme
 * @author    Pascal Birchler <pascal.birchler@spinpress.com>
 * @license   GPL-2.0+
 * @link      https://pascalbirchler.com
 * @copyright 2014 Pascal Birchler
 *
 * @wordpress-plugin
 * Plugin Name:       Keep My Theme
 * Plugin URI:        https://spinpress.com/keep-my-theme/
 * Description:       Keep My Theme will display posts with the theme that was active when they were published.
 * Version:           1.0.0
 * Author:            Pascal Birchler
 * Author URI:        https://pascalbirchler.com
 * Text Domain:       keep-my-theme
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */

// Don't call this file directly
defined( 'ABSPATH' ) or die;

/**
 * Plugin Activation.
 *
 * Adds the plugin option with the current theme as default.
 */
function keepmytheme_activation() {
	$themes = array(
		current_time( 'timestamp' ) => get_stylesheet()
	);

	add_option( 'keepmytheme', $themes, '', 'no' );
}

register_activation_hook( __FILE__, 'keepmytheme_activation' );

/**
 * Update Theme History.
 *
 * When the user switches a theme, we add it to the history together with the current timestamp.
 *
 * @param string   $new_name  Name of the new theme.
 * @param WP_Theme $new_theme New theme object.
 */
function keepmytheme_theme_switch( $new_name, $new_theme ) {

	$trace = debug_backtrace();

	if ( in_array( $trace[4]['function'], array( 'keepmytheme_switch_theme' ) ) ) {
		return;
	}

	$option = get_option( 'keepmytheme', array() );

	$latest_theme = array_shift( array_values( $option ) );

	// This theme is already marked as the currently active theme, don't add it twice
	if ( $new_theme === $latest_theme ) {
		return;
	}

	$option[ current_time( 'timestamp' ) ] = $new_theme->get_stylesheet();

	update_option( 'keepmytheme', $option );
}

add_action( 'switch_theme', 'keepmytheme_theme_switch', 10, 2 );

/**
 * Returns the theme history ordered by timestamp.
 *
 * Newest theme is first in the returned array.
 *
 * The `keepmytheme_history` filter allows users to add data for posts that were written
 * before this plugin was installed.
 *
 * @return array The Keep My Theme option with the theme history.
 */
function keepmytheme_get_option() {

	$option = apply_filters(
		'keepmytheme_history',
		get_option( 'keepmytheme', array() )
	);

	krsort( $option, SORT_NUMERIC );

	return $option;

}

/**
 * Switches the WordPress theme for single posts.
 *
 * This is the heart of the plugin. If a post was written while another theme was active,
 * we temporarily switch themes.Only works if the theme active at that time is still installed.
 *
 * Does some request parsing trickery to find the current post.
 */
function keepmytheme_switch_theme() {

	if ( is_admin() ) {
		return;
	}

	$wp = new WP();
	$wp->parse_request();
	$wp->query_posts();

	$themes        = keepmytheme_get_option();
	$latest_theme  = array_shift( $themes );
	$current_theme = wp_get_theme();

	if ( $latest_theme !== $current_theme->get_stylesheet() && ! is_single( get_queried_object() ) ) {
		switch_theme( $latest_theme );
	}

	if ( ! is_single( get_queried_object() ) ) {
		return;
	}

	$history = keepmytheme_get_option();

	if ( ! is_array( $history ) || empty( $history ) ) {
		return;
	}

	$post_date = get_post_time( 'U', true, get_queried_object() );

	foreach ( $history as $timestamp => $theme ) {
		if ( $timestamp >= $post_date ) {
			continue;
		}

		if ( $timestamp < $post_date ) {
			$theme_obj = wp_get_theme( $theme );

			// Only switch if the theme exists and isn't already active
			if ( $theme_obj->exists() && $current_theme->get_stylesheet() !== $theme ) {
				switch_theme( $theme );
			}

			return;

		}
	}

	// Switch to the oldest theme in the history if there wasn't a switch yet.
	$oldest_possible_theme     = array_pop( $history );
	$oldest_possible_theme_obj = wp_get_theme( $oldest_possible_theme );

	if ( $oldest_possible_theme_obj->exists() ) {
		switch_theme( $oldest_possible_theme );

		return;
	}

}

add_action( 'setup_theme', 'keepmytheme_switch_theme' );