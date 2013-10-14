<?php
/*
Plugin Name: Woocommerce CSV Import
Plugin URI: http://allaerd.org/
Description: Import CSV files in Woocommerce
Version: 1.2.1
Author: Allaerd Mensonides
License: GPLv2 or later
Author URI: http://allaerd.org
parent: woocommerce
*/

//include the classes
include dirname( __FILE__ ) . '/include/woocommerce-csvimport.php';
include dirname( __FILE__ ) . '/include/woocommerce-csvimport-admin.php';
include dirname( __FILE__ ) . '/include/woocommerce-csvimport-admin-header.php';
include dirname( __FILE__ ) . '/include/woocommerce-csvimport-admin-info.php';
include dirname( __FILE__ ) . '/include/woocommerce-csvimport-admin-settings.php';
include dirname( __FILE__ ) . '/include/woocommerce-csvimport-admin-import.php';
include dirname( __FILE__ ) . '/include/woocommerce-csvimport-product.php';


//global stuff
$woocsvImport = new woocsvImport();
$wooProduct = '';

woocsvAdmin::start();

$max_upload = (int)(@ini_get('upload_max_filesize'));
$max_post = (int)(@ini_get('post_max_size'));
$memory_limit = (int)(@ini_get('memory_limit'));

$upload_mb = min($max_upload, $max_post, $memory_limit).'MB';
unset($max_upload,$max_post, $memory_limit );

//some things
@ini_set('auto_detect_line_endings', true);

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


?>