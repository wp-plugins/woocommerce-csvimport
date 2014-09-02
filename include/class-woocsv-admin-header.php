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
		<h2><?php echo __('Create a header','woocsv-import'); ?></h2>
		<form name="headerFileForm" id="headerFileForm" enctype="multipart/form-data" method="POST">
		<fieldset>
			<input id="file" name="action" type="hidden" value="uploadHeader" />
			<input id="file" name="file" type="file" accept="text/csv" />
			<sup><?php printf (__('Max file size: %d','woocsv-import'), $upload_mb);?></sup>
			<br/><br/>
			<button type="submit" class="button button-primary button-hero"><?php echo __('start','woocsv-import'); ?></button>
		<?php wp_nonce_field('uploadHeaderFile', 'uploadHeaderFile'); ?>
		</fieldset>
		</form>
		<?php
	}

	public static function checkHeader()
	{
		global $woocsvImport;

		if ($woocsvImport->header) {
			echo '<h2>'. __('Your current header is:','woocsv-import') .'</h2>';
			echo '<p>';
			
			foreach ($woocsvImport->header as $field) {
				echo $field.'; ';
			}
			echo '</p>';
		} else {
?>
			<h2><?php echo __('You have not created a header yet!','woocsv-import'); ?></h2>
			<p><?php echo __('Upload your csv file and map the columns to the right fields and press load!','woocsv-import'); ?></p>
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
			echo '<h2>'. __('I think you have the wrong seperator','woocsv-import').'</h2>';
			echo '<p>'.__('Please goto the settings page and change your seperator!','woocsv-import').'</p>';
			return;
		}
		fclose($handle);
		$length = count($csvcontent[0]);
		//===================================
		//! 2.0.0 create a hook for the header
		//===================================
		do_action('woocsvOutputHeader', $woocsvImport->header);
		
?>
			<h2><?php echo __('Header preview','woocsv-import'); ?></h2>
			<form id="headerForm" method="POST">
			<input id="file" name="action" type="hidden" value="saveHeader" />
			<table class="widefat">
			<thead>
				<tr>
					<th><?php echo __('Fields','woocsv-import'); ?></th>
					<th><?php echo __('Row 1','woocsv-import'); ?></th>
					<th><?php echo __('Row 2','woocsv-import'); ?></th>
					<th><?php echo __('Row 3','woocsv-import'); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php for ($i = 0; $i <= $length-1; $i++) : ?>
			<tr>
				<td>
				<select name="fields_<?php echo $i;?>">
					<option value="skip">Skip</option>
					<?php foreach (array_unique($woocsvImport->fields) as $field) :?>
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
				<tr><th><button type="submit" class="button button-primary button-hero"><?php echo __('save','woocsv-import'); ?></button></th></tr>
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
