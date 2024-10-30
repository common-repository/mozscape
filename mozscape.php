<?php 
/*
Plugin Name: Mozscape API WordPress Plugin
Version: 1.1.1
Plugin URI: http://www.iteachcoding.com/
Description: A plugin for WordPress that provides Mozscape API data for posts and pages.
Author: George Andrews
Author URI: http://www.iteachcoding.com/
License: GPL v3

Mozscape Plugin
Copyright (C) 2013, George Andrews

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
/**
 * @package Main
 */
if ( !defined('MOZ_PATH') )
	define( 'MOZ_PATH', plugin_dir_path( __FILE__ ) );
	
if ( !defined('MOZ_URL') )
	define( 'MOZ_URL', plugin_dir_url( __FILE__ ) );
	
require MOZ_PATH.'inc/class-mozscape.php';

/**
 * Used to load the required files on the plugins_loaded hook, instead of immediately.
 */
function mozscape_admin_init() {
	
	require MOZ_PATH.'admin/class-admin.php';
	
	global $pagenow;
	
	if ( in_array( $pagenow, array('admin.php') ) )
		require MOZ_PATH.'admin/class-config.php';
}

if (is_admin()) {
	add_action( 'plugins_loaded', 'mozscape_admin_init', 0 );
}

function mozscape_box() {
	$screens = array('post', 'page');
	foreach ($screens as $screen) {
		add_meta_box(
            'mozscape',
            __( 'Moz Analytics', 'wp-mozscape' ),
            'moz_custom_box',
            $screen
        );
	}
}

function moz_custom_box() {
	$moz = new Moz();
	?>
<div class="moz">
	<p>Currently, the Moz Analytics plugin gives you access to URL Metrics, Link Metrics, and Anchor Text 
	Metrics.</p>
	<p>You can adjust the plugin settings in the <a href="<?php echo admin_url( 'admin.php?page=mozscape_dashboard' ) ?>">Mozscape API Dashboard</a>.</p>
	<table class="form-table">
		<?php echo $moz->get_all_moz_data() ?>
	</table>
</div>
<?php
}

add_action("add_meta_boxes", "mozscape_box");
?>