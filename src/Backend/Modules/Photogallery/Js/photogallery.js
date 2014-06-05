if(!jsBackend) { var jsBackend = new Object(); }

jsBackend.photogallery =
{
	init: function()
	{
		// init others
		jsBackend.photogallery.resolutions();
		jsBackend.photogallery.settings();

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

	settings: function()
	{
		$('.js-checkbox-dependant').each(function(i,el){
			target = $('input[name='+$(el).data('dependant-on')+']');
			jsBackend.photogallery.check_dependance(target);
			$('input[name='+$(el).data('dependant-on')+']').change(function(e){
				jsBackend.photogallery.check_dependance($(e.target));
			});
		});
	},

	check_dependance: function(target)
	{
		if(target.is(':checked'))
		{
			$('*[data-dependant-on='+target.attr('name')+']').slideDown();
		} else {
			$('*[data-dependant-on='+target.attr('name')+']').slideUp();
		}
	},

	resolutions: function()
	{
		if(typeof jsData.photogallery === 'undefined' ||
			typeof jsData.photogallery.allow_edit === 'undefined')
		{
			// data not available, don't bother with the inits
		} else {
			// disabled if edit not allowed
			edit_allowed = jsBackend.photogallery.resolutions_editallowed_check();

			if(edit_allowed)
			{
				// resolutions checking
				jsBackend.photogallery.resolutions_null_check(null);
				$('#widthNull, #heightNull').change(function(e){
					jsBackend.photogallery.resolutions_null_check(e)
				});

				// watermark checking
				jsBackend.photogallery.resolutions_watermark_check();
				$('#allowWatermark').change(function(e){
					jsBackend.photogallery.resolutions_watermark_check(e);
				});
			}
		}
	},

	resolutions_editallowed_check: function()
	{
		if(!jsData.photogallery.allow_edit)
			$('input,select').attr('disabled', 'disabled');

		return jsData.photogallery.allow_edit ? true : false;
	},

	resolutions_null_check: function(e)
	{
		$target = false;
		if(e) $target = $(e.target);
		if($('#widthNull, #heightNull').is(':checked'))
		{
			$('.js-warning-method').slideDown();
			$('#method').val('resize');
			$('#method').attr('disabled','disabled');
			if($target)
			{
				$toggleTarget = $target.closest('.options').find('input[type="checkbox"]').not('#allowWatermark').not("#"+$target.attr('id'))
				if($target.is(':checked')) $toggleTarget.removeAttr('checked','checked');
			}
		}
		else
		{
			$('.js-warning-method').slideUp();
			$('#method').removeAttr('disabled');
		}
	},

	resolutions_watermark_check: function(e)
	{
		// show warning if global is disabled
		if($('#allowWatermark').is(':checked') && !jsData.photogallery.allow_watermark)
		{
			$('.js-warning-watermark').slideDown();
		}
		else
		{
			$('.js-warning-watermark').slideUp();
		}

		// hide/show options
		if($('#allowWatermark').is(':checked'))
		{
			$('.js-dependant-allowwatermark').slideDown();
		}
		else
		{
			$('.js-dependant-allowwatermark').slideUp();
		}
	},

	// end
	eoo: true
}


$(document).ready(function() { jsBackend.photogallery.init(); });