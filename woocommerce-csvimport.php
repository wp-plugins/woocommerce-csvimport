<?php
/*
	Plugin Name: Woocommerce CSV Import
	Plugin URI: http://allaerd.org/
	Description: Import CSV files in Woocommerce
	
	Version: 2.2.2
	
	License: GPLv2 or later
	
	Author: Allaerd Mensonides
	Author URI: http://allaerd.org
	
	Text Domain: woocsv-import
	Domain Path: /languages
*/


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//include the classes
include dirname( __FILE__ ) . '/include/class-woocsv-csvimport.php';
include dirname( __FILE__ ) . '/include/class-woocsv-admin.php';
include dirname( __FILE__ ) . '/include/class-woocsv-admin-header.php';
include dirname( __FILE__ ) . '/include/class-woocsv-admin-info.php';
include dirname( __FILE__ ) . '/include/class-woocsv-admin-settings.php';
include dirname( __FILE__ ) . '/include/class-woocsv-admin-import.php';
include dirname( __FILE__ ) . '/include/class-woocsv-product.php';


//help functions
if (!function_exists('notempty')) {
	function notempty($var) {
	    return ($var==="0"||$var);
	}
}

//languages
add_action('init', 'woocsv_load_plugin_textdomain');

if (!function_exists('woocsv_load_plugin_textdomain')) {

function woocsv_load_plugin_textdomain()
	{
	    load_plugin_textdomain('woocsv-import', FALSE, dirname(plugin_basename(__FILE__)).'/languages/');
	}
}


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

do_action ('woocsvBeforeInit');

//add-ons
if (class_exists('woocsvCustomFields')) {
	$woocsvCustomFields = new woocsvCustomFields();
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
// the good hook for loading add-ons. others will be removed
do_action ('woocsvAfterInit');

?>
