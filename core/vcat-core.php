<?php

require_once("sidebar.php");

/**
 * checks if the VCAT EDULABS main options page does not exist and creates it,
 * if necessary
 */
if( !function_exists( 'vcat_core_create_main_options_page' ) ) {
	function vcat_core_create_main_options_page()  {
		global $menu;
	
		$exists = FALSE;
		$hdl = "vcat-options";
		
		foreach( $menu as $item ) {
			if( $item[ 2 ] ==  $hdl ) {
				$exists = TRUE;
				break;
			}	
		}
	
		if( $exists == FALSE ) {
	   		add_menu_page( 'VCAT EDULABS', 'VCAT EDULABS', 'manage_options', $hdl, 'vcat_core_main_options_page',  plugin_dir_url( __FILE__ ) . 'images/favicon.ico', 26.0223120 );
		}
			
		wp_enqueue_style(
			'vcat-main-backend-styles',
			plugins_url( '/backend.css', __FILE__ )
		);

		return $hdl;
	}
}

/**
 * displays the page content for the VCAT menu
 */
if( !function_exists( 'vcat_core_main_options_page' ) ) {
	function vcat_core_main_options_page() {
		vcat_core_backend_header( 'Plugin-&Uuml;bersicht' );
	
		echo __("<p>Hier finden Sie eine Liste der in Ihrem WordPress installierten VCAT EDULABS Plugins:</p>",'vcgmapsatposts');
		
		$vcat_plugins_data = apply_filters( "vcat_plugins_list", array() );
		
		if( sizeof( $vcat_plugins_data ) == 0 ) {
		echo __('
			<div class="error">
				<h3>Es wurden keine VCAT EDULABS Plugins gefunden!</h3>
				<p>Besuchen Sie <a href="http://vcat.de/edulabs/" target="_blank">unsere Webseite</a> oder finden Sie <a href="http://profiles.wordpress.org/vcatconsulting/" target="_blank">unsere Plugins bei WordPress</a>...</p>
			</div>
		','vcgmapsatposts');
		} else {
			echo '<ul id="vcat-plugins-list">';
			foreach( $vcat_plugins_data as $vcat_plugin ) {
				echo '<li><h3>' . $vcat_plugin[ 'name' ] . '</h3>';
				echo '<a href="' . $vcat_plugin[ 'settings' ]. '"><img src="' . $vcat_plugin[ 'image' ]. '" class="vcat-plugin-image" /></a></li>';
			}
			echo '</ul>';
		}
		
		vcat_core_backend_footer();
	}
}

/**
 * displays the original VCAT EDULABS Backend Header
 */
if( !function_exists( 'vcat_core_backend_header' ) ) {
	function vcat_core_backend_header( $title = "" ) {

		vcat_core_get_backend_sidebar();
		
		echo '<div class="wrap vcat-edulabs">';
		screen_icon( 'vcat-edulabs' );
		echo '<h2>VCAT EDULABS ' . $title . '</h2>';
	}
}

/**
 * displays the original VCAT EDULABS Backend Footer
 */
if( !function_exists( 'vcat_core_backend_footer' ) ) {
	function vcat_core_backend_footer() {
		echo '</div><!-- end of wrapper -->';
	}
}
