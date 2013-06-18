<?php
class woocsvImportAdmin extends woocsvImport
{

	public $csvContent = array();
	
	public $options = array();

	public function __construct()
	{
		add_action('admin_menu', array($this, 'adminMenu'));
		add_action('wp_ajax_saveHeader', array($this, 'saveHeader'));
		add_action('wp_ajax_header', array($this, 'header'));
		add_action('wp_ajax_saveSettings', array($this, 'saveSettings'));
		add_action('wp_ajax_runImport', array($this, 'runImport'));		
		ini_set('auto_detect_line_endings', true);
		
		
		$this->options = get_option('woocsv-options');
	}

	public function adminMenu()
	{
		$page=add_menu_page('CSV Import', 'CSV Import', 'manage_options', 'woocsv_import', array($this, 'mainPage'), '', 58);
		add_action('admin_print_scripts-' .$page, array(&$this, 'initJsCss'));
		$this->handleRequest();
	}

	public function initJsCss()
	{
		wp_enqueue_script('jquery');
		wp_register_script( 'woocsv-script', plugins_url( '/woocommerce-csvimport/js/woocsv.js' ) );
		wp_enqueue_script( 'woocsv-script' );
	}

	public function handleRequest()
	{
		add_action('woocsv_admin_menu' , array(&$this, 'mainPageContent'));
	}

	public function mainPageContent()
	{

		$tab = (isset($_REQUEST['tab']))?$_REQUEST['tab']:'main';

?>
	<div id="icon-themes" class="icon32"><br></div>
	<h2 class="nav-tab-wrapper">
		<a href="<?php echo admin_url('admin.php?page=woocsv_import');?>"
			class="nav-tab <?php echo ($tab==='main')?'nav-tab-active':''; ?>">Import</a>
		<a href="<?php echo admin_url('admin.php?page=woocsv_import&amp;tab=header');?>"
			class="nav-tab <?php echo ($tab==='header')?'nav-tab-active':''; ?>">Header</a>
		<a href="<?php echo admin_url('admin.php?page=woocsv_import&amp;tab=settings');?>"
			class="nav-tab <?php echo ($tab==='settings')?'nav-tab-active':''; ?>">Settings</a>
		<a href="<?php echo admin_url('admin.php?page=woocsv_import&amp;tab=info');?>"
			class="nav-tab <?php echo ($tab==='info')?'nav-tab-active':''; ?>">Info</a>
	</h2>

	<?php
		switch ($tab) {
		case 'main':
			$this->import();
			break;
		case 'header':
			$this->header();
			break;
		case 'settings':
			$this->settings();
			break;
		case 'info':
			$this->info();
			break;
		default:
			$this->import();
		}


	}

	public function mainPage()
	{
		echo '<div class="wrap">';
		echo '<div id="woocsv_warning" style="display:none" class="updated"></div>';
		$this->mainPageContent();
		echo '</div>';
	}

	public function addons() {
		do_action('woocsv_add_addons_to_menu');
	}

