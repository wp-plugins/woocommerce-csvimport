<?php
class woocsv_import
{
	public $separator;
	
	public $addons;

	public $api_url = 'http://allaerd.org/api/wc-api/check_for_updates';	
	
	public $import_log;
	
	public $options;
	
	public $header;
	
	public $headers = array();

	public $message;
	
	public $version = '3.0.1';

	public $options_default = array (
		'woocsv_separator'=>',',
		'woocsv_skip_first_line'=>1,
		'woocsv_blocksize' => 1,
		'woocsv_merge_products'=>1,
		'woocsv_add_to_categories'=>1,
		'woocsv_debug'=>0,
		'woocsv_match_by' => 'sku',
		'woocsv_roles' => array('shop_manager'), 
		'woocsv_match_author_by' => 'login',
		'woocsv_convert_to_utf8' => 1,
	);

	public $fields = array (
		0 =>  'sku',
		1 =>  'post_name',
		2 =>  'post_status',
		3 =>  'post_title',
		4 =>  'post_content',
		5 =>  'post_excerpt',
		6 =>  'category',
		7 =>  'tags',
		8 =>  'stock',
		10 => 'regular_price',
		11 => 'sale_price',
		12 => 'weight' ,
		13 => 'length',
		14 => 'width' ,
		15 => 'height' ,
		17 => 'tax_status',
		18 => 'tax_class' ,
		19 => 'stock_status', 	// instock, outofstock
		20 => 'visibility', 	// visible, catelog, search, hidden
		21 => 'backorders', 	// yes,no
		22 => 'featured',		// yes,no
		23 => 'manage_stock', 	// yes,no
		24 => 'featured_image',
		25 => 'product_gallery',
		26 => 'shipping_class',
		27 => 'comment_status', //closed, open
		28 => 'change_stock', 	// +1 -1 + 5 -8
		29 => 'ID',
		30 => 'ping_status',		// open,closed
		31 => 'menu_order',		
		32 => 'post_author',    //user name or nice name of an user
		33 => 'post_date',
	);


	public function __construct()
	{
		//load dependencies
		$this->load_dependenies();		
		
		// activation hook
		register_activation_hook( __FILE__, array($this, 'install' ));

		//load options
		$this->set_options();

		//check install
		$this->check_install();

		//fill header
		$this->set_header();
		
		//add ajax
		add_action( 'wp_ajax_run_import', array( $this,'run_import' ) );
	}
	
	public function get_roles () {
		return get_option( 'woocsv_roles' );
	}
	
	public function get_match_author_by() {
		return get_option( 'woocsv_match_author_by' );
	}
	
	public function get_match_by () {
		return get_option( 'woocsv_match_by');
	}
	
	public function get_add_to_categories() {
		return get_option( 'woocsv_add_to_categories' );
	}

	public function get_merge_products() {
		return get_option( 'woocsv_merge_products' );
	}
	
	public function get_debug() {
		return get_option( 'woocsv_debug' );
	}
	
	public function get_skip_first_line() {
		return get_option( 'woocsv_skip_first_line' );
	}
	
	public function get_blocksize () {
		return get_option( 'woocsv_blocksize' );
	}
	
	public function get_separator() {
		return get_option( 'woocsv_separator' );
	}
	
	public function load_dependenies () {
		
		//admin
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-woocsv-import-admin.php';
		$woocsv_import_admin = new woocsv_import_admin();
		
		//settings
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'include/class-woocsv-import-settings.php';
		$woocsv_import_settings = new woocsv_import_settings();

		//main product
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'include/class-woocsv-product.php';
	}
	
	public function install()
	{
		$upload_dir = wp_upload_dir();
		$dir = $upload_dir['basedir'] .'/csvimport/';
		@mkdir($dir);
		
		//create options
		$this->set_options();
	}

	public function set_header() {
		
		$header = get_option('woocsv_header');
		if ( $header ) {
			$this->header = $header;
		}
		
		$headers = get_option ('woocsv_headers');
		if ( $headers ) {
			$this->headers = $headers;
		}
	}

	public function set_options() {
		$options = $this->options_default;

		foreach ($options as $key=>$value) {
			if (get_option( $key ) !== FALSE ) {
				$options[substr($key, 7)] = get_option($key);
			} else {
				update_option($key,$value);
			}
		}
		$this->options = $options;
	}

	public function check_install()
	{
		$message = $this->message;

		//old way
		if (!get_option('woocsv-options')) {
			update_option( 'woocsv-options' , $this->options );
		}
		
		//new way
		if (!get_option('woocsv_options')) {
			update_option( 'woocsv_options' , $this->options );
		}		
		
			
		$upload_dir = wp_upload_dir();
		$dir = $upload_dir['basedir'] .'/csvimport/';
		if  (!is_dir($dir))
			@mkdir($dir);
		
		if (!is_writable($upload_dir['basedir'] .'/csvimport/'))
			$message .= __('Upload directory is not writable, please check you permissions','woocsv-import');

		$this->message = $message;
		if ($message)
			add_action( 'admin_notices', array($this, 'show_warning'));

	}

	public function show_warning($message = '')
	{
		global $current_screen;
		if ($current_screen->parent_base == 'woocsv_import' )
			echo '<div class="error"><p>'.$this->message.'</p></div>';
	}

	public function handle_file_upload($from_location, $filename)
	{
		do_action('woocsv_before_csv_upload', $filename);
		$upload_dir = wp_upload_dir();
		$to_location = $upload_dir['basedir'] .'/csvimport/'.$filename;

		if (@move_uploaded_file($from_location, $to_location)) {
			do_action('woocsv_after_csv_upload', $filename);
			return $to_location;
		} else return false;
	}
	
