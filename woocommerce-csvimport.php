<?php
/*
Plugin Name: Woocommerce CSV Import
#Plugin URI: http://allaerd.org/woocommerce-csv-importer/
Description: Import CSV files in Woocommerce
Version: 1.2.0
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


//some things
ini_set('auto_detect_line_endings', true);

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