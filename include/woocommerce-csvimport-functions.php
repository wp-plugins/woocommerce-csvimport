<?php
function wppcsv_handle_csv_import_random () {
	try {
	//create temp directory
	$upload_dir = wp_upload_dir();
	$dir = $upload_dir['basedir'] .'/csvimport/' . woocsv_random_string() . '/';
	mkdir($dir);
	//handle upload
	if (!woocsv_handle_uploads ($dir))
		throw new Exception(__('Er is iets mis gegaan bij het uploaden'));

	$are_there_csv_files = glob($dir.'*.csv');
	
	if (count($are_there_csv_files) != 1) throw new Exception(__('Er is geen of meerdere csv bestand(en) gevonden'));
	//run the magic
	$result = woocsv_import_products_from_csv ($are_there_csv_files[0],$dir);
	}  catch (Exception $e) {
	 woocsv_admin_notice ('No CSV file is found');
	}

}


function woocsv_handle_zip_import () {
	//create temp directory
	try {
		$upload_dir = wp_upload_dir();
		$dir = $upload_dir['basedir'] .'/' . woocsv_random_string() . '/';
		mkdir($dir);
		$file = $_FILES['zip_file']['name'];
		move_uploaded_file( $_FILES['zip_file']['tmp_name'], $dir.$file );
		$zip = new ZipArchive;
		if ($zip->open($dir.$file) === TRUE) {
			$zip->extractTo($dir);
			$zip->close();
		} else throw new Exception(__('Kan de zip file niet uitpakken'));

		$are_there_csv_files = glob($dir.'*.csv');
		if (count($are_there_csv_files) != 1) throw new Exception(__('Er is geen of meerdere csv bestand(en) gevonden'));
		//run the magic
		$result = woocsv_import_products_from_csv ($are_there_csv_files[0],$dir);
	}
	catch (Exception $e) {
		woocsv_admin_notice ($e->getMessage());
	}

}

function woocsv_handle_fixed_import () {
	global $woocsv_options;
	//get the upload dir
	$upload_dir = wp_upload_dir();
	$dir = $upload_dir['basedir'] .$_POST['fixed_dir'] .'/';
	try {
		//check the existence of the directory
		if (!glob($dir)) throw new Exception(__('De directory bestaat niet'));

		//check to see if there are files in
		if ( count( scandir( $dir ) ) <= 2) throw new Exception(__('Er zijn geen bestanden aanwezig in de directory'));

		//now check to see if there is a csv in there
		$are_there_csv_files = glob($dir.'*.csv');
		if (count($are_there_csv_files) != 1) throw new Exception(__('Er is geen of meerdere csv bestand(en) gevonden'));

		//run the magic
		if ($woocsv_options['use_schedule_event'] == 0) {
			$result = woocsv_import_products_from_csv ($are_there_csv_files[0],$dir); 
		} else {
			wp_schedule_single_event(time()-1, 'woocsv_schedule_import',array ($are_there_csv_files,$dir));
		}
	} catch (Exception $e) {
		woocsv_admin_notice ($e->getMessage());
	}
}


function woocsv_schedule_import($are_there_csv_files,$dir) {
	woocsv_import_products_from_csv ($are_there_csv_files[0],$dir);
}

add_action('woocsv_schedule_import','woocsv_schedule_import');




