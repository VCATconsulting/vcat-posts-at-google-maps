=== VCAT EDULABS Posts at Google Maps ===
Contributors: VCATconsulting, nida78
Tags: EDULABS, VCAT, Geo-Coding, Geo-Location, Google-Maps, Shortcode
Requires at least: 3.0
Tested up to: 4.3.1
Stable tag: 1.6.2
Donate link: http://www.vcat.de/edulabs/ueber-vcat-edulabs/spenden/
License: GPLv2

A Geo-Coding Plugin that enables to tag Posts and Pages with Geo-Coordinates.


== Description ==

Using this plugin you can place posts and pages on a Google map. The Geo-Position will be determined from an address.
The Google map can be embedded to a post or page with one of two shortcodes that accept parameters controlling the display properties of the map and the behaviour of the links.
So, you can show in a very simple way where you gathered some information, took a photograph or where an event will happen.

- The current version uses an own SQL table to store and retrieve the Geo-Data.
- The global settings can be modified on an own dashboard page in the WordPress backend.
- The Plugin ships a backend widget for entering the data on a post or page which can even be edited on the Quick-Edit screen.
- There are two shortcodes (1) to embed a large map with all posts/pages as markers and (2) a mini-map with the current one as marker.


Planned Features:
- Manage locations as a Custom Post Type to reuse several Geo-Data more than ones in a blog.
- Put posts and pages an predefined routes.
- Further features may be requested on the Plugin page.


== Installation ==

The installation follows the common paths:

1a) Automatically Installation in WordPress:
Log in to your wordpress.
Navigate to Plugin > Add new
Search for "vcat-posts-at-google-maps".
Click "Install now".
When done, click "activate".

1b) Manual Installation:
Download the Plugin at [http://downloads.wordpress.org/plugin/vcat-posts-at-google-maps.zip](http://downloads.wordpress.org/plugin/vcat-posts-at-google-maps.zip) and unzip it.
Store the content under /wp-content/plugins/ on your server.
Navigate to "Plugins" in your dashboard and activate it.

2) Usage and Options
Further information how to use the shortcodes and their available options may be found at [our website](http://www.vcat.de/edulabs/projekte/wordpress/geo-plugin/).

== Screenshots ==

1. This is a dashboard-view that show the global settings.
2. An additional panel to edit the Geo-Data can be found on posts and pages editing page.
3. Here you see a small map created with a shortcode. You can override the global settings within the shortcode.
4. This finally is the map using the shortcode for large maps.


== Changelog ==

= 1.6.2 =
* Fixing some PHP version incompatibility

= 1.6.1 =
* bug fix: replace short open tags

= 1.6 =
* current location can be directly read from the client (browser)
* location can be changed be drag-and-drop a pin on a mini-map beside the input form
* language support for English and German reviewed and cleaned
* minor bug-fixes, e.g. preplacing the deprecated screen_icon method

= 1.5.2 =
* bug fix: deleting location not possible, when special pin-color was chosen

= 1.5.1 =
* micro bug-fix: re-uploaded images

= 1.5 =
* coloured pin selection added (default values for posts/pages, overridden by shortcode option, rewritable for singlie posts/pages)
* bug-fix: multiple mini-maps are working now

= 1.2 =
* micro bug-fix

= 1.1 =
* some backend css bug-fixes due to WP 3.8

= 1.0 =
* new filter options for the big map
* optional dynamic center and zoom at the big map
* a separated core part of all VCAT EDULABS plugins
* some backend css bug-fixes

= 0.7 =
* Initial Realse on wordpress.org
