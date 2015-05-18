<?php 
/*
Plugin Name: VCAT EDULABS Posts At Google Maps
Plugin URI: http://www.vcat.de/edulabs/projekte/wordpress/geo-plugin/
Description: Dieses Plugin zeigt die Lage der Posts und Pages in einer Google Map an. Die Lage wird durch die Latitude und Longitude des Punktes bestimmt. Maps können über die Shortcodes [vcat-dpagm] & [vcat-dpagm-mini] auf eine beliebige Seite oder Artikel eingebunden werden. Verschiedene Attribute erlauben die manuelle Manipulation eines jeden Shortcodes.  
Version: 2.0
Author: VCAT Consulting GmbH (Nico Danneberg, Daniel Dziamski, Robin Kramer, Melanie Sommer)
Author URI: http://www.vcat.de
*/

/**
 * @package VCAT EDULABS Posts At Google Maps
 * @author VCAT Consulting GmbH (Nico Danneberg, Daniel Dziamski, Robin Kramer, Melanie Sommer)
 * @copyright GNU GPL v2
 */

load_plugin_textdomain('vcgmapsatposts', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/');

require_once("core/vcat-core.php");
require_once("vcat-geo-costum-field.php");
require_once("vcat-geo-quickedit-field.php");
require_once("vcat-geo-database.php");
require_once("vcat-settings-menu.php");
require_once("vcat-location-classes.php");
/**
 * 
 */
class global_vcat_posts_at_google_maps_class extends vcat_magic_methods {

	protected $VCAT_MAP_DEFAULTS = array(
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
		'align' => array( 'align' => 'left' ),
		'color' => array( 
			'postcolor' => 'orange', 
			'pagecolor' => 'gray'
		),
		'margin' => array('margin' => '0px'),
		'padding' => array('padding' => '0px')
	);
	
	protected $VCAT_MINI_MAP_DEFAULTS = array(
		'width' => array('width' => '200px'),
		'height' => array('height' => '200px'),
		'zoom' => array('zoom' => '9'),
		'target' => array('target' => 'blank'),
		'align' => array( 'align' => 'left' ),
		'color' => array( 
			'postcolor' => 'orange', 
			'pagecolor' => 'gray'
		)
	);

	protected $VCAT_PIN_COLORS;
																									// Shortcode-Filter-Parameter for:
	protected $legals = array(	
		'author', 'author_name', 'author__in', 'author__not_in', 									// Author												
		'cat', 'category_name', 'category__and', 'category__in', 'category__not_in', 				// Category
		'tag', 'tag_id', 'tag__and', 'tag__in', 'tag__not_in', 'tag_slug__and', 'tag_slug__in',		// Tag
		's',																						// Search
		'p', 'name', 'page_id', 'pagename', 'post_parent', 'post_parent__in', 						// Post & Page
		'post_parent__not_in', 'post__in', 'post__not_in', 											// -||-
		'post_type', 'post_status', 																// Type $ Status
		'year', 'monthnum', 'w', 'day', 'hour', 'minute', 'second', 'm', 							// Date
		'meta_key', 'meta_value', 'meta_value_num', 'meta_compare', 								// Costum Field/Meta
		'perm', 																					// Permission
		'center', 'postcolor', 'pagecolor'															// VCAT specials 
		);		 
	
	
	
	function __construct(){
		$this->VCAT_PIN_COLORS = array(
			'blue'    => __( 'Blue', 'vcgmapsatposts' ),
			'red'     => __( 'Red', 'vcgmapsatposts' ),
			'yellow'  => __( 'Yellow', 'vcgmapsatposts' ),
			'green'   => __( 'Green', 'vcgmapsatposts' ),
			'orange'  => __( 'Orange', 'vcgmapsatposts' ),
			'purple'  => __( 'Purple', 'vcgmapsatposts' ),
			'magenta' => __( 'Magenta', 'vcgmapsatposts' ),
			'cyan'    => __( 'Cyan', 'vcgmapsatposts' ),
			'pink'    => __( 'Pink', 'vcgmapsatposts' ),
			'brown'   => __( 'Brown', 'vcgmapsatposts' ),
			'beige'   => __( 'Beige', 'vcgmapsatposts' ),
			'gray'    => __( 'Gray', 'vcgmapsatposts' )
		)
	}
	/**
	 * enqueues the scripts needed for the google-api and map
	 */
	function enqueue_google_maps_scripts() {
		wp_enqueue_script(
	    	'google-maps-api-js',
	    	'http://maps.google.com/maps/api/js?sensor=false',
			array('jquery'),
			false
	    );
	    
	    wp_enqueue_script(
	    	'vcat-geo-map-js',
	    	plugins_url('/scripts/functions.js', __FILE__)
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
	function display_posts_at_google_maps( $atts ){
		global $post, $vcmain;
		$options = array_merge(
			get_option( 'vcat_geo_width', $this->VCAT_MAP_DEFAULTS[ 'width' ] ),
			get_option( 'vcat_geo_height', $this->VCAT_MAP_DEFAULTS[ 'height' ] ),
			get_option( 'vcat_geo_center', $this->VCAT_MAP_DEFAULTS[ 'center' ] ),
			get_option( 'vcat_geo_zoom', $this->VCAT_MAP_DEFAULTS[ 'zoom' ] ),
			get_option( 'vcat_geo_target', $this->VCAT_MAP_DEFAULTS[ 'target' ] ),
			get_option( 'vcat_geo_align', $this->VCAT_MAP_DEFAULTS[ 'align' ] ),
			get_option( 'vcat_geo_margin', $this->VCAT_MAP_DEFAULTS[ 'margin' ] ),
			get_option( 'vcat_geo_padding', $this->VCAT_MAP_DEFAULTS[ 'padding' ] )
		);	

		extract( shortcode_atts( array(
			'width' => $options['width'],	
			'height' => $options['height'],
			'center_lat' => $options['center_lat'],
			'center_lng' => $options['center_lng'],
			'target' => $options['target'],
			'zoom' => $options['zoom'],
			'align' => $options['align'],
			'margin' => $options['margin'],
			'padding' => $options['padding']
		), $atts ) );
	
		if (isset($atts['center'])&&$atts['center']!='dynamic') {
			$latlng = $vcmain->get_lat_lng_by_address($atts['center']);
			$center_lat = $latlng['lat'];
			$center_lng = $latlng['lng'];
			unset($atts['center']);
		}
	
		$this->filter_check($atts);
	
		if (is_array($atts)) {
			$filter = array_merge(array('dynamic' => $options['dynamic']), $atts);
		} else {
			$filter = array('dynamic' => $options['dynamic']);
		}
	
		return "
			<div id='map_canvas' style='width:" . $width . ";height:" . $height .";' class='align-" . $align . "'></div>
			<script type='text/javascript'>
				jQuery(document).ready(function(){
					vcatInitialize(" . $center_lat . ", " . $center_lng . ", " . $zoom . ", '');
					" . $this->set_markers( $target , $filter ) . "
				});
			</script>
		";
	}	
	
	/**
	 * checks if there are only legal filters in the array and unsets any other!
	 * 
	 * @param $filter an array with all filters which shall be added to the next wp_query
	 */
	function filter_check(&$filter)
	{
		global $legals;

		foreach ($filter as $key => $value)
		{	
			if (!in_array($key, $this->legals)) 
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
	function display_posts_at_google_maps_mini( $atts ){
		global $post;
		$options = array_merge(
			get_option( 'vcat_geo_mini_width', $this->VCAT_MINI_MAP_DEFAULTS[ 'width' ] ),
			get_option( 'vcat_geo_mini_height', $this->VCAT_MINI_MAP_DEFAULTS[ 'height' ] ),
			get_option( 'vcat_geo_mini_zoom', $this->VCAT_MINI_MAP_DEFAULTS[ 'zoom' ] ),
			get_option( 'vcat_geo_mini_target', $this->VCAT_MINI_MAP_DEFAULTS[ 'target' ] ),
			get_option( 'vcat_geo_mini_align', $this->VCAT_MINI_MAP_DEFAULTS[ 'align' ] ),
			get_option( 'vcat_geo_mini_color', $this->VCAT_MINI_MAP_DEFAULTS[ 'color' ])
			//get_option( 'vcat_geo_margin', $this->VCAT_MAP_DEFAULTS[ 'margin' ] ),
			//get_option( 'vcat_geo_padding', $this->VCAT_MAP_DEFAULTS[ 'padding' ] )
		);	
	
		extract( shortcode_atts( array(
			'width' => $options['width'],	
			'height' => $options['height'],
			'target' => $options['target'],
			'zoom' => $options['zoom'],
			'align' => $options['align'],
			'margin' => $options['margin'],
			'padding' => $options['padding']
		), $atts ) );
	
		//13.11.13 aus der extraction genommen, da sie nicht veränderbar sein soll...
		$center_lat = $post->lat;
		$center_lng = $post->lng;
	
		$post_title =  $post->post_title;
		$post_link = get_permalink( $post->post_id );
		$post_address = $post->str . " " . $post->plz . " " . $post->ort;
		
		if (isset($post->color)) {
			$image_url = plugins_url('/images/vcat-'.$post->color.'-dot.png', __FILE__);
		}else {
			if( $post->post_type=='page' ) {
				 $image_url = plugins_url('/images/vcat-'.$options['pagecolor'].'-dot.png', __FILE__);
			} else {
				 $image_url = plugins_url('/images/vcat-'.$options['postcolor'].'-dot.png', __FILE__); 
			}
		}

	    if(is_admin()){
	        return "
	            <div id='map_canvas".$post->post_id."' style='width:" . $width . "; margin:" . $margin . "; padding:" . $padding . "; height:" . $height ."; class='align-" . $align . "'></div>
	            <script type='text/javascript'>
	                jQuery(document).ready(function(){
	                    vcatInitializeBackend(" . $center_lat . ", " . $center_lng . ", " . $zoom . ", " . $post->post_id . ");

	                });
	            </script>
		    ";
	    } else {
			return "
	            <div id='map_canvas".$post->post_id."' style='width:" . $width . "; margin:" . $margin . "; padding:" . $padding . "; height:" . $height ."; class='align-" . $align . "'></div>
				<script type='text/javascript'>
					jQuery(document).ready(function(){
						vcatInitialize(" . $center_lat . ", " . $center_lng . ", " . $zoom . ", " . $post->post_id . ");
						vcatAddMarker(" . $center_lat . ", " . $center_lng . ", '" . $post_address . "', '" . $post_title . "', '" . $post_link . "', '" . $image_url . "', '" . $target . "' );
					});
				</script>
			";
		}
	}

	/**
	 * sends an request to the google-api to get the coordinates for an given address 
	 * converts it to str and xml to extract thecoordinates
	 *
	 * @param $address	the given address
	 * @return array 	the coordinates
	 */
	function get_lat_lng_by_address( $address ) {
		if( $address == "" || !isset( $address ) || $address == null )
			return array( "", "" );
			
		$req = 'http://maps.googleapis.com/maps/api/geocode/xml?sensor=false&address=' . urlencode( $address );
		
		$str = file_get_contents( $req );
		
		$xml = new SimpleXMLElement( $str );
		
		$lat= floatval( $xml->result[0]->geometry->location->lat );
		$lng = floatval( $xml->result[0]->geometry->location->lng );
		return array('lat'=>$lat, 'lng'=>$lng);
	}
	
	/** dadz 21042015 add function
	 * sends an request to the google-api to get the address for an given latitude and longitude
	 * converts it to str and xml to extract the address
	 *
	 * @param $latlng the given latitude and longitude
	 * @return array address
	 */
	function get_address_by_lat_lng( $latlng ) {
		if( $latlng == "" || !isset( $latlng ) || $latlng == null ) {
			return array( "", "", "" );
		}

		$req = 'http://maps.googleapis.com/maps/api/geocode/xml?latlng=' . $latlng. '&sensor=false';

		if ( $str = file_get_contents( $req ) ) {

			if ( $xml = new SimpleXMLElement( $str ) ) {

				if ( isset( $xml->result[0]->formatted_address ) ) {
					$formatted_address_parts = explode( ',', $xml->result[0]->formatted_address );
					$plz_ort_parts           = explode( ',', $formatted_address_parts[1] );

					$str = $formatted_address_parts[0];
					$plz = $plz_ort_parts[0];
					$ort = $plz_ort_parts[1];

					return array( 'ort' => $ort, 'plz' => $plz, 'str' => $str );
				}
			}
		}

		return false;
	}

	/**
	 * sets the marker for the posts and pages on the the googlemap 
	 *
	 * @param $target	specifies where the infobox will appear later
	 * @return string	the calls of a JavaScript function to add a marker per Post or Page
	 */
	function set_markers( $target, $filter ) {
		$options = get_option( 'vcat_geo_color', $this->VCAT_MAP_DEFAULTS[ 'color' ]); 
		
		if (isset($filter['postcolor'])&&file_exists( dirname(__FILE__).'/images/vcat-'.$filter['postcolor'].'-dot.png')) {
			$postcolor=$filter['postcolor'];
		}else {
			$postcolor=$options['postcolor'];
		}
		if (isset($filter['pagecolor'])&&file_exists( dirname(__FILE__).'/images/vcat-'.$filter['pagecolor'].'-dot.png')) {
			$pagecolor=$filter['pagecolor'];
		}else {
			$pagecolor=$options['pagecolor'];
		}
		
		
		$args = array( 'post_type' => array('page','post'), 'posts_per_page' => -1);
		
		if ($filter!=null) {
			$args=array_merge($args, $filter);
		}
	
		$map_posts = new WP_Query( $args );
		
		if ($filter['center']=='dynamic') {
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
				
				if (isset($post->color)) {
					$image_url = plugins_url('/images/vcat-'.$post->color.'-dot.png', __FILE__);
				}	else {
					if( $post->post_type=='page' ) {
						 $image_url = plugins_url('/images/vcat-'.$pagecolor.'-dot.png', __FILE__);
					} else {
						 $image_url = plugins_url('/images/vcat-'.$postcolor.'-dot.png', __FILE__); 
					}
				}
				
							
				if( $post_lat != "" && $post_lng != "" ){
		       		$out .= "vcatAddMarker( " . $post_lat . ", " . $post_lng . ", '" . $post_address . "', '" . $post_title . "', '" . $post_link . "', '" . $image_url . "', '" . $target . "' );\n";
					
					if ($filter['center']=='dynamic') {
						$out .= "bounds.extend ( new google.maps.LatLng ( $post_lat, $post_lng ) );\n";
					}
				}
			}
		}
	
		if ($filter['center']=='dynamic') {
			$out .= "map.fitBounds(bounds); ";
		} 
		
		return $out;
	}

	function e(){
		ob_start();
		var_dump(	func_get_args()	);
		$contents = ob_get_contents();
		ob_end_clean();
		error_log($contents);
	}

}
global $vcmain;
$vcmain = new global_vcat_posts_at_google_maps_class();
add_action( 'admin_menu', array(&$vcsetting, 'add_settings' ) );						// is triggered after the basic admin panel menu structure is in place 
add_filter(
	'plugin_action_links_' . plugin_basename( __FILE__ ),
	 array(&$vcsetting, 'add_plugin_settings_link')
);																			// adds a link to the settings page under the plugin's name on plugin list
add_filter( 'vcat_plugins_list', array(&$vcsetting, 'add_plugins_list_info') );		// adds an entry for this plugin in the VCAT EDULABS main settings page


add_action( 'wp_enqueue_scripts', array(&$vcmain, 'enqueue_google_maps_scripts') );		// is the proper hook to use when enqueuing items that are meant to appear on the front end, it is used for enqueuing both: scripts & styles 

add_shortcode( 'vcat-dpagm', array(&$vcmain, 'display_posts_at_google_maps') );
add_shortcode( 'vcat-dpagm-mini', array(&$vcmain, 'display_posts_at_google_maps_mini') );

add_action('add_meta_boxes', array(&$vccostum, 'custom_fields_init') );						// triggers when the edit screen is loading

add_action('delete_post', array(&$vcdb, 'delete_data') );								// is fired before and after a post (or page) is deleted from the database 

add_action('save_post', array(&$vccostum, 'custom_fields_save') );							// is triggered whenever a post or page is created or updated

add_filter('manage_post_posts_columns', array(&$vcquick, 'add_post_column') );			// is a filter applied to the columns shown when listing posts
add_filter('manage_page_posts_columns', array(&$vcquick, 'add_post_column') );			// is a filter applied to the columns shown when listing pages
add_action('manage_posts_custom_column', array(&$vcquick, 'render_post_columns'), 10, 2 );// Only combined with manage_post_posts_columns filter! this allows you to add custom columns to posts
add_action('manage_pages_custom_column', array(&$vcquick, 'render_post_columns'), 10, 2 );// Only combined with manage_page_posts_columns filter! this allows you to add custom columns to pages
add_action('quick_edit_custom_box', array(&$vcquick, 'add_quick_edit'), 10, 2 );			// is triggered when quick editing, this action is called one time for each custom column!!

/** NiDa: really necessary?
add_action('save_post', 'quick_edit_save'); 							// is triggered whenever a post or page is created or updated
**/

//pre-input for the data in the quick edit is only available through javascript
add_action('admin_footer', array(&$vcquick, 'quick_edit_javascript') );					// is triggered at the end of the admin panel inside the <body>-tag
add_filter('post_row_actions', array(&$vcquick, 'expand_quick_edit_link'), 10, 2 );		// allows to modify the row action links for non-hierarchical post types(e.g. posts)
add_filter('page_row_actions', array(&$vcquick, 'expand_quick_edit_link'), 10, 2 );  		// allows to modify the row action links for hierarchical post types(e.g. pages)

//register_activation_hook( __FILE__, 'db_install' );					// this hook will cause our function to run when the plugin is activated
add_action('init', array(&$vcdb, 'version_checker'));							// this hook runs after wp loads completly but before other stuff is happening
																				// due to the fact that the init even runs right after activating the Plugin we don'T need the other hook anymore

add_filter( 'posts_clauses', array(&$vcdb, 'posts_clauses_filter'), 10, 2 );			// adds own clauses to the standart wp_query request
?>