	public function settings()
	{
		global $woocsvImport;
?>
		<form id="settingsForm" method="POST">
		<h2>Import settings</h2>
		<table class="form-table">
		<tbody>
			<tr>
				<th scope="row" class="titledesc"><label for="seperator">Seperator</label></th>
				<td>
					<select id="seperator" name="seperator">
						<option value=";" <?php if ($woocsvImport->options['seperator']==';') echo 'selected';?> >;</option>
						<option value="," <?php if ($woocsvImport->options['seperator']==',') echo 'selected';?> >,</option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row" class="titledesc"><label for="skipfirstline">Skip the first line</label></th>
				<td>
					<select id="skipfirstline" name="skipfirstline">
						<option value="0" <?php if ($woocsvImport->options['skipfirstline']=='0') echo 'selected';?>>No</option>
						<option value="1" <?php if ($woocsvImport->options['skipfirstline']=='1') echo 'selected';?>>Yes</option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row" class="titledesc"><label for="blocksize">How many rows to process in one call</label></th>
				<td>
					<select id="blocksize" name="blocksize">
						<option value="1" <?php if ($woocsvImport->options['blocksize']=='1') echo 'selected';?>>1</option>
						<option value="10" <?php if ($woocsvImport->options['blocksize']=='10') echo 'selected';?>>10</option>
						<option value="25" <?php if ($woocsvImport->options['blocksize']=='25') echo 'selected';?>>25</option>
						<option value="50" <?php if ($woocsvImport->options['blocksize']=='50') echo 'selected';?>>50</option>
						<option value="75" <?php if ($woocsvImport->options['blocksize']=='75') echo 'selected';?>>75</option>
						<option value="100" <?php if ($woocsvImport->options['blocksize']=='100') echo 'selected';?>>100</option>
						<option value="250" <?php if ($woocsvImport->options['blocksize']=='250') echo 'selected';?>>250</option>
					</select>
				</td>
			</tr>
			<!--
			<tr>
				<th scope="row" class="titledesc"><label for="language">Language</label></th>
				<td>
					<select id="language" name="language">
					<?php foreach ($this->language as $key=>$value) :?>
						<option value="<?php echo $key ?>" <?php if ($woocsvImport->options['language']==$key) echo 'selected';?>><?php echo $value?></option>
					<?php endforeach;?>
					</select>
				</td>
			</tr>
			-->
			<tr>
				<td><button type="submit" class="button-primary">Save</button></td>
			</tr>
		</tbody>
		</table>

		<input type="hidden" name="action" value="saveSettings">
		</fieldset>
		</form>
		<?php
	}

	public function saveSettings()
	{
		$options = array (
			'seperator'=> $_POST['seperator'] ,
			'skipfirstline'=> $_POST['skipfirstline'],
			'blocksize' => $_POST['blocksize'],
			//'language' => $_POST['language'],
		);
		update_option('woocsv-options', $options);
		wp_die('<p>settings saved!</p>');
	}

	public function header()
	{
		global $woocsvImport;
		$file = (!empty($_FILES['file']['name']))?$_FILES['file']['name']:false;
		if (!$file) {
?>

	<form id="headerFileForm" enctype="multipart/form-data" method="POST">
		<h2>Set your header</h2>
		<table class="form-table">
		<tbody>
			<tr>
				<th scope="row" class="titledesc"><label for="file">Select your csv file</label></th>
				<td><input id="file" name="file" type="file" accept="text/csv" /></td>
			</tr>
			<tr>
				<td><button type="submit" class="button-primary">Load</button></td>
				<td></td>
			</tr>
		</tbody>
		</table>
	</form>
	<?php
			$currentHeader = get_option('woocsv-header');

			if ($currentHeader) {
				echo '<hr><h2>Your current header is:</h2>';
				foreach ($currentHeader as $field) {
					echo '<span class="badge">'.$field.';</span>';
				}
			}

		} else {

			$handle = fopen($_FILES['file']['tmp_name'], 'r');
			$row = 1;
			$csvcontent = '';
			while ($row < 4) {
				$csvcontent[] = @fgetcsv($handle, 0, $woocsvImport->options['seperator']);
				$row ++;
			}

			if (count($csvcontent[0]) == 1 ) {
				echo 'I think you have the wrong seperator';
				return;
			}
			fclose($handle);
			$length = (count($csvcontent[0]) >= count($woocsvImport->fields))?count($csvcontent[0]):count($woocsvImport->fields);

?>

		<form id="headerForm">
		<input type="hidden" name="action" value="saveHeader">
		<table class="widefat">
		<thead>
			<tr>
				<th>Fields</th>
				<th>Row 1</th>
				<th>Row 2</th>
				<th>Row 3</th>
			</tr>
		</thead>
		<tbody>
		<?php for ($i = 0; $i <= $length-1; $i++) : ?>
		<tr>
			<td>
			<select name="fields_<?php echo $i;?>">
				<option value="skip">Skip</option>
				<?php foreach ($woocsvImport->fields as $field) :?>
					<option value="<?php echo $field;?>" <?php if ( $field === $csvcontent[0][$i] ) echo 'selected'; ?>>
						<?php echo $field;?>
					</option>
				<?php endforeach; ?>
			</select>
			</td>
			<td><?php if (isset($csvcontent[0][$i])) echo $csvcontent[0][$i];?></td>
			<td><?php if (isset($csvcontent[1][$i])) echo $csvcontent[1][$i];?></td>
			<td><?php if (isset($csvcontent[2][$i])) echo $csvcontent[2][$i];?></td>
		</tr>
		<?php endfor;?>
		<tfoot>
			<tr><th><button type="submit" class="button-primary button-hero">Safe</button></th></tr>
		</tfoot>
		</tbody>
		</table>
		</form>
		<?php
		}
	}

