<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * @link       https://pascalbirchler.com
 * @since      1.0.0
 *
 * @package    Keep_My_Theme
 */

// If uninstall not called from WordPress, then exit.
defined( 'WP_UNINSTALL_PLUGIN' ) or die;

// Delete our option
delete_option( 'keepmytheme' );