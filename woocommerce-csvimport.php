<?php
/*
Plugin Name: Woocommerce CSV Import
Plugin URI: http://allaerd.org/
Description: Import CSV files in Woocommerce
Version: 1.2.7
Author: Allaerd Mensonides
License: GPLv2 or later
Author URI: http://allaerd.org
parent: woocommerce
*/

//add_action ( 'save_post' , 'woocsv_test_post' );

function woocsv_test_post () {
	echo '<pre>';
	echo var_dump($_POST);
	echo '</pre>';
} 


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//include the classes
include dirname( __FILE__ ) . '/include/class-woocsv-csvimport.php';
include dirname( __FILE__ ) . '/include/class-woocsv-admin.php';
include dirname( __FILE__ ) . '/include/class-woocsv-admin-header.php';
include dirname( __FILE__ ) . '/include/class-woocsv-admin-info.php';
include dirname( __FILE__ ) . '/include/class-woocsv-admin-settings.php';
include dirname( __FILE__ ) . '/include/class-woocsv-admin-import.php';
include dirname( __FILE__ ) . '/include/class-woocsv-product.php';


//global stuff
$woocsvImport = new woocsvImport();
$wooProduct = '';

$max_upload = (int)(@ini_get('upload_max_filesize'));
$max_post = (int)(@ini_get('post_max_size'));
$memory_limit = (int)(@ini_get('memory_limit'));

$upload_mb = min($max_upload, $max_post, $memory_limit).'MB';
unset($max_upload,$max_post, $memory_limit );

@ini_set('auto_detect_line_endings', true);

woocsvAdmin::start();

/* ! 1.2.7 add extra hook*/
do_action ('woocsvBeforeInit');

//add-ons
if (class_exists('woocsvCustomFields')) {
	$woocsvCustomfields = new woocsvCustomFields();
}

if (class_exists('woocsvAttributes')) {
	$woocsvAttributes = new woocsvAttributes();
}

if (class_exists('woocsvVariableProducts')) {
	$woocsvVariableProducts = new woocsvVariableProducts();
}

if (class_exists('woocsvPremium')) {
	$woocsvPremium = new woocsvPremium();
}

if (class_exists('woocsvWPML')) {
	$woocsvWPML = new woocsvWPML();
}

if (class_exists('woocsvCustom')) {
	$woocsvCustom = new woocsvCustom();
}
/* ! 1.2.7 add extra hook*/
do_action ('woocsvAfterInit');

?>