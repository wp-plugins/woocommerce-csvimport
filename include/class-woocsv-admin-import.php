<?php
class woocsvAdminImport 
{
	
	public static function start()
	{
		
		global $woocsvImport,$upload_mb;
		if (isset($_REQUEST['action']) && check_admin_referer('woocsv', 'uploadCsvFile')) {

			$filename = self::handleUpload($_FILES['file']['tmp_name'], $_FILES['file']['name']);
			if (!$filename) wp_die('<h2>Could not upload file.</h2>');

			$handle = fopen($filename, 'r');
			$row = 0;
			$csvcontent = '';
			while (($line = fgetcsv($handle, 0, $woocsvImport->options['seperator'])) !== FALSE) {
				$csvcontent[] = $line;
				$row ++;
			}
			$length = count($csvcontent[0]);
			
			if (count($csvcontent[0]) == 1 ) {
				echo '<h2>I think you have the wrong seperator</h2>';
				echo '<p>Please goto the settings page and change your seperator!</p>';
				return;
			}
			?>			
			<div id="importPreview"> 
			<h2>Import preview</h2>
			<table class="widefat">
			<thead>
				<tr>
					<th>Row 1</th>
					<th>Row 2</th>
					<th>Row 3</th>
					<th>Row 4</th>
				</tr>
			</thead>
			<tbody>
			<?php for ($i = 0; $i <= $length-1; $i++) : ?>
			<tr>

				<td><?php if (isset($csvcontent[0][$i])) echo $csvcontent[0][$i];?></td>
				<td><?php if (isset($csvcontent[1][$i])) echo $csvcontent[1][$i];?></td>
				<td><?php if (isset($csvcontent[2][$i])) echo $csvcontent[2][$i];?></td>
				<td><?php if (isset($csvcontent[3][$i])) echo $csvcontent[3][$i];?></td>
			</tr>
			<?php endfor;?>
			</tbody>
			</table>

			<form id="runImportForm"  method="POST">
				<input type="hidden" name="currentrow" value="0" />
				<input type="hidden" name="blocksize" value="<?php echo $woocsvImport->options['blocksize']; ?>" />
				<input type="hidden" name="rows" value="<?php echo $row;?>" />
				<input type="hidden" name="filename" value="<?php echo $filename; ?>" />
				<input type="hidden" name="action" value="runImport">
				<button type="submit" class="button button-primary button-hero">Run</button>
			</form>
			</div>
			<div class="postbox" style="margin:1em 0 0 0;">
				<div class="inside">
					<div id="import_log">
					</textarea>
				</div>
			</div>
			<?php
			unset($csvcontent,$line);
		} else {
?>			
			<h2>Let's import!</h2>
			<form name="loadPreview" method="POST" enctype="multipart/form-data">
			<fieldset>
				<input id="file" name="file" type="file" accept="text/csv" /><sup><?php echo "Max file size: $upload_mb";?></sup>
				<input type="hidden" name="action" value="runImport">
				<?php wp_nonce_field('woocsv', 'uploadCsvFile'); ?>
				<br/><br/>
				<button type="submit" class="button button-primary button-hero">Load</button>
			</fieldset>
			</form>
			<hr>
			<?php 
			if ($options = get_option('woocsv-lastrun')) {
				echo 'If you are merging products, please be sure you have set the right header!<br/>';
				echo 'Last run: '.$options['date'].'<br/>';
				echo 'filename: '.$options['filename'].'<br/>';
				echo 'Number of rows: '.$options['rows'].'<br/>';
			}
			?>	
		<?php
		}
	}

	public static function runImport()
	{
		global $wooProduct, $woocsvImport,$wpdb;

		wp_suspend_cache_invalidation ( true );
		/* ! 1.2.7 disable term counting */
		wp_defer_term_counting( true ) ;
		$postData = $_POST;
		
		/* ! 1.2.7 solve escape problem when running on windows */ 
		if (isset($postData['filename'])) {
			//get the filename and save it
			$filename = $postData['filename'];
			update_option('woocsv-importfile', $filename);
			unset($postData['filename']);
		} else {
			
			$filename = get_option('woocsv-importfile');
		}


		$count = 0;
		$csvcontent = '';
		$handle = fopen($filename, 'r');
		
		//loop through file and only import the needed block
		while (($line = fgetcsv($handle, 0, $woocsvImport->options['seperator'])) !== FALSE) {
			if ( $count >= $postData['currentrow'] && $count < ( (int)$postData['currentrow'] + (int)$postData['blocksize'])  )
				$csvContent[$count] = $line;
			$count ++;
		}
		
		unset($handle,$line);
		
		for ($i = 1; $i <= $woocsvImport->options['blocksize']; $i++) {
			$wooProduct = new woocsvImportProduct;
			$wooProduct->header = $woocsvImport->header;
			if ($postData['currentrow'] >= $postData['rows'] ) {
				ob_get_clean();
				update_option('woocsv-lastrun',array('date'=>date("Y-m-d H:i:s"),'filename'=>basename($filename),'rows'=>$postData['rows']));
				delete_option('woocsv-importfile');
				do_action('woocsv_after_import_finished');
				die('done');
			}

			if ($woocsvImport->options['skipfirstline'] ==  1 && $postData['currentrow'] == 0) {
				$postData['currentrow'] ++;
				ob_get_clean();
				echo json_encode($postData);
				/* ! 1.2.5 delete trancient */
				$wpdb->query("DELETE FROM wp_options WHERE option_name LIKE '%_transient_%'");
				die();
			}


			if ($woocsvImport->options['skipfirstline'] ==  0 && $postData['currentrow'] == 0) {
				$wooProduct->rawData = $csvContent[0];
				/* ! 1.2.5 delete trancient */
				$wpdb->query("DELETE FROM wp_options WHERE option_name LIKE '%_transient_%'");			
			}

			if ($postData['currentrow'] > 0)
				$wooProduct->rawData = $csvContent[$postData['currentrow']];

			$postData['currentrow'] ++;

			//create a new product
			do_action('woocsv_before_fill_in_data' );
			
			//fill all data
			$wooProduct->fillInData();
			do_action('woocsv_after_fill_in_data' );	
			
			//save it
			try {
				$id = $wooProduct->save();
			} catch (Exception $e) {
				$id = '';
			}

			$postData['ID'] = $id;
			$postData['sku'] = $wooProduct->meta['_sku'];
			$postData['memory'] = round(memory_get_usage()/1024/1024,2);
		}
		
		
		wp_suspend_cache_invalidation ( false );
		/* !1.2.7 */
		wp_defer_term_counting( false ) ;
		
		/* !new added for debug */
		if ( 0 == $woocsvImport->options['debug'])
			ob_get_clean();
		
		if ( 1 == $woocsvImport->options['debug'])
			$postData['product'] = $wooProduct;
			
		$wooProduct = null;
		echo json_encode($postData);
		die();
	}

	static function handleUpload($from_location, $filename)
	{
		do_action('woocsv_before_csv_upload',$filename);
		$upload_dir = wp_upload_dir();
		$to_location = $upload_dir['basedir'] .'/csvimport/'.$filename;
		
		if (@move_uploaded_file($from_location, $to_location)) {
			do_action('woocsv_after_csv_upload',$filename);
			return $to_location;
		} else return false;
	}
}
