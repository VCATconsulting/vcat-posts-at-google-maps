var geocoder;
var map;

/**
 * initializes the Google Map and makes is visible in the frontend
 * 
 * @param lat	latitude value for the map center
 * @param lng	longitude value for the map center
 * @param zm	zoom value for the map
 * @param pid	postid for the minimaps
 */
function vcatInitialize(lat, lng, zm, pid) {	
	geocoder = new google.maps.Geocoder();
	var latlng = new google.maps.LatLng(lat,lng);
	var mapOptions = {
		zoom: zm,
		center: latlng,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	}
		
	var name = "map_canvas" + pid;	
	
	map = new google.maps.Map(document.getElementById(name), mapOptions);
}


/**
 * sets the marker for each called post on the google map
 *
 * @param lat		latitude value for the marker
 * @param lng		longitude value for the marker
 * @param addres	address value for the marker
 * @param title		title of the given post
 * @param link		link of the given post
 * @param image		image value for the marker
 * @param $target	specifies where the infobox will appear later
 */
function vcatAddMarker( lat, lng, address, title, link, image, target ) {
		
    var marker = new google.maps.Marker({
        map: map,
        position: new google.maps.LatLng(lat, lng),
        icon: new google.maps.MarkerImage( image ),
        title: title
    });

	var infowindow = new google.maps.InfoWindow({ 
		content: '<a href="' + link + '" target="_' + target + '">' + title + '</a><br /><small><font color="#000000">' + address + '</font></small>'', 
	});
	google.maps.event.addListener(marker, 'click', (function (m) {
	    return function() {
	        infowindow.open(m,marker);
	    };
	}(map)));
	
	
	
}