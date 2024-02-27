let map, mapAutocomplete, markerCluster;
let mapMarkers = [];
let clusterConfig = {
    imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m',
    gridSize: 60,
    minimumClusterSize: 4,
    styles: [
        {
            textColor: 'white',
            url: '/icon/circle-24.png',
            height: 24,
            width: 24,
            textSize: 12,
            backgroundPosition: 'center',
            backgroundRepeat: 'no-repeat',
            backgroundColor: 'red'
        }
    ]
};

function initMap() {
    // Initialize the autocomplete
    initAutocomplete();

    map = new google.maps.Map(document.getElementById('gmap'), {
        center: { lat: 0, lng: 0 },
        zoom: 1,
    });

    // add merker to map
    addMarkersToMap(listMaps);
}

function addMarkersToMap(markerData) {
    markerData.forEach((data) => {
        const lat = Number(data.map_lat);
        const lng = Number(data.map_lng);
        const newMarker = new google.maps.Marker({
            position: { lat, lng },
            map: map,
            title: data.title,
            icon: data.icon,
        });

        contentString = getPopupMarker(data);

        const infowindow = new google.maps.InfoWindow({
            content: contentString,
        });

        newMarker.addListener("click", () => {
            infowindow.open(map, newMarker);
        });

        mapMarkers.push(newMarker);
    });

    // Create the MarkerClusterer
    markerCluster = new MarkerClusterer(map, mapMarkers, clusterConfig);
}

function getPopupMarker(data) {
    const contentString =
        `
            <div class="card" style="overflow: hidden;">
                <div class="card card-custom card-has-bg click-col" style="background-image: url(${data.image}); width: 250px;">
                    <div class="card-img-overlay d-flex align-items-end">
                        <div>
                            <h5 class="card-title mt-0 mb-0" style="text-overflow: ellipsis; overflow:hidden; font-size: 16px;">
                                <a class="text-white" href="${data.url}">${data.title}</a>
                            </h5>
                            <span class="text-white"> <i class="fa fa-map-marker"></i> ${data.address}</span>
                        </div>
                    </div>
                </div>
            </div>
            `;

    return contentString;
}

function filterMarkers(condition) {
    resetMarkers();

    const filteredMarkers = listMaps.filter(condition);

    addMarkersToMap(filteredMarkers);
}

function defaultMarkers() {
    resetMarkers();

    addMarkersToMap(listMaps);
}

function resetMarkers() {
    markerCluster.clearMarkers();
    mapMarkers.forEach(marker => marker.setMap(null));
    mapMarkers = [];
}

function initAutocomplete() {
    let input = document.getElementById('search_location');
    mapAutocomplete = new google.maps.places.Autocomplete(input);
    mapAutocomplete.addListener('place_changed', onPlaceChanged);
}

function onPlaceChanged() {
    const place = mapAutocomplete.getPlace();
    if (!place.geometry) {
        console.error("Place not found");
        return;
    }

    const lat = place.geometry.location.lat();
    const lng = place.geometry.location.lng();

    $('#search_lat').val(lat);
    $('#search_lng').val(lng);
}

function onChangeTab() {
    $('.nav-category').on('click', function (e) {
        let id = $(this).attr('id');
        id = id.replace('-tab', '');

        if (id == 'all') return defaultMarkers();

        filterMarkers(marker => marker.category == id);
    });
}

$(document).ready(function () {
    initMap();
    onChangeTab();
});