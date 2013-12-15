<?php
global $VCAT_GEO_PI_TABLE;
$VCAT_GEO_PI_TABLE  = "wp_vcat_geo_plugin";

/**
 * creates a new table in the database for the geo datas
 */
function vcat_geo_db_install() {
   global $wpdb;
   global $VCAT_GEO_PI_TABLE ;
   global $vcat_db_version;

   $vcat_db_version = "1.0";
   
   $sql = "CREATE TABLE IF NOT EXISTS $VCAT_GEO_PI_TABLE (
      id INTEGER NOT NULL AUTO_INCREMENT,
      post_id INTEGER NOT NULL,
      lat FLOAT NOT NULL,
      lng FLOAT NOT NULL,
      str LONGTEXT, 
      plz INTEGER(5) ZEROFILL, 
      ort VARCHAR(20), 
      UNIQUE KEY id (id)
    );";

   require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
   dbDelta( $sql );
 
   add_option( "vcat_db_version", $vcat_db_version );
}

/**
 *	function for saving and updating data in the table and in given cases redirecting to the delete function 
 *
 * @param $data	the data which shall be saved in the database
 */
function vcat_geo_install_data( $data ) {
 	global $wpdb;
	global $post;
 	global $VCAT_GEO_PI_TABLE ;
 	
 	require_once( ABSPATH . 'wp-config.php');
	
	$current_data = false;

	$args = array( 'post_type' => array('page','post'), 'posts_per_page' => 1, 'p'=>$post->ID );
    $current = new WP_Query( $args );
	
	if( isset( $current->post->lat ) ) {
		$current_data = array(
			'lat' => $current->post->lat,
			'lng' => $current->post->lng,
			'str' => $current->post->str,
			'plz' => $current->post->plz,
			'ort' => $current->post->ort
		);
  	}
    
	if( !is_null( $data ) ) {
		$latlng = vcat_geo_get_lat_lng_by_address( $data['str']." ".$data['plz']." ".$data['ort'] );
	}  
	
    if( $current_data ) { // latlng data allready exist
        if( is_null( $data ) )  { // data have to be removed
			vcat_geo_delete_data();
		} else  { // update existing entry
        	$data = array_merge( $data, $latlng );
 		
		   	$update = $wpdb->update(
		   		$VCAT_GEO_PI_TABLE,
		   		$data,
		   		array(
		   			'post_id' => ( $post->post_type == "revision" && isset( $current->post->lat ) ) ? $current->post->post_id : $post->ID
				)
			);
			
			// cool, everything worked fine
			if( $update == 1 )
				return true;
        }
    } elseif( !is_null( $data ) ) { // new entry wanted
        $data = array_merge( $data, $latlng );
		$data[ 'post_id' ] = ( $post->post_type == "revision" && isset( $current->post->lat ) ) ? $current->post->post_id : $post->ID;
 		$wpdb->insert( $VCAT_GEO_PI_TABLE , $data );
    } 
}

/**
 * function for deleting entries in the table
 */
 function vcat_geo_delete_data() {
	global $wpdb;
    global $VCAT_GEO_PI_TABLE ;
	global $post;
		      
    require_once( ABSPATH . 'wp-config.php');
	
	$wpdb->delete( $VCAT_GEO_PI_TABLE , array('post_id' => $post->ID));
}

/**
 * adds a join to our table and the fields which shall be called from the standart WP_Query request
 *
 * @param $clauses	the request clauses from the standart WP_Query request
 * 
 * @return $clauses	the request clauses after our manipulation
 */
function vcat_geo_posts_clauses_filter($clauses){

	global $wpdb, $VCAT_GEO_PI_TABLE, $post;
	
    $join = &$clauses['join'];
    $join .= " LEFT JOIN $VCAT_GEO_PI_TABLE ON $VCAT_GEO_PI_TABLE.post_id = $wpdb->posts.ID";
	
	$fields = &$clauses['fields'];
	$fields .= ", $VCAT_GEO_PI_TABLE.post_id, $VCAT_GEO_PI_TABLE.lat, $VCAT_GEO_PI_TABLE.lng, $VCAT_GEO_PI_TABLE.str, $VCAT_GEO_PI_TABLE.plz, $VCAT_GEO_PI_TABLE.ort";

	return $clauses;
}
?>