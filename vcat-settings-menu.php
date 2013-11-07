<?php

/**
 * adds the vcat main menu if it isn't already created by an other VCAT Plug-In
 * and a submenu for the geo plugin to that topmenu
 */
function vcat_add_geo_settings()
{
	global $menu;
	
	foreach ($menu as $menupoint)
	{
		if ($menupoint[2]=="vcat-options")
		{
			$exists=TRUE;
			break;
		}	
	}
	
	if ($exists!=TRUE)
	{
	   	add_menu_page('VCAT EDULABS', 'VCAT EDULABS', 'manage_options', 'vcat-options', 'vcat_main_options_page',  plugin_dir_url( __FILE__ ).'/images/favicon.ico', 26.0223120 );
	}
	   
    add_submenu_page('vcat-options', 'Geo Map Optionen', 'Geo Map Optionen', 'manage_options', 'vcat_geo_settings', 'vcat_geo_map_page');
	
	add_action('admin_init', 'vcat_register_settings' );
	
	wp_enqueue_script( 'jquery-ui-slider' );
	 
	wp_enqueue_style('jquery-ui.css', PLUGIN_PATH . '/backend/jquery-ui-1.10.3.custom.min.css');
	
	wp_enqueue_script(
    	'zoom-slider-js',
    	plugins_url('/scripts/js/zoom-slider.js', __FILE__)
    );

	wp_enqueue_style(
		'vcat-backend-styles',
		plugins_url( '/backend/backend.css', __FILE__ )
	);
	
}

/**
 * displays the page content for the VCAT menu
 */
function vcat_main_options_page()
{
	include( 'backend/sidebar.php' );
?>
		
	<div class="wrap">
		<?php screen_icon( 'vcat-edulabs' ); ?>
		<h2>VCAT EDULABS Einstellungen</h2>
	</div>
<?php
}

/**
 * displays the page content for the geo submenu of the VCAT menu
 */
function vcat_geo_map_page()
{
	include( 'backend/sidebar.php' );
?>
    
	<div class="wrap"> 
		<?php screen_icon( 'vcat-edulabs' ); ?>
	    <h2>VCAT Geo Map Einstellungen</h2>	
		<form method="post" action="options.php"> 
			
			<?php 
			settings_fields( 'vcat_geo_settings' ); 
			do_settings_sections( 'vcat_geo' ); 
			do_settings_sections( 'vcat_geo_mini' ); 
			submit_button(); 
			?>
		
		</form>
	</div>
<?php
}


/*
 * registers the geo map settings
 */
