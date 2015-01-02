# Keep My Theme #
**Contributors:** swissspidy  
**Tags:** Post, posts, theme, themes, content, archives, template, history, stylesheet, Style  
**Requires at least:** 3.4  
**Tested up to:** 4.1  
**Stable tag:** 1.0.0  
**License:** GPLv2 or later  
**License URI:** http://www.gnu.org/licenses/gpl-2.0.html  

Keep My Theme will display posts with the theme that was active when they were published.

## Description ##

Many of us have been blogging for many years. It happens regularly that we change WordPress themes.
But when we do that, we loose the zeitgeist of the posts that were once written to perfectly fit the design of that theme.

Keep My Theme detects theme changes and always displays your single posts with the theme active at the time of writing.
Of course the theme needs to be installed in order for this to work.

Using some `switch_theme()` trickery, widgets and menu settings will be preserved.
This doesn't guarantee perfectly styled posts though, so make sure to do some testing first.
When you deactivate the plugin, everything will go back to normal. Uninstalling deletes the theme change history.

Have a look at the FAQ to see how you can use this plugin with older posts.
See [this blog post](https://spinpress.com/keep-my-theme/ "SpinPress - Keep My Theme") for further information about the plugin.

Original idea by [Christian Leu](http://leumund.ch/wuensche-2015-0020864).

## Installation ##

1. Upload the `keep-my-theme` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Switch your theme and publish a new post.
1. Switch your theme back and visit that post. It should use the previous theme.

## Frequently Asked Questions ##

### I published a post with a different theme active, but it is using my default theme ###

Check if the theme is still installed in WordPress. The plugin can't activate a theme that isn't available anymore.

### What happens to posts published before I installed the plugin? ###

Good question! You can use the `keepmytheme_history` filter to let the plugin know which themes were active a couple of years ago.
Example:

```php
function change_keepmytheme_history( $history ) {
  $history[ strtotime( '2010-08-01' ) ] = 'default', // I've used the old default theme after August 1st, 2010.
  $history[ strtotime( '2014-01-01' ) ] = 'twentyfourteen' // I've used Twenty Fourteen after January 1st, 2014.

  return $history;
}

add_filter( 'keepmytheme_history', 'change_keepmytheme_history' );
```

Note: You should put this snippet in a new (must-use) plugin or your current theme's `functions.php` file.

## Screenshots ##

### 1. Each post uses the theme that was active when it was initially published. ###
![Each post uses the theme that was active when it was initially published.](https://raw.githubusercontent.com/swissspidy/keep-my-theme/master/screenshot-1.png)


### 2. Your homepage and other areas of the website keep the regular theme. ###
![Your homepage and other areas of the website keep the regular theme.](https://raw.githubusercontent.com/swissspidy/keep-my-theme/master/screenshot-2.png)


## Changelog ##

### 1.0.0 ###
* Initial release.