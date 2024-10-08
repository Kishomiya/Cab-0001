var myLatLng = { lat: 7.8731, lng: 80.7718 };
var mapOptions = {
    center: myLatLng,
    zoom: 7,  // Set a zoom level appropriate for Sri Lanka
    mapTypeId: google.maps.MapTypeId.ROADMAP
};

// Hide result box
document.getElementById("output").style.display = "none";

// Create/Init map
var map = new google.maps.Map(document.getElementById('google-map'), mapOptions);

// Create a DirectionsService object to use the route method and get a result for our request
var directionsService = new google.maps.DirectionsService();

// Create a DirectionsRenderer object which we will use to display the route
var directionsDisplay = new google.maps.DirectionsRenderer();

// Bind the DirectionsRenderer to the map
directionsDisplay.setMap(map);

// Define calcRoute function
function calcRoute() {
    // Create request
    var request = {
        origin: document.getElementById("location-1").value,
        destination: document.getElementById("location-2").value,
        travelMode: google.maps.TravelMode.DRIVING,
        unitSystem: google.maps.UnitSystem.METRIC
    }

    // Routing
    directionsService.route(request, function (result, status) {
        if (status == google.maps.DirectionsStatus.OK) {

            // Get distance and time            
            $("#output").html("<div class='result-table'> Driving distance: " + result.routes[0].legs[0].distance.text + ".<br />Duration: " + result.routes[0].legs[0].duration.text + ".</div>");
            document.getElementById("output").style.display = "block";

            // Display route
            directionsDisplay.setDirections(result);
        } else {
            // Delete route from map
            directionsDisplay.setDirections({ routes: [] });
            // Center map on Sri Lanka
            map.setCenter(myLatLng);

            // Show error message           
            alert("Can't find road! Please try again!");
            clearRoute();
        }
    });
}

// Clear results
function clearRoute(){
    document.getElementById("output").style.display = "none";
    document.getElementById("location-1").value = "";
    document.getElementById("location-2").value = "";
    directionsDisplay.setDirections({ routes: [] });
}

// Create autocomplete objects for all inputs
var options = {
    types: ['(cities)'],
    componentRestrictions: { country: "LK" }  // Restrict search to Sri Lanka
}

var input1 = document.getElementById("location-1");
var autocomplete1 = new google.maps.places.Autocomplete(input1, options);

var input2 = document.getElementById("location-2");
var autocomplete2 = new google.maps.places.Autocomplete(input2, options);
