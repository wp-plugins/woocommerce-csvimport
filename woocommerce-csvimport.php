<?php
/*
	Plugin Name: Woocommerce CSV Import
	Plugin URI: http://allaerd.org/
	Description: Import CSV files in Woocommerce
	
	Version: 3.0.5
	
	License: GPLv2 or later
	
	Author: Allaerd Mensonides
	Author URI: http://allaerd.org
	
	Text Domain: woocsv-import
	Domain Path: /languages
*/


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//include the main classes
include dirname( __FILE__ ) . '/include/class-woocsv-import.php';

//global stuff
$woocsv_import = new woocsv_import();
$woocsv_product = '';

//languages
add_action('init', 'woocsv_load_plugin_textdomain');

if (!function_exists('woocsv_load_plugin_textdomain')) {

function woocsv_load_plugin_textdomain()
	{
	    load_plugin_textdomain('woocsv-import', FALSE, dirname(plugin_basename(__FILE__)).'/languages/');
	}
}

@ini_set('auto_detect_line_endings', true);

// the good hook for loading add-ons. others will be removed
do_action ('woocsv_after_init');


/* future stuf
	
// test update

add_filter ('pre_set_site_transient_update_plugins', 'display_transient_update_plugins');

function display_transient_update_plugins ($transient)
{
    var_dump($transient);
}


$batches = get_option('woocsv_batches');

foreach ( rsort ( $batches ) as $b) {
	echo date('m/d/Y H:i:s', $b['timestamp']) . '<br />' ;	
}

add_filter('plugin_row_meta', 'add_support_link' ,10,2);

function add_support_link($links, $file) {
	$plugin = plugin_basename(__FILE__);
	if(!current_user_can('install_plugins')){
		return $links;
	}
	//if($file == $this->plugin_basefile){
	if( $file == $plugin	){ 
		$links[] = '<a href="https://allaerd.org/knowledgebase/" target="_blank">'.__('Docs', 'woocsv-import').'</a>';
		$links[] = '<a href="https://allaerd.org/shop/" target="_blank">'.__('Add-ons', 'woocsv-import').'</a>';			}
	return $links;
}
*/