function vcat_register_settings() {
	register_setting( 'vcat_geo_settings', 'vcat_geo_width', 'vcat_settings_validate_px_percent'); 
	register_setting( 'vcat_geo_settings', 'vcat_geo_height', 'vcat_settings_validate_px'); 
	register_setting( 'vcat_geo_settings', 'vcat_geo_center', 'vcat_settings_validate_address'); 
	register_setting( 'vcat_geo_settings', 'vcat_geo_zoom', 'vcat_settings_validate_zoom'); 
	register_setting( 'vcat_geo_settings', 'vcat_geo_target', 'vcat_settings_validate_target'); 
	register_setting( 'vcat_geo_settings', 'vcat_geo_align', 'vcat_settings_validate_align'); 

	add_settings_section('vcat_geo_map_section', 'Große Karte', 'vcat_geo_map_headline', 'vcat_geo');

	add_settings_field('vcat_geo_map_width', 'Breite', 'vcat_geo_map_widthbox', 'vcat_geo', 'vcat_geo_map_section');
	add_settings_field('vcat_geo_map_height', 'Höhe', 'vcat_geo_map_heightbox', 'vcat_geo', 'vcat_geo_map_section');
	add_settings_field('vcat_geo_map_center', 'Zentrum', 'vcat_geo_map_centerbox', 'vcat_geo', 'vcat_geo_map_section');
	add_settings_field('vcat_geo_map_zoom', 'Zoom', 'vcat_geo_map_zoombox', 'vcat_geo', 'vcat_geo_map_section');
	add_settings_field('vcat_geo_map_target', 'Ziel', 'vcat_geo_map_targetbox', 'vcat_geo', 'vcat_geo_map_section');
	add_settings_field('vcat_geo_map_align', 'Ausrichtung', 'vcat_geo_map_alignbox', 'vcat_geo', 'vcat_geo_map_section');
	
	register_setting( 'vcat_geo_settings', 'vcat_geo_mini_width', 'vcat_settings_validate_px_percent_mini'); 
	register_setting( 'vcat_geo_settings', 'vcat_geo_mini_height', 'vcat_settings_validate_px_mini'); 
	register_setting( 'vcat_geo_settings', 'vcat_geo_mini_zoom', 'vcat_settings_validate_zoom_mini'); 
	register_setting( 'vcat_geo_settings', 'vcat_geo_mini_target', 'vcat_settings_validate_target_mini'); 
	register_setting( 'vcat_geo_settings', 'vcat_geo_mini_align', 'vcat_settings_validate_align_mini'); 

	add_settings_section('vcat_geo_mini_map_section', 'Kleine Karte', 'vcat_geo_mini_map_headline', 'vcat_geo_mini');

	add_settings_field('vcat_geo_mini_map_width', 'Breite', 'vcat_geo_mini_map_widthbox', 'vcat_geo_mini', 'vcat_geo_mini_map_section');
	add_settings_field('vcat_geo_mini_map_height', 'Höhe', 'vcat_geo_mini_map_heightbox', 'vcat_geo_mini', 'vcat_geo_mini_map_section');
	add_settings_field('vcat_geo_mini_map_zoom', 'Zoom', 'vcat_geo_mini_map_zoombox', 'vcat_geo_mini', 'vcat_geo_mini_map_section');
	add_settings_field('vcat_geo_mini_map_target', 'Ziel', 'vcat_geo_mini_map_targetbox', 'vcat_geo_mini', 'vcat_geo_mini_map_section');
	add_settings_field('vcat_geo_mini_map_align', 'Ausrichtung', 'vcat_geo_mini_map_alignbox', 'vcat_geo_mini', 'vcat_geo_mini_map_section');
}

/**
 * displays the description for the first (big map) settings section
 */
function vcat_geo_map_headline() {
echo 'Hier können sie die Standardeinstellungen für die große Google Map Karte einstellen, Höhe und Breite bestimmen die Größe der Karte, 
	  während Zentrum und Zoom die Standartausrichtung der Karte bestimmen. Das Ziel bestimmt wohin die Links der verschiedenen Marker führt. ';
}

/**
 * displays the input and description of the big maps width
 */
function vcat_geo_map_widthbox() {
global $VCAT_MAP_DEFAULTS; 	
$width = get_option('vcat_geo_width', $VCAT_MAP_DEFAULTS['width']);

echo "<input id='width' name='vcat_geo_width[width]' type='text' value='".$width['width']."' class='regular-text ".((isset($width['fail'])) ? "fail" : "")."'/>";
echo "<p class='description'> Nur Pixel oder Prozentangaben! </p>";

if(isset($width['fail'])){
	unset($width['fail']);
update_option( 'vcat_geo_width', $width );
}
}

/**
 * displays the input and description of the big maps height
 */
function vcat_geo_map_heightbox() {
global $VCAT_MAP_DEFAULTS; 	
$height = get_option('vcat_geo_height', $VCAT_MAP_DEFAULTS['height']);

echo "<input id='height' name='vcat_geo_height[height]' type='text' value='".$height['height']."' class='regular-text ".((isset($height['fail'])) ? "fail" : "")."'/>";
echo "<p class='description'> Nur Pixelangaben! </p>";

if(isset($height['fail'])){
	unset($height['fail']);
update_option( 'vcat_geo_height', $height );
}
}

/**
 * displays the input and description of the big maps center
 */
function vcat_geo_map_centerbox() {
global $VCAT_MAP_DEFAULTS; 	
$center = get_option('vcat_geo_center', $VCAT_MAP_DEFAULTS['center']);


echo "<input id='center' name='vcat_geo_center[center]' type='text' value='".$center['center']."' class='regular-text ".((isset($center['fail'])) ? "fail" : "")."'/>";
echo "<p class='description'> Adressen dürfen neben Buchstaben(inkl. ä,ö,ü,ß) und Zahlen nur Bindestriche und Punkte enthalten. 
							  Multiple Hausnummern(z.B. 26-53) und Begriffszusätze(z.B. Medienhaus) werden zudem von der Google-API ignoriert!
							  Und für ein möglichst genaues Ergebnis sollten sie diese bei eigenen Adressen weglassen.</p>";
							  
if(isset($center['fail'])){
	unset($center['fail']);
update_option( 'vcat_geo_center', $center );
	}
}

