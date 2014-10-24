<?php
class woocsvAdminImport
{
		
	public static function start()
	{

		global $woocsvImport, $upload_mb;
		if (isset($_REQUEST['action']) && check_admin_referer('woocsv', 'uploadCsvFile')) {

			$filename = self::handleUpload($_FILES['file']['tmp_name'], $_FILES['file']['name']);
			if (!$filename) wp_die(__('Could not upload file','woocsv-import'));

			$handle = fopen($filename, 'r');
			$row = 0;
			$csvcontent = '';
			while (($line = fgetcsv($handle, 0, $woocsvImport->options['seperator'])) !== FALSE) {
				$csvcontent[] = $line;
				$row ++;
			}
			$length = count($csvcontent[0]);

			if (count($csvcontent[0]) == 1 ) {
				echo '<h2>'.__('I think you have the wrong seperator','woocsv-import').'</h2>';
				echo '<p>'.__('Please goto the settings page and change your seperator!','woocsv-import').'</p>';
				return;
			}
?>
			<div id="importPreview">
			<h2><?php echo __('Import preview','woocsv-import'); ?></h2>
			<table class="widefat">
			<thead>
				<tr>
					<th><?php echo __('Row 1','woocsv-import'); ?></th>
					<th><?php echo __('Row 2','woocsv-import'); ?></th>
					<th><?php echo __('Row 3','woocsv-import'); ?></th>
					<th><?php echo __('Row 4','woocsv-import'); ?></th>
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
				<button type="submit" class="button button-primary button-hero"><?php echo __('start','woocsv-import'); ?></button>
			</form>
			</div>
			<div class="postbox" style="margin:1em 0 0 0;">
				<div class="inside">
					<div id="import_log">
					</textarea>
				</div>
			</div>
			<?php
			unset($csvcontent, $line);
		} else {
?>
			<h2><?php echo __('Import','woocsv-import'); ?></h2>
			<form name="loadPreview" method="POST" enctype="multipart/form-data">
			<fieldset>
				<input id="file" name="file" type="file" accept="text/csv" />
				<sup><?php printf (__('Max file size: %d mb','woocsv-import'), $upload_mb);?></sup>
				<input type="hidden" name="action" value="runImport">
				<?php wp_nonce_field('woocsv', 'uploadCsvFile'); ?>
				<br/><br/>
				<button type="submit" class="button button-primary button-hero"><?php echo __('start','woocsv-import'); ?></button>
			</fieldset>
			</form>
			<hr>
			<?php
			if ($options = get_option('woocsv-lastrun')) {
				echo __('If you are merging products, please be sure you have set the right header!','woocsv-import').'<br/>';
				echo sprintf(__('Last run: %s','woocsv-import'),$options['date']) .'<br/>';
				echo sprintf(__('Filename: %s','woocsv-import'),$options['filename']) .'<br/>';
				echo sprintf(__('Number of rows: %s','woocsv-import'),$options['rows']) .'<br/>';
			}
		}
	}

	public static function runImport()
	{
		global $wooProduct, $woocsvImport, $wpdb;

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
		
		//================================
		//! only import the rows needed.
		//================================

		while (($line = fgetcsv($handle, 0, $woocsvImport->options['seperator'])) !== FALSE) {
		
			if ( $count >= $postData['currentrow'] && $count < ( (int)$postData['currentrow'] + (int)$postData['blocksize'])  ) {
				$csvContent[$count] = $line;
			}
			$count ++;
		}

		unset($handle, $line);

		//========================================================
		//! Run only the block from currentrow and the blocksize
		//========================================================
		
		for ($i = 1; $i <= $woocsvImport->options['blocksize']; $i++) {

			$wooProduct = new woocsvImportProduct;
			$wooProduct->header = $woocsvImport->header;
			$realRow = $postData['currentrow'] +1;
			
			
			//===================
			//! We are finished
			//===================

			if ($postData['currentrow'] >= $postData['rows'] ) {
				ob_get_clean();
				update_option('woocsv-lastrun', array('date'=>date("Y-m-d H:i:s"), 'filename'=>basename($filename), 'rows'=>$postData['rows']));
				delete_option('woocsv-importfile');
				do_action('woocsv_after_import_finished');
				self::dieNice($postData,true);
			}

			// count the rows here else we have a row and than die.
			$woocsvImport->importLog[] = "--> ".__('row','woocsv-import').":". $realRow ." / ". ((int)$postData['rows']) ;

			//==================================
			//! We want to skip the first line
			//==================================

			if ($woocsvImport->options['skipfirstline'] ==  1 && $postData['currentrow'] == 0) {
				$postData['currentrow'] ++;
				$woocsvImport->importLog[] = __('Skipping the first row','woocsv-import');
				self::dieNice($postData);
			}

			//=========================================
			//! We do not want to skip the first line
			//=========================================

			if ($woocsvImport->options['skipfirstline'] ==  0 && $postData['currentrow'] == 0) {
				$wooProduct->rawData = $csvContent[0];
			}

			if ($postData['currentrow'] > 0)
				$wooProduct->rawData = $csvContent[$postData['currentrow']];

			$postData['currentrow'] ++;

			//=========================
			//! Lets fill in the data
			//=========================

			do_action('woocsv_before_fill_in_data' );
			
			$wooProduct->fillInData();

			do_action('woocsv_after_fill_in_data' );

			//===================
			//! version 2.0.0 
			//  lets parse data
			//===================

			$wooProduct->parseData();

			//=======================
			//! let's save the data
			//=======================

			try {
				$id = $wooProduct->save();
			} catch (Exception $e) {
				$id = '';
			}

			//===============================================
			//! lets fill in the memory stuff for debugging
			//===============================================

			$postData['memory'] = round(memory_get_usage()/1024/1024, 2);

		}


		wp_suspend_cache_invalidation ( false );
		/* ! 1.2.7 */
		wp_defer_term_counting( false ) ;

		//==========================
		//! version 2.0.0
		//  New die nice function
		//==========================

		self::dieNice($postData);
	}


	public static function dieNice($postData,$done=false)
	{
		global $wpdb,$woocsvImport,$wooProduct;
		//===================
		//! Clear transients
		//===================
		$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '%_transient_%'");
		
		//=============================
		//! Check if we need to debug
		//=============================
		if ( 0 == $woocsvImport->options['debug']) {
			ob_get_clean();
		} else {
			$postData['product'] = $wooProduct;
		}

		//===============
		//! Add to logs
		//===============
		$postData['log'] = $woocsvImport->importLog;

		//add done flag
		if ($done) $postData['done'] = 1; else $postData['done']= 0;

		//unset the product to be sure it's reset for the next run
		unset($wooProduct);
			
		//echo the json and die nice
		echo json_encode($postData);
		die();
	}


	static function handleUpload($from_location, $filename)
	{
		do_action('woocsv_before_csv_upload', $filename);
		$upload_dir = wp_upload_dir();
		$to_location = $upload_dir['basedir'] .'/csvimport/'.$filename;

		if (@move_uploaded_file($from_location, $to_location)) {
			do_action('woocsv_after_csv_upload', $filename);
			return $to_location;
		} else return false;
	}
}
