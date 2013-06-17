var d = Date.now();
jQuery(document).ready(function()
{
	jQuery('#headerForm').submit(function(e)
	{
		var saveHeaderForm = jQuery(this).serialize();
		jQuery.ajax(
		{
			type: "POST",
			url: "/wp-admin/admin-ajax.php",
			data: saveHeaderForm,
			success: function(data)
			{
				jQuery('html, body').animate(
				{
					scrollTop: 0
				}, 'slow');
				jQuery('#woocsv_warning').html(data);
				jQuery("#woocsv_warning").slideDown().delay(2500).slideUp();
			}
		});
		e.preventDefault();
	});
	
	jQuery('#settingsForm').submit(function(e)
	{
		var formData = jQuery(this).serialize();
		jQuery.ajax(
		{
			type: "POST",
			url: "/wp-admin/admin-ajax.php",
			data: formData,
			success: function(data)
			{
				jQuery('#woocsv_warning').html(data);
				jQuery("#woocsv_warning").slideDown().delay(2500).slideUp();
			}
		});
		e.preventDefault();
	});
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

function doAjaxImport(formData)
{
	jQuery.ajax(
	{
		type: "POST",
		url: "/wp-admin/admin-ajax.php",
		data: formData,
		success: function(data)
		{ 
			console.log(data);
			if (data != 'done')
			{
				var newFormData = jQuery.parseJSON(data);
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