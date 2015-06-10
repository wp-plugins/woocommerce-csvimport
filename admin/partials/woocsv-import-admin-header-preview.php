<?php
/*
	*
	* header preview
	*
*/
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">
	<h2>Headers</h2>
	<ul class="subsubsub">
		<li><a href="<?php echo admin_url('admin.php?page=woocsv_import&amp;tab=import');?>">Import</a> |</li>
		<li><a href="<?php echo admin_url('admin.php?page=woocsv_import&amp;tab=headers');?>" class="current">Headers</a></li>
	</ul>
	<br class="clear">
	<hr>

<?php 
global $woocsv_import;
		
		//open the temp file	
		$handle = fopen($_FILES['csvfile']['tmp_name'], 'r');
		$row = 1;
		$csvcontent = '';
		
		// loop through the first 4 lines
		while ($row < 5) {
			$csvcontent[] = @fgetcsv($handle, 0, $woocsv_import->get_separator());
			$row ++;
		}

		//check to see if the we might have the wrong separator
		if (count($csvcontent[0]) == 1 ) {
			echo '<h2>'. __('I think you have the wrong separator','woocsv-import').'</h2>';
			echo '<p>'.__('Please goto the settings page and change your separator!','woocsv-import').'</p>';
			return;
		}
		
		//close the file
		fclose($handle);
		
		//cset the amount of rows
		$length = count($csvcontent[0]);
		
		//hook after header is done
		do_action('woocsv_header_preview', $woocsv_import->header);
		
?>
			<h2><?php echo __('Header preview','woocsv-import'); ?></h2>
			<form id="header_prieview_form" method="POST">
			<table class="widefat">
			<thead>
				<tr>
					<th><?php echo __('Fields','woocsv-import'); ?></th>
					<th><?php echo __('Row 1','woocsv-import'); ?></th>
					<th><?php echo __('Row 2','woocsv-import'); ?></th>
					<th><?php echo __('Row 3','woocsv-import'); ?></th>
					<th><?php echo __('Row 4','woocsv-import'); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php for ($i = 0; $i <= $length-1; $i++) : ?>
			<tr class="<?php echo ($i % 2 == 0)?'':'alt';?>">
				<td>
				<select name="fields_<?php echo $i;?>">
					<option value="skip">Skip</option>
					<?php 
					// loop through the fields and check if the match the defined ones.
					foreach (array_unique($woocsv_import->fields) as $field) : ?> 
						<option value="<?php echo $field;?>" <?php if ( trim(strtolower($field)) == trim(strtolower($csvcontent[0][$i])) ) echo 'selected'; ?>>
							<?php echo trim($field);?>
						</option>
					<?php endforeach; ?>
				</select>
				</td>
				<td><?php if (isset($csvcontent[0][$i])) echo $csvcontent[0][$i];?></td>
				<td><?php if (isset($csvcontent[1][$i])) echo $csvcontent[1][$i];?></td>
				<td><?php if (isset($csvcontent[2][$i])) echo $csvcontent[2][$i];?></td>
				<td><?php if (isset($csvcontent[3][$i])) echo $csvcontent[3][$i];?></td>
			</tr>
			<?php endfor;?>
			</tbody>
			<tfoot>			
				<tr>
					<th><input required type="text" class="regular-text" name="header_name" id="header_name" placeholder="The name of your header"></th>
					<th><button type="submit" class="button-primary" disabled ><?php echo __('save','woocsv-import'); ?></button></th>
				</tr>
			</tfoot>
			</table>
			<input id="text" name="action" type="hidden" value="save_header_preview" />
			<?php wp_nonce_field('save_header_preview', 'save_header_preview'); ?>
			</form>
</div>

<script>
	
jQuery( '#header_name, select' ).keypress(function (e) {
	chars = jQuery('#header_name').val();
	
	if ( chars.length > 0) {
		jQuery('button').prop("disabled", false);
	} else {
		jQuery ('button').prop("disabled", true);
	}
	
	if (e.which == 13) {
		e.preventDefault();
	}
});


</script>

