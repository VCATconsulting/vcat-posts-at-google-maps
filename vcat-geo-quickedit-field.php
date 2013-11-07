<?php
/**
 * adds a column called "Adresse" to the standart columns
 * 
 * @param $columns	the standart columns for the edit screen
 * 
 * @return $columns	the standart columns + 1
 */
function vcat_add_post_column($columns) {
    $columns['post_address'] = 'Adresse';
    return $columns;
}

/**
 * sets and fills the custom column up
 * 
 * @param $column_name	the name of the current column		
 */
 function vcat_render_post_columns($column_name) {
    switch ($column_name) {
    case 'post_address':

	global $post;    

	if (isset($post->lat)) echo $post->str.", ".$post->plz." ".$post->ort."<br> Latitude.: ".$post->lat." / Longitude.: ".$post->lng;
    else echo '---';

    break;
    }	
}  

/**
 * sets the quick edit field up
 * 
 * @param $column_name	the name of the current column		
 */
function vcat_add_quick_edit($column_name) {
    if ($column_name != 'post_address') return;
	
	include(PLUGIN_FOLDER . '/quickedit/meta.php');
}
 
/**
 * secures the saving progress for the geo data and collects the data for the real saving progress
 * 
 * @param $post_id	the ID of the post to save
 */
function vcat_quick_edit_save($post_id) {
		
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
        return $post_id;   
	
    if (!wp_verify_nonce($_POST['vcat_quickedit_field_nonce'], 'vcat'.$post_id)) return $post_id;
	
    if ( 'page' == $_POST['post_type'] ) 
    {
        if ( !current_user_can( 'edit_page', $post_id ) ) return $post_id;
    } 
    else 
    {
        if ( !current_user_can( 'edit_post', $post_id ) ) return $post_id;
    }  
    
    $new_data = $_POST['_vcat_quick_edit'];
	
	vcat_custom_fields_clean($new_data); 
	
    vcat_install_data($new_data);
    
} 
 
/**
 * puts the insert_data() function in to the footer for the edit-screens for post and pages
 */ 
function vcat_quick_edit_javascript() {
    global $current_screen;
    if ((($current_screen->id != 'edit-post')&&($current_screen->id != 'edit-page')) || (($current_screen->post_type != 'post')&&($current_screen->post_type != 'page'))) return

    ?>
    <script type="text/javascript">
    <!--
    /**
 	* javascript function, which inserts the current geo datas into the quick-edit fields
 	* 
 	* @param str	the street value
 	* @param plz	the postcode value
 	* @param ort	the cityname value
 	* @param nonce	the security value
 	*/
    function insert_data(str, plz, ort, nonce) {
        // revert Quick Edit menu so that it refreshes properly
        inlineEditPost.revert();
        var strInput = document.getElementById('quick_edit_str');
        var plzInput = document.getElementById('quick_edit_plz');
        var ortInput = document.getElementById('quick_edit_ort');
      	var nonceInput = document.getElementById('vcat_quickedit_field_nonce');
      	
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
function vcat_expand_quick_edit_link($actions, $post) {
    global $current_screen;

	$nonce = wp_create_nonce( 'vcat'.$post->ID);
	$str = $post->str;
	$plz = $post->plz;
	$ort = $post->ort;
		
    $actions['inline hide-if-no-js'] = '<a href="#" class="editinline" title="';
    $actions['inline hide-if-no-js'] .= esc_attr( __( 'Edit this item inline' ) ) . '" ';
    $actions['inline hide-if-no-js'] .= " onclick=\"insert_data('{$str}','{$plz}','{$ort}','{$nonce}')\">";
    $actions['inline hide-if-no-js'] .= __( 'Quick&nbsp;Edit' );
    $actions['inline hide-if-no-js'] .= '</a>';
    return $actions;   
}

?>
