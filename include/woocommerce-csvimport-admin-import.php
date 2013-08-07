<?php
$woocsvHandleImport = new woocsvHandleImport();

class woocsvHandleImport 
{
	public $options = array ();

	public $csvContent = array();
	
	public function __construct() {
		$this->options = get_option('woocsv-options');
	}
	
	public function runImport()
	{
		wp_suspend_cache_invalidation ( true );
		$postData = $_POST;
		for ($i = 1; $i <= $this->options['blocksize']; $i++) {
			$product = new woocsvImportProduct;
			if ($postData['currentrow'] >= $postData['rows'] ) {
				ob_get_clean();
				do_action('woocsv_after_import_finished');
				die('done');
			}

			if (!$this->csvContent) {
				$handle = fopen($postData['filename'], 'r');
				while (($line = fgetcsv($handle, 0, $this->options['seperator'])) !== FALSE) {
					$this->csvContent[] = $line;
				}
			}

			if ($this->options['skipfirstline'] ==  1 && $postData['currentrow'] == 0) {
				$postData['currentrow'] ++;
				ob_get_clean();
				echo json_encode($postData);
				die();
			}


			if ($this->options['skipfirstline'] ==  0 && $postData['currentrow'] == 0)
				$product->rawData = $this->csvContent[0];

			if ($postData['currentrow'] > 0)
				$product->rawData = $this->csvContent[$postData['currentrow']];

			$postData['currentrow'] ++;

			//create a new product


			//parse all data
			$product->fillInData();

			//save it
			try {
				$id = $product->save();
			} catch (Exception $e) {
				$id = '';
			}

			$postData['ID'] = $id;
			$postData['sku'] = $product->meta['_sku'];
			$postData['product'] = $product;
		}
		
		delete_option('category_children');
		wp_suspend_cache_invalidation ( false );
		ob_get_clean();
		echo json_encode($postData);
		die();
	}

	function handleUpload($from_location, $filename)
	{
		do_action('woocsv_before_csv_upload',$filename);
		$upload_dir = wp_upload_dir();
		$to_location = $upload_dir['basedir'] .'/csvimport/'.$filename;
		if (@move_uploaded_file($from_location, $to_location)) {
			do_action('woocsv_after_csv_upload',$filename);
			return $to_location;
		} else return false;
	}

	public function import()
	{
		if (isset($_REQUEST['action']) && check_admin_referer('woocsv', 'uploadCsvFile')) {
			$options = get_option('woocsv-options');
			$filename = $this->handleUpload($_FILES['file']['tmp_name'], $_FILES['file']['name']);
			if (!$filename) wp_die('<h2>Could not upload file.</h2>');
			$handle = fopen($filename, 'r');
			$row = 0;
			$csvcontent = '';
			while (($line = fgetcsv($handle, 0, $options['seperator'])) !== FALSE) {
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
		} else {
?>			
			<h2>Let's import!</h2>
			<form name="loadPreview" method="POST" enctype="multipart/form-data">
			<fieldset>
				<input id="file" name="file" type="file" accept="text/csv" />
				<input type="hidden" name="action" value="runImport">
				<?php wp_nonce_field('woocsv', 'uploadCsvFile'); ?>
				<br/><br/>
				<button type="submit" class="button button-primary button-hero">Load</button>
			</fieldset>
			</form>
		<?php
		}
	}
}
