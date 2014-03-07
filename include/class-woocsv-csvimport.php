<?php
class woocsvImport
{
	public $version = '1.2.8';
	
	public $options;
	
	public $plugin_url;
	
	public $header;

	public $message;

	public $options_default = array (
		'seperator'=>',',
		'skipfirstline'=>1,
		'upload_dir' => '/csvimport/',
		'blocksize' => 1,
		'language' => 'EN',
		'add_to_gallery' => 1,
		'merge_products'=>1,
		'add_to_categories'=>1,
		'debug'=>0,
		'version' =>'1.2.8'
	);

	public $fields = array (
		0 =>'sku',
		1 =>'post_name',
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
		16 =>'images', //depreciated since 1.2.0, will be removed in 1.4.0
		17 =>'tax_status',
		18 =>'tax_class' ,
		19 => 'stock_status', 	// instock, outofstock
		20 => 'visibility', 	// visible, catelog, search, hidden
		21 => 'backorders', 	// yes,no
		22 => 'featured',		// yes,no
		23 => 'manage_stock', 	// yes,no
		24 => 'featured_image',
		25 => 'product_gallery',
		26 => 'shipping_class',
		27 => 'comment_status', //closed, open
		//28 => 'change_stock', // +1 -1 + 5 -8
	);


	public function __construct()
	{
		register_activation_hook( __FILE__, array($this, 'install' ));
		$options = get_option('woocsv-options');
		if (empty($options)) {
			update_option('woocsv-options', $this->options_default);
		}
		$this->options = get_option('woocsv-options');
		$this->checkInstall();
		$this->checkOptions();
		$this->fillHeader();
	}

	/* !1.2.7 plugins url */
	
	public function plugin_url() {
		if ( $this->plugin_url ) return $this->plugin_url;
		return $this->plugin_url = untrailingslashit( plugins_url( '/', __FILE__ ) );
	}


	public function install()
	{
		$upload_dir = wp_upload_dir();
		$dir = $upload_dir['basedir'] .'/csvimport/';
		@mkdir($dir);
	}

	public function fillHeader() {
		$header = get_option('woocsv-header');
		if (!empty($header))
			$this->header = $header;
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
		if ($update) {
			update_option('woocsv-options',$options);			
			$this->options = $options;
		}
	}

	public function checkInstall()
	{
		$message = $this->message;

		if (!get_option('woocsv-options'))
			$message .= '<p>Please save your settings!</p>';

		$upload_dir = wp_upload_dir();
		$dir = $upload_dir['basedir'] .'/csvimport/';
		if  (!is_dir($dir))
			@mkdir($dir);
		
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