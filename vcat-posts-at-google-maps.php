<?php 
/*
Plugin Name: VCAT Posts At Google Maps
Plugin URI: 
Description: Dieses Plugin zeigt die Lage der Posts und Pages in einer Google Map an. Die Lage wird durch die Latitude und Longitude des Punktes bestimmt.  Die Map wird über den Shortcode [vcat-dpagm] auf eine beliebige Seite eingebunden. (height & width im shortcode erlauben es die Größe anzupassen)  
Version: 0.7
Author: VCAT Consulting GmbH, Robin Kramer, Originally by Melanie Sommer
Author URI: http://www.vcat.de
*/

/**
 * @package VCAT Posts At Google Maps
 * @author VCAT Consulting GmbH, Robin Kramer, originally by Melanie Sommer
 * @copyright GNU GPL v2
 */

# require_once("vcat-posts-at-google-maps-post.php");
require_once("vcat-geo-costum-field.php");
require_once("vcat-geo-quickedit-field.php");
require_once("vcat-geo-database.php");
require_once("vcat-settings-menu.php");

add_action('admin_menu', 'vcat_add_geo_settings');							// is triggered after the basic admin panel menu structure is in place 

add_action( 'wp_enqueue_scripts', 'vcatEnqueueGoogleMapsScripts' );			// is the proper hook to use when enqueuing items that are meant to appear on the front end, it is used for enqueuing both: scripts & styles 

add_shortcode( 'vcat-dpagm', 'vcatDisplayPostsAtGoogleMaps' );
add_shortcode( 'vcat-dpagm-mini', 'vcatDisplayPostsAtGoogleMaps_mini' );

define('PLUGIN_FOLDER',str_replace("\\",'/',dirname(__FILE__)));
define('PLUGIN_PATH','/' . substr(PLUGIN_FOLDER,stripos(PLUGIN_FOLDER,'wp-content')));
 
add_action('add_meta_boxes','vcat_custom_fields_init');						// triggers when the edit screen is loading

add_action('delete_post','vcat_delete_data');								// is fired before and after a post (or page) is deleted from the database 

add_action('save_post','vcat_custom_fields_save');							// is triggered whenever a post or page is created or updated

add_filter('manage_post_posts_columns', 'vcat_add_post_column');			// is a filter applied to the columns shown when listing posts
add_filter('manage_page_posts_columns', 'vcat_add_post_column');			// is a filter applied to the columns shown when listing pages
add_action('manage_posts_custom_column', 'vcat_render_post_columns', 10, 2);// Only combined with manage_post_posts_columns filter! this allows you to add custom columns to posts
add_action('manage_pages_custom_column', 'vcat_render_post_columns', 10, 2);// Only combined with manage_page_posts_columns filter! this allows you to add custom columns to pages
add_action('quick_edit_custom_box',  'vcat_add_quick_edit', 10, 2);			// is triggered when quick editing, this action is called one time for each custom column!!
add_action('save_post', 'vcat_quick_edit_save'); 							// is triggered whenever a post or page is created or updated

//pre-input for the data in the quick edit is only available throut javascript
add_action('admin_footer', 'vcat_quick_edit_javascript');					// is triggered at the end of the admin panel inside the <body>-tag
add_filter('post_row_actions', 'vcat_expand_quick_edit_link', 10, 2);		// allows to modify the row action links for non-hierarchical post types(e.g. posts)
add_filter('page_row_actions', 'vcat_expand_quick_edit_link', 10, 2);  		// allows to modify the row action links for hierarchical post types(e.g. pages)

register_activation_hook( __FILE__, 'vcat_db_install' );					// this hook will cause our function to run when the plugin is activated

add_filter( 'posts_clauses', 'vcat_geo_filter', 10, 2 ); 					// adds own clauses to the standart wp_query request

global $VCAT_MAP_DEFAULTS, $VCAT_MINI_MAP_DEFAULTS;

$VCAT_MAP_DEFAULTS=array(
	'width' => array('width' => '100%'),
	'height' => array('height' => '500px'),
	'center' => array(
		'center' => 'August-Bebel-Str. 26-53 MedienHaus 14482 Potsdam', 
		'center_lat' => '52.388119',
		'center_lng' => '13.119443'
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
						 

/**
 * enqueues the scripts needed for the google-api and map
 */
function vcatEnqueueGoogleMapsScripts() {
	wp_enqueue_script(
    	'google-maps-api-js',
    	'http://maps.google.com/maps/api/js?sensor=false',
		array('jquery'),
		false
    );
    
    wp_enqueue_script(
    	'vcat-maps-js',
    	plugins_url('/scripts/js/functions.js', __FILE__)
    );
    
    wp_enqueue_style(
		'map-css',
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
function vcatDisplayPostsAtGoogleMaps( $atts ){
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

	return "
		<div id='map_canvas' style='width:" . $width . ";height:" . $height .";' class='align-" . $align . "'></div>
		<script type='text/javascript'>
			jQuery(document).ready(function(){
				vcatInitialize(" . $center_lat . ", " . $center_lng . ", " . $zoom . ");
				" . vcatSetMarkers( $target ) . "
			});
		</script>
	";
}	

/**
 * extracts the informations out of the shortcode and adds some more for the initialization,
 * also sets a div-box for the map, and calls some function afterwards, 
 * which will, when the document is ready, initialize the map and set the post marker
 * default: map size = 200px*200px, center = each individual post
 *
 * @param $atts	attributes which can be given to the extraction from the shortcode,
 */
function vcatDisplayPostsAtGoogleMaps_mini( $atts ){
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
		'center_lat' => $post->lat,
		'center_lng' => $post->lng,
		'target' => $options['target'],
		'zoom' => $options['zoom'],
		'align' => $options['align']
	), $atts ) );

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
function vcatGetLatLngFromAddress( $address ) {

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
function vcatSetMarkers( $target ) {
	
	$args = array( 'post_type' => array('page','post'), 'posts_per_page' => -1);
	$map_posts = new WP_Query( $args );
	
	$out = "";
	
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
			}
		}
	}
	
	return $out;
}
?>
