var d = Date.now();

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
			if (data != 'done')
			{
				var newFormData = JSON.parse(data);
				jQuery('#import_log').prepend('<p> row: ' + newFormData.currentrow + ' / ' + newFormData.rows + '</p>');
				doAjaxImport(newFormData);
			}
			else
			{
				jQuery('#import_log').prepend('<p>Done!</p>');
			}
		},
		error: function(data)
		{
		console.log(data);
			alert('something went wrong');
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
		jQuery('#import_log').prepend('<p>Starting.....</p>');
		var formData = jQuery(this).serialize();
		doAjaxImport(formData);
		e.preventDefault();
	});
});
