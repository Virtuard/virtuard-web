var listMaps = [];
var listMapCount = null;
let map, mapAutocomplete, markerCluster, currentInfoWindow;
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
        zoom: 3,
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
            if (currentInfoWindow != null) {
                currentInfoWindow.close();
            } 

            infowindow.open(map, newMarker);

            currentInfoWindow = infowindow; 
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
                <div class="card card-custom card-has-bg click-col" style="background-image: url(${data.banner_image_id}); width: 250px;">
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

    filterContent(filteredMarkers)
}

function defaultMarkers() {
    resetMarkers();

    addMarkersToMap(listMaps);

    resetContent()
}

function resetMarkers() {
    markerCluster.clearMarkers();
    mapMarkers.forEach(marker => marker.setMap(null));
    mapMarkers = [];
}

function initAutocomplete() {
    let input = document.getElementById('map_place');
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
    const name = place.name;

    $('#map_lat').val(lat);
    $('#map_lgn').val(lng);
    $('#service_name').val(name);
}

function onChangeTab() {
    $('.nav-category').on('click', function (e) {
        let id = $(this).attr('id');
        id = id.replace('-tab', '');

        if (id == 'all') return defaultMarkers();

        filterMarkers(marker => marker.category == id);
    });
}

function resetContent(attr) {
    $.ajax({
        url: "/explore/list",
        type: "POST",
        data: attr,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(data) {
            let maps = data.data;

            resetMarkers();
            addMarkersToMap(maps)
            listMaps = maps;
            listMapCount = maps.length;

            $("#list-item").html(data.html);
            $("#count-list").html(`Showing ${listMapCount} Result`);
        },
    });
}

function filterContent(filtered) {
    $.ajax({
        url: '/explore/filter',
        type: 'POST',
        data: { filter: filtered },
        success: function(data) {
            $('#list-item').html(data.html);
            $("#count-list").html(`Showing ${data.data.length} Result`);
        },
        error: function(xhr, status, error) {
            console.error('Error fetching data:', error);
        }
    });
}

$(document).ready(function () {
    initMap();
    onChangeTab();
});