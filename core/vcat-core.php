<?php

load_plugin_textdomain('vccore', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/');

require_once("sidebar.php");

/**
 * checks if the VCAT EDULABS main options page does not exist and creates it,
 * if necessary
 */
if( !function_exists( 'vcat_core_create_main_options_page' ) ) {
	function vcat_core_create_main_options_page()  {
		global $menu;
	
		$exists = FALSE;
		$hdl = 'vcat-options';
		
		foreach( $menu as $item ) {
			if( $item[ 2 ] ==  $hdl ) {
				$exists = TRUE;
				break;
			}	
		}
	
		if( $exists == FALSE ) {
	   		add_menu_page( __('VCAT EDULABS', 'vccore'), __('VCAT EDULABS', 'vccore'), 'manage_options', $hdl, 'vcat_core_main_options_page',  plugin_dir_url( __FILE__ ) . 'images/favicon.ico', 26.0223120 );
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
		vcat_core_backend_header( __( 'Plugin Overview', 'vccore' ), 'vcat_wp_core' );
	
		echo '<p>' . __('These VCAT EDULABS Plugins are installed in your WordPress:', 'vccore' ) . '</p>';
		
		$vcat_plugins_data = apply_filters( 'vcat_plugins_list', array() );
		
		if( sizeof( $vcat_plugins_data ) == 0 ) {
			echo '<div class="error">';
			echo '<h3>' . __( 'Sorry, no VCAT EDULABS Plugin found!', 'vccore' ) . '</h3>';
			echo '<p>' . __( 'Visit <a href="http://vcat.de/edulabs/" target="_blank">our website</a> or find <a href="http://profiles.wordpress.org/vcatconsulting/" target="_blank">our plugins at WordPress</a>...', 'vccore' ) . '</p>';
			echo '</div>';
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
	function vcat_core_backend_header( $title = "", $slug = "" ) {

		vcat_core_get_backend_sidebar( $slug );
		
		echo '<div class="wrap vcat-edulabs">';
		// dadz 22042015
		//screen_icon( 'vcat-edulabs' );
		echo '<h2>' . __( 'VCAT EDULABS', 'vccore' ) . ' ' . $title . '</h2>';
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
