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
		global $woocsvImport,$wp_roles;

?>
		<form id="settingsForm" method="POST">
		<h2><?php echo __('Import Settings','woocsv-import'); ?></h2>
		<table class="form-table">
		<tbody>
			<?php if (current_user_can( 'manage_options' ))  : ?>
			<tr>
				<th>
					<?php echo __('Allowed Roles:','woocsv-import'); ?>
				</th>
				<td>
					<select size=<?php echo count($wp_roles->role_names) ?> name=roles[] multiple required>
					<?php
					foreach ($wp_roles->role_names as $key=>$value) {
						if (in_array($key, $woocsvImport->options['roles']))
							echo "<option selected value=$key>$value</option>"; 
						else
 						 	echo "<option value=$key>$value</option>"; 
					}
					?>
					</select>
				</td>
			</tr>
			<?php endif; ?>
			<tr>
				<th><?php echo __('Separator','woocsv-import'); ?></th>
				<td>
					<select id="seperator" name="seperator">
						<option value=";" <?php if ($woocsvImport->options['seperator']==';') echo 'selected';?> >;</option>
						<option value="," <?php if ($woocsvImport->options['seperator']==',') echo 'selected';?> >,</option>
					</select>
				</td>
			</tr>
			<tr>
				<th><?php echo __('Skip the first line','woocsv-import'); ?></th>
				<td>
					<select id="skipfirstline" name="skipfirstline">
						<option value="0" <?php if ($woocsvImport->options['skipfirstline']=='0') echo 'selected';?>>No</option>
						<option value="1" <?php if ($woocsvImport->options['skipfirstline']=='1') echo 'selected';?>>Yes</option>
					</select>
				</td>
			</tr>
			<tr>
				<th><?php echo __('Merge products','woocsv-import'); ?></th>
				<td>
					<select id="merge_products" name="merge_products">
						<option value="0" <?php if ($woocsvImport->options['merge_products']=='0') echo 'selected';?>>No</option>
						<option value="1" <?php if ($woocsvImport->options['merge_products']=='1') echo 'selected';?>>Yes</option>
					</select>
				<p>
					<?php echo __('When you merge products, existing values of the product will be preserved. Only values that are in your CSV will be updated. With this option you can update only the price and stock for example!','woocsv-import'); ?>
				</p>
				</td>
			</tr>
			<tr>
				<th><?php echo __('Add product to all categories','woocsv-import'); ?></th>
				<td>
					<select id="add_to_categories" name="add_to_categories">
						<option value="0" <?php if ($woocsvImport->options['add_to_categories']=='0') echo 'selected';?>>No</option>
						<option value="1" <?php if ($woocsvImport->options['add_to_categories']=='1') echo 'selected';?>>Yes</option>
					</select>
					<p>
					<?php echo __('If you enable this, products will be added to all categories on not only the latest. Example :<code>cat1->subcat1</code> , if the option is enabled, the product belongs to both else it will only belong to the sub categorie.','woocsv-import'); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th><?php echo __('How many rows to process in one call','woocsv-import'); ?></th>
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
					<?php echo __('The importer works with AJAX calls to prevent timeouts on large datasets. With this setting you can adjust how many rows will be handled during ONE AJAX call. If you set this number to high, you can still have timeouts!','woocsv-import'); ?></p>
				</td>
			</tr>
			<tr>
				<th><?php echo __('Enable debug','woocsv-import'); ?></th>
				<td>
					<select id="debug" name="debug">
						<option value="0" <?php if ($woocsvImport->options['debug']=='0') echo 'selected';?>>No</option>
						<option value="1" <?php if ($woocsvImport->options['debug']=='1') echo 'selected';?>>Yes</option>
					</select>
				</td>
			</tr>
			<tr>
				<th><?php echo __('Match product by SKU or post title','woocsv-import'); ?></th>
				<td>
					<select id="match_by" name="match_by">
						<option value="sku" <?php if ($woocsvImport->options['match_by']=='sku') echo 'selected';?>>sku</option>
						<option value="title" <?php if ($woocsvImport->options['match_by']=='title') echo 'selected';?>>title</option>
					</select>
				</td>
			</tr>
			
			<tr>
				<th><?php echo __('Match authors by there id, slug, email, or login','woocsv-import'); ?></th>
				<td>
					<select id="match_author_by" name="match_author_by">
						<option value="id" <?php if ($woocsvImport->options['match_author_by']=='id') echo 'selected';?>>id</option>
						<option value="slug" <?php if ($woocsvImport->options['match_author_by']=='slug') echo 'selected';?>>slug</option>
						<option value="email" <?php if ($woocsvImport->options['match_author_by']=='email') echo 'selected';?>>email</option>
						<option value="login" <?php if ($woocsvImport->options['match_author_by']=='login') echo 'selected';?>>login</option>
					</select>
				</td>
			</tr>
			
			
			<tr>
				<td><button type="submit" class="button button-primary button-hero"><?php echo __('save','woocsv-import'); ?></button></td>
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
		global $woocsvImport;
		$options = array (
			'seperator'=> empty($_POST['seperator'])?'':$_POST['seperator'],
			'skipfirstline'=> empty($_POST['skipfirstline'])?'':$_POST['skipfirstline'],
			'blocksize' => empty($_POST['blocksize'])?'':$_POST['blocksize'],
			'add_to_gallery' => empty($_POST['add_to_gallery'])?'':$_POST['add_to_gallery'],
			'merge_products' => empty($_POST['merge_products'])?'':$_POST['merge_products'],
			'add_to_categories' => empty($_POST['add_to_categories'])?'':$_POST['add_to_categories'],
			'debug' => empty($_POST['debug'])?'':$_POST['debug'],
			'match_by' => empty($_POST['match_by'])?'':$_POST['match_by'],
			'roles' => empty($_POST['roles'])?'':$_POST['roles'],
			'match_author_by' => empty($_POST['match_author_by'])?'':$_POST['match_author_by'],
		);
		update_option('woocsv-options', $options);
		$woocsvImport->options = $options;
	}
}
