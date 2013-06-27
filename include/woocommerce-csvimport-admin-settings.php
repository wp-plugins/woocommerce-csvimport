<?php
class woocsvImportAdminSettings
{

	public function __construct() {
		if ( !empty($_POST) && check_admin_referer('saveSettings', 'saveSettings') ) {
			$this->saveSettings();
		}
		
		$this->settings();
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
				<th scope="row" class="titledesc"><label for="add_to_gallery">Add images to the product gallery</label></th>
				<td>
					<select id="add_to_gallery" name="add_to_gallery">
						<option value="0" <?php if ($woocsvImport->options['add_to_gallery']=='0') echo 'selected';?>>No</option>
						<option value="1" <?php if ($woocsvImport->options['add_to_gallery']=='1') echo 'selected';?>>Yes</option>
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
			<tr>
				<td><button type="submit" class="button-primary">Save</button></td>
			</tr>
		</tbody>
		</table>
		<?php wp_nonce_field('saveSettings', 'saveSettings'); ?>
		</fieldset>
		</form>
		<?php
	}

	public function saveSettings()
	{
		global $woocsvImport;
		$options = array (
			'seperator'=> empty($_POST['seperator'])?'':$_POST['seperator'],
			'skipfirstline'=> empty($_POST['skipfirstline'])?'':$_POST['skipfirstline'],
			'blocksize' => empty($_POST['blocksize'])?'':$_POST['blocksize'],
			'add_to_gallery' => empty($_POST['add_to_gallery'])?'':$_POST['add_to_gallery'],
		);
		update_option('woocsv-options', $options);
		$woocsvImport->options = $options;
	}
}
