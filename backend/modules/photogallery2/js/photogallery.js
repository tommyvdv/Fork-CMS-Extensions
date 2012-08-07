if(!jsBackend) { var jsBackend = new Object(); }

jsBackend.photogallery =
{
	init: function()
	{
		// do meta
		if($('#title').length > 0) $('#title').doMeta();
		
		if($('select.categoriesBox').length > 0) 
		{ 
			$('select.categoriesBox').multipleSelectbox({ 
				emptyMessage: '{$msgNoCategoriesSelected}', 
				addLabel: '{$lblAdd|ucfirst}', 
				removeLabel: '{$lblDelete|ucfirst}' 
			}); 
		}
		
		if($('#new').length > 0) 
		{
			$('#new').change(function() 
			{
				if($(this).is(':checked')) $('.toggleNew input').removeAttr('disabled').removeClass('disabled');
				else $('.toggleNew input').attr('disabled', 'disabled').addClass('disabled');
			});
			
			$('#new').change();
		}
	},

	// end
	eoo: true
}


$(document).ready(function() { jsBackend.photogallery.init(); });