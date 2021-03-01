var map, geo,$context ,markers = [], poly;
function builderMapsProInit(){
	
	var $add_new = jQuery( '#markers',top_iframe ).next( 'p.add_new' ).find('a');
	geo = new google.maps.Geocoder();

	// update add_new row button label
	$add_new.text( builderMapsPro.labels.add_marker );

	builderMapsPro_make_preview();
}
function builderMapsPro_make_preview() {
	var $ = jQuery;

	map = new google.maps.Map( top_iframe.getElementById( 'map-canvas' ), {
		center: new google.maps.LatLng( -34.397, 150.644 )
	} );

	poly = new google.maps.Polyline( {} );
	poly.setMap( map );

	builderMapsPro_update_map_preview();
	builderMapsPro_setup_markers();
}

function builderMapsPro_setup_markers() {
	var $ = jQuery,
		markers = $( '#markers',top_iframe ).find( '.tb_repeatable_field' ),
	timeoutcounter = 0;
	markers.each(function(i){
		var row = $( this );
		row.data( 'marker_index', i );
		// lat/lng has already been resolved
		if( row.find( '[name="latlng"]' ).val() !== '' ) {
			markers[i] = builderMapsPro_add_new_marker( row.find( '[name="latlng"]' ).val(), row.find( '[name="title"]' ).val(), row.find( '[name="image"]' ).val(), i, row );
		} else {
			setTimeout( function(){
				markers[i] = builderMapsPro_add_new_marker( row.find( '[name="address"]' ).val(), row.find( '[name="title"]' ).val(), row.find( '[name="image"]' ).val(), i, row );
			}, timeoutcounter * 350 );
			timeoutcounter++;
		}
	});
}

function builderMapsPro_update_map_preview() {
	if(typeof geo ==='undefined'){
		return;
	}
	var $ = jQuery;
	geo.geocode( { 'address': $( '#map_center',top_iframe ).val() }, function( results, status ) {
		if (status === google.maps.GeocoderStatus.OK) {
			map.setCenter( results[0].geometry.location );
		}
	});

	var options = {
		zoom : parseInt( $( '#zoom_map',top_iframe ).val() ),
		mapTypeId : google.maps.MapTypeId[ $( '#type_map',top_iframe ).val() ],
		styles : builderMapsPro.styles[ $( '#style_map',top_iframe ).val() ],
		disableDefaultUI : $( '#disable_map_ui',top_iframe ).val() === 'yes',
		draggable : false,
		scrollwheel : false
	};
	map.setOptions( options );

	// Polyline update
	poly.setOptions( {
		geodesic: $( '#map_polyline_geodesic', top_iframe ).val() === 'yes',
		strokeColor: $( '#map_polyline_color', top_iframe ).val(),
		strokeOpacity: $( '#map_polyline_color', top_iframe ).parent().next( '.color_opacity' ).val() || 1,
		strokeWeight: $( '#map_polyline_stroke', top_iframe ).val(),
		visible: $( '#map_polyline', top_iframe ).val() === 'yes'
	} );
}

function builderMapsPro_resolve_address( address, callback ) {
	if( address === null || address.trim() === '' ) {
		return false;
	}

	/* matches a valid lat/long value */
	var position = address.match( /^([-+]?[1-8]?\d(\.\d+)?|90(\.0+)?),?\s*([-+]?180(\.0+)?|[-+]?((1[0-7]\d)|([1-9]?\d))(\.\d+)?)(,\d+z)?$/ );
	if( jQuery.isArray( position ) ) {
		callback( new google.maps.LatLng( position[1], position[4] ) );
	} else {
		geo.geocode( { 'address': address }, function( results, status ) {
			if (status === google.maps.GeocoderStatus.OK) {
				callback( results[0].geometry.location );
			}
			return null;
		});
	}
}

function builderMapsPro_add_new_marker( address, title, image, index, row ) {
	if( address === null || address.trim() === '' ) {
		return null;
	}

	builderMapsPro_resolve_address( address, function(position){
		poly && poly.getPath().push( position );

		markers[index] = new google.maps.Marker({
			map : map,
			position: position,
			icon : image
		});

		if( title.trim() !== '' ) {
			var infowindow = new google.maps.InfoWindow({
				content: '<div class="maps-pro-content">' + title + '</div>'
			});
			google.maps.event.addListener( markers[index], 'click', function() {
				infowindow.open( map, markers[index] );
			});
		}

		row.find( '[name="latlng"]' ).val( position.lat() + ',' + position.lng() );
	} );
}

function builderMapsPro_remove_marker( index ) {
	if( markers[index] !== undefined ) {
		markers[index].setMap( null );
		markers[index] = null;
	}
}

(function( w ) {
		
	jQuery(function($){
		$( 'body' ).on( 'editing_module_option', function(e,type,settings,context){
                    if(type==='maps-pro'){
                        setTimeout(function(){
                            if (typeof google !== 'object') {
                                Themify.LoadAsync('//maps.google.com/maps/api/js?v=3.exp&callback=builderMapsProInit&key='+builderMapsPro.key, false,false, false, function(){
                                    return typeof google === 'object' && typeof google.maps === 'object';
                                });
                            } else {
                                builderMapsProInit();
                            }
                        }
                        ,1000);
                    }
		} );
	$( 'body',top_iframe )
		.on( 'change', '#map_center, #zoom_map, #type_map, #style_map, #disable_map_ui, #map_polyline, #map_polyline_geodesic, #map_polyline_stroke, #map_polyline_color', function(e){
			(! e.isTrigger || $( '#map_polyline_color', top_iframe ).is( e.target ) )
				&& builderMapsPro_update_map_preview();
		} )
		.on( 'change', '#markers .tb_lb_option_child', update_markers )
		.on( 'click', '#markers .themify_builder_delete_row', delete_marker_action );
			
		function update_markers() {
			var row = $( this ).closest( '.tb_repeatable_field' ),
				index = ( row.data( 'marker_index' ) === undefined ) ? markers.length : row.data( 'marker_index' );

			// make sure it's removed first
			builderMapsPro_remove_marker( index );

			markers[index] = builderMapsPro_add_new_marker( row.find( '[name="address"]' ).val(), row.find( '[name="title"]' ).val(), row.find( '[name="image"]' ).val(), index, row );
		}

		function delete_marker_action() {
			var index = $( this ).closest( '.tb_repeatable_field' ).data( 'marker_index' );
			builderMapsPro_remove_marker( index );
		}
	});
}( window ));