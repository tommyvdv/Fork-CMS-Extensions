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
				emptyMessage: jsBackend.locale.msg('NoCategoriesSelected'), 
				addLabel: utils.string.ucfirst(jsBackend.locale.lbl('AddCategory')), 
				removeLabel: utils.string.ucfirst(jsBackend.locale.lbl('Delete'))
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