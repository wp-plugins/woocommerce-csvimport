<?php
/*
Plugin Name: Woocommerce CSV Import
Plugin URI: http://allaerd.org/woocommerce-csv-importer/
Description: Import CSV files in Woocommerce
Version: 0.6.1
Author: Allaerd Mensonides
License: GPLv2 or later
Author URI: http://allaerd.org
parent: woocommerce
*/

//include the fuctions
include(dirname( __FILE__ ) . '/include/woocommerce-csvimport-functions.php');
include(dirname( __FILE__ ) . '/include/woocommerce-csvimport-admin.php');

//options
$woocsv_options = array(
			'importdir'=> 'csvimport',
			'fixeddir' => 'fixed',
			'deleteimages' => 0,
			'arrayseperator' => '|',
			'fieldseperator' => ',',
			'auto_detect_line_endings' => 0,
			'change_comma_to_dot' => 0,
			);
			
//check options and update them...but preserve the values
function woocsv_check_options() {
	global $woocsv_options;
	$options = get_option('csvimport-options');
	if (!$options) $options = $woocsv_options;
	foreach( $options as $key => $value) {
		$woocsv_options[$key]=$value;
	}
	update_option('csvimport-options',$woocsv_options);
}

//include actions
add_action('admin_notices', 'woocsv_admin_notice');
//add_action('admin_menu', 'submenu_csv_import');

register_activation_hook( __FILE__, 'woocsvimport_activate' );
//register_deactivation_hook($file, 'woocsvimport_deactivate');

add_action('admin_init', 'woocsv_js_and_css');
add_action('admin_init', 'woocsv_check_options');

function woocsv_js_and_css() {
	//include javascript and css
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-tabs');
	wp_enqueue_style('jquery.ui.theme', '/wp-content/plugins/woocommerce-csvimport/css/jquery-ui-1.8.23.custom.css');
}

function woocsvimport_activate() {
global $woocsv_options;
	//set default options
	if (!get_option( 'csvimport-options' )) {
		update_option( 'csvimport-options', $woocsv_options );
	}
}


?>