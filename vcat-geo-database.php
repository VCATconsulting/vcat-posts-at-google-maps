<?php
global $VCAT_GEO_PI_TABLE, $vcat_db_version ;
$VCAT_GEO_PI_TABLE  = "wp_vcat_geo_plugin";
$vcat_db_version = "1.1";


/*
 * checks if the current database is the latest one, else starts the db_install to update 
 * (due to that updating a plugin won't activate the __FILE__ hook)
 */
function vcat_geo_db_version_checker() {
	global $vcat_db_version;
	
	$current_db_version = get_option("vcat_db_version");	  

	if ($current_db_version<$vcat_db_version) {
		vcat_geo_db_install();
	}
}

/**
 * creates a new table in the database for the geo datas
 */
function vcat_geo_db_install() {
   global $wpdb;
   global $VCAT_GEO_PI_TABLE ;
   global $vcat_db_version;

	$sql = "CREATE TABLE $VCAT_GEO_PI_TABLE (
      id INTEGER NOT NULL AUTO_INCREMENT,
      post_id INTEGER NOT NULL,
      lat FLOAT NOT NULL,
      lng FLOAT NOT NULL,
      str LONGTEXT, 
      plz INTEGER(5) ZEROFILL, 
      ort VARCHAR(20), 
      color VARCHAR(20),
      UNIQUE KEY id (id)
    );";
		
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	$echo = dbDelta( $sql );

	update_option( "vcat_db_version", $vcat_db_version );   		
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

	ob_start();
	//var_dump(	$data	);
	$contents = ob_get_contents();
	ob_end_clean();
	//error_log($contents);
	
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

	if( !is_null( $data['ort']||!is_null($data['plz']) ) ) {
	// atleast ort or plz has to exist
		$latlng = vcat_geo_get_lat_lng_by_address( $data['str']." ".$data['plz']." ".$data['ort'] );
	}  
	// dadz 21042015 check lat and lng
	if( !is_null( $data['lat']) && !is_null( $data['lng'])){
		$address = vcat_geo_get_address_by_lat_lng($data['lat'].",".$data['lng']);
	}
	
	// dadz 21042015
    if( $current_data ) { // latlng data allready exist
        if( is_null( $data ) || (count($data)==1 && !is_null($data["color"]) ) )  { // data have to be removed
			vcat_geo_delete_data();
		} else  { // update existing entry
        	if( is_null($address) ){
        		$data = array_merge( $data, $latlng );	
        	} else {
        		$data = array_merge( $data, $address );
        	}
        	
 		
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
    // dadz 21042015 check if location button or save button
 		if( !is_null($address) ){
 			$data = array_merge( $data, $address );
			$data[ 'post_id' ] = ( $post->post_type == "revision" && isset( $current->post->lat ) ) ? $current->post->post_id : $post->ID;
 			$wpdb->insert( $VCAT_GEO_PI_TABLE , $data );
 		} else {
 			$data = array_merge( $data, $latlng );
			$data[ 'post_id' ] = ( $post->post_type == "revision" && isset( $current->post->lat ) ) ? $current->post->post_id : $post->ID;
 			$wpdb->insert( $VCAT_GEO_PI_TABLE , $data );
 		}  
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

	global $wpdb;
    global $VCAT_GEO_PI_TABLE ;
	global $post;
	
    $join = &$clauses['join'];
    $join .= " LEFT JOIN $VCAT_GEO_PI_TABLE ON $VCAT_GEO_PI_TABLE.post_id = $wpdb->posts.ID";
	
	$fields = &$clauses['fields'];
	$fields .= ", $VCAT_GEO_PI_TABLE.post_id, $VCAT_GEO_PI_TABLE.lat, $VCAT_GEO_PI_TABLE.lng, $VCAT_GEO_PI_TABLE.str, $VCAT_GEO_PI_TABLE.plz, $VCAT_GEO_PI_TABLE.ort, $VCAT_GEO_PI_TABLE.color";

	return $clauses;
}
?>