/**
 * displays the input of the big maps zoom
 */
function vcat_geo_map_zoombox() {
global $VCAT_MAP_DEFAULTS; 	
$zoom = get_option('vcat_geo_zoom', $VCAT_MAP_DEFAULTS['zoom']);

//echo "<input id='zoom' name='vcat_geo_zoom[zoom]' type='text' value='".$zoom['zoom']."' class='regular-text ".((isset($zoom['fail'])) ? "fail" : "")."'/>";

echo "<div id='zoom-slider'></div><input id='zoom' name='vcat_geo_zoom[zoom]' value='".$zoom['zoom']."' type='text' readonly/>";


if(isset($zoom['fail'])){
	unset($zoom['fail']);
update_option( 'vcat_geo_zoom', $zoom );
}
}

/**
 * displays the radio buttons of the big maps target
 */
function vcat_geo_map_targetbox() {
global $VCAT_MAP_DEFAULTS; 	
$target = get_option('vcat_geo_target', $VCAT_MAP_DEFAULTS['target']);

echo "<label><input type='radio' name='vcat_geo_target[target]' value='blank' ".(($target['target']=='blank')? "checked": "")." ><span> Neues Fenster/Neuer Tab </span></label><br>
	  <label><input type='radio' name='vcat_geo_target[target]' value='top' ".(($target['target']=='top')? "checked": "")."><span> Aktives Fenster </span></label>";
}

/**
 * displays the radio buttons of the big maps alignment
 */
function vcat_geo_map_alignbox() {
	global $VCAT_MAP_DEFAULTS; 	
	$align = get_option( 'vcat_geo_align', $VCAT_MAP_DEFAULTS[ 'align' ]);

	echo "<label><input type='radio' name='vcat_geo_align[align]' value='left' ".(($align['align']=='left')? "checked": "")." ><span> links </span></label><br>
	  	  <label><input type='radio' name='vcat_geo_align[align]' value='right' ".(($align['align']=='right')? "checked": "")."><span> rechts </span></label>";
}

/**
 * validates the width input, checks if it's a pixel or percent input, and sets the setting back if the input wasn't valid
 */
function vcat_settings_validate_px_percent($input) {
global $VCAT_MAP_DEFAULTS; 	
$width = get_option('vcat_geo_width', $VCAT_MAP_DEFAULTS['width']);	
	
$newinput['width'] = trim($input['width']);
if(!preg_match('/^\d+(px|\%){1}$/', $newinput['width'])) {
$newinput['width'] = $width['width'];	$newinput['fail']='TRUE';}

return $newinput;
}

/**
 * validates the height input, checks if it's a pixel input, and sets the setting back if the input wasn't valid
 */
function vcat_settings_validate_px($input) {
global $VCAT_MAP_DEFAULTS; 	
$height = get_option('vcat_geo_height', $VCAT_MAP_DEFAULTS['height']);

$newinput['height'] = trim($input['height']);
if(!preg_match('/^\d+(px){1}$/', $newinput['height'])) {
$newinput['height'] = $height['height'];	$newinput['fail']='TRUE';}

return $newinput;
}

/**
 * validates the address input, checks if input only contains valid characters, and sets the setting back if the input wasn't valid
 */
function vcat_settings_validate_address($input) {
global $VCAT_MAP_DEFAULTS; 	
$center = get_option('vcat_geo_center', $VCAT_MAP_DEFAULTS['center']);

$newinput['center'] = trim($input['center']);
if(!preg_match('/^[-a-zA-Z0-9äöüßÄÖÜ. ]+$/', $newinput['center'])) {
$newinput['center'] = $center['center'];	$newinput['fail']='TRUE';}

if (!$newinput['center']==$center['center']) {
	$latlng = vcatGetLatLngFromAddress($newinput['center']);
	$newinput['center_lat'] = $latlng['lat'];
	$newinput['center_lng'] = $latlng['lng'];
} else {
	$newinput['center_lat'] = $center['center_lat'];
	$newinput['center_lng'] = $center['center_lng'];
}

return $newinput;
}

