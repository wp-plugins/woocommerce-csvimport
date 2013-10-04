<?php
class woocsvImportProduct
{

	public $header = array();

	public $tags =array();

	public $categories = array();

	public $images = array();
	
	public $rawData = array();

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

	public function __construct()
	{
		$this->header = get_option('woocsv-header');
	}

	public function getProductId($sku)
	{
		global $wpdb;
		$product_id = $wpdb->get_var($wpdb->prepare("SELECT post_id FROM $wpdb->postmeta a, $wpdb->posts b
				WHERE a.post_id= b.id and meta_key='_sku' AND meta_value='%s' LIMIT 1", $sku ));
		return ($product_id) ? $product['ID'] = $product_id : $my_product['ID'] = false;
	}

	public function save()
	{
		do_action( 'woocsv_before_save',$this );
		//save the post
		$post_id = wp_insert_post($this->body);
		$this->body['ID'] = $post_id;
		
		//product type
		wp_set_object_terms( $post_id, 'simple' , 'product_type', true );
		
		//save the meta
		foreach ($this->meta as $key=>$value) {
			update_post_meta($post_id, $key, $value);
		}

		//save tags
		if ($this->tags)
			$this->saveTags($post_id);

		//save categories
		if (!empty($this->categories))
			$this->saveCategories($post_id);

		if ($this->images)
			$this->saveImages($post_id);
			
		do_action( 'woocsv_after_save',$this );
		
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

	public function saveCategories($post_id)
	{	
		//check out http://wordpress.stackexchange.com/questions/24498/wp-insert-term-parent-child-problem
		delete_option("product_cat_children");
		foreach ($this->categories as $category) {
			$cats = explode( '|', $category );
			foreach ($cats as $cat) {
				$cat_taxs = explode( '->', $cat );
				$parent = false;
				foreach ( $cat_taxs as $cat_tax) {
					$new_cat = term_exists( $cat_tax, 'product_cat' );
					if ( ! is_array( $new_cat ) ) {
						$new_cat = wp_insert_term( $cat_tax, 'product_cat', array( 'slug' => $cat_tax, 'parent'=> $parent) );
					}
					if (!is_wp_error($new_cat)) {
						$parent = $new_cat['term_id'];
					}
				}
				if (!is_wp_error($new_cat)) {
					//wp_set_object_terms( $post_id, null , 'product_cat');
					wp_set_object_terms( $post_id, (int)$new_cat['term_id'], 'product_cat', true );
				}

			}
		}
	}

	public function isValidUrl($url)
	{
		return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
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
						$post_title =  basename($filename,'.'.$info['extension']);
						
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
		
		if(!empty($gallery) && $options['add_to_gallery'] == 1) {
			$meta_value = implode(',', $gallery);
			update_post_meta($post_id, '_product_image_gallery', $meta_value);
		}
	}

	public function fillInData()
	{
		
		//check if the product already exists by cheking it's sku
		if (in_array('sku', $this->header)) {
			$sku = $this->rawData[array_search('sku', $this->header)];
			if (!empty($sku))
				$id = $this->getProductId($sku);
		}

		//fill in the product body
		foreach ($this->body as $key=>$value) {
			if (in_array($key, $this->header)) {
				$this->body[$key] = $this->rawData[array_search($key, $this->header)];
			}
		}

		//fill in the ID if the product already exists
		if ($id) {
			$this->body['ID'] = $id;
		}

		//fill in the meta data
		foreach ($this->meta as $key=>$value) {
			if (in_array(substr($key, 1), $this->header)) {
				$this->meta[$key] = $this->rawData[array_search(substr($key, 1), $this->header)];
			}
		}

		//check if there are tags
		if (in_array('tags', $this->header)) {
			foreach ($this->header as $key=>$value) {
				if ($value == 'tags')
					$this->tags[] = $this->rawData[$key];
			}
		}

		//check if there are categories
		if (in_array('category', $this->header)) {
			foreach ($this->header as $key=>$value) {
				if ($value == 'category')
					$this->categories[] = $this->rawData[$key];
			}
		}

		//check if there are images
		if (in_array('images', $this->header)) {
			foreach ($this->header as $key=>$value) {
				if ($value == 'images')
					$this->images[] = $this->rawData[$key];
			}
		}


	}

}
