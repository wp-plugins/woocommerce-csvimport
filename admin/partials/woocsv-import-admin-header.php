<?php

/**
	*Import
**/
global $woocsv_import;
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

	<p class="description">
		Headers are the mappings between your CSV columns and the actual fields of woocommerce. It is essential that you make a header before you import. You can make multiple headers and use them for different CSV files or for the same. Example: one header to import new products and one to only merge prices and stock.
	</p>

	<table id="headertable" class="widefat">
	<thead>
		<tr>
			<th><?php echo __('name','woocsv-import'); ?></th>
			<th><?php echo __('header','woocsv-import'); ?></th>
			<!--
				<th>&nbsp;</th>
			-->
			<th>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	<?php $i=0; ?>
	<?php foreach ($woocsv_import->headers as $key => $value) : ?>
		<tr class="<?php echo ($i % 2 == 0)?'':'alt';?>" id="<?php echo $key;?>">
			<td><?php echo $key; ?></td>
			<td><?php echo implode($woocsv_import->get_separator().' ', $value);?></td>
			<!-- 
			<td><span class="dashicons dashicons-arrow-up-alt2 up" data-header-name="<?php echo $key; ?>"></span></td>
			<td><span class="dashicons dashicons-arrow-down-alt2 down" data-header-name="<?php echo $key; ?>"></span></td>
			-->
			<td><button class="button-primary delete" data-header-name="<?php echo $key; ?>">delete</button></td>
		</tr>
	<?php $i++; ?>
	<?php endforeach;?>

	</tbody>
	</table>
	<br/>
	
	<form name="upload_header_form" id="upload_header_form" enctype="multipart/form-data" method="POST">
		<fieldset style="border:1px solid; padding:10px;">
			<legend><span>Upload a CSV file to create a header</span></legend>
			<input id="csvfile" name="csvfile" type="file" accept="text/csv" />
			<input type="text" hidden name="action" value="start_header_preview">
			<button type="submit" class="button-primary"><?php echo __('Load file','woocsv-import'); ?></button>
		<?php wp_nonce_field('upload_header_file', 'upload_header_file'); ?>
		</fieldset>
	</form>
</div>

<script>
	
//down
jQuery ('td span.dashicons.down').click(function() {

});

//up
jQuery ('td span.dashicons.up').click(function() {

});
	
jQuery('td button.delete').click(function() {
	
    var data = {
        action: 'delete_header',
        header_name: jQuery(this).data('header-name')
    };
    
    jQuery.post(ajaxurl, data, function(response) {
	    
       jQuery(this).closest('.tr').remove();       
       if (response) {
			jQuery("table#headertable tr[id='"+response+"']").remove();   
       }
       
    });
});		
</script>