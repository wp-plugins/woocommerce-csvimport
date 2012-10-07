<?php
/*
Plugin Name: Woocommerce CSV Import
Description: Import CSV files in Woocommerce
Version: 0.01
Author: Allaerd Mensonides
License: Buy it!
Author URI: http://allaerd.org
parent: woocommerce
*/

//include the fuctions
include(dirname( __FILE__ ) . '/include/woocommerce-csvimport-functions.php');
include(dirname( __FILE__ ) . '/include/woocommerce-csvimport-admin.php');

//include actions
add_action('admin_notices', 'woocsv_admin_notice');
//add_action('admin_menu', 'submenu_csv_import');

register_activation_hook( __FILE__, 'woocsvimport_activate' );
//register_deactivation_hook($file, 'woocsvimport_deactivate');

add_action('admin_init', 'woocsv_js_and_css');

function woocsv_js_and_css() {
	//include javascript and css
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-tabs');
	wp_enqueue_style('jquery.ui.theme', '/wp-content/plugins/woocommerce-csvimport/css/jquery-ui-1.8.24.custom.css');
	
}

function woocsvimport_activate() {
	if (!is_dir($upload_dir['basedir'].'/csvimport'))
		woocsv_admin_notice ('Import directory niet gevonden. check og /uploads/csvimport bestaat');
}


?>