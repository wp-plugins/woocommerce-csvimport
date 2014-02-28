<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class woocsvImportProduct
{

	public $new = true;
	
	public $header = array();

	public $tags = array();

	public $categories = array();

	public $images = array();

	public $rawData = array();

	public $shippingClass = '';

	public $featuredImage = '';

	public $productGallery = '';
	
	public $product_type = 'simple';

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
		/* !version 1.2.2 */
		'comment_status'=> 'open',
	);

	public $meta = array(
		'_sku'   => '',
		'_downloadable'  => 'no',
		'_virtual'   => 'no',
		'_price'   => '',
		'_visibility' => 'visible',
		'_stock'   => 0,
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
		'total_sales'=>0,
	);

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
		/* !version 1.2.2 */
		$this->body = apply_filters('woocsv_product_before_body', $this->body,$this->new);
		$this->meta = apply_filters('woocsv_product_before_meta', $this->meta,$this->new);
		
		$post_id = wp_insert_post($this->body);
		$this->body['ID'] = $post_id;

		do_action( 'woocsv_before_save', $this);

		do_action( 'woocsv_product_after_body_save');

		/* !version 1.2.2 */
		//new add product type for more efficient save
		
		/* !version 1.2.4 */
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
		
		/* !--DEPRECIATED */
		if ($this->images)
			$this->saveImages($post_id);

		/* !version 1.2.2 */
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

		return $post_id;
	}

	public function saveTags($post_id)
	{
		//handle tags
		foreach ($this->tags as $tags) {
			$tags = explode('|', $tags);
			wp_set_object_terms( $post_id, $tags, 'product_tag', true );
		}
	}

	public function saveShippingClass()
	{
		$term = term_exists($this->shippingClass, 'product_shipping_class');

		if (!$term) {
			$term=wp_insert_term( $this->shippingClass, 'product_shipping_class');
			wp_set_object_terms( $this->body['ID'], array ((int)$term['term_id']) , 'product_shipping_class' );
		}

		wp_set_object_terms( $this->body['ID'], array ( (int)$term['term_id'] ) , 'product_shipping_class' );

	}

	public function saveCategories()
	{
		global $woocsvImport;

		//check out http://wordpress.stackexchange.com/questions/24498/wp-insert-term-parent-child-problem
		delete_option("product_cat_children");

		//clear currrent
		wp_set_object_terms( $this->body['ID'], null, 'product_cat' );

		foreach ($this->categories as $category) {
			$cats = explode( '|', $category );
			foreach ($cats as $cat) {
				$cat_taxs = explode( '->', $cat );
				$parent = false;
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
	
	public function saveFeaturedImage()
	{
		$imageID = false;
		
		if ($this->isValidUrl($this->featuredImage)) {
			$imageID = $this->saveImageWithUrl($this->featuredImage);
		} else {
			$imageID = $this->saveImageWithName($this->featuredImage);
		}
$imageID = $this->saveImageWithUrl($this->featuredImage);
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
		
		
		/* !version 1.2.1 */
		/*
		use curl to get image instead of
		$image_data = file_get_contents($image);
		*/
		
		$ch = curl_init();
		$timeout = 0;
		// curl set options
		curl_setopt ($ch, CURLOPT_URL, $image);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		/* !version 1.2.7 */
		curl_setopt ($ch, CURLOPT_AUTOREFERER, true);
		
		
		// Getting binary data
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
		
		
		$image_data = curl_exec($ch);

		curl_close($ch);

		//get the filename
		$filename = basename($image);

		//create the dir or take the current one
		if (wp_mkdir_p($upload_dir['path'])) {
			$file = $upload_dir['path'] . '/' . $filename;
		} else {
			$file = $upload_dir['basedir'] . '/' . $filename;
		}

		if (file_put_contents($file, $image_data)) {
			$wp_filetype = wp_check_filetype($filename, null );
			$attachment = array(
				'post_mime_type' => $wp_filetype['type'],
				'post_title' => sanitize_file_name($filename),
				'post_content' => '',
				'post_status' => 'inherit'
			);

			$attach_id = wp_insert_attachment( $attachment, $file); //,$this->body['ID'] );
			require_once ABSPATH . 'wp-admin/includes/image.php';
			$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
			wp_update_attachment_metadata( $attach_id, $attach_data );	
		}
		return $attach_id;
	}

	public function saveImageWithName($image)
	{
		global $wpdb;

		/* !version 1.2.3 */
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
		
		//check if the product already exists by checking it's sku
		if (in_array('sku', $woocsvImport->header)) {
			$sku = $this->rawData[array_search('sku', $woocsvImport->header)];
			/* !version 1.2.2 */
			
			if (!empty($sku)) {
				$id = $this->getProductId($sku);
				if ( !empty( $id ) )
					$this->new = false;
			}

			//check for if we need to merge the product
			if ($id && $woocsvImport->options['merge_products'] == 1) {
				$this->mergeProduct($id);
			}

		}

		//fill in the product body
		foreach ($this->body as $key=>$value) {
			if (in_array($key, $woocsvImport->header)) {
				$this->body[$key] = $this->rawData[array_search($key, $woocsvImport->header)];
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
			$this->shippingClass = $this->rawData[$key];
		}

		//check if there are categories
		if (in_array('category', $woocsvImport->header)) {
			foreach ($woocsvImport->header as $key=>$value) {
				if ($value == 'category')
					$this->categories[] = $this->rawData[$key];
			}
		} 
		
		/* 1.2.7 change_stock 
		if (in_array('change_stock', $woocsvImport->header)) {
			$key = array_search('change_stock', $woocsvImport->header);
			$change_stock = $this->rawData[$key];
			if ($this->new = false)
				
		}
		*/
		
		/* !--DEPRECIATED */
		//check if there are images
		if (in_array('images', $woocsvImport->header)) {
			foreach ($woocsvImport->header as $key=>$value) {
				if ($value == 'images')
					$this->images[] = $this->rawData[$key];
			}
		}
		
		
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
		return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);

		/* !version 1.2.7
		if(filter_var($url, FILTER_VALIDATE_URL) === FALSE)
			{
			        return false;
			}else{
					return true;
			}
		*/ 
	}

}
