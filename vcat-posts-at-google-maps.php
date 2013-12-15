<?php 
/*
Plugin Name: VCAT EDULABS Posts At Google Maps
Plugin URI: http://www.vcat.de/edulabs/projekte/wordpress/geo-plugin/
Description: Dieses Plugin zeigt die Lage der Posts und Pages in einer Google Map an. Die Lage wird durch die Latitude und Longitude des Punktes bestimmt.  Die Map wird über den Shortcode [vcat-dpagm] auf eine beliebige Seite eingebunden. (height & width im shortcode erlauben es die Größe anzupassen)  
Version: 1.0
Author: VCAT Consulting GmbH (Nico Danneberg, Robin Kramer, Melanie Sommer)
Author URI: http://www.vcat.de
*/

/**
 * @package VCAT EDULABS Posts At Google Maps
 * @author VCAT Consulting GmbH (Nico Danneberg, Robin Kramer, Melanie Sommer)
 * @copyright GNU GPL v2
 */

load_plugin_textdomain('vcgmapsatposts', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/');

require_once("core/vcat-core.php");
require_once("vcat-geo-costum-field.php");
require_once("vcat-geo-quickedit-field.php");
require_once("vcat-geo-database.php");
require_once("vcat-settings-menu.php");

add_action( 'admin_menu', 'vcat_geo_add_settings' );						// is triggered after the basic admin panel menu structure is in place 
add_filter(
	'plugin_action_links_' . plugin_basename( __FILE__ ),
	'vcat_geo_add_plugin_settings_link'
);																			// adds a link to the settings page under the plugin's name on plugin list
add_filter( 'vcat_plugins_list', 'vcat_geo_add_plugins_list_info' );		// adds an entry for this plugin in the VCAT EDULABS main settings page


add_action( 'wp_enqueue_scripts', 'vcat_geo_enqueue_google_maps_scripts' );			// is the proper hook to use when enqueuing items that are meant to appear on the front end, it is used for enqueuing both: scripts & styles 

add_shortcode( 'vcat-dpagm', 'vcat_geo_display_posts_at_google_maps' );
add_shortcode( 'vcat-dpagm-mini', 'vcat_display_posts_at_google_maps_mini' );

add_action('add_meta_boxes','vcat_geo_custom_fields_init');						// triggers when the edit screen is loading

add_action('delete_post','vcat_geo_delete_data');								// is fired before and after a post (or page) is deleted from the database 

add_action('save_post','vcat_geo_custom_fields_save');							// is triggered whenever a post or page is created or updated

add_filter('manage_post_posts_columns', 'vcat_geo_add_post_column');			// is a filter applied to the columns shown when listing posts
add_filter('manage_page_posts_columns', 'vcat_geo_add_post_column');			// is a filter applied to the columns shown when listing pages
add_action('manage_posts_custom_column', 'vcat_geo_render_post_columns', 10, 2);// Only combined with manage_post_posts_columns filter! this allows you to add custom columns to posts
add_action('manage_pages_custom_column', 'vcat_geo_render_post_columns', 10, 2);// Only combined with manage_page_posts_columns filter! this allows you to add custom columns to pages
add_action('quick_edit_custom_box',  'vcat_geo_add_quick_edit', 10, 2);			// is triggered when quick editing, this action is called one time for each custom column!!

/** NiDa: really necessary?
add_action('save_post', 'vcat_geo_quick_edit_save'); 							// is triggered whenever a post or page is created or updated
**/

//pre-input for the data in the quick edit is only available through javascript
add_action('admin_footer', 'vcat_geo_quick_edit_javascript');					// is triggered at the end of the admin panel inside the <body>-tag
add_filter('post_row_actions', 'vcat_geo_expand_quick_edit_link', 10, 2);		// allows to modify the row action links for non-hierarchical post types(e.g. posts)
add_filter('page_row_actions', 'vcat_geo_expand_quick_edit_link', 10, 2);  		// allows to modify the row action links for hierarchical post types(e.g. pages)

register_activation_hook( __FILE__, 'vcat_geo_db_install' );					// this hook will cause our function to run when the plugin is activated

add_filter( 'posts_clauses', 'vcat_geo_posts_clauses_filter', 10, 2 );			// adds own clauses to the standart wp_query request

global $VCAT_MAP_DEFAULTS, $VCAT_MINI_MAP_DEFAULTS, $legals;

$VCAT_MAP_DEFAULTS=array(
	'width' => array('width' => '100%'),
	'height' => array('height' => '500px'),
	'center' => array(
		'center' => 'August-Bebel-Str. 26-53 MedienHaus 14482 Potsdam', 
		'center_lat' => '52.388119',
		'center_lng' => '13.119443',
		'dynamic' => 'FALSE'
	),
	'zoom' => array('zoom' => '8'),
	'target' => array('target' => 'blank'),
	'align' => array( 'align' => 'left' )
);

$VCAT_MINI_MAP_DEFAULTS=array(
	'width' => array('width' => '200px'),
	'height' => array('height' => '200px'),
	'zoom' => array('zoom' => '9'),
	'target' => array('target' => 'blank'),
	'align' => array( 'align' => 'left' )
);

																										// Parameter for: 
$legals=array(	'author', 'author_name', 'author__in', 'author__not_in', 								// Author												
 				'cat', 'category_name', 'category__and', 'category__in', 'category__not_in', 			// Category
				'tag', 'tag_id', 'tag__and', 'tag__in', 'tag__not_in', 'tag_slug__and', 'tag_slug__in', // Tag
				's',																					// Search
				'p', 'name', 'page_id', 'pagename', 'post_parent', 'post_parent__in', 					// Post & Page
				'post_parent__not_in', 'post__in', 'post__not_in', 										// -||-
				'post_type', 'post_status', 															// Type $ Status
				'year', 'monthnum', 'w', 'day', 'hour', 'minute', 'second', 'm', 						// Date
				'meta_key', 'meta_value', 'meta_value_num', 'meta_compare', 							// Costum Field/Meta
				'perm', 																				// Permission
				'center'																				// VCAT specials 
				);		 



/**
 * enqueues the scripts needed for the google-api and map
 */
function vcat_geo_enqueue_google_maps_scripts() {
	wp_enqueue_script(
    	'google-maps-api-js',
    	'http://maps.google.com/maps/api/js?sensor=false',
		array('jquery'),
		false
    );
    
    wp_enqueue_script(
    	'vcat-geo-map-js',
    	plugins_url('/scripts/js/functions.js', __FILE__)
    );
    
    wp_enqueue_style(
		'vcat-geo-map-css',
		plugins_url('/styles/styles.css', __FILE__)
	);
	
} 

/**
 * extracts the informations out of the shortcode and adds some more for the initialization,
 * also sets a div-box for the map, and calls some function afterwards, 
 * which will, when the document is ready, initialize the map and set the post markers
 * default: map size = 100%*500px(width*height), center = VCAT Consulting Gmbh
 *
 * @param $atts	attributes which can be given to the extraction from the shortcode,
 */
function vcat_geo_display_posts_at_google_maps( $atts ){
	global $post, $VCAT_MAP_DEFAULTS;
	$options = array_merge(
		get_option( 'vcat_geo_width', $VCAT_MAP_DEFAULTS[ 'width' ] ),
		get_option( 'vcat_geo_height', $VCAT_MAP_DEFAULTS[ 'height' ] ),
		get_option( 'vcat_geo_center', $VCAT_MAP_DEFAULTS[ 'center' ] ),
		get_option( 'vcat_geo_zoom', $VCAT_MAP_DEFAULTS[ 'zoom' ] ),
		get_option( 'vcat_geo_target', $VCAT_MAP_DEFAULTS[ 'target' ] ),
		get_option( 'vcat_geo_align', $VCAT_MAP_DEFAULTS[ 'align' ] )
	);	
		
	extract( shortcode_atts( array(
		'width' => $options['width'],	
		'height' => $options['height'],
		'center_lat' => $options['center_lat'],
		'center_lng' => $options['center_lng'],
		'target' => $options['target'],
		'zoom' => $options['zoom'],
		'align' => $options['align']
	), $atts ) );

	vcat_geo_filter_check($atts);

	if (is_array($atts)) {
		$filter = array_merge($atts, array('dynamic' => $options['dynamic']));
	} else {
		$filter = array('dynamic' => $options['dynamic']);
	}

	return "
		<div id='map_canvas' style='width:" . $width . ";height:" . $height .";' class='align-" . $align . "'></div>
		<script type='text/javascript'>
			jQuery(document).ready(function(){
				vcatInitialize(" . $center_lat . ", " . $center_lng . ", " . $zoom . ");
				" . vcat_geo_set_markers( $target , $filter ) . "
			});
		</script>
	";
}	

/**
 * checks if there are only legal filters in the array and unsets any other!
 * 
 * @param $filter an array with all filters which shall be added to the next wp_query
 */
function vcat_geo_filter_check(&$filter)
{
	global $legals;
	
	foreach ($filter as $key => $value) 
	{	
		if (!in_array($key, $legals)) 
		{
			unset($filter[$key]);
		}
	}
}


/**
 * extracts the informations out of the shortcode and adds some more for the initialization,
 * also sets a div-box for the map, and calls some function afterwards, 
 * which will, when the document is ready, initialize the map and set the post marker
 * default: map size = 200px*200px, center = each individual post
 *
 * @param $atts	attributes which can be given to the extraction from the shortcode,
 */
function vcat_geo_display_posts_at_google_maps_mini( $atts ){
	global $post, $VCAT_MINI_MAP_DEFAULTS;
	$options = array_merge(
		get_option( 'vcat_geo_mini_width', $VCAT_MINI_MAP_DEFAULTS[ 'width' ] ),
		get_option( 'vcat_geo_mini_height', $VCAT_MINI_MAP_DEFAULTS[ 'height' ] ),
		get_option( 'vcat_geo_mini_zoom', $VCAT_MINI_MAP_DEFAULTS[ 'zoom' ] ),
		get_option( 'vcat_geo_mini_target', $VCAT_MINI_MAP_DEFAULTS[ 'target' ] ),
		get_option( 'vcat_geo_mini_align', $VCAT_MINI_MAP_DEFAULTS[ 'align' ] )
	);	

	extract( shortcode_atts( array(
		'width' => $options['width'],	
		'height' => $options['height'],
		'target' => $options['target'],
		'zoom' => $options['zoom'],
		'align' => $options['align']
	), $atts ) );

	//13.11.13 aus der extraction genommen, da sie nicht veränderbar sein soll...
	$center_lat = $post->lat;
	$center_lng = $post->lng;

	$post_title =  $post->post_title;
	$post_link = get_permalink( $post->post_id );
	$post_address = $post->str . " " . $post->plz . " " . $post->ort;
	$image_url = ($post->post_type=='page') ? plugins_url('/images/vcat-gray-dot.png', __FILE__) : plugins_url('/images/vcat-orange-dot.png', __FILE__);

	return "
		<div id='map_canvas' style='width:" . $width . ";height:" . $height .";' class='align-" . $align . "'></div>
		<script type='text/javascript'>
			jQuery(document).ready(function(){
				vcatInitialize(" . $center_lat . ", " . $center_lng . ", " . $zoom . ");
				vcatAddMarker(" . $center_lat . ", " . $center_lng . ", '" . $post_address . "', '" . $post_title . "', '" . $post_link . "', '" . $image_url . "', '" . $target . "' );
			});
		</script>
	";
}	

/**
 * sends an request to the google-api to get the coordinates for an given address 
 * converts it to str and xml to extract thecoordinates
 *
 * @param $address	the given address
 * @return array 	the coordinates
 */
function vcat_geo_get_lat_lng_by_address( $address ) {
	if( $address == "" || !isset( $address ) || $address == null )
		return array( "", "" );
		
	$req = 'http://maps.googleapis.com/maps/api/geocode/xml?sensor=false&address=' . urlencode( $address );
	
	$str = file_get_contents( $req );
	
	$xml = new SimpleXMLElement( $str );
	
	$lat= floatval( $xml->result[0]->geometry->location->lat );
	$lng = floatval( $xml->result[0]->geometry->location->lng );
	return array('lat'=>$lat, 'lng'=>$lng);
}

/**
 * sets the marker for the posts and pages on the the googlemap 
 *
 * @param $target	specifies where the infobox will appear later
 * @return string	the calls of a JavaScript function to add a marker per Post or Page
 */
function vcat_geo_set_markers( $target, $filter ) {
	
	$args = array( 'post_type' => array('page','post'), 'posts_per_page' => -1);
	
	if ($filter!=null) {
		$args=array_merge($args, $filter);
	}

	$map_posts = new WP_Query( $args );
	
	if ($filter['center']=='dynamic'||$filter['dynamic']=='TRUE') {
		$out = "var bounds = new google.maps.LatLngBounds ();\n";
	} else {
		$out ="";
	}

	if( ! empty( $map_posts->posts ) ) {
	
		foreach( $map_posts->posts as $post ) {
				
			$post_title = $post->post_title;
			$post_link = get_permalink( $post->post_id );				
			$post_address = $post->str . " " . $post->plz . " " . $post->ort;
			$post_lat = $post->lat;
			$post_lng = $post->lng;			
				
			if( $post->post_type=='page' ) {
				 $image_url = plugins_url('/images/vcat-gray-dot.png', __FILE__);
			} else {
				 $image_url = plugins_url('/images/vcat-orange-dot.png', __FILE__); 
			}
						
			if( $post_lat != "" && $post_lng != "" ){
	       		$out .= "vcatAddMarker( " . $post_lat . ", " . $post_lng . ", '" . $post_address . "', '" . $post_title . "', '" . $post_link . "', '" . $image_url . "', '" . $target . "' );\n";
				
				if ($filter['center']=='dynamic'||$filter['dynamic']=='TRUE') {
					$out .= "bounds.extend ( new google.maps.LatLng ( $post_lat, $post_lng ) );\n";
				}
			}
		}
	}

	if ($filter['center']=='dynamic'||$filter['dynamic']=='TRUE') {
		$out .= "map.fitBounds(bounds); ";
	} 
	
	return $out;
}
?>