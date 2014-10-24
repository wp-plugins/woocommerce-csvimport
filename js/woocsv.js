

function doAjaxImport(formData)
{
	jQuery.ajax(
	{
		type: "POST",
		url: ajaxurl,
		data: formData,
		success: function(data)
		{ 
			console.log(data);
			var newFormData = JSON.parse(data);
			console.log(newFormData);
			if (newFormData.done !=1)
			{								
				if (newFormData.log && newFormData.log.length > 0) {
					jQuery.each(newFormData.log, function( index, value ) {
						jQuery('#import_log').prepend('<p> '+value+' </p>');
					});
				}
				doAjaxImport(newFormData);
			}
			else
			{
				if (newFormData.log && newFormData.log.length > 0) {
					jQuery.each(newFormData.log, function( index, value ) {
						jQuery('#import_log').prepend('<p> '+value+' </p>');
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
