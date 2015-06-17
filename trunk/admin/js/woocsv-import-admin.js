function doAjaxImport(formData)
{
	jQuery.ajax(
	{
		type: "POST",
		url: ajaxurl,
		data: formData,
		success: function(data)
		{ 
			try { 
				console.log (data);
				var newFormData = JSON.parse(data); 
			} 
			catch (err) {
				jQuery('#import_log').prepend('<div>'+ data +'</div>');
				jQuery('#import_log').prepend('<p><h2>Something went wrong. The stack trace is printed below</h2></p>');
			}

			if (newFormData.done !=1)
			{								
				if (newFormData.log && newFormData.log.length > 0) {
					jQuery.each(newFormData.log, function( index, value ) {
						jQuery('#import_log').prepend('<p> '+value+' </p>');
						
						jQuery('#woocsv_import_progress').val(newFormData.currentrow);
					});
				}
				doAjaxImport(newFormData);
			}
			else
			{	
				if (newFormData.log && newFormData.log.length > 0) {
					jQuery.each(newFormData.log, function( index, value ) {
						jQuery('#import_log').prepend('<p> '+value+' </p>');
						
						jQuery('#woocsv_import_progress').val(newFormData.currentrow);
					});
				}
				jQuery('#import_log').prepend('<p><h2>'+strings.done+'</h2></p>');
			}
		},
		error: function(data)
		{
			console.log(data);
			alert(strings.error);
		}
	});
}

jQuery(document).ready(function()
{
	jQuery('#runImportForm').submit(function(e)
	{
		jQuery('html, body').animate(
		{
			scrollTop: 0
		}, 'slow');
		jQuery('#importPreview').slideUp();
		jQuery('#import_log').prepend('<p>'+strings.start+'</p>');
		var formData = jQuery(this).serialize();
		doAjaxImport(formData);
		e.preventDefault();
	});
});
