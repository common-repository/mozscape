<?php 
/**
 * Mozscape admin dashboard page.
 */
global $mozscape_admin_pages;

if (!current_user_can('manage_options')) {
	wp_die( __('You do not have sufficient permissions to access this page.') );
}

$mozscape_admin_pages->mozscape_header(__( 'Settings', 'wp-mozscape' ), true);
$mozscape_admin_pages->mozscape_dashboard();
$mozscape_admin_pages->mozscape_footer(true);
?>