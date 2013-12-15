=== VCAT EDULABS Posts at Google Maps ===
Contributors: VCATconsulting, nida78
Tags: EDULABS, VCAT, Geo-Coding, Geo-Location, Google-Maps, Shortcode
Requires at least: 3.0
Tested up to: 3.6
Stable tag: 0.7
Donate link: http://www.vcat.de/edulabs/ueber-vcat-edulabs/spenden/
License: GPLv2

A Geo-Coding Plugin that enables to tag Posts and Pages with Geo-Coordinates.


== Description ==

Using this Plugin you can place posts and pages on a google map. The Geo-Position will be determined from an address.
The map can be embedded to a post or page with one of two shortcodes that accept parameters controlling the display properties of the map and the behaviour of the links.
So you can show in a very simple way where you gathered some informations, took a photograph or where an event will happen.

- The current version uses an own sql-table to store and retrieve the GeoData.
- The global settings can be set on an own dashboard page.
- The Plugin ships a backend widget for entering the data on a post or page which can even be edited on the QuickEdit screen.
- There are two shorcodes (1) to embed a large map with all posts/pages as markers and (2) a mini-map with the current one as marker.


Planned Features:

- Inidividual choice of pin color per post/page.
- Drag 'n drop of pins on the backend widget.
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
2. Here you see a small map created with a shortcode. You can override the global settings within the shortcode.
3. This finally is the map using the shortcode for large maps.


== Changelog ==

= 0.7 =
* Initial Realse on wordpress.org

= 1.0 =
* new filter options for the big map
* optional dynamic center and zoom at the big map
* a separated core part of all VCAT EDULABS plugins
* some backend css bug-fixes