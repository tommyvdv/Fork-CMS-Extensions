/**
 * Interaction for the location_openstreetmap module
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Jelmer Snoeck <jelmer.snoeck@netlash.com>
 */
jsBackend.location_openstreetmap =
{
	// base values
	bounds: null, center: null, centerLat: null, centerLng: null, height: null,  
	map: null, mapId: null, showDirections: false, showLink: false, showOverview: true,
	type: null, width: null, zoomLevel: null,

	init: function()
	{
		// only show a map when there are options and markers given
		if(typeof markers != 'undefined' && typeof mapOptions != 'undefined') 
		{
			jsBackend.location_openstreetmap.showMap();

			// add listeners for the zoom level and terrain
			google.maps.event.addListener(jsBackend.location_openstreetmap.map, 'maptypeid_changed', jsBackend.location_openstreetmap.setDropdownTerrain);
			google.maps.event.addListener(jsBackend.location_openstreetmap.map, 'zoom_changed', jsBackend.location_openstreetmap.setDropdownZoom);

			// if the zoom level or map type changes in the dropdown, the map needs to change
			$('#zoomLevel').bind('change', function() { jsBackend.location_openstreetmap.setMapZoom($('#zoomLevel').val()); });
			$('#mapType').bind('change', jsBackend.location_openstreetmap.setMapTerrain);

			// the panning save option
			$('#saveLiveData').bind('click', function(e)
			{
				e.preventDefault();

				// save the live map data
				jsBackend.location_openstreetmap.getMapData();
				jsBackend.location_openstreetmap.saveLiveData();
			});
		}
	},

	addMarker: function(map, bounds, object)
	{
		position = new google.maps.LatLng(object.lat, object.lng);
		bounds.extend(position);

		// add marker
		var marker = new google.maps.Marker(
		{
			position: position,
			map: map,
			title: object.title
		});
		
		if(typeof object.dragable != 'undefined' && object.dragable)
		{
			marker.setDraggable(true);
			
			// add event listener
			google.maps.event.addListener(marker, 'dragend', function()
			{
				jsBackend.location_openstreetmap.updateMarker(marker);
			});
		}
		
		// add click event on marker
		google.maps.event.addListener(marker, 'click', function()
		{
			// create infowindow
			new google.maps.InfoWindow(
			{ 
				content: '<h1>'+ object.title +'</h1>' + object.text 
			}).open(map, marker);
		});
	},

	getMapData: function()
	{
		// get the live data
		jsBackend.location_openstreetmap.zoomLevel = jsBackend.location_openstreetmap.map.getZoom();
		jsBackend.location_openstreetmap.type = jsBackend.location_openstreetmap.map.getMapTypeId();
		jsBackend.location_openstreetmap.center = jsBackend.location_openstreetmap.map.getCenter();
		jsBackend.location_openstreetmap.centerLat = jsBackend.location_openstreetmap.center.lat();
		jsBackend.location_openstreetmap.centerLng = jsBackend.location_openstreetmap.center.lng();

		// get the form data
		jsBackend.location_openstreetmap.mapId = parseInt($('#mapId').val());
		jsBackend.location_openstreetmap.height = parseInt($('#height').val());
		jsBackend.location_openstreetmap.width = parseInt($('#width').val());

		jsBackend.location_openstreetmap.showDirections = ($('#directions').attr('checked') == 'checked');
		jsBackend.location_openstreetmap.showLink = ($('#fullUrl').attr('checked') == 'checked');
		jsBackend.location_openstreetmap.showOverview = ($('#markerOverview').attr('checked') == 'checked');
	},
	
	// this will refresh the page and display a certain message
	refreshPage: function(message)
	{
		var currLocationOpenstreetmap = window.location_openstreetmap;
		var reloadLocationOpenstreetmap = (currLocationOpenstreetmap.search.indexOf('?') >= 0) ? '&' : '?';
		reloadLocationOpenstreetmap = currLocationOpenstreetmap + reloadLocationOpenstreetmap + 'report=' + message;

		// cleanly redirect so we can display a message
		window.location_openstreetmap = reloadLocationOpenstreetmap;
	},
	
	saveLiveData: function()
	{
		$.ajax(
		{
			data:
			{
				fork: { module: 'location_openstreetmap', action: 'save_live_location_openstreetmap' },
				zoom: jsBackend.location_openstreetmap.zoomLevel,
				type: jsBackend.location_openstreetmap.type,
				centerLat: jsBackend.location_openstreetmap.centerLat,
				centerLng: jsBackend.location_openstreetmap.centerLng,
				height: jsBackend.location_openstreetmap.height,
				width: jsBackend.location_openstreetmap.width,
				id: jsBackend.location_openstreetmap.mapId,
				link: jsBackend.location_openstreetmap.showLink,
				directions: jsBackend.location_openstreetmap.showDirections,
				showOverview: jsBackend.location_openstreetmap.showOverview
			},
			success: function(json, textStatus)
			{
				// reload the page on success
				if(json.code == 200)
				{
					// no redirect given, refresh the page
					if(typeof $('input#redirect').val() == 'undefined')
					{
						jsBackend.location_openstreetmap.refreshPage('map-saved');
					}
					
					$('input#redirect').val('edit');
					$('form#edit').submit();
				}
			}
		});
	},
	
	// this will set the terrain type of the map to the dropdown
	setDropdownTerrain: function()
	{
		jsBackend.location_openstreetmap.getMapData();
		$('#mapType').val(jsBackend.location_openstreetmap.type.toUpperCase());
	},

	// this will set the zoom level of the map to the dropdown
	setDropdownZoom: function()
	{
		jsBackend.location_openstreetmap.getMapData();
		$('#zoomLevel').val(jsBackend.location_openstreetmap.zoomLevel);
	},

	// this will set the terrain type of the map to the dropdown
	setMapTerrain: function()
	{
		jsBackend.location_openstreetmap.type = $('#mapType').val();
		jsBackend.location_openstreetmap.map.setMapTypeId(jsBackend.location_openstreetmap.type.toLowerCase());
	},

	// this will set the zoom level of the map to the dropdown
	setMapZoom: function(zoomlevel)
	{
		jsBackend.location_openstreetmap.zoomLevel = zoomlevel;

		// set zoom automatically, defined by points (if allowed)
		if(zoomlevel == 'auto') jsBackend.location_openstreetmap.map.fitBounds(jsBackend.location_openstreetmap.bounds);
		else jsBackend.location_openstreetmap.map.setZoom(parseInt(zoomlevel));
	},

	showMap: function()
	{
		// create boundaries
		jsBackend.location_openstreetmap.bounds = new google.maps.LatLngBounds();

		// set options
		var options =
		{
			center: new google.maps.LatLng(mapOptions.center.lat, mapOptions.center.lng),
			mapTypeId: eval('google.maps.MapTypeId.' + mapOptions.type)
		};

		// create map
		jsBackend.location_openstreetmap.map = new google.maps.Map(document.getElementById('map'), options);

		// loop the markers
		for(var i in markers)
		{
			jsBackend.location_openstreetmap.addMarker(
				jsBackend.location_openstreetmap.map, jsBackend.location_openstreetmap.bounds, markers[i]
			);
		}

		jsBackend.location_openstreetmap.setMapZoom(mapOptions.zoom);
	},
	
	// this will re-set the position of a marker
	updateMarker: function(marker)
	{
		jsBackend.location_openstreetmap.getMapData();
		
		var lat = marker.getPosition().lat();
		var lng = marker.getPosition().lng();
		
		$.ajax(
		{
			data:
			{
				fork: { module: 'location_openstreetmap', action: 'update_marker' },
				id: jsBackend.location_openstreetmap.mapId,
				lat: lat,
				lng: lng
			},
			success: function(json, textStatus)
			{
				// reload the page on success
				if(json.code == 200) jsBackend.location_openstreetmap.saveLiveData();
			}
		});
	}
}

$(jsBackend.location_openstreetmap.init);