/**
 * validates the zoom input, checks if it's a 2-digit number input, and sets the setting back if the input wasn't valid
 */
function vcat_settings_validate_zoom($input) {
global $VCAT_MAP_DEFAULTS; 	
$zoom = get_option('vcat_geo_zoom', $VCAT_MAP_DEFAULTS['zoom']);

$newinput['zoom'] = trim($input['zoom']);
if(!preg_match('/^\d{1,2}$/', $newinput['zoom'])) {
$newinput['zoom'] = $zoom['zoom'];	$newinput['fail']='TRUE';}

return $newinput;
}

/**
 * validates the target input, checks if it's a word input, and sets the setting back if the input wasn't valid
 */
function vcat_settings_validate_target($input) {
global $VCAT_MAP_DEFAULTS; 	
$target = get_option('vcat_geo_target', $VCAT_MAP_DEFAULTS['target']);

$newinput['target'] = trim($input['target']);
if(!preg_match('/^\w+$/', $newinput['target'])) {
$newinput['target'] = $target['target'];}

return $newinput;
}

/**
 * validates the align input, checks if it's a word input, and sets the setting back if the input wasn't valid
 */
function vcat_settings_validate_align( $input ) {
	global $VCAT_MAP_DEFAULTS; 	
	$align = get_option( 'vcat_geo_align', $VCAT_MAP_DEFAULTS[ 'align' ] );

	$newinput[ 'align' ] = trim( $input[ 'align' ] );
	if( !preg_match( '/^\w+$/', $newinput[ 'align' ] ) ) {
		$newinput[ 'align' ] = $align[ 'align' ];
	}

	return $newinput;
}

/**
 * displays the description for the second(small map) settings section
 */
function vcat_geo_mini_map_headline() {
echo 'Hier können sie die Standardeinstellungen für die kleinen Google Map Karten einstellen, Höhe und Breite bestimmen die Größe der Karte, 
	  die Standartausrichtung bildet der jeweilige Post auf der die Minimap zusehen ist zusammen mit dem Zoom. Das Ziel bestimmt wohin die Links der verschiedenen Marker führt.';
}

/**
 * displays the input and description of the small maps width
 */
function vcat_geo_mini_map_widthbox() {
global $VCAT_MINI_MAP_DEFAULTS; 	
$width = get_option('vcat_geo_mini_width', $VCAT_MINI_MAP_DEFAULTS['width']);

echo "<input id='width' name='vcat_geo_mini_width[width]' type='text' value='".$width['width']."' class='regular-text ".((isset($width['fail'])) ? "fail" : "")."'/>";
echo "<p class='description'> Nur Pixel oder Prozentangaben! </p>";

if(isset($width['fail'])){
	unset($width['fail']);
update_option( 'vcat_geo_mini_width', $width );
}
}

/**
 * displays the input and description of the small maps height
 */
function vcat_geo_mini_map_heightbox() {
global $VCAT_MINI_MAP_DEFAULTS; 	
$height = get_option('vcat_geo_mini_height', $VCAT_MINI_MAP_DEFAULTS['height']);

echo "<input id='height' name='vcat_geo_mini_height[height]' type='text' value='".$height['height']."' class='regular-text ".((isset($height['fail'])) ? "fail" : "")."'/>";
echo "<p class='description'> Nur Pixelangaben! </p>";

if(isset($height['fail'])){
	unset($height['fail']);
update_option( 'vcat_geo_mini_height', $height );
}
}

/**
 * displays the input of the small maps zoom
 */
function vcat_geo_mini_map_zoombox() {
global $VCAT_MINI_MAP_DEFAULTS; 	
$zoom = get_option('vcat_geo_mini_zoom', $VCAT_MINI_MAP_DEFAULTS['zoom']);

//echo "<input id='zoom' name='vcat_geo_mini_zoom[zoom]' type='text' value='".$zoom['zoom']."' class='regular-text ".((isset($zoom['fail'])) ? "fail" : "")."'/>";

echo "<div id='mini-zoom-slider'></div><input id='mini_zoom' name='vcat_geo_mini_zoom[zoom]' value='".$zoom['zoom']."' type='text' readonly/>";


if(isset($zoom['fail'])){
	unset($zoom['fail']);
update_option( 'vcat_geo_mini_zoom', $zoom );
}
}

