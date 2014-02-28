<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class woocsvAdminHeader
{

	public static function start()
	{
	
		/* 		!1.2.1		 */		
		if ( !empty($_POST) && $_POST['action'] === 'uploadHeader' &&
			!empty($_FILES['file']['name']) &&
			check_admin_referer('uploadHeaderFile', 'uploadHeaderFile')
		) {
			self::showHeaderPreview();
		} elseif ( !empty($_POST) && $_POST['action'] === 'saveHeader' &&
			check_admin_referer('saveHeaderFields', 'saveHeaderFields')
		) {
			self::saveHeader();
			self::checkHeader();
			self::headerUpload();
		} else {
			self::checkHeader();
			self::headerUpload();
		}
	}

	public static function headerUpload()
	{
	global $upload_mb;
?>
		<h2>Create a header</h2>
		<form name="headerFileForm" id="headerFileForm" enctype="multipart/form-data" method="POST">
		<input id="file" name="action" type="hidden" value="uploadHeader" />
		<table class="form-table">
		<tbody>
			<tr>
				<th scope="row" class="titledesc"><label for="file">Select your csv file <sup><?php echo "Max file size: $upload_mb";?></sup></label></th>
				<td><input id="file" name="file" type="file" accept="text/csv" /></td>
			</tr>
			<tr>
				<td><button type="submit" class="button button-primary button-hero"">Load</button></td>
				<td></td>
			</tr>
		</tbody>
		</table>
		<?php wp_nonce_field('uploadHeaderFile', 'uploadHeaderFile'); ?>
		</form>
		<?php
	}

	public static function checkHeader()
	{
		global $woocsvImport;

		if ($woocsvImport->header) {
			echo '<h2>Your current header is:</h2>';
			echo '<p>';
			foreach ($woocsvImport->header as $field) {
				echo $field.'; ';
			}
			echo '</p>';
		} else {
?>
			<h2>You have not created a header yet!</h2>
			<p>Upload your csv file and map the columns to the right fields and press load!</p>
		<?php
		}
	}

	public static function showHeaderPreview()
	{
		global $woocsvImport;
		
				
		$handle = fopen($_FILES['file']['tmp_name'], 'r');
		$row = 1;
		$csvcontent = '';
		while ($row < 4) {
			$csvcontent[] = @fgetcsv($handle, 0, $woocsvImport->options['seperator']);
			$row ++;
		}

		if (count($csvcontent[0]) == 1 ) {
			echo '<h2>I think you have the wrong seperator</h2>';
			echo '<p>Please goto the settings page and change your seperator!</p>';
			return;
		}
		fclose($handle);
		$length = count($csvcontent[0]);
?>
			<h2>Header preview</h2>
			<form id="headerForm" method="POST">
			<input id="file" name="action" type="hidden" value="saveHeader" />
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
					<optgroup label="Body">
					<?php foreach ($woocsvImport->fields as $field) :?>
						<option value="<?php echo $field;?>" <?php if ( $field === $csvcontent[0][$i] ) echo 'selected'; ?>>
							<?php echo $field;?>
						</option>
					<?php endforeach; ?>
					</optgroup>
				</select>
				</td>
				<td><?php if (isset($csvcontent[0][$i])) echo $csvcontent[0][$i];?></td>
				<td><?php if (isset($csvcontent[1][$i])) echo $csvcontent[1][$i];?></td>
				<td><?php if (isset($csvcontent[2][$i])) echo $csvcontent[2][$i];?></td>
			</tr>
			<?php endfor;?>
			<tfoot>
				<tr><th><button type="submit" class="button button-primary button-hero"">Save</button></th></tr>
			</tfoot>
			</tbody>
			</table>
			<?php wp_nonce_field('saveHeaderFields', 'saveHeaderFields'); ?>
			</form>
			<?php
	}


	public static function saveHeader()
	{
		global $woocsvImport;
		$headerOrder = '';
		foreach ($_POST as $key=>$value) {
			if (preg_match("/fields_[0-9]/", $key, $matches)) {
				$headerOrder[] = $value;
			}
		}
		update_option('woocsv-header', $headerOrder);
		$woocsvImport->header = $headerOrder;
	}


}
