<?php
/**
 * initializes VCAT's Geo meta box
 */
function vcat_geo_custom_fields_init()
{
    wp_enqueue_style(
    	'vcat_geo_meta_css',
    	plugins_url( '/styles/meta.css', __FILE__ )
	);
	/* dadz 24042015 */
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
 
    foreach( array('post','page') as $type )
    {
        add_meta_box( 'vcat_geo_custom_fields_meta', __('VCAT Geo Data','vcgmapsatposts'), 'vcat_geo_custom_fields_setup', $type, 'normal', 'high' );
    }
} 

/**
 * sets the geo meta box up, gives it's design and if given the current values
 */
function vcat_geo_custom_fields_setup()
{
    global $post;
	
	$args = array( 'post_type' => array('page','post'), 'posts_per_page' => 1, 'p'=>$post->ID);
    $current = new WP_Query( $args );
	$post = $current->post;
	
    // including the actual, seeable meta box
    include( plugin_dir_path( __FILE__ ) . 'custom/meta.php' );
	
	echo '<input type="hidden" name="vcat_custom_fields_nonce" value="' . wp_create_nonce( __FILE__ ) . '" />';
}
  
/**
 * secures the saving progress for the geo data and collects the data for the real saving progress
 *
 * @param $post_id	the ID of the post to save
 */
function vcat_geo_custom_fields_save( $post_id ) {
    if( !current_user_can( ( $_POST['post_type'] == 'page' ) ? 'edit_page' : 'edit_post', $post_id ) )
    	return $post_id;
      
	$vcat_edit_type = $_POST[ '_vcat_type' ];
	
	if( $vcat_edit_type == "custom_fields" ) {
	    if( !wp_verify_nonce( $_POST[ 'vcat_custom_fields_nonce' ], __FILE__ ) )
	    	return $post_id;


		// dadz 21042015 latlng abfangen
		if( empty( $_POST[ 'location' ] ) ){
			$new_data = $_POST[ '_vcat_custom_fields' ];	
		} else {
			$new_data = $_POST[ 'location' ];
		}
	} elseif( $vcat_edit_type == "quick_edit" ) {
	    if ( !wp_verify_nonce( $_POST[ 'vcat_quickedit_field_nonce' ], 'vcat' . $post_id ) )
	    	return $post_id;
    	$new_data = $_POST[ '_vcat_quick_edit' ];

		global $post;
		$post = get_post( $post_id );
		setup_postdata( $post );
	} else {
		return $post_id;
	}
 
    vcat_geo_custom_fields_clean( $new_data ); 

	$result = vcat_geo_install_data( $new_data );
}


/**
 * removes empty, useless strings in the Array
 * 
 * @param &$arr	the array to be cleaned
 */
function vcat_geo_custom_fields_clean(&$arr)
{
	
    if (is_array($arr))
    {
        foreach ($arr as $i => $v)
        {
            if (is_array($arr[$i]))
            {
                vcat_geo_custom_fields_clean($arr[$i]);
 
                if (!count($arr[$i]))
                {
                    unset($arr[$i]);
                }
            }
            else
            {
                if (trim($arr[$i]) == '')
                {
                    unset($arr[$i]);
                }
            }
        }
 
        if (!count($arr))
        {
            $arr = NULL;
        }
    }
}
 
?>