/**
 * displays the radio buttons of the small maps target
 */
function vcat_geo_mini_map_targetbox() {
global $VCAT_MINI_MAP_DEFAULTS; 	
$target = get_option('vcat_geo_mini_target', $VCAT_MINI_MAP_DEFAULTS['target']);

echo "<label><input type='radio' name='vcat_geo_mini_target[target]' value='blank' ".(($target['target']=='blank')? "checked": "")." ><span> Neues Fenster/Neuer Tab </span></label><br>
	  <label><input type='radio' name='vcat_geo_mini_target[target]' value='top' ".(($target['target']=='top')? "checked": "")."><span> Aktives Fenster </span></label>";
}

/**
 * displays the radio buttons of the small maps alignment
 */
function vcat_geo_mini_map_alignbox() {
	global $VCAT_MAP_DEFAULTS; 	
	$align = get_option( 'vcat_geo_mini_align', $VCAT_MAP_DEFAULTS[ 'align' ] );

	echo "<label><input type='radio' name='vcat_geo_mini_align[align]' value='left' ".(($align['align']=='left')? "checked": "")." ><span> links </span></label><br>
	  	  <label><input type='radio' name='vcat_geo_mini_align[align]' value='right' ".(($align['align']=='right')? "checked": "")."><span> rechts </span></label>";
}

/**
 * validates the mini width input, checks if it's a pixel or percent input, and sets the setting back if the input wasn't valid
 */
function vcat_settings_validate_px_percent_mini($input) {
global $VCAT_MINI_MAP_DEFAULTS; 	
$width = get_option('vcat_geo_mini_width', $VCAT_MINI_MAP_DEFAULTS['width']);	
	
$newinput['width'] = trim($input['width']);
if(!preg_match('/^\d+(px|\%){1}$/', $newinput['width'])) {
$newinput['width'] = $width['width'];	$newinput['fail']='TRUE';}

return $newinput;
}

/**
 * validates the mini height input, checks if it's a pixel input, and sets the setting back if the input wasn't valid
 */
function vcat_settings_validate_px_mini($input) {
global $VCAT_MINI_MAP_DEFAULTS; 	
$height = get_option('vcat_geo_mini_height', $VCAT_MINI_MAP_DEFAULTS['height']);

$newinput['height'] = trim($input['height']);
if(!preg_match('/^\d+(px){1}$/', $newinput['height'])) {
$newinput['height'] = $height['height'];	$newinput['fail']='TRUE';}

return $newinput;
}

/**
 * validates the mini zoom input, checks if it's a 2-digit number input, and sets the setting back if the input wasn't valid
 */
function vcat_settings_validate_zoom_mini($input) {
global $VCAT_MINI_MAP_DEFAULTS; 	
$zoom = get_option('vcat_geo_mini_zoom', $VCAT_MINI_MAP_DEFAULTS['zoom']);

$newinput['zoom'] = trim($input['zoom']);
if(!preg_match('/^\d{1,2}$/', $newinput['zoom'])) {
$newinput['zoom'] = $zoom['zoom'];	$newinput['fail']='TRUE';}

return $newinput;
}

/**
 * validates the mini target input, checks if it's a word input, and sets the setting back if the input wasn't valid
 */
function vcat_settings_validate_target_mini($input) {
global $VCAT_MINI_MAP_DEFAULTS; 	
$target = get_option('vcat_geo_mini_target', $VCAT_MINI_MAP_DEFAULTS['target']);

$newinput['target'] = trim($input['target']);
if(!preg_match('/^\w+$/', $newinput['target'])) {
$newinput['target'] = $target['target'];}

return $newinput;
}

/**
 * validates the mini align input, checks if it's a word input, and sets the setting back if the input wasn't valid
 */
function vcat_settings_validate_align_mini( $input ) {
	global $VCAT_MAP_DEFAULTS; 	
	$align = get_option( 'vcat_geo_mini_align', $VCAT_MINI_MAP_DEFAULTS[ 'align' ] );

	$newinput[ 'align' ] = trim( $input[ 'align' ] );
	if( !preg_match( '/^\w+$/', $newinput[ 'align' ] ) ) {
		$newinput[ 'align' ] = $align[ 'align' ];
	}

	return $newinput;
}
