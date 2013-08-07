<?php
/*
Plugin Name: Woocommerce CSV Import
#Plugin URI: http://allaerd.org/woocommerce-csv-importer/
Description: Import CSV files in Woocommerce
Version: 1.1.3
Author: Allaerd Mensonides
License: GPLv2 or later
Author URI: http://allaerd.org
parent: woocommerce
*/

//include the fuctions
include dirname( __FILE__ ) . '/include/woocommerce-csvimport-admin.php';
include dirname( __FILE__ ) . '/include/woocommerce-csvimport-admin-header.php';
include dirname( __FILE__ ) . '/include/woocommerce-csvimport-admin-settings.php';
include dirname( __FILE__ ) . '/include/woocommerce-csvimport-admin-import.php';
include dirname( __FILE__ ) . '/include/woocommerce-csvimport-product.php';

class woocsvImport
{
	public $options;

	public $message;

	public $options_default = array (
		'seperator'=>',',
		'skipfirstline'=>1,
		'upload_dir' => '/csvimport/',
		'blocksize' => 10,
		'language' => 'EN',
		'add_to_gallery' => 1,
	);

	public $fields = array (
		0 =>'sku',
		2 =>'post_status',
		3 =>'post_title',
		4 =>'post_content',
		5 =>'post_excerpt',
		6 =>'category',
		7 =>'tags',
		8 =>'stock',
		9 =>'price',
		10 =>'regular_price',
		11 =>'sale_price',
		12 =>'weight' ,
		13 =>'length',
		14 =>'width' ,
		15 =>'height' ,
		16 =>'images',
		17 =>'tax_status',
		18 =>'tax_class' ,
		19 => 'stock_status', 	// instock, outofstock
		20 => 'visibility', 	// yes,no
		21 => 'backorders', 	// yes,no
		22 => 'featured',		// yes,no
		23 => 'manage_stock', 	// yes/no
	);

	public function __construct()
	{
		$this->init();
	}

	public function init()
	{
		register_activation_hook( __FILE__, array($this, 'install' ));
		$options = get_option('woocsv-options');
		if (empty($options)) {
			update_option('woocsv-options', $this->options_default);
		}
		$this->options = get_option('woocsv-options');
		do_action ('woocsv_main_init');
		$this->checkInstall();
		$this->checkOptions();
	}

	public function install()
	{
		$upload_dir = wp_upload_dir();
		$dir = $upload_dir['basedir'] .'/csvimport/';
		@mkdir($dir);
	}

	public function checkOptions()
	{
		$update = false;
		$options = get_option('woocsv-options');
		$options_default = $this->options_default;
		foreach ($options_default as $key=>$value) {
			if (!isset($options[$key])) {
				$options[$key] = $value;
				$update = true;
			}
		}
		if ($update) 
			update_option('woocsv-options',$options);			
	}

	public function checkInstall()
	{
		$message = '';

		if (!get_option('woocsv-options'))
			$message = '<p>Please save your settings!</p>';

		$upload_dir = wp_upload_dir();
		if (!is_writable($upload_dir['basedir'] .'/csvimport/'))
			$message .= '<p>Upload directory is not writable, please check you permissions</p>';

		$this->message = $message;
		if ($message)
			add_action( 'admin_notices', array($this, 'showWarning'));

	}

	public function showWarning()
	{
		global $current_screen;
		if ($current_screen->parent_base == 'woocsv_import' )
			echo '<div class="error"><p>'.$this->message.'</p></div>';
	}


}

$woocsvImport = new woocsvImport();
$woocsvImportAdmin = new woocsvImportAdmin();

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


?>