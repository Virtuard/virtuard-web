document.addEventListener("DOMContentLoaded", function (event) {
    //
});

$(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $("meta[name='csrf-token']").attr("content"),
        },
    });
});

/* ------------------------------Copy Clipboard-------------------------------- */
function copyToClipboard(str) {
    let text = document.getElementById(str).innerText;
    
    actionCopyToClipBoard(text)
    
    $("#share-copy-btn")
        .tooltip("hide")
        .attr("data-original-title", "Copied")
        .tooltip("show");
}

function outCopyFunc() {
    $("#share-copy-btn")
        .tooltip("hide")
        .attr("data-original-title", "Copy to clipboard");
}

function actionCopyToClipBoard(text) {
    if (navigator.clipboard && window.isSecureContext) {
        // use Clipboard API if exist
        navigator.clipboard
            .writeText(text)
            .then(function () {
                // console.error("Text Copied: ", text);
            })
            .catch(function (err) {
                // console.error("Error copy text: ", err);
            });
    } else {
        let tempInput = $("<input>");
        tempInput.attr("type", "text");
        $("body").append(tempInput);
        tempInput.val(text).select();
        document.execCommand("copy");
        tempInput.remove();
    }
}
/* ------------------------------Copy Clipboard-------------------------------- */

function showModalLogin() {
    $('#login').modal('show');
}

async function getLocationOsm() {
    if ("geolocation" in navigator) {
        try {
            // Geolocation is available
            const position = await new Promise((resolve, reject) => {
                navigator.geolocation.getCurrentPosition(resolve, reject);
            });

            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;

            // Use the latitude and longitude to make a request to a reverse geocoding service
            const reverseGeocodingApiUrl =
                `https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}`;

            try {
                const response = await fetch(reverseGeocodingApiUrl);
                const data = await response.json();
                console.log(data)

                const locationName = data.display_name;
                $('#location_id').val(locationName);
                $('#location').val(locationName);
                $('#search_location').val(locationName);

                $('#search_lat').val(latitude);
                $('#search_lng').val(longitude);
            } catch (error) {
                console.error("Error retrieving location data: " + error);
            }
        } catch (error) {
            switch (error.code) {
                case error.PERMISSION_DENIED:
                    console.error("User denied the request for geolocation.");
                    break;
                case error.POSITION_UNAVAILABLE:
                    console.error("Location information is unavailable.");
                    break;
                case error.TIMEOUT:
                    console.error("The request to get user location timed out.");
                    break;
                case error.UNKNOWN_ERROR:
                    console.error("An unknown error occurred.");
                    break;
            }
        }
    } else {
        console.error("Geolocation is not supported by your browser.");
    }
}

function getLocation(id) {
    const target = document.getElementById(id);
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(showPositionGeo, showErrorGeo);
    } else {
      target.innerHTML = "Geolocation is not supported by this browser.";
    }
  }
  
function showPositionGeo(position) {
    // console.log(position)
    // x.innerHTML = "Latitude: " + position.coords.latitude +
    // "<br>Longitude: " + position.coords.longitude;
    const lat = position.coords.latitude;
    const lng = position.coords.longitude;
    const latlng = { lat: lat, lng: lng };

    const geocoder = new google.maps.Geocoder();
    geocoder.geocode({ location: latlng }, (results, status) => {
      if (status === "OK") {
        if (results[0]) {
        const address = results[0].formatted_address
        console.log(address)
        //   document.getElementById("location").innerHTML = address;
        } else {
        //   document.getElementById("location").innerHTML = "No results found";
        }
      } else {
        // document.getElementById("location").innerHTML = "Geocoder failed due to: " + status;
      }
    });
}

function showErrorGeo(error) {
    switch(error.code) {
      case error.PERMISSION_DENIED:
        // document.getElementById("location").innerHTML = "User denied the request for Geolocation."
        break;
      case error.POSITION_UNAVAILABLE:
        // document.getElementById("location").innerHTML = "Location information is unavailable."
        break;
      case error.TIMEOUT:
        // document.getElementById("location").innerHTML = "The request to get user location timed out."
        break;
      case error.UNKNOWN_ERROR:
        // document.getElementById("location").innerHTML = "An unknown error occurred."
        break;
    }
  }
  