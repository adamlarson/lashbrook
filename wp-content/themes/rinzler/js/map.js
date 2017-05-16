var startLocation = {lat: 40.5145148, lng: -111.8866591};
var startLocationUpdated = false;
var map = null;
var popup = document.getElementById("store-popup");
var popupTail = document.querySelector("img.tail");
var overlayView = null;
var addressSearch = document.getElementById("address-search");
var addressSearchMag = document.getElementById("address-search-mag");
var addressSearchButton = document.getElementById("address-search-button");
var popupWidth = 450;
var mapEl = document.getElementById("storelocations");
var nearBtn = document.getElementById("near-me-btn");
var markers = [];

function initMap(){
	map = new google.maps.Map(mapEl, {
		center: startLocation,
		scrollwheel: false,
		zoom: 17
	});
	map.panBy(0, 200);
	var mkDataReq = new XMLHttpRequest();
	mkDataReq.addEventListener("load", gotMarkers);
	mkDataReq.open("GET", "/wp-content/plugins/lashbrook-stores/api.php");
	mkDataReq.send();
	
	map.addListener("drag", closePopup);
	map.addListener("zoom_changed", closePopup);
}

function gotMarkers(data){
	var response = JSON.parse(data.target.response);

	
	if(response.success){
		buildMarkers(response.stores);
	}
}

function gotCoords(data){
	if(data.coords){
		startLocation.lat = data.coords.latitude;
		startLocation.lng = data.coords.longitude;
		if(map){
			map.panTo(startLocation);
		}
	}
}

function noCoords(error){
	console.log("noCoords");
	if(nearBtn){
		nearBtn.setAttribute("style","display:none;");
		nearOr = document.getElementById("near-me-or");
		if(nearOr){
			nearOr.setAttribute("style","display:none;");
		}
	}
}

function buildMarkers(markerData){
	for(var i = 0; i < markerData.length;++i){
		markerData[i].position.lat = Number(markerData[i].position.lat);
		markerData[i].position.lng = Number(markerData[i].position.lng);
		var mkr = new google.maps.Marker({
			position: markerData[i].position,
			map:map,
			icon: "/wp-content/themes/rinzler/images/map-icon.png",
			animation: google.maps.Animation.DROP,
			title: markerData[i].name,
			storeData: markerData[i]
		});

		markers.push(mkr);
		/*google.maps.event.addListener(mkr,'click',function(){
			activateMarker(mkr);
		});*/
		mkr.addListener("click", activateMarker);
	}
	//centerMapResults(markers);
}

function pixelOffset(marker){
	var scale = Math.pow(2, map.getZoom());
	var nw = new google.maps.LatLng(
	    map.getBounds().getNorthEast().lat(),
	    map.getBounds().getSouthWest().lng()
	);
	var worldCoordinateNW = map.getProjection().fromLatLngToPoint(nw);
	var worldCoordinate = map.getProjection().fromLatLngToPoint(marker.getPosition());
	var pixelOffset = new google.maps.Point(
	    Math.floor((worldCoordinate.x - worldCoordinateNW.x) * scale),
	    Math.floor((worldCoordinate.y - worldCoordinateNW.y) * scale)
	);
	return pixelOffset;
}

function activateMarker(data){
	// hide 
	popup.classList.remove("on");
	popup.classList.add("hidden");
	popup.classList.add("off");
	popupTail.classList.remove("on");
	popupTail.classList.add("off");

	map.panTo(offsetCenter(this.storeData.position, 0, -200));
	popup.querySelector(".store-name").innerHTML = this.storeData.name;
	popup.querySelector(".store-address").innerHTML = this.storeData.address;
	popup.querySelector(".store-phone").innerHTML = this.storeData.phone;
	popup.querySelector(".store-hours").innerHTML = "Hours: " + this.storeData.hours;
	var storelink = popup.querySelector(".store-web a");
	storelink.href = storelink.innerHTML = this.storeData.url;
	popup.querySelector(".store-logo").setAttribute('style','background-image:url(' + this.storeData.logo + ')');

	//popup.querySelector(".store-logo").src = this.storeData.logo;
	var storeCollectionsList = popup.querySelector(".store-collections-list");
	storeCollectionsList.innerHTML = "";
	for(var i = 0; i < this.storeData.materials.length;++i){
		var collectionItem = document.createElement("p");
		collectionItem.innerHTML = this.storeData.materials[i];
		storeCollectionsList.appendChild(collectionItem);
	}

	popup.classList.remove("off");
	popup.classList.remove("hidden");
	popup.classList.add("on");
	
	popup.style.left = ((mapEl.offsetWidth/2) - (popupWidth/2)) + "px";
	popupTail.style.left = ((mapEl.offsetWidth/2)) + "px";

	var marker = this;

	setTimeout(function(){
		var pixels = pixelOffset(marker);
		var markerPlusTail = 97;
		var popupTop = (pixels.y - (popup.clientHeight + markerPlusTail)) + "px";
		popup.style.top = popupTop;
		popupTail.classList.remove("off");
		popupTail.classList.add("on");
	},500);
}

function closePopup(){
	popup.classList.remove("on");
	popupTail.classList.remove("on");
	popup.classList.add("off");
	popupTail.classList.add("off");
	
	popup.style.left = ((mapEl.offsetWidth/2)) + "px";
	popupTail.style.left = ((mapEl.offsetWidth/2) - 38) + "px";
}

