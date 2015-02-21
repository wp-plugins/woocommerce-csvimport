<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class woocsvImportProduct
{

	public $new = true;
	
	/* ! 2.0.2 deleted */
	//public $log = array();
	
	public $header = array();

	public $tags = array();

	public $categories = array();

	public $images = array();

	public $rawData = array();

	public $shippingClass = '';

	public $featuredImage = '';

	public $productGallery = '';
	
	public $product_type = 'simple';

	//body
	public $body = array(
		'ID' => '',
		'post_type'  => 'product',
		'menu_order'  => '',
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

	public function parseData(){
		global $woocsvImport;
		
		//===================
		//! version 2.0.0
		//  -check body data and fill in the log
		// 
		//===================

		//add action before parsing all data
		do_action('woocsv_before_parse_data');

		//check the post_status
		$post_status = array('publish','pending','draft','auto-draft','future','private','inherit','trash');
		
		if ( !in_array( $this->body['post_status'], $post_status) ) {
			$woocsvImport->importLog[] = sprintf(__('post status changed from %s to publish','woocsv-import'),$this->body['post_status']);
			$this->body['post_status'] = 'publish';
		}
		
		//check if there is a name or a title, else put status to draft
		/* !2.0.2 added product type check to make sure only to check for simple products  */
		if (empty($this->body['post_title']) && $this->body['post_type'] == 'product'  ) {
			$woocsvImport->importLog[] = __('title is empty status changed to draft','woocsv-import');
			$this->body['post_status'] = 'draft';
		}
		
		//check ping status
		if ( !in_array( $this->body['ping_status'], array('open','closed')) ) {
			$woocsvImport->importLog[] = sprintf(__('ping status changed from %s to ping','woocsv-import'),$this->body['ping_status']);
			$this->body['ping_status'] = 'open';
		}	
	
		//check menu_order
		if ( !is_numeric ( $this->body['menu_order'] )) {
			$woocsvImport->importLog[] = sprintf(__('menu order changed from %s to 0','woocsv-import'),$this->body['menu_order']);
			$this->body['menu_order'] = 0;
		}	
		

		//==========================
		//! version 2.0.0
		//  -check some meta data and fill in the log
		//
		//==========================
		
		//check stock status
		if (in_array('stock_status', $this->header) && !in_array($this->meta['_stock_status'], array('instock', 'outofstock'))) { 
			$woocsvImport->importLog[] = sprintf(__('stock status changed from %s to instock','woocsv-import'),$this->meta['_stock_status']);
			$this->meta['_stock_status'] = 'instock';
		}

		//check visibility
		if (in_array('visibility', $this->header) && !in_array($this->meta['_visibility'], array('visible', 'catalog', 'search', 'hidden'))) { 
			$woocsvImport->importLog[] = sprintf(__('visibility changed from %s to visible','woocsv-import'),$this->meta['_visibility']);
			$this->meta['_visibility'] = 'visible';
		}

		//check backorders
		if (in_array('backorders', $this->header) && !in_array($this->meta['_backorders'], array('yes','no','notify'))) { 
			$woocsvImport->importLog[] = sprintf(__('backorders changed from %s to no','woocsv-import'),$this->meta['_backorders']);
			$this->meta['_backorders'] = 'no';
		}

		//check featured
		if (in_array('featured', $this->header) && !in_array($this->meta['_featured'], array('yes','no'))) { 
			$woocsvImport->importLog[] = sprintf(__('featured changed from %s to no','woocsv-import'),$this->meta['_featured']);
			$this->meta['_featured'] = 'no';
		}

		//check manage_stock
		if (in_array('manage_stock', $this->header) && !in_array($this->meta['_manage_stock'], array('yes','no'))) { 
			$woocsvImport->importLog[] = sprintf(__('manage_stock changed from %s to no','woocsv-import'),$this->meta['_manage_stock']);
			$this->meta['_manage_stock'] = 'no';
		}
				
		
		
		//=======================
		//! version 2.0.0
		//  sort out the prices and fill in the log
		//  
		//=======================

		/* !2.0.3 changed 0 to '' */
		/* !2.0.5 empty function to notempty to allow 0 values */
		

		if ($woocsvImport->options['merge_products'] == 1) {	
			$regular_price = (in_array('regular_price', $this->header) && notempty($this->meta['_regular_price'] )) ?  $this->meta['_regular_price']:$this->meta['_regular_price'];
			$sale_price = (in_array('sale_price', $this->header) && notempty($this->meta['_sale_price'] )) ? $this->meta['_sale_price']:$this->meta['_sale_price'];
			$price = (in_array('price', $this->header) && notempty($this->meta['_price'] )) ? $this->meta['_price']:$this->meta['_price'];
		} else {
			$regular_price = (in_array('regular_price', $this->header) && notempty($this->meta['_regular_price'] )) ?  $this->meta['_regular_price'] : '';
			$sale_price = (in_array('sale_price', $this->header) && notempty($this->meta['_sale_price'] )) ? $this->meta['_sale_price'] : '' ;
			$price = (in_array('price', $this->header) && notempty($this->meta['_price'] )) ? $this->meta['_price'] : '' ;
		}
		
		// @ as of 2.1.1 remove old price way
		//old way
		/*
		if ($price && !$sale_price && !$regular_price){
			$woocsvImport->importLog[] = __('Old price field used!!!! Please use regular_price and sale_price in stead','woocsv-import');
			$regular_price = $price;
		}
		*/
		
		//new way
		//product on sale
		if ($sale_price >0 && $sale_price < $regular_price) {
			$woocsvImport->importLog[] = __('Product is on sale','woocsv-import');
			$price = $sale_price;
		} else {
		//the product is not on sale
			$price = $regular_price;
			$sale_price = '';
		}		

		$this->meta['_regular_price'] = $regular_price;
		$this->meta['_sale_price'] = $sale_price;
		$this->meta['_price'] = $price;
		
		//add action after parsing all data		
		do_action('woocsv_after_parse_data');
	}

	public function mergeProduct($id)
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

	public function getProductId($sku)
	{
		global $wpdb;
		$product_id = $wpdb->get_var($wpdb->prepare("SELECT max(post_id) FROM $wpdb->postmeta a, $wpdb->posts b
				WHERE a.post_id= b.id and meta_key='_sku' AND meta_value='%s' LIMIT 1", $sku ));

		if ($product_id) $product['ID'] = $product_id; else $product_id = false;
		
		return $product_id = apply_filters('woocsv_get_product_id',$product_id,$sku);
	}

	public function save()
	{
		//save the post
		/* ! 2.1.0 - remove filters.... you can use the global $wooProduct and the hooks to alter */
		//$this->body = apply_filters('woocsv_product_before_body', $this->body,$this->new);
		//$this->meta = apply_filters('woocsv_product_before_meta', $this->meta,$this->new);
		
		$post_id = wp_insert_post($this->body);
		$this->body['ID'] = $post_id;

		do_action( 'woocsv_before_save', $this);

		do_action( 'woocsv_product_after_body_save');
		
		// fixed bug with if condition
		wp_set_object_terms( $post_id, $this->product_type , 'product_type', false );

		do_action( 'woocsv_product_before_meta_save');

		//save the meta
		foreach ($this->meta as $key=>$value) {
			update_post_meta($post_id, $key, $value);
		}

		do_action( 'woocsv_product_before_tags_save');

		//save tags
		if ($this->tags)
			$this->saveTags($post_id);

		do_action( 'woocsv_product_before_categorie_save');

		//save categories
		if (!empty($this->categories))
			$this->saveCategories($post_id);

		do_action( 'woocsv_product_before_images_save');
		
		/* !--deprecated */
		//if ($this->images)
		//	$this->saveImages($post_id);

		// added empty() else it overrrides the above function)	
			
		if (!empty($this->featuredImage))
			$this->saveFeaturedImage();

		if (!empty($this->productGallery))
			$this->saveProductGallery();

		do_action( 'woocsv_product_before_shipping_save');
		if ($this->shippingClass) {
			$this->saveShippingClass();
		}

		do_action( 'woocsv_after_save', $this);
		
		/* !version 2.0.4 */
		if ( function_exists('wc_delete_product_transients') )
			wc_delete_product_transients ($post_id);	

		
		//and return the ID		
		return $post_id;
	}

	public function saveTags($post_id)
	{
		global $woocsvImport;
		//2.1.1 If merging do not delete else clear currrent tag
		if (!$woocsvImport->options['merge_products'])
			wp_set_object_terms( $this->body['ID'], null, 'product_tag' );
		
		//handle tags
		foreach ($this->tags as $tags) {
			$tags = explode('|', $tags);
			wp_set_object_terms( $post_id, $tags, 'product_tag', true );
		}
	}

	public function saveShippingClass()
	{
		global $woocsvImport;
		
		//2.2.2 If merging do not delete else clear currrent tag
		if ( ! $woocsvImport->options['merge_products'] ) {
			wp_set_object_terms( $this->body['ID'], null, 'product_shipping_class' );
		}
		$term = term_exists($this->shippingClass, 'product_shipping_class');
		
		// @since  2.2.2 beter handling for shipping class
		if ( ! is_array( $term ) ) {
			$term = wp_insert_term( $this->shippingClass, 'product_shipping_class');
		}

		if ( ! is_wp_error( $term ) ) {
			wp_set_object_terms( $this->body['ID'] , array ( (int)$term['term_id'] ) , 'product_shipping_class' );			
		}
	}

	public function saveCategories()
	{
		global $woocsvImport;

		//check out http://wordpress.stackexchange.com/questions/24498/wp-insert-term-parent-child-problem
		delete_option("product_cat_children");

		//clear currrent
		//2.1.1 If merging do not delete else clear currrent category
		if (!$woocsvImport->options['merge_products'])
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

					if (!is_wp_error($new_cat) && $woocsvImport->options['add_to_categories'] == 1)
						wp_set_object_terms( $this->body['ID'], (int)$new_cat['term_id'], 'product_cat', true );
				}

				if (!is_wp_error($new_cat) && $woocsvImport->options['add_to_categories'] == 0)
					wp_set_object_terms( $this->body['ID'], (int)$new_cat['term_id'], 'product_cat', true );
			}
		}
	}
	
	public function saveFeaturedImage() {
		global $woocsvImport;
		
		$imageID = false;
		if ($this->isValidUrl($this->featuredImage)) {
			$woocsvImport->importLog[] = 'featured image is imported using the URL';
			$imageID = $this->saveImageWithUrl($this->featuredImage);
		} else {
			$woocsvImport->importLog[] = 'featured image is imported using the filename';			
			$imageID = $this->saveImageWithName($this->featuredImage);
		}
		
		if ($imageID)
			set_post_thumbnail( $this->body['ID'], $imageID );	
	}

	public function saveProductGallery()
	{	
		$images = explode('|', $this->productGallery);
		$gallery = false;
		foreach ($images as $image) {
			if ($this->isValidUrl($image)) {
				$imageID = $this->saveImageWithUrl($image);
			} else {
				$imageID = $this->saveImageWithName($image);
			}
			
			if ($imageID)
				$gallery[] = $imageID;
		}

		if ($gallery) {
			$meta_value = implode(',', $gallery);
			update_post_meta($this->body['ID'], '_product_image_gallery', $meta_value);
		}
		
	}

	public function saveImageWithUrl($image)
	{
		$attach_id = false;
		$upload_dir = wp_upload_dir();
		
		
		/* use curl to get image instead of $image_data = file_get_contents($image);*/
		$ch = curl_init();
		$timeout = 0;

		// curl set options
		curl_setopt ($ch, CURLOPT_URL, $image);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt ($ch, CURLOPT_AUTOREFERER, true);
		
		// Getting binary data
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
		
		//exec curl command
		$image_data = curl_exec($ch);
		
		/* !2.1.0 get the mime type incase there is no extension */
		$mime_type =  curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

		//close the curl command
		curl_close($ch);

		//get the filename
		/* ! development add sanatize filename if name has spaces or %20 */
		$filename =  sanitize_file_name( basename(urldecode($image)) );


		//create the dir or take the current one
		if (wp_mkdir_p($upload_dir['path'])) {
			$file = $upload_dir['path'] . '/' . $filename;
		} else {
			$file = $upload_dir['basedir'] . '/' . $filename;
		}

		/* !2.2.0 check if file is already there and rename it if needed */
		$i= 1;
		
		//split it up
		list($directory, , $extension, $filename) = array_values(pathinfo($file));
		
		//loop until it works
		while (file_exists($file))
		{
			//create a new filename
			$file = $directory . '/' . $filename . '-' . $i . '.' . $extension;
			$i++;
		}

		if (file_put_contents($file, $image_data)) {
			$wp_filetype = wp_check_filetype($filename, null );
			
			/* ! 2.1.0 added mime type */
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
				'post_status' => 'inherit'
			);

			$attach_id = wp_insert_attachment( $attachment, $file );
			require_once ABSPATH . 'wp-admin/includes/image.php';
			$attach_data = @wp_generate_attachment_metadata( $attach_id, $file );
			wp_update_attachment_metadata( $attach_id, $attach_data );	
		}
		return $attach_id;
	}

	public function saveImageWithName($image)
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

	public function saveImages($post_id)
	{
		global $wpdb;

		foreach ($this->images as $image_array) {
			$upload_dir = wp_upload_dir();
			$images = explode('|', $image_array);
			$gallery = array();
			if (count($images) > 0 && $images[0] !== "" ) {
				foreach ($images as $image) {
					$filename = $post_title =  basename($image);
					$info = pathinfo($image);
					if (!empty($info['extension'])) {
						$post_title =  basename($filename, '.'.$info['extension']);

					}

					//check if the filename is not already uploaded...and if yes, pick th latest
					$already_there= $wpdb->get_row(
						$wpdb->prepare(
							"SELECT max(ID) as maxid ,COUNT(*) as amount FROM $wpdb->posts where post_type='attachment' and post_title=%s",
							$post_title));

					if ( $already_there->amount > 0 ) {
						set_post_thumbnail( $post_id, $already_there->maxid );
						$gallery[] = $already_there->maxid;
						continue;
					}

					if ( $this->isValidUrl( $image ) ) {
						$image_data = @file_get_contents($image);
					} else {
						$image_data = @file_get_contents($dir.$image);
					}
					if ( $image_data !== false ) {

						if (wp_mkdir_p($upload_dir['path']))
							$file = $upload_dir['path'] . '/' . $filename;
						else
							$file = $upload_dir['basedir'] . '/' . $filename;

						if (file_put_contents($file, $image_data)) {
							$wp_filetype = wp_check_filetype($filename, null );
							$attachment = array(
								'post_mime_type' => $wp_filetype['type'],
								'post_title' => sanitize_file_name($filename),
								'post_content' => '',
								'post_status' => 'inherit'
							);

							$attach_id = wp_insert_attachment( $attachment, $file, $post_id );
							$gallery[] = $attach_id;
							require_once ABSPATH . 'wp-admin/includes/image.php';
							$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
							wp_update_attachment_metadata( $attach_id, $attach_data );
							set_post_thumbnail( $post_id, $attach_id );
						}
					}
				}
			}
		}
		$options = get_option('woocsv-options');

		if (!empty($gallery) && $options['add_to_gallery'] == 1) {
			$meta_value = implode(',', $gallery);
			update_post_meta($post_id, '_product_image_gallery', $meta_value);
		}
	}

	public function fillInData()
	{
		global $woocsvImport;
				
		do_action( 'woocsv_product_before_fill_in_data');
		
		$id = false;
		
		/* ! version 2.0 added ID */
		
		//check if the product already exists by checking it's ID		
		if (in_array('ID', $woocsvImport->header) )  
		{
			$tempID = $this->rawData[array_search('ID', $woocsvImport->header)];
			
			if ($tempID) {			
			 	$testID = get_posts( array('post_type' => 'product','p'=> $tempID));
			 	if ($testID) {
				 	$woocsvImport->importLog[] = 'Product found (ID), ID is: '.$tempID;
				 	$id = $tempID;
					$this->new = false;
			 	} else {	 	
					/* ! version 2.0.1 set the ID to null */
				 	$this->rawData[array_search('ID', $woocsvImport->header)] = '';
		
				 	$woocsvImport->importLog[] = 'ID :'.$tempID . ' not found!';
			 	}
		 	}
		 	
		}
		
		//check if the product already exists by checking it's sku
		if (empty($id) && in_array('sku', $woocsvImport->header) && $woocsvImport->options['match_by'] == 'sku' )  
		{
			$sku = $this->rawData[array_search('sku', $woocsvImport->header)];
			
			if (!empty($sku)) {
				$id = $this->getProductId($sku);
				if ( !empty( $id ) ) {
					$this->new = false;
					$woocsvImport->importLog[] = 'Product found (SKU), ID is: '. $id;
				} else {
					$woocsvImport->importLog[] = "New product";
				}
			}
		}
		
		//check if the product already exists by checking it's post title		
		if (empty($id) && in_array('post_title', $woocsvImport->header) && $woocsvImport->options['match_by'] == 'title' )  
		{
			$post_title = $this->rawData[array_search('post_title', $woocsvImport->header)];
			
			if ($post_title) {			
			 	$testID = get_page_by_title( $post_title,ARRAY_A , 'product' );
			 	if ($testID) {
				 	$woocsvImport->importLog[] = 'Product found (TITLE), ID is: '.$testID['ID'];
				 	$id = $testID['ID'];
					$this->new = false;
			 	} else {
				 	$woocsvImport->importLog[] = 'ID :'.$testID['ID'] . ' not found!';
			 	}
		 	}
		}
				
		//check for if we need to merge the product
		if ($id && $woocsvImport->options['merge_products'] == 1) {
			$this->mergeProduct($id);
		}

		//fill in the product body
		foreach ($this->body as $key=>$value) {
			if (in_array($key, $woocsvImport->header)) {
				$this->body[$key] = $this->rawData[array_search($key, $woocsvImport->header)];
			}
		}
		
		// ! 2.1 get the author
		if (isset($this->body['post_author'])) {
			
			$user = get_user_by( ($woocsvImport->options['match_author_by'])?$woocsvImport->options['match_author_by']:'login', $this->body['post_author'] );
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
			if (in_array(substr($key, 1), $woocsvImport->header)) {
				$this->meta[$key] = $this->rawData[array_search(substr($key, 1), $woocsvImport->header)];
			}
		}
		
		//check if there are tags
		if (in_array('tags', $woocsvImport->header)) {
			foreach ($woocsvImport->header as $key=>$value) {
				if ($value == 'tags')
					$this->tags[] = $this->rawData[$key];
			}
		}

		//check if there is a shipping
		if (in_array('shipping_class', $woocsvImport->header)) {
			$key = array_search('shipping_class', $woocsvImport->header);
			$this->shippingClass = trim($this->rawData[$key]);
		}

		//check if there are categories
		if (in_array('category', $woocsvImport->header)) {
			foreach ($woocsvImport->header as $key=>$value) {
				if ($value == 'category')
					$this->categories[] = $this->rawData[$key];
			}
		} 
		
		/* ! 2.0.2 change_stock */
		if (in_array('change_stock', $woocsvImport->header)) {
			$key = array_search('change_stock', $woocsvImport->header);
			$change_stock = $this->rawData[$key];
			
			//get the stock
			$stock = get_post_meta($this->body['ID'],'_stock', true);
			
			//if the stock is empty set it to 0
			if (!$stock) $stock = 0;
			
			//calculate the new stock level
			$new_stock = $stock + $change_stock;

			//set new stock in the meta
			$this->meta['_stock'] = $new_stock;

			//set log
			$woocsvImport->importLog[] = "Change stock modus: stock changed from $stock to $new_stock";
		}
		
		/* !--deprecated 
		//check if there are images
		if (in_array('images', $woocsvImport->header)) {
			foreach ($woocsvImport->header as $key=>$value) {
				if ($value == 'images')
					$this->images[] = $this->rawData[$key];
			}
		}
		*/
		
		//check if there is a featured image
		if (in_array('featured_image', $woocsvImport->header)) {
			$key = array_search('featured_image', $woocsvImport->header);
			$this->featuredImage = $this->rawData[$key];
		}
		
		//check if there is a product gallery
		if (in_array('product_gallery', $woocsvImport->header)) {
			$key = array_search('product_gallery', $woocsvImport->header);
			$this->productGallery = $this->rawData[$key];
		}


		do_action( 'woocsv_product_after_fill_in_data');

	}

	// ! helpers
	public function isValidUrl($url)
	{
		// alternative way to check for a valid url
		// !development
		if  (filter_var($url, FILTER_VALIDATE_URL) === FALSE) return false; else return true;

	}

}
