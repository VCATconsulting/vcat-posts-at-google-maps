<?php
/**
 * adds a column called "Adresse" to the standart columns
 * 
 * @param $columns	the standart columns for the edit screen
 * 
 * @return $columns	the standart columns + 1
 */
function vcat_geo_add_post_column($columns) {
    $columns['post_address'] = __('Address','vcgmapsatposts');
    return $columns;
}

/**
 * sets and fills the custom column up
 * 
 * @param $column_name	the name of the current column		
 */
 function vcat_geo_render_post_columns( $column_name ) {
    switch ($column_name) {
    	case 'post_address':
			global $post;    
			
			/**
			 * Robin: after a quickedit, he won't get our table data out of $post on his own, so he shall make a new request, if he ain't got data from our Table 
			 */
			if ($post->lat==NULL) {
				$args = array( 'post_type' => array( 'page', 'post' ), 'posts_per_page' => 1, 'p' => $post->ID );
	   			$current = new WP_Query( $args );
				$post=$current->post;
			}
			
			if( isset( $post->lat ) )
				echo $post->str . ", " . $post->plz . " " . $post->ort . "<br/>" . __("Latitude",'vcgmapsatposts') . ": " . $post->lat . " / " . __("Longitude",'vcgmapsatposts') . ": " . $post->lng;
		default:
			echo '';
    }	
}  

/**
 * sets the quick edit field up
 * 
 * @param $column_name	the name of the current column		
 */
function vcat_geo_add_quick_edit($column_name) {
    if ($column_name != 'post_address') return;
	
    include( plugin_dir_path( __FILE__ ) . 'quickedit/meta.php' );
}
 
/**
 * secures the saving progress for the geo data and collects the data for the real saving progress
 * 
 * @param $post_id	the ID of the post to save
 */
 
/** NiDa: really necessary?
  
function vcat_geo_quick_edit_save( $post_id ) {

	error_log( 'save post (' . $post_id . ') by quick_edit' );
		
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
        return $post_id;   
	
    if ( !wp_verify_nonce( $_POST[ 'vcat_quickedit_field_nonce' ], 'vcat' . $post_id ) ) return $post_id;
	
    if ( 'page' == $_POST['post_type'] ) {
        if ( !current_user_can( 'edit_page', $post_id ) ) return $post_id;
    } else {
        if ( !current_user_can( 'edit_post', $post_id ) ) return $post_id;
    }  

    $new_data = $_POST['_vcat_quick_edit'];
	
	vcat_geo_custom_fields_clean( $new_data ); 
	
    vcat_geo_install_data( $post_id, $new_data );
} 

**/
 
 
/**
 * puts the insert_data() function in to the footer for the edit-screens for post and pages
 */ 
function vcat_geo_quick_edit_javascript() {
    global $current_screen;
    if( ( ( $current_screen->id != 'edit-post') && ( $current_screen->id != 'edit-page') )
     || ( ( $current_screen->post_type != 'post') && ( $current_screen->post_type != 'page' ) ) ) return;

    ?>
    <script type="text/javascript">
    <!-- -->
    /**
 	* javascript function, which inserts the current geo datas into the quick-edit fields
 	* 
 	* @param str	the street value
 	* @param plz	the postcode value
 	* @param ort	the cityname value
 	* @param nonce	the security value
 	* @param color	
 	*/
    function insert_data(str, plz, ort, nonce, color) {
        // revert Quick Edit menu so that it refreshes properly
        inlineEditPost.revert();
        var strInput = document.getElementById('quick_edit_str');
        var plzInput = document.getElementById('quick_edit_plz');
        var ortInput = document.getElementById('quick_edit_ort');
      	var nonceInput = document.getElementById('vcat_quickedit_field_nonce');
      	var colorInput = document.getElementById('quick_edit_color');
      	
		//only way to access the correct option per js
		for(var i=colorInput.length-1; i>0; i--) { 
			if(colorInput[i].value==color) {
				colorInput.options[i].defaultSelected = true;
			}else{
				colorInput.options[i].defaultSelected = false;
			}
		}
		
		nonceInput.value = nonce;
		strInput.value = str;
		plzInput.value = plz;
		ortInput.value = ort;
    }
    </script>
    <?php
}

/**
 * adds the action to call the insert_data() for posts and pages, when quick editing them 
 * 
 * @param $actions	the current action list for posts/pages(different hooks)
 * @param $post		the current post data for the calling post
 */
function vcat_geo_expand_quick_edit_link($actions, $post) {
    global $current_screen;


	/**
	 * Robin: after a quickedit, he won't get our table data out of $post on his own, so he shall make a new request, if he ain't got data from our Table 
	 */
	if ($post->lat==NULL) {
		$args = array( 'post_type' => array( 'page', 'post' ), 'posts_per_page' => 1, 'p' => $post->ID );
		$current = new WP_Query( $args );
		$post=$current->post;
	}

	$nonce = wp_create_nonce( 'vcat'.$post->ID );
	$str = $post->str;
	$plz = $post->plz;
	$ort = $post->ort;
	$color = $post->color;
		
    $actions['inline hide-if-no-js'] = '<a href="#" class="editinline" title="';
    $actions['inline hide-if-no-js'] .= esc_attr( __( 'Edit this item inline', 'vcgmapsatposts' ) ) . '" ';
    $actions['inline hide-if-no-js'] .= " onclick=\"insert_data('{$str}','{$plz}','{$ort}','{$nonce}','{$color}')\">";
    $actions['inline hide-if-no-js'] .= __( 'Quick-Edit', 'vcgmapsatposts' );
    $actions['inline hide-if-no-js'] .= '</a>';
    return $actions;   
}

?>
