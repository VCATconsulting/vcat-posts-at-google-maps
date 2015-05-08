;(function($){
			// helper
			
			// init
			$(function(){
				$('#mini-zoom-slider').slider({
					min: 1,
					max: 23,
					value: $('#mini_zoom').attr('value'),
					change:function( event, ui ){
						$('#mini_zoom').attr('value', ui.value);
					} 
				});
				$('#zoom-slider').slider({
					min: 1,
					max: 23,
					value: $('#zoom').attr('value'),
					change:function( event, ui ){
						$('#zoom').attr('value', ui.value);
					} 
				});
			});
		})(jQuery);

/* dadz 21042015 current Geo-Location*/
/**
 * locate your current position
 * @param position = Object about all information (current location)
 */
function checkGeoApiSuccess(position) {
    var elemLat = document.getElementById("latitude");
    var elemLong = document.getElementById("longitude");
    var elemStr = document.getElementById("str");
    var elemPlz = document.getElementById("plz");
    var elemOrt = document.getElementById("ort");
    var lat_ = position.coords.latitude;
    var long_ = position.coords.longitude;
    var pos = new google.maps.LatLng(parseFloat(position.coords.latitude), parseFloat(position.coords.longitude));
    var geocoder = new google.maps.Geocoder();

    geocoder.geocode({'latLng': pos}, function (results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            if (results[0]) {
                elemLat.value = lat_;
                elemLong.value = long_;
                elemStr.value = results[0].address_components[1].long_name + ' ' + results[0].address_components[0].long_name;
                elemOrt.value = results[0].address_components[3].long_name;
                elemPlz.value = results[0].address_components[6].long_name;
                elemLat.name = 'location[lat]';
                elemLong.name = 'location[lng]';
            } else {
                alert('No results found!');
            }
        } else {
            alert('GeoCoder failed due to: ' + status);
        }
    });
}

function checkGeoApiError(msg) {
	alert(typeof msg == 'string' ? msg : 'Ihre Ortung ist deaktiviert!');
}


function checkGeoApi(){
	if(navigator.geolocation) {
		navigator.geolocation.getCurrentPosition(checkGeoApiSuccess, checkGeoApiError);
	} else {
		alert("GeoLocation ist momentan nicht verfügbar");
	}
}		