function addressSearchKeyUp(ev){

	if(ev.keyCode == 13){
		startSearch();
	}
}

var rad = function(x) {
	return x * Math.PI / 180;
};

var getDistance = function(p1, p2) {
	var radius = 6378137;
	var dLat = rad(p2.lat() - p1.lat());
	var dLong = rad(p2.lng() - p1.lng());
	var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
		Math.cos(rad(p1.lat())) * Math.cos(rad(p2.lat())) *
		Math.sin(dLong / 2) * Math.sin(dLong / 2);
	var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
	var d = radius * c;
	return (d / 1000);
}

function startSearch(){
	geocoder = new google.maps.Geocoder();
	geocoder.geocode( { 'address': addressSearch.value}, function(results, status) {
		if(status == "OK"){
			//map.setZoom(10);
			//console.log("panning");
			//console.log(results[0].geometry.location);
			//map.panTo(results[0].geometry.location);
			var count1k   = 0;
			var count500  = 0;
			var count100  = 0;
			var count50   = 0;
			var count25   = 0;

			for( index=0; index < markers.length; index++ ) {
				var distance = getDistance(results[0].geometry.location, markers[index].position);

	
				if(distance < 1000) {
					count1k++;
					if(distance < 500) {
						count500++;
						if(distance < 100) {
							count100++;
							if(distance < 50) {
								count50++;
								if(distance < 25) {
									count25++;
								}
							}
						}
					}
				}
			}

			map.setCenter(results[0].geometry.location);
			// console.log('1k ' + count1k);
			// console.log('500 ' + count500);
			// console.log('100 ' + count100);
			// console.log('50 ' + count50);
			// console.log('25 ' + count25);
			if( count25 >= 2 ) {
				map.setZoom(12);
			} else if( count50 >= 2 ) {
				map.setZoom(10);
			} else if( count100 >= 2 ) {
				map.setZoom(9);
			} else if( count500 >= 2 ) {
				map.setZoom(8);
			} else if( count1k >= 2 ) {
				map.setZoom(7);
			} else {
				map.setZoom(5);
			}

			closePopup();
		} else {
			alert("Unable to locate: " + status);
		}
	});
}


function fromLatLngToPoint(latLng, map) {
	var topRight = map.getProjection().fromLatLngToPoint(map.getBounds().getNorthEast());
	var bottomLeft = map.getProjection().fromLatLngToPoint(map.getBounds().getSouthWest());
	var scale = Math.pow(2, map.getZoom());
	var worldPoint = map.getProjection().fromLatLngToPoint(latLng);
	return new google.maps.Point((worldPoint.x - bottomLeft.x) * scale, (worldPoint.y - topRight.y) * scale);
}


function offsetCenter(latlng,offsetx,offsety) {

	// latlng is the apparent centre-point
	// offsetx is the distance you want that point to move to the right, in pixels
	// offsety is the distance you want that point to move upwards, in pixels
	// offset can be negative
	// offsetx and offsety are both optional

	var scale = Math.pow(2, map.getZoom());
	var latlngPt = new google.maps.Data.Point(latlng);
	var projection = map.getProjection();
	var worldCoordinateCenter = projection.fromLatLngToPoint(latlngPt.b);
	var pixelOffset = new google.maps.Point((offsetx/scale) || 0,(offsety/scale) ||0)
	var worldCoordinateNewCenter = new google.maps.Point(
		worldCoordinateCenter.x - pixelOffset.x,
		worldCoordinateCenter.y + pixelOffset.y
	);
	var newCenter = map.getProjection().fromPointToLatLng(worldCoordinateNewCenter);
	return newCenter;

}

function getPixelPosition (marker) {
    var scale = Math.pow(2, map.getZoom());
    var nw = new google.maps.LatLng(
        map.getBounds().getNorthEast().lat(),
        map.getBounds().getSouthWest().lng()
    );
    var worldCoordinateNW = map.getProjection().fromLatLngToPoint(nw);
    var worldCoordinate = map.getProjection().fromLatLngToPoint(marker.getPosition());
    var pixelOffset = new google.maps.Point(
        Math.floor((worldCoordinate.x - worldCoordinateNW.x) * scale),
        Math.floor((worldCoordinate.y - worldCoordinateNW.y) * scale)
    );

    return pixelOffset;
}

function centerMapResults(locations){
	console.log(locations);
	//create empty LatLngBounds object
	var bounds = new google.maps.LatLngBounds();
	var infowindow = new google.maps.InfoWindow();    

	for (i = 0; i < locations.length; i++) {  
		var marker = locations[i];
	  /*var marker = new google.maps.Marker({
	    position: new google.maps.LatLng(locations[i][1], locations[i][2]),
	    map: map
	  });*/

	  //extend the bounds to include each marker's position
	  bounds.extend(marker.position);
	}

	//now fit the map to the newly inclusive bounds
	map.fitBounds(bounds);

}


if(navigator.geolocation){
	navigator.geolocation.getCurrentPosition(gotCoords,noCoords);
}

if(nearBtn){
	nearBtn.addEventListener("click", function(){
		if(map){
			map.panTo(startLocation);
		}
	});
}

if(addressSearchMag){
	addressSearchMag.addEventListener("click",function(){
		startSearch();
	});
}

popup.getElementsByClassName("fa-close")[0].addEventListener("click", closePopup);
addressSearch.addEventListener("keyup", addressSearchKeyUp);
if(addressSearchButton != null){
	addressSearchButton.addEventListener("click",startSearch)
}
