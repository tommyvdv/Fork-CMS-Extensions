if(!jsBackend) { var jsBackend = new Object(); }

jsBackend.amazon_s3 =
{
	init: function()
	{
		jsBackend.amazon_s3.linkAccount.init();
	},

	// end
	eoo: true
}

jsBackend.amazon_s3.linkAccount =
{
	init: function()
	{
		// cache objects
		var confirm = $('#linkAccount');
		var awsAccessKey = $('#awsAccessKey');
		var awsSecretKey = $('#awsSecretKey');

		// prevent submit on keyup
		$('#accountBox input').keypress(function(e)
		{
			if(e.keyCode == 13)
			{
				// prevent the default action
				e.preventDefault();

				// if all fields are set
				if(url.val() != '' && awsAccessKey.val() != '' && awsSecretKey.val() != '')
				{
					// do the call to link the account
					jsBackend.amazon_s3.linkAccount.doCall();
				}
			}
		});

		// link account button clicked
		confirm.live('click', function(e)
		{
			// prevent default
			e.preventDefault();

			// do the call to link the account
			jsBackend.amazon_s3.linkAccount.doCall();
		});
		
		jsBackend.amazon_s3.linkAccount.disablefields();
		
		
		// create client is checked
		$('#buckets').change(function(e)
		{
			var bucket = $(this).val();

			jsBackend.amazon_s3.linkAccount.disablefields();
			
			// '0' is the 'create new client' option, so we have to reset the input
			if(bucket == '0')
			{
				$('#regions').val('');
				$('#bucket').val('');
			}

			// an existing client was chosen, so we have to update the info fields with the current details of the client
			else
			{
				$.ajax(
				{
					data:
					{
						fork: { action: 'load_bucket_info' },
						bucket: bucket
					},
					success: function(data, textStatus)
					{
						$.each($('#regions').find('option'), function(index, item)
						{
							if($(this).val() == data.data.region)
							{
								$(this).prop('selected', true);
								
							}
						});
						
						
						$('#bucket').val(data.data.bucket);
					}
				});
			}
		});
	},
	
	disablefields: function()
	{
		if($('#buckets').val() == '0')
		{
			$('#regions').removeAttr("disabled");
			$('#bucket').removeAttr("disabled");
		}
		else
		{
			$('#regions').attr("disabled", true);
			$('#bucket').attr("disabled", true); 
		}
	},
	

	doCall: function()
	{
		var awsAccessKey = $('#awsAccessKey');
		var awsSecretKey = $('#awsSecretKey');

		// make the call
		$.ajax(
		{
			data:
			{
				fork: { action: 'link_account' },
				awsAccessKey: awsAccessKey.val(),
				awsSecretKey: awsSecretKey.val()
			},
			success: function(data, textStatus)
			{
				
				// remove all previous errors
				$('.formError').remove();

				// success!
				if(data.code == 200)
				{
					// client_id field is set
					window.location = window.location.pathname + '?token=true&report=' + data.data.message + '#tabSettingsBucket';
				}
				else
				{
					// field was set
					if(data.data.field)
					{
						// add error to the field respective field
						$('#'+ data.data.field).after('<span class="formError">'+ data.message +'</span>');
					}
				}
			}
		});
	},

	// end
	eoo: true
};



$(document).ready(function() { jsBackend.amazon_s3.init(); });