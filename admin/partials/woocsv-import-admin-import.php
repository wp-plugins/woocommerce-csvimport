<?php

/**
	*Import
**/
global $woocsv_import;
$headers = array_keys( $woocsv_import->headers);
?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">
	<h2>Import</h2>	
	<ul class="subsubsub">
		<li><a href="<?php echo admin_url('admin.php?page=woocsv_import&amp;tab=import');?>" class="current">Import</a> |</li>
		<li><a href="<?php echo admin_url('admin.php?page=woocsv_import&amp;tab=headers');?>">Headers</a></li>
	</ul>
	<br class="clear">
	<hr>
	<table class="form-table">
	<form name="upload_header_form" id="upload_header_form" enctype="multipart/form-data" method="POST">
		<tr>
			<th>Select a header</th>
			<td><select id="header_name" name="header_name">
	 		<?php foreach ($headers as $header) : ?>
	 			<option value="<?php echo $header; ?>"><?php echo $header; ?></option>
	 		<?php endforeach;?>
			</select></td>
		</tr>
		<tr>
			<th>Select a CSV file</th>
			<td><input id="csvfile" name="csvfile" type="file" accept="text/csv" /></td>
		</tr>
		<tr>
			<th>&nbsp;</th>
			<td><button type="submit" class="button-primary"><?php echo __('Load file','woocsv-import'); ?></button></td>
		</tr>
	<?php wp_nonce_field('upload_import_file', 'upload_import_file'); ?>
	<input type="text" hidden name="action" value="start_import_preview">
	</form>
	</table>
</div>