//import the products
function woocsv_import_products_from_csv ($file,$dir) {
	global $wpdb; 
	$woocsv_options = get_option('csvimport-options');
	$fieldseperator = (isset($woocsv_options['fieldseperator'])) ?  $woocsv_options['fieldseperator'] : ',';
	set_time_limit(0);
	$row = 0;
	if ( $woocsv_options['auto_detect_line_endings'] == 1 ) 
		ini_set('auto_detect_line_endings', true);
	if ($handle = fopen($file, 'r') == FALSE) throw new Exception(__('Can not open file!'));
	$handle = fopen($file, 'r');
	$csvcontent = '';
	while (($line = fgetcsv($handle,0,$woocsv_options['fieldseperator'])) !== FALSE) {
		if ($row <> 0 ) $csvcontent[] = $line;
		$row ++;
	}
	fclose($handle);
	
	if (!$csvcontent) {
		woocsv_admin_notice('No content in csv....check it (also the line endings!)');
		exit;
	}
		
	$content = $csvcontent;
	/*
	0 title,
	1 description,
	2 short_description,
	3 category
	4 stock,
	5 price,
	6 regular_price,
	7 sales_price,
	8 weight,
	9 length,
	10 width,
	11 height,
	12 sku,
	13 picture
	14 tags
	15 tax status ( taxable, shipping, none )
	16 tax class 
	*/
	foreach ( $content as $data ) {
		$num = count($data);
		$row ++;
		$my_product = array(
			'post_title' => wp_strip_all_tags( $data[0] ),
			'post_content' => $data[1],
			'post_excerpt' => $data[2],
			'post_status' => 'publish' ,
			'post_type' => 'product',
		);
		//check to see if the product already exists and add the ID if true
		$product_id = $wpdb->get_var($wpdb->prepare("SELECT post_id FROM $wpdb->postmeta
				WHERE meta_key='_sku' AND meta_value='%s' LIMIT 1", $data[12] ));
		($product_id) ? $my_product['ID'] = $product_id : $my_product['ID'] = false;

		//now we create the product...ig the id is there is will update the product else it will make a new
		$post_id = wp_update_post($my_product);

		//set the attributes etc
		if ( isset($data[4]) && $data[4] ) 
			update_post_meta( $post_id, '_stock', $data[4] );

		//set the price and replace , by . if set 
		if (isset($data[5]) &&  $data[5] ) {
			if ($woocsv_options['change_comma_to_dot'] == 1) $data[5] = str_replace(',', '.', $data[5]);
			update_post_meta( $post_id, '_price', $data[5] );
		}

		if ( isset($data[6]) && $data[6] ) {
			if ($woocsv_options['change_comma_to_dot'] == 1) $data[6] = str_replace(',', '.', $data[6]);
			update_post_meta( $post_id, '_regular_price', $data[6] );
		}

		if (isset($data[7]) &&  $data[7] ) {
			if ($woocsv_options['change_comma_to_dot'] == 1) $data[7] = str_replace(',', '.', $data[7]);
			update_post_meta( $post_id, '_sale_price', $data[7] );
		}
		//end prices

		//set the weight
		if (isset($data[8]) && $data[8]) 
			update_post_meta( $post_id, '_weight', $data[8] );

		//set the length 
		if (isset($data[9]) && $data[9])
			update_post_meta( $post_id, '_length', $data[9] );

		//set the height 
		if (isset($data[10]) && $data[10] )
			update_post_meta( $post_id, '_width', $data[10] );

		//set the height 
		if (isset($data[12]) && $data[11] )
			update_post_meta( $post_id, '_height', $data[11] );

		//set the SKU
		if (isset($data[12]) && $data[12] ) 
			update_post_meta( $post_id, '_sku', $data[12] );

		update_post_meta( $post_id, '_manage_stock', 'yes' );
		update_post_meta( $post_id, '_visibility', 'visible' );
		
		//tax status taxable, shipping, none
		$tax_status = array ('taxable', 'shipping', 'none');
		if (isset($data[15]) && $data[15]) {
			//check if the data is in the array
			if (in_array($data[15], $tax_status))
				update_post_meta( $post_id, '_tax_status', $data[15] );
		}
		
		//tax class
		if (isset($data[16]) && $data[16])
			update_post_meta( $post_id, '_tax_class', $data[16] );
		
		//link the product to the category
		$cats = explode ( '|', $data[3] );
		foreach ($cats as $cat){
			$cat_taxs = explode( '->', $cat );
			$parent = false;
			foreach ( $cat_taxs as $cat_tax)
			{
				$new_cat = term_exists( $cat_tax, 'product_cat' );
				if ( ! is_array( $new_cat ) ) {
					$new_cat = wp_insert_term(	$cat_tax, 'product_cat', array( 'slug' => $cat_tax, 'parent'=> $parent) );
				}
				$x = wp_set_object_terms( $post_id, (int)$new_cat['term_id'], 'product_cat', true );
				$parent = $new_cat['term_id'];
				
				//check out http://wordpress.stackexchange.com/questions/24498/wp-insert-term-parent-child-problem
				delete_option("product_cat_children");
			}
			unset($parent);	
		}

		//handle tags
		if ( isset( $data[14] )){
			$tags = explode('|', $data[14]);
			
			wp_set_object_terms( $post_id, $tags, 'product_tag',true );			
		}

		//get picture if there is one and add it as featured image
		if ( isset( $data[13] )) {
			woocsv_add_featured_image ( $post_id , $data[13], $dir );
		}
	}
}

function woocsv_add_featured_image($post_id,$image_array,$dir) {
	$options = get_option('csvimport-options');
	$upload_dir = wp_upload_dir();
	//delete images
	if ($options['deleteimages'] == 1) {
		//get the images
		$attachments = get_posts( array(
				'post_type' => 'attachment',
				'post_parent' => $post_id,
			));
		foreach ($attachments as $attachment) {
			wp_delete_attachment ($attachment->ID);
		}
	}

	$images = explode('|', $image_array);
	if (count($images) > 0 && $images[0] !== "" ) {
		foreach ($images as $image) {
			if ( woocsv_isvalidurl( $image ) ) {
				$image_data = @file_get_contents($image); 
			} else {
				$image_data = @file_get_contents($dir.$image);
			}

			if ( $image_data !== false ) {
				$filename = basename($image);
				if(wp_mkdir_p($upload_dir['path']))
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
					require_once(ABSPATH . 'wp-admin/includes/image.php');
					$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
					wp_update_attachment_metadata( $attach_id, $attach_data );
					set_post_thumbnail( $post_id, $attach_id );

				}
			}
		}
	}

}

function woocsv_admin_notice($message=''){
	if ($message)
		echo '<div class="error"><p>'.$message.'</p></div>';
}

//well this doed what is does....create a reandom string
function woocsv_random_string($length = 10) {
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVW';
	$string = '';
	for ($p = 0; $p < $length; $p++) {
		$string .= $characters[mt_rand(0, strlen($characters))];
	}
	return $string;
}

function woocsv_get_normalized_files()
{
	$newfiles = array();
	foreach($_FILES as $fieldname => $fieldvalue)
		foreach($fieldvalue as $paramname => $paramvalue)
			foreach((array)$paramvalue as $index => $value)
				$newfiles[$fieldname][$index][$paramname] = $value;
			return $newfiles;
}



//handle file uploads
function woocsv_handle_uploads ( $dir ){
	try {
		$files = woocsv_get_normalized_files();
		foreach ($files['all_files'] as $file) {
			$from_location = $file['tmp_name'];
			$to_location = $dir . $file['name'];
			//check if file is csv or jpg
			move_uploaded_file($from_location, $to_location);
		}
		return true;
	} catch (MyException $e) {
		return false;
	}
}

function woocsv_isvalidurl($url)
{
return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
}