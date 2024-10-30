<?php 

class Mozscape_Admin {
	
	function __construct() {
		add_action( 'admin_init', array( $this, 'options_init' ) );
		add_action( 'admin_menu', array( $this, 'register_wp_mozscape' ) );
	}
	
	function options_init() {
		register_setting( 'mozscape_options', 'moz' );
	}
	
	function register_wp_mozscape() {
		add_menu_page( __( 'Mozscape Plugin Configuration', 'wp-mozscape' ), __( 'Mozscape', 'wp-mozscape' ), 'manage_options', 'mozscape_dashboard', array( $this, 'config_page' ), MOZ_URL . 'images/mozscape.gif' );
		
	}
	
	/**
	 * Loads Dashboard page.
	 */
	function config_page() {
		if ( isset( $_GET['page'] ) && 'mozscape_dashboard' == $_GET['page'] )
			include( MOZ_PATH . '/admin/pages/dashboard.php' );
	}
	
}

global $mozscape_admin;
$mozscape = new Mozscape_Admin();	
?>