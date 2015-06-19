<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class woocsv_import_product
{
	// @since 3.0.2 skip products if flag is set to true during runtime
	public $skip = false;

	public $new = true;
	
	public $header = array();

	public $tags = array();

	public $categories = array();

	public $images = array();

	public $raw_data = array();

	public $shipping_class = '';

	public $featured_image = '';

	public $product_gallery = '';
	
	public $product_type = 'simple';

	//body
	public $body = array(
		'ID' => '',
		'post_type'  => 'product',
		'post_status' => 'publish',
		'post_title' => '',
		'post_name'  => '',
		'post_date'  => '',
		'post_date_gmt' => '',
		'post_content' => '',
		'post_excerpt' => '',
		'post_parent' => 0,
		'post_password' => '',
		'comment_status'=> 'open',
		'ping_status'=>'open',
		'menu_order'=> 0,
		'post_author' => '',
	);

	public $meta = array(
		'_sku'   => '',
		'_downloadable'  => 'no',
		'_virtual'   => 'no',
		'_price'   => '',
		'_visibility' => 'visible',
		'_stock'   => '',
		'_stock_status' => 'instock',
		'_backorders' => 'no',
		'_manage_stock' => 'yes',
		'_sale_price' => '',
		'_regular_price' => '',
		'_weight'  => '',
		'_length'  => '',
		'_width'   => '',
		'_height'  => '',
		'_tax_status' => 'taxable',
		'_tax_class'  => '',
		'_upsell_ids' => array(),
		'_crosssell_ids' => array(),
		'_sale_price_dates_from' => '',
		'_sale_price_dates_to'  => '',
		'_min_variation_price' => '',
		'_max_variation_price' => '',
		'_min_variation_regular_price' => '',
		'_max_variation_regular_price' => '',
		'_min_variation_sale_price' => '',
		'_max_variation_sale_price' => '',
		'_featured'  => 'no',
		'_file_path'  => '',
		'_download_limit' => '',
		'_download_expiry' => '',
		'_product_url' => '',
		'_button_text' => '',
//		'total_sales'=>0,
	);

	public function parse_data(){
		global $woocsv_import;
		
		//===================
		// check body data and fill in the log
		//===================

		//add action before parsing all data
		do_action('woocsv_before_parse_data');
		
		//check content, title, name
		if (	empty($this->body['post_title']) && 
				empty($this->body['post_name']) &&
				empty($this->body['post_content']) && 
				$this->body['post_type'] == 'product'
			) {
			$woocsv_import->import_log[] = 'No title, slug or content. Filled in dummy content';
			$this->body['post_content'] = ' ';
		}
		
		//check the post_status
		$post_status = array('publish','pending','draft','auto-draft','future','private','inherit','trash');
		if ( !in_array( $this->body['post_status'], $post_status) ) {
			$woocsv_import->import_log[] = sprintf(__('post status changed from %s to publish','woocsv-import'),$this->body['post_status']);
			$this->body['post_status'] = 'publish';
		}
		
		//check if there is a name or a title, else put status to draft
		//added product type check to make sure only to check for simple products 
		if (empty($this->body['post_title']) && $this->body['post_type'] == 'product'  ) {
			$woocsv_import->import_log[] = __('title is empty status changed to draft','woocsv-import');
			$this->body['post_status'] = 'draft';
		}
		
		//check ping status
		if ( !in_array( $this->body['ping_status'], array('open','closed')) ) {
			$woocsv_import->import_log[] = sprintf(__('ping status changed from %s to ping','woocsv-import'),$this->body['ping_status']);
			$this->body['ping_status'] = 'open';
		}	
	
		//check menu_order
		if ( !is_numeric ( $this->body['menu_order'] )) {
			$woocsv_import->import_log[] = sprintf(__('menu order changed from %s to 0','woocsv-import'),$this->body['menu_order']);
			$this->body['menu_order'] = 0;
		}	
		
		//! DEV remove because SKU's can be numeric!!!!
		//check post_parent
		//if ( !is_numeric ( $this->body['post_parent'] )) {
		//	$woocsv_import->import_log[] = __('post_parent was not numeric','woocsv-import');
		//	$this->body['post_parent'] = '';
		//}	

		//==========================
		// check some meta data and fill in the log
		//==========================
		
		//check stock status
		if (in_array('stock_status', $this->header) && !in_array($this->meta['_stock_status'], array('instock', 'outofstock'))) { 
			$woocsv_import->import_log[] = sprintf(__('stock status changed from %s to instock','woocsv-import'),$this->meta['_stock_status']);
			$this->meta['_stock_status'] = 'instock';
		}

		//check visibility
		if (in_array('visibility', $this->header) && !in_array($this->meta['_visibility'], array('visible', 'catalog', 'search', 'hidden'))) { 
			$woocsv_import->import_log[] = sprintf(__('visibility changed from %s to visible','woocsv-import'),$this->meta['_visibility']);
			$this->meta['_visibility'] = 'visible';
		}

		//check backorders
		if (in_array('backorders', $this->header) && !in_array($this->meta['_backorders'], array('yes','no','notify'))) { 
			$woocsv_import->import_log[] = sprintf(__('backorders changed from %s to no','woocsv-import'),$this->meta['_backorders']);
			$this->meta['_backorders'] = 'no';
		}

		//check featured
		if (in_array('featured', $this->header) && !in_array($this->meta['_featured'], array('yes','no'))) { 
			$woocsv_import->import_log[] = sprintf(__('featured changed from %s to no','woocsv-import'),$this->meta['_featured']);
			$this->meta['_featured'] = 'no';
		}

		//check manage_stock
		if (in_array('manage_stock', $this->header) && !in_array($this->meta['_manage_stock'], array('yes','no'))) { 
			$woocsv_import->import_log[] = sprintf(__('manage_stock changed from %s to no','woocsv-import'),$this->meta['_manage_stock']);
			$this->meta['_manage_stock'] = 'no';
		}
		
		//handle prices		
		if ($woocsv_import->get_merge_products() == 1) {	
			$regular_price = (in_array('regular_price', $this->header) && strlen($this->meta['_regular_price'] ) >0 ) ?  $this->meta['_regular_price']:$this->meta['_regular_price'];
			$sale_price = (in_array('sale_price', $this->header) && strlen($this->meta['_sale_price'] )>0) ? $this->meta['_sale_price']:$this->meta['_sale_price'];
			$price = (in_array('price', $this->header) && strlen($this->meta['_price'] )>0) ? $this->meta['_price']:$this->meta['_price'];
		} else {
			$regular_price = (in_array('regular_price', $this->header) && strlen($this->meta['_regular_price'] )>0) ?  $this->meta['_regular_price'] : '';
			$sale_price = (in_array('sale_price', $this->header) && strlen($this->meta['_sale_price'] )>0) ? $this->meta['_sale_price'] : '' ;
			$price = (in_array('price', $this->header) && strlen($this->meta['_price'] )>0) ? $this->meta['_price'] : '' ;
		}
			
		//product on sale
		if ($sale_price >0 && $sale_price < $regular_price) {
			$woocsv_import->import_log[] = __('Product is on sale','woocsv-import');
			$price = $sale_price;
		} else {
		//the product is not on sale
			$price = $regular_price;
			$sale_price = '';
		}		
		
		//set prices
		$this->meta['_regular_price'] = $regular_price;
		$this->meta['_sale_price'] = $sale_price;
		$this->meta['_price'] = $price;
		
		//add action after parsing all data		
		do_action('woocsv_after_parse_data');
	}

	public function merge_product($id)
	{
		//get post data and store it
		$post = get_post( $id, 'ARRAY_A' );
		$this->body = $post;

		//get meta data and store it
		$post_meta = get_metadata('post', $id, '', true );
		foreach ($post_meta as $key=>$value) {
			$this->meta[$key] = maybe_unserialize($value[0]);
		}
		
		//get product_tpe
		$product_types = wp_get_object_terms( $this->body['ID'], 'product_type' );
		
		if ( !is_wp_error($product_types) ){
			foreach ($product_types as $product_type) {
				$this->product_type = $product_type->name;
			}
		}
			
	}

	public function get_product_by_id($sku)
	{
		global $wpdb;
		$product_id = $wpdb->get_var($wpdb->prepare("SELECT max(post_id) FROM $wpdb->postmeta a, $wpdb->posts b
				WHERE a.post_id= b.id and meta_key='_sku' AND meta_value='%s' LIMIT 1", $sku ));

		if ($product_id) $product['ID'] = $product_id; else $product_id = false;
		
		return $product_id = apply_filters('woocsv_get_product_id',$product_id,$sku);
	}

	public function save()
	{
		global $woocsv_import;
		
		// @since 3.0.2 if skip is true, skip the product during import
		if ($this->skip) {
			return false;
		}
		
		//save the post
		$post_id = wp_insert_post($this->body, true);
			
	
		if ( is_wp_error($post_id)) {
			$woocsv_import->import_log[] = __('Import failed, could not save product body');
			return;
		}
		 
		
		if (is_wp_error($post_id)) {
			$woocsv_import->import_log[] = __('Product could not be saved ','woocsv-import');
		} else {
			$woocsv_import->import_log[] = sprintf(__('Product saved with ID: %s','woocsv-import'),$post_id);
			$this->body['ID'] = $post_id;
		}
		
		do_action( 'woocsv_product_after_body_save');
		
		//save the product type
		wp_set_object_terms( $post_id, $this->product_type , 'product_type', false );

		do_action( 'woocsv_product_before_meta_save');

		//save the meta
		foreach ($this->meta as $key=>$value) {
			update_post_meta($post_id, $key, $value);
		}

		do_action( 'woocsv_product_before_tags_save');

		//save tags
		if ($this->tags) {
			$this->save_tags($post_id);
		}

		do_action( 'woocsv_product_before_categorie_save');

		//save categories
		if (!empty($this->categories)) {
			$this->save_categories($post_id);
		}

		do_action( 'woocsv_product_before_images_save');
		
		// added empty() else it overrrides the above function)	
		if (!empty($this->featured_image)) {
			$this->save_featured_image();
		}
			
		//save the product gallery
		if (!empty($this->product_gallery)) {
			$this->save_product_gallery();
		}
			
		do_action( 'woocsv_product_before_shipping_save');

		// save shipping class
		if ($this->shipping_class) {
			$this->save_shipping_class();
		}

		do_action( 'woocsv_after_save', $this);
		
		//clear transients
		if ( function_exists('wc_delete_product_transients') ) {
			wc_delete_product_transients ($post_id);	
		}

		do_action ( 'woocsv_product_after_save' );

		//and return the ID	
		return $post_id;
	}

	public function save_tags($post_id)
	{
		global $woocsv_import;
		//2.1.1 If merging do not delete else clear currrent tag
		if (!$woocsv_import->get_merge_products())
			wp_set_object_terms( $this->body['ID'], null, 'product_tag' );
		
		//handle tags
		foreach ($this->tags as $tags) {
			$tags = explode('|', $tags);
			wp_set_object_terms( $post_id, $tags, 'product_tag', true );
		}
	}

	public function save_shipping_class()
	{
		global $woocsv_import;
		
		//2.2.2 If merging do not delete else clear currrent tag
		if ( ! $woocsv_import->get_merge_products() ) {
			wp_set_object_terms( $this->body['ID'], null, 'product_shipping_class' );
		}
		$term = term_exists($this->shipping_class, 'product_shipping_class');
		
		// @since  2.2.2 beter handling for shipping class
		if ( ! is_array( $term ) ) {
			$term = wp_insert_term( $this->shipping_class, 'product_shipping_class');
		}

		if ( ! is_wp_error( $term ) ) {
			wp_set_object_terms( $this->body['ID'] , array ( (int)$term['term_id'] ) , 'product_shipping_class' );			
		}
	}

	public function save_categories()
	{
		global $woocsv_import;

		//check out http://wordpress.stackexchange.com/questions/24498/wp-insert-term-parent-child-problem
		delete_option("product_cat_children");

		//clear currrent
		//2.1.1 If merging do not delete else clear currrent category
		if (!$woocsv_import->get_merge_products())
			wp_set_object_terms( $this->body['ID'], null, 'product_cat' );

		foreach ($this->categories as $category) {
			$cats = explode( '|', $category );
			foreach ($cats as $cat) {
				$cat_taxs = explode( '->', $cat );
				
				$parent = 0;
				
				foreach ( $cat_taxs as $cat_tax) {
					
					$new_cat = term_exists( $cat_tax, 'product_cat', $parent );
					if ( ! is_array( $new_cat ) ) {
						$new_cat = wp_insert_term( $cat_tax, 'product_cat', array( 'slug' => $cat_tax, 'parent'=> $parent) );
					}
					if (!is_wp_error($new_cat)) {
						$parent = $new_cat['term_id'];
					}

					if (!is_wp_error($new_cat) && $woocsv_import->get_add_to_categories() == 1)
						wp_set_object_terms( $this->body['ID'], (int)$new_cat['term_id'], 'product_cat', true );
				}

				if (!is_wp_error($new_cat) && $woocsv_import->get_add_to_categories() == 0)
					wp_set_object_terms( $this->body['ID'], (int)$new_cat['term_id'], 'product_cat', true );
			}
		}
	}
	
	public function save_featured_image() {
		global $woocsv_import;
		
		$imageID = false;
		if ($this->is_valid_url($this->featured_image)) {
			$woocsv_import->import_log[] = 'featured image is imported using the URL';
			$imageID = $this->save_image_with_url($this->featured_image);
		} else {
			$woocsv_import->import_log[] = 'featured image is imported using the filename';			
			$imageID = $this->save_image_with_name($this->featured_image);
		}
		
		if ($imageID)
			set_post_thumbnail( $this->body['ID'], $imageID );	
	}

	public function save_product_gallery()
	{	
		$images = explode('|', $this->product_gallery);
		$gallery = false;
		foreach ($images as $image) {
			if ($this->is_valid_url($image)) {
				$imageID = $this->save_image_with_url($image);
			} else {
				$imageID = $this->save_image_with_name($image);
			}
			
			if ($imageID)
				$gallery[] = $imageID;
		}

		if ($gallery) {
			$meta_value = implode(',', $gallery);
			update_post_meta($this->body['ID'], '_product_image_gallery', $meta_value);
		}
		
	}

	public function save_image_with_url($image)
	{
		$attach_id = false;
		$upload_dir = wp_upload_dir();

		/* use curl to get image instead of $image_data = file_get_contents($image);*/
		$ch = curl_init();
		$timeout = 0;

		//special chars
		// @ since 3.0.1 added parse_url to encode path
		// @ since 3.0.2 use rawurlencode so that / is not encoded
		$parse = parse_url($image);

		$parse['path'] = implode('/', array_map('rawurlencode', explode('/', $parse['path'])));	
		$image = $parse['scheme'].'://'.$parse['host'].'/'.$parse['path'];
		
		// curl set options
		curl_setopt ($ch, CURLOPT_URL, $image);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt ($ch, CURLOPT_AUTOREFERER, true);

		// @ since 3.0.1 to follow redirects
		// @ since 3.0.4 have a setting because it can interfere with open_basedir or safe_mode
		
		if (get_option('woocsv_curl_followlocation')) {
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		} 
		
		
		// Getting binary data
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
		
		//set user agent
		curl_setopt($ch, CURLOPT_USERAGENT,'');
		
		//exec curl command
		$image_data = curl_exec($ch);
		
		/* get the mime type incase there is no extension */
		$mime_type =  curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

		//close the curl command
		curl_close($ch);

		//get the filename
		$filename =  sanitize_file_name( basename(urldecode($image)) );

		//create the dir or take the current one
		if (wp_mkdir_p($upload_dir['path'])) {
			$file = $upload_dir['path'] . '/' . $filename;
		} else {
			$file = $upload_dir['basedir'] . '/' . $filename;
		}
		

		/* check if file is already there and rename it if needed */
		$i= 1;
		
		//split it up
		list($directory, , $extension, $filename) = array_values(pathinfo($file));
		$new_file_name = $filename . '.' . $extension;
		
		//loop until it works
		while (file_exists($file))
		{
			//create a new filename
			$file = $directory . '/' . $filename . '-' . $i . '.' . $extension;
			// ! DEV fix file url in image
			$new_file_name = $filename . '-' . $i . '.' . $extension;
			$i++;
		}
		
		// ! DEV fix file url in image
		$file_url = $upload_dir['url'] . '/' . $new_file_name;
		
		if (file_put_contents($file, $image_data)) {
			$wp_filetype = wp_check_filetype($filename, null );
			
			/* added mime type */
			if (!$wp_filetype['type'] && !empty($mime_type)) {
				$allowed_content_types = wp_get_mime_types();
				
				if (in_array($mime_type, $allowed_content_types)){
					$wp_filetype['type'] = $mime_type;
				}
			}
			
			$attachment = array(
				'post_mime_type' => $wp_filetype['type'],
				'post_title' => sanitize_file_name($filename),
				'post_content' => '',
				'post_status' => 'inherit',
				'guid' => $file_url,
			);
			
			$attach_id = wp_insert_attachment( $attachment, $file );
			require_once ABSPATH . 'wp-admin/includes/image.php';
			$attach_data = @wp_generate_attachment_metadata( $attach_id, $file );
			
			wp_update_attachment_metadata( $attach_id, $attach_data );	
		}
		return $attach_id;
	}

	public function save_image_with_name($image)
	{
		global $wpdb;

		/* use  get_posts to retreive image instead of query direct!*/
		
		//set up the args
		$args = array(
            'numberposts'	=> 1,
            'orderby'		=> 'post_date',
			'order'			=> 'DESC',
            'post_type'		=> 'attachment',
            'post_mime_type'=> 'image',
            'post_status' =>'any',
		    'meta_query' => array(
		        array(
		            'key' => '_wp_attached_file',
		            'value' => sanitize_file_name($image),
		            'compare' => 'LIKE'
		        )
		    )
		);
		//get the images
        $images = get_posts($args);

        if (!empty($images)) {
        //we found a match, return it!
	        return (int)$images[0]->ID;
        } else {
        //no image found with the same name, return false
	        return false;
        }
		
	}

	public function fill_in_data()
	{
		global $woocsv_import;		
		do_action( 'woocsv_product_before_fill_in_data');
		
		$id = false;
		
		//check if the product already exists by checking it's ID		
		if (in_array('ID', $woocsv_import->header) )  
		{
			$tempID = $this->raw_data[array_search('ID', $woocsv_import->header)];
			if ($tempID) {			
				
				//use get_post instead of get_posts
				$test = new WC_Product($tempID);
				
			 	if ($test->post) {
				 	$woocsv_import->import_log[] = 'Product found (ID), ID is: '.$tempID;
					$this->new = false;
			 	} else {	 	
					/* set the ID to null */
				 	$this->raw_data[array_search('ID', $woocsv_import->header)] = '';
				 	$this->body['ID'] = '';
				 	$woocsv_import->import_log[] = 'ID :'.$tempID . ' not found!';
			 	}
		 	}
		 	
		}
		//check if the product already exists by checking it's sku
		if (empty($id) && in_array('sku', $woocsv_import->header) && $woocsv_import->get_match_by() == 'sku' )  
		{
			$sku = $this->raw_data[array_search('sku', $woocsv_import->header)];
			
			if (!empty($sku)) {
				$id = $this->get_product_by_id($sku);
				if ( !empty( $id ) ) {
					$this->new = false;
					$woocsv_import->import_log[] = 'Product found (SKU), ID is: '. $id;
				} else {
					$woocsv_import->import_log[] = "New product";
				}
			}
		}
		
		//check if the product already exists by checking it's post title		
		if (empty($id) && in_array('post_title', $woocsv_import->header) && $woocsv_import->get_match_by() == 'title' )  
		{
			$post_title = $this->raw_data[array_search('post_title', $woocsv_import->header)];
			
			if ($post_title) {			
			 	$testID = get_page_by_title( $post_title,ARRAY_A , 'product' );
			 	if ($testID) {
				 	$woocsv_import->import_log[] = 'Product found (TITLE), ID is: '.$testID['ID'];
				 	$id = $testID['ID'];
					$this->new = false;
			 	} else {
				 	$woocsv_import->import_log[] = 'ID :'.$testID['ID'] . ' not found!';
			 	}
		 	}
		}
				
		//check for if we need to merge the product

		if ($id && $woocsv_import->get_merge_products() == 1) {
			$this->merge_product($id);
		}
		
		//fill in the product body
		foreach ($this->body as $key=>$value) {
			if (in_array($key, $woocsv_import->header)) {
				$this->body[$key] = $this->raw_data[array_search($key, $woocsv_import->header)];
			}
		}
		
		// get the author
		if (isset($this->body['post_author'])) {
			
			$user = get_user_by( ($woocsv_import->get_match_author_by())?$woocsv_import->get_match_author_by():'login', $this->body['post_author'] );
			if ($user) {
				$this->body['post_author'] = $user->ID;				
			} else {
				$this->body['post_author'] = '';
			} 
		}
		
		//fill in the ID if the product already exists
		if ($id) {
			$this->body['ID'] = $id;
		}
		
		//fill in the meta data
		foreach ($this->meta as $key=>$value) {
			if (in_array(substr($key, 1), $woocsv_import->header)) {
				$this->meta[$key] = $this->raw_data[array_search(substr($key, 1), $woocsv_import->header)];
			}
		}
		
		//check if there are tags
		if (in_array('tags', $woocsv_import->header)) {
			foreach ($woocsv_import->header as $key=>$value) {
				if ($value == 'tags')
					$this->tags[] = $this->raw_data[$key];
			}
		}

		//check if there is a shipping
		if (in_array('shipping_class', $woocsv_import->header)) {
			$key = array_search('shipping_class', $woocsv_import->header);
			$this->shipping_class = trim($this->raw_data[$key]);
		}

		//check if there are categories
		if (in_array('category', $woocsv_import->header)) {
			foreach ($woocsv_import->header as $key=>$value) {
				if ($value == 'category')
					$this->categories[] = $this->raw_data[$key];
			}
		} 
		
		/* change_stock */
		if (in_array('change_stock', $woocsv_import->header)) {
			$key = array_search('change_stock', $woocsv_import->header);
			$change_stock = $this->raw_data[$key];
			
			//get the stock
			$stock = get_post_meta($this->body['ID'],'_stock', true);
			
			//if the stock is empty set it to 0
			if (!$stock) $stock = 0;
			
			//calculate the new stock level
			$new_stock = $stock + $change_stock;

			//set new stock in the meta
			$this->meta['_stock'] = $new_stock;

			//set log
			$woocsv_import->import_log[] = "Change stock modus: stock changed from $stock to $new_stock";
		}
		
		//check if there is a featured image
		if (in_array('featured_image', $woocsv_import->header)) {
			$key = array_search('featured_image', $woocsv_import->header);
			$this->featured_image = $this->raw_data[$key];
		}
		
		//check if there is a product gallery
		if (in_array('product_gallery', $woocsv_import->header)) {
			$key = array_search('product_gallery', $woocsv_import->header);
			$this->product_gallery = $this->raw_data[$key];
		}


		do_action( 'woocsv_product_after_fill_in_data');

	}

	// helpers
	public function is_valid_url($url)
	{
		// alternative way to check for a valid url
		if  (filter_var($url, FILTER_VALIDATE_URL) === FALSE) return false; else return true;

	}

}