	public function run_import()
	{
		global $woocsv_product, $wpdb;
		
		//reset time to 30
		set_time_limit(0);
		
		//no more cache
		wp_suspend_cache_invalidation ( true );

		//disable term counting
		wp_defer_term_counting( true ) ;

		$post_data = $_POST;
		
		/* solve escape problem when running on windows */
		if (isset($post_data['filename'])) {
			//get the filename and save it
			$filename = $post_data['filename'];
			update_option('woocsv_importfile', $filename);
			unset($post_data['filename']);
		} else {
			$filename = get_option('woocsv_importfile');
		}

		$post_data['batch_filename'] = $filename;

		//we are starting - first time around	
		if (empty($post_data['batch'])) {
		
			do_action ('woocsv_start_import');
						
			$post_data['batch'] = $this->unique_number ();
			
			//create a new batch
			$this->update_batch ($post_data,'running');
		}

		$count = 0;
		$csvcontent = '';
		$handle = fopen($filename, 'r');
		
		//================================
		// only import the rows needed.
		//================================

		while (($line = fgetcsv($handle, 0, $this->get_separator() )) !== FALSE) {
		
			if ( $count >= $post_data['currentrow'] && $count < ( (int)$post_data['currentrow'] + (int)$post_data['blocksize'])  ) {
				//utf-8 support
				if ( get_option('woocsv_convert_to_utf8') ) {
					$line = array_map("utf8_encode", $line);
				}

				$csvContent[$count] = $line;
			}
			$count ++;
		}
		
		unset($handle, $line);

		//========================================================
		// Run only the block from currentrow and the blocksize
		//========================================================
		
		for ($i = 1; $i <= $this->get_blocksize(); $i++) {

			$woocsv_product = new woocsv_import_product;
			$woocsv_product->header = $this->header;
			
			$realRow = (int) $post_data['currentrow'] +1;
			
			//===================
			// We are finished
			//===================

			if ($post_data['currentrow'] >= $post_data['rows'] ) {
				ob_get_clean();
				update_option('woocsv_lastrun', array('date'=>date("Y-m-d H:i:s"), 'filename'=>basename($filename), 'rows'=>$post_data['rows']));
				delete_option('woocsv_importfile');
				do_action('woocsv_after_import_finished');
				
				//finish a new batch
				$this->die_nice($post_data,true);
			}

			// count the rows here else we have a row and than die.
			$this->import_log[] = "--> ".__('row','woocsv-import').":". $realRow ." / ". ((int)$post_data['rows']) ;

			//==================================
			// We want to skip the first line
			//==================================

			if ($this->get_skip_first_line() ==  1 && $post_data['currentrow'] == 0) {
				$post_data['currentrow'] ++;
				$this->import_log[] = __('Skipping the first row','woocsv-import');
				$this->die_nice($post_data);
			}

			//=========================================
			// We do not want to skip the first line
			//=========================================
			
			if ($this->get_skip_first_line() ==  0 && $post_data['currentrow'] == 0) {
				$woocsv_product->raw_data = $csvContent[0];
			} 
			
			
			if ( (int)$post_data['currentrow'] > 0 ) {
				$woocsv_product->raw_data = $csvContent[$post_data['currentrow']];
			} 
				
			$post_data['currentrow'] ++;
			
			
			//=========================
			// Lets fill in the data
			//=========================

			do_action('woocsv_before_fill_in_data' );
			
			$woocsv_product->fill_in_data();

			do_action('woocsv_after_fill_in_data' );

			//===================
			//  lets parse data
			//===================

			$woocsv_product->parse_data();

			//=======================
			// let's save the data
			//=======================

			try {
				$id = $woocsv_product->save();
			} catch (Exception $e) {
				$id = '';
			}

			//===============================================
			// lets fill in the memory stuff for debugging
			//===============================================

			$post_data['memory'] = round(memory_get_usage()/1024/1024, 2);

		}

		//and die nice
		$this->die_nice($post_data);
	}


	public function die_nice($post_data,$done=false)
	{
		global $wpdb,$woocsv_product,$woocsv_import ;
	
		//===================
		// Clear transients
		//===================
		if ( function_exists('wc_delete_product_transients')) {
			wc_delete_product_transients($woocsv_product->body['ID']);
		} else {
			$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '%_transient_%'");
		}
		
		//turn it on
		wp_suspend_cache_invalidation ( false );
		wp_defer_term_counting( false );		
		
		//=============================
		// Check if we need to debug
		//=============================
		if ( 0 == $this->get_debug()) {
			ob_get_clean();
		} else {
			$post_data['product'] = $woocsv_product;
		}

		//===============
		// Add to logs
		//===============
		$post_data['log'] = $this->import_log;

		//add done flag
		if ($done) {
			$post_data['done'] = 1;
			$this->update_batch ($post_data, 'done');
		} else { 
			$post_data['done']= 0;
			$this->update_batch ($post_data, 'paused');
		}
		//unset the product to be sure it's reset for the next run
		unset($woocsv_product);
			
		//echo the json and die nice
		echo json_encode($post_data);
		die();
	}

	public function unique_number() {
    	return substr(md5(uniqid(mt_rand(), true)), 0, 10);
    }
    
    public function update_batch ($post_data = array (), $status= 'new') {
		$batches = array ();
		$batches = get_option('woocsv_batches');
		
		$batch_data= array(
			'currentrow' 	=> $post_data['currentrow'],
			'blocksize' 	=> $post_data['blocksize'],
			'rows' 			=> $post_data['rows'],
			'filename' 		=> $post_data['batch_filename'],
			'batch_status' 	=> "$status",
			'batch' 		=> $post_data['batch'],
			'timestamp'		=> time(),
		);
		
		$batches[$post_data['batch']] = $batch_data;
		update_option('woocsv_batches', $batches);
    }
}