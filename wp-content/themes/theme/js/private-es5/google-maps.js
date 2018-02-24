if (document.getElementById('mapAddress')) {
	var geocoder;
	var map;
	var tabletWidth = 480;
	var tabletLandscapeWidth = 768;
	var desktopWidth = 1024;
	var geocoder = new google.maps.Geocoder();
	var panAmount;
	var address = document.getElementById('mapAddress').innerHTML;

	function setPanAmount() {
		panAmount = 0;
		var w = window.innerWidth;
		if (w > desktopWidth) {
			panAmount = -260;
		} else if (w > tabletLandscapeWidth) {
			panAmount = -220;
		} else if (w > tabletWidth) {
			panAmount = -185;
		}
	}



	function setPin() {
		setPanAmount();
		var geocoder = new google.maps.Geocoder();
		geocodeAddress(geocoder, map);
	}
	setPanAmount();



	(function initMap() {
		var styledMapType = new google.maps.StyledMapType(
[
   {
       "featureType": "administrative",
       "elementType": "labels.text.fill",
       "stylers": [
           {
               "color": "#444444 "
           }
       ]
   },
   {
       "featureType": "landscape",
       "elementType": "all",
       "stylers": [
           {
               "hue": "#0074ff "
           }
       ]
   },
   {
       "featureType": "poi",
       "elementType": "all",
       "stylers": [
           {
               "visibility": "off"
           }
       ]
   },
   {
       "featureType": "road",
       "elementType": "all",
       "stylers": [
           {
               "saturation": -100
           },
           {
               "lightness": 45
           }
       ]
   },
   {
       "featureType": "road",
       "elementType": "labels.text",
       "stylers": [
           {
               "color": "#606060 "
           }
       ]
   },
   {
       "featureType": "road",
       "elementType": "labels.text.stroke",
       "stylers": [
           {
               "color": "#ffffff "
           }
       ]
   },
   {
       "featureType": "road.highway",
       "elementType": "all",
       "stylers": [
           {
               "visibility": "simplified"
           }
       ]
   },
   {
       "featureType": "road.highway",
       "elementType": "geometry.fill",
       "stylers": [
           {
               "color": "#dedede "
           }
       ]
   },
   {
       "featureType": "road.arterial",
       "elementType": "labels.icon",
       "stylers": [
           {
               "visibility": "off"
           }
       ]
   },
   {
       "featureType": "transit",
       "elementType": "all",
       "stylers": [
           {
               "visibility": "off"
           }
       ]
   },
   {
       "featureType": "water",
       "elementType": "all",
       "stylers": [
           {
               "color": "#dde6e8 "
           },
           {
               "visibility": "on"
           }
       ]
   }
], {name: 'styled_map'});

		map = new google.maps.Map(document.getElementById('map'), {
		    //center: {lat: -34.397, lng: 150.644},
		    zoom: 14,
		    disableDefaultUI: true,
		    scrollwheel: false,
		    draggable: false,
		    mapTypeControlOptions: {
		    	//mapTypeIds: [google.maps.MapTypeId.ROADMAP, 'Styled Map']
					mapTypeIds: ['styled_map']
		    }
		  });
		// setPanAmount();
		map.mapTypes.set('styled_map', styledMapType);
    map.setMapTypeId('styled_map');


		setPin();

		// var geocoder = new google.maps.Geocoder();
		// geocodeAddress(geocoder, map);
	})();


	function geocodeAddress(geocoder, resultsMap) {
	  geocoder.geocode({'address': address}, function(results, status) {
	    if (status === google.maps.GeocoderStatus.OK) {
	      resultsMap.setCenter(results[0].geometry.location);
	      var marker = new google.maps.Marker({
	        map: resultsMap,
	        position: results[0].geometry.location
	      });

			map.panBy(panAmount,0);
	    } else {
	      alert('Geocode was not successful for the following reason: ' + status);
	    }
	  });
	}


	var resizeId;
	window.addEventListener('resize', function() {
	        // if (w%5 == 0) {
	        // }
	        clearTimeout(resizeId);
		    resizeId = setTimeout(doneResizing, 200);
	    }, true);




	function doneResizing(){
	    setPin();
	}
}

(function($) {

    /*
     *  new_map
     *
     *  This function will render a Google Map onto the selected jQuery element
     *
     *  @type	function
     *  @date	8/11/2013
     *  @since	4.3.0
     *
     *  @param	$el (jQuery element)
     *  @return	n/a
     */

    function new_map( $el ) {

        // var
        var $markers = $el.find('.marker');


        // vars
        var args = {
            zoom		: 16,
            center		: new google.maps.LatLng(0, 0),
            mapTypeId	: google.maps.MapTypeId.ROADMAP,
        };


        // create map
        var map = new google.maps.Map( $el[0], args);


        // add a markers reference
        map.markers = [];


        // add markers
        $markers.each(function(){

            add_marker( $(this), map );

        });


        // center map
        center_map( map );


        // return
        return map;

    }

    /*
     *  add_marker
     *
     *  This function will add a marker to the selected Google Map
     *
     *  @type	function
     *  @date	8/11/2013
     *  @since	4.3.0
     *
     *  @param	$marker (jQuery element)
     *  @param	map (Google Map object)
     *  @return	n/a
     */

    function add_marker( $marker, map ) {

        // var
        var latlng = new google.maps.LatLng( $marker.attr('data-lat'), $marker.attr('data-lng') );

        // create marker
        var marker = new google.maps.Marker({
            position	: latlng,
            map			: map
        });

        // add to array
        map.markers.push( marker );

        // if marker contains HTML, add it to an infoWindow
        if( $marker.html() )
        {
            // create info window
            var infowindow = new google.maps.InfoWindow({
                content		: $marker.html()
            });

            // show info window when marker is clicked
            google.maps.event.addListener(marker, 'click', function() {

                infowindow.open( map, marker );

            });
        }

    }

    /*
     *  center_map
     *
     *  This function will center the map, showing all markers attached to this map
     *
     *  @type	function
     *  @date	8/11/2013
     *  @since	4.3.0
     *
     *  @param	map (Google Map object)
     *  @return	n/a
     */

    function center_map( map ) {

        // vars
        var bounds = new google.maps.LatLngBounds();

        // loop through all markers and create bounds
        $.each( map.markers, function( i, marker ){

            var latlng = new google.maps.LatLng( marker.position.lat(), marker.position.lng() );

            bounds.extend( latlng );

        });

        // only 1 marker?
        if( map.markers.length == 1 )
        {
            // set center of map
            map.setCenter( bounds.getCenter() );
            map.setZoom( 16 );
        }
        else
        {
            // fit to bounds
            map.fitBounds( bounds );
        }

    }

    /*
     *  document ready
     *
     *  This function will render each map when the document is ready (page has loaded)
     *
     *  @type	function
     *  @date	8/11/2013
     *  @since	5.0.0
     *
     *  @param	n/a
     *  @return	n/a
     */
// global var
    var map = null;

    $(document).ready(function(){

        $('.acf-map').each(function(){

            // create map
            map = new_map( $(this) );

        });

    });

})(jQuery);