	public function saveHeader()
	{
		$headerOrder = '';

		foreach ($_POST as $key=>$value) {
			if (preg_match("/fields_[0-9]/", $key, $matches)) {
				$headerOrder[] = $value;
			}
		}
		update_option('woocsv-header', $headerOrder);
		wp_die('<p>header saved!</p>');
	}

	

	public function runImport() {
			wp_suspend_cache_invalidation ( true );
			$postData = $_POST;
			
			for ($i = 1; $i <= $this->options['blocksize']; $i++) {
			$product = new woocsvImportProduct;
			if ($postData['currentrow'] >= $postData['rows'] ) {
				die('done');
			}
			
			if (!$this->csvContent) {
				$handle = fopen($postData['filename'], 'r');
				while (($line = fgetcsv($handle, 0, $this->options['seperator'])) !== FALSE) 
				{
					$this->csvContent[] = $line;
				}
			}
			
			if ($this->options['skipfirstline'] ==  1 && $postData['currentrow'] == 0) {
				$postData['currentrow'] ++;
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
			$id = $product->save();
			
			$postData['ID'] = $id;
			$postData['sku'] = $product->meta['_sku'];
			
			}
			wp_suspend_cache_invalidation ( false );
			echo json_encode($postData);
			die();
	}

	function handleUpload ($from_location,$filename) {
		$upload_dir = wp_upload_dir();
		$to_location = $upload_dir['basedir'] .'/csvimport/'.$filename;
		if (@move_uploaded_file($from_location, $to_location)) {
			return $to_location;
		} else return false;
	}

	public function import()
	{
			if (isset($_REQUEST['action']) && check_admin_referer('woocsv','uploadCsvFile')) {
			$options = get_option('woocsv-options');
			$filename = $this->handleUpload($_FILES['file']['tmp_name'],$_FILES['file']['name']);
			if (!$filename) wp_die('<h2>Could not upload file.</h2>');
			$handle = fopen($filename, 'r');
			$row = 0;
			$csvcontent = '';
			while (($line = fgetcsv($handle, 0, $options['seperator'])) !== FALSE) {
				$csvcontent[] = $line;
				$row ++;
			}
			?>
			<h2>Import preview</h2>
			<div id="importPreview">
			<table class="widefat">
				<thead>
			<tr>
				<th><?php echo implode('</th><th>', $csvcontent[0]);?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			for ($i=1;$i<=5 && $i<=$row-1;$i++) {
				echo '<tr><td>'.implode('</td><td>',$csvcontent[$i]).'</td></tr>';
			}
			?>
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
				<?php wp_nonce_field('woocsv','uploadCsvFile'); ?>
				<br/><br/>
				<button type="submit" class="button button-primary button-hero">Load</button>
			</fieldset>
			</form>
		<?php
		}
	}


	public function info() {
		?>
		<h2>Support the free plugin</h2>
		Want to support the free version. Please consider a donation :-)
<form action="https://www.paypal.com/cgi-bin/webscr" method="post"><input type="hidden" name="cmd" value="_s-xclick" />
<input type="hidden" name="hosted_button_id" value="PGEBD4BHNH6W4" />
<input type="image" alt="PayPal - The safer, easier way to pay online!" name="submit" src="https://www.paypalobjects.com/en_US/NL/i/btn/btn_donateCC_LG.gif" />
<img alt="" src="https://www.paypalobjects.com/nl_NL/i/scr/pixel.gif" width="1" height="1" border="0" /></form>
		<p>
			There are also a few add-ons you can use. You van find them at <a href="http://allaerd.org">allaerd.org</a>	
		</p>
		<?php
	}


}
