<?php

/**
 * initializes VCAT's Geo meta box
 */
function vcat_custom_fields_init()
{
    wp_enqueue_style('meta_css', PLUGIN_PATH . '/styles/meta.css');
 
    foreach (array('post','page') as $type)
    {
        add_meta_box('vcat_custom_fields_meta', 'VCAT Geo Daten', 'vcat_custom_fields_setup', $type, 'normal', 'high');
    }
} 

/**
 * sets the geo meta box up, gives it's design and if given the current values
 */
function vcat_custom_fields_setup()
{
    global $post;
	
	$args = array( 'post_type' => array('page','post'), 'posts_per_page' => 1, 'p'=>$post->ID);
    $current = new WP_Query( $args );
	$post = $current->post;
	
    // including the actual, seeable meta box
    include(PLUGIN_FOLDER . '/custom/meta.php');

    // create a custom nonce for submit verification later
    echo '<input type="hidden" name="vcat_custom_fields_nonce" value="' . wp_create_nonce(__FILE__) . '" />';
}
  
/**
 * secures the saving progress for the geo data and collects the data for the real saving progress
 *
 * @param $post_id	the ID of the post to save
 */
function vcat_custom_fields_save($post_id)
{
 	
    if (!wp_verify_nonce($_POST['vcat_custom_fields_nonce'],__FILE__)) return $post_id;
 	
    if ($_POST['post_type'] == 'page')
    {
        if (!current_user_can('edit_page', $post_id)) return $post_id;
    }
    else
    {
        if (!current_user_can('edit_post', $post_id)) return $post_id;
    }
      
    $new_data = $_POST['_vcat_custom_fields'];
 
    vcat_custom_fields_clean($new_data); 
	 
	vcat_install_data($new_data);
}

/**
 * removes empty, useless strings in the Array
 * 
 * @param &$arr	the array to be cleaned
 */
function vcat_custom_fields_clean(&$arr)
{
	
    if (is_array($arr))
    {
        foreach ($arr as $i => $v)
        {
            if (is_array($arr[$i]))
            {
                vcat_custom_fields_clean($arr[$i]);
 
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