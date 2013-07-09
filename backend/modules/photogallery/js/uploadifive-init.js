if(!jsBackend) { var jsBackend = new Object(); }

jsBackend.uploadifive =
{
	init: function()
	{		
		$('#images').uploadifive({
				'auto'             : false,
				'formData'         : {
										'simUploadLimit' : 3,
									   'timestamp' : uploadTimestamp,
									   'token'     : uploadToken,
									   'album_id': uploadAlbumId,
									   'fork[module]'     : 'photogallery',
									   'fork[action]'	: 'upload_image',
									   'fork[language]'	: jsBackend.current.language
				                     },
				'queueID'          : 'queue',
				'uploadScript'     : '/backend/ajax.php',
				'removeCompleted' : true,
				'fileType'     : 'image',

				'onQueueComplete' : function(file, data)
				{ 
					window.location = uploadSuccessURL
				},
				'onFallback'   : function() {
					window.location = uploadFallbackURL
       			 }
			});


		$('.uploadifiveButton').click(function(e){
			e.preventDefault();
			$('#images').uploadifive('upload')
		})
	},

	// end
	eoo: true
}


$(document).ready(function() { jsBackend.uploadifive.init(); });