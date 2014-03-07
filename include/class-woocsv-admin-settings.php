<?php
class woocsvAdminSettings
{
	public static function start() {
		if ( !empty($_POST) && check_admin_referer('saveSettings', 'saveSettings') ) {
			self::saveSettings();
		}
		
		self::settings();
	}

	static function settings()
	{
		global $woocsvImport;
?>
		<form id="settingsForm" method="POST">
		<h2>Import settings</h2>
		<table class="form-table">
		<tbody>
			<tr>
				<th scope="row" class="titledesc"><label for="seperator">Separator</label></th>
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
				<th scope="row" class="titledesc"><label for="merge_products">Merge products</label></th>
				<td>
					<select id="merge_products" name="merge_products">
						<option value="0" <?php if ($woocsvImport->options['merge_products']=='0') echo 'selected';?>>No</option>
						<option value="1" <?php if ($woocsvImport->options['merge_products']=='1') echo 'selected';?>>Yes</option>
					</select>
				<p>
					When you merge products, existing values of the product will be preserved. Only values that are in your CSV will be updated. With this option you can update only the price and stock for example!
				</p>
				</td>
			</tr>
			<tr>
				<th scope="row" class="titledesc"><label for="add_to_categories">Add product to all categories</label></th>
				<td>
					<select id="add_to_categories" name="add_to_categories">
						<option value="0" <?php if ($woocsvImport->options['add_to_categories']=='0') echo 'selected';?>>No</option>
						<option value="1" <?php if ($woocsvImport->options['add_to_categories']=='1') echo 'selected';?>>Yes</option>
					</select>
					<p>
					If you enable this, products will be added to all categories on not only the latest. Example :<code>cat1->subcat1</code> , if the option is enabled, the product belongs to both else it will only belong to the sub categorie.
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row" class="titledesc"><label for="add_to_gallery">Add images to the product gallery<b> (DEPRECIATED)</b></label></th>
				<td>
					<select id="add_to_gallery" name="add_to_gallery">
						<option value="0" <?php if ($woocsvImport->options['add_to_gallery']=='0') echo 'selected';?>>No</option>
						<option value="1" <?php if ($woocsvImport->options['add_to_gallery']=='1') echo 'selected';?>>Yes</option>
					</select>
					<p>
					If you use the featured image or product gallery fields, this field will be ignored!
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row" class="titledesc"><label for="blocksize">How many rows to process in one call</label></th>
				<td>
					<select id="blocksize" name="blocksize">
						<option value="1" <?php if ($woocsvImport->options['blocksize']=='1') echo 'selected';?>>1</option>
						<option value="5" <?php if ($woocsvImport->options['blocksize']=='5') echo 'selected';?>>5</option>
						<option value="10" <?php if ($woocsvImport->options['blocksize']=='10') echo 'selected';?>>10</option>
						<option value="25" <?php if ($woocsvImport->options['blocksize']=='25') echo 'selected';?>>25</option>
						<option value="50" <?php if ($woocsvImport->options['blocksize']=='50') echo 'selected';?>>50</option>
						<option value="75" <?php if ($woocsvImport->options['blocksize']=='75') echo 'selected';?>>75</option>
						<option value="100" <?php if ($woocsvImport->options['blocksize']=='100') echo 'selected';?>>100</option>
						<option value="250" <?php if ($woocsvImport->options['blocksize']=='250') echo 'selected';?>>250</option>
					</select>
					<p>
					The importer works with AJAX calls to prevent timeouts on large datasets. With this setting you can adjust how many rows will be handled during ONE AJAX call. If you set this number to high, you can still have timeouts!</p>
				</td>
			</tr>
			<tr>
				<th scope="row" class="titledesc"><label for="debug">Debug (check javascript console!)</label></th>
				<td>
					<select id="debug" name="debug">
						<option value="0" <?php if ($woocsvImport->options['debug']=='0') echo 'selected';?>>No</option>
						<option value="1" <?php if ($woocsvImport->options['debug']=='1') echo 'selected';?>>Yes</option>
					</select>
				</td>
			</tr>
			<tr>
				<td><button type="submit" class="button button-primary button-hero"">Save</button></td>
			</tr>
		</tbody>
		</table>
		<?php wp_nonce_field('saveSettings', 'saveSettings'); ?>
		</fieldset>
		</form>
		<?php
	}

	static function saveSettings()
	{
		/* !new added debug option */
		global $woocsvImport;
		$options = array (
			'seperator'=> empty($_POST['seperator'])?'':$_POST['seperator'],
			'skipfirstline'=> empty($_POST['skipfirstline'])?'':$_POST['skipfirstline'],
			'blocksize' => empty($_POST['blocksize'])?'':$_POST['blocksize'],
			'add_to_gallery' => empty($_POST['add_to_gallery'])?'':$_POST['add_to_gallery'],
			'merge_products' => empty($_POST['merge_products'])?'':$_POST['merge_products'],
			'add_to_categories' => empty($_POST['add_to_categories'])?'':$_POST['add_to_categories'],
			'debug' => empty($_POST['debug'])?'':$_POST['debug'],
		);
		update_option('woocsv-options', $options);
		$woocsvImport->options = $options;
	}
}
