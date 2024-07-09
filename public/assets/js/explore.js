var isLoadingScroll = false;
var countService = 0;
var pagePaginate = 1;
var tabActive = 'all';
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

function initFilterRadiusMain() {
    let radiusSlider = document.getElementById("explore_search_radius");
    let radiusText = document.getElementById("explore_proximity_text");
    radiusText.innerHTML = radiusSlider.value;

    radiusSlider.oninput = function() {
        radiusText.innerHTML = this.value;
    }
}

function initFilterRadius() {
    $('.filter-search-radius').on('input', function() {
        let id = $(this).data('id');

        let radiusSlider = document.getElementById(`${id}_search_radius`);
        let radiusText = document.getElementById(`${id}_proximity_text`);

        radiusSlider.oninput = function() {
            radiusText.innerHTML = this.value;
        }
    });
}

function initAutocomplete() {
    let arrPlaces = $('.filter_map_place');

    arrPlaces.each(function() {
        let elem = $(this);
        let id = elem.attr('data-id');

        var filterAutoComplete;
        let input = document.getElementById(`${id}_map_place`);
        filterAutoComplete = new google.maps.places.Autocomplete(input);
        filterAutoComplete.addListener('place_changed', onFilterChangePlace);

        function onFilterChangePlace() {
            const place = filterAutoComplete.getPlace();
            if (!place.geometry) {
                console.error("Place not found");
                return;
            }

            const lat = place.geometry.location.lat();
            const lng = place.geometry.location.lng();
            const name = place.name;

            $(`#${id}_map_lat`).val(lat);
            $(`#${id}_map_lgn`).val(lng);
            // $(`#${id}_service_name`).val(name);
        }
    });

}

function initMap() {
    // Initialize the autocomplete
    initAutocomplete();

    map = new google.maps.Map(document.getElementById('gmap'), {
        center: { lat: 0, lng: 0 },
        zoom: 3,
    });

    // add merker to map
    // addMarkersToMap(listMaps);
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
    if(markerCluster){
        markerCluster.clearMarkers();
        mapMarkers.forEach(marker => marker.setMap(null));
        mapMarkers = [];
    }
}

function initAutocomplete() {
    let input = document.getElementById('explore_map_place');
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

        const attr = {
            service_type: id,
        };

        tabActive = id;
        resetServiceList();
        

        setTimeout(() => {
            onFetchData(attr)
        }, 500);

        // if (id == 'all') return defaultMarkers();

        // filterMarkers(marker => marker.category == id);
    });
}

function resetServiceList() {
    countService = 0;
    pagePaginate = 1;

    $("#list-item").html('');
    $("#count-list").html('');
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

$scrollableDiv = $('#list-scroll');
$scrollableDiv.scroll(function (e) {
    e.preventDefault();
    
    if (!isLoadingScroll && $scrollableDiv.scrollTop() + $scrollableDiv.height() >= ($scrollableDiv[0].scrollHeight - 20)) {
        pagePaginate++;
        isLoadingScroll = true;
        
        console.log('page', pagePaginate)

        setTimeout(() => {
            infinteLoadMore();
        }, 500);
    }
});

function infinteLoadMore() {
    const formData = $(`#form_explore_${tabActive}`).serializeArray();
    const attr = {};
    $(formData).each(function(index, obj) {
        attr[obj.name] = obj.value;
    });

    fetchService(attr);
}

function onSubmitForm() {
    $('.bravo_form_filter').on('submit', function(e) {
        e.preventDefault();

        const formData = $(this).serializeArray();
        const attr = {};
        $(formData).each(function(index, obj) {
            attr[obj.name] = obj.value;
        });

        fetchService(attr);
        fetchMap(attr);
    });
}

function onFetchData(attr) {
    $("#list-item").html('');
    $("#count-list").html('');

    fetchService(attr);
    fetchMap(attr);
}

function fetchMap(attr) {
    $.ajax({
        type: "POST",
        url: "/explore/map/search",
        data: attr,
        success: function(data) {
            let maps = data.data;

            resetMarkers();
            addMarkersToMap(maps);
        },
    });
}

function fetchService(attr = {}) {
    isLoadingScroll = true;
    $('#service-loading').show();

    $.ajax({
        url: `/explore/service/search?page=${pagePaginate}`,
        data: attr,
        success: function(data) {
            countService += data.data.length;

            if (data.data) {
                $("#list-item").append(data.html);
                // $("#count-list").html(`Showing ${countService} Result`);
            }

            isLoadingScroll = false;
            $('#service-loading').hide();
        },
        error: function(xhr) {
            isLoadingScroll = false;
            $('#service-loading').hide();
        }
    });
}

function onSubmitSearch() {
    $('#explore-form').on('submit', function(e) {
        e.preventDefault();
        
        let attr = {
            service_name: $('#explore_service_name').val(),
            map_place: $('#explore_map_place').val(),
            map_lat: $('#explore_map_lat').val(),
            map_lgn: $('#explore_map_lgn').val(),
            search_radius: $('#explore_search_radius').val(),
            service_type: tabActive,
        };

        onFetchData(attr);
    })
}

jQuery(function($) {
    $(".bravo_filter .g-filter-item").each(function() {
        if ($(window).width() <= 990) {
            $(this).find(".item-title").toggleClass("e-close");
        }
        $(this).find(".item-title").click(function() {
            $(this).toggleClass("e-close");
            if ($(this).hasClass("e-close")) {
                $(this).closest(".g-filter-item").find(".item-content").slideUp();
            } else {
                $(this).closest(".g-filter-item").find(".item-content").slideDown();
            }
        });
        $(this).find(".btn-more-item").click(function() {
            $(this).closest(".g-filter-item").find(".hide").removeClass("hide");
            $(this).addClass("hide");
        });
        $(this).find(".bravo-filter-price").each(function() {
            var input_price = $(this).find(".filter-price");
            var min = input_price.data("min");
            var max = input_price.data("max");
            var from = input_price.data("from");
            var to = input_price.data("to");
            var symbol = input_price.data("symbol");
            input_price.ionRangeSlider({
                type: "double",
                grid: true,
                min: min,
                max: max,
                from: from,
                to: to,
                prefix: symbol
            });
        });
    });

    $(".bravo_form_filter input[type=checkbox]").change(function(e) {
        e.preventDefault();

        resetServiceList();

        $(this).closest("form").submit();
    });
    
    $(".bravo_form_filter select").change(function(e) {
        e.preventDefault();

        resetServiceList();

        $(this).closest("form").submit();
    });
    
    $('.btn-all-back').on('click', function(){
        $('.panel-all-back').removeClass('active')
        $('.sub-nav-link').removeClass('active')
        $('#all-category').addClass('show active')
    })
});

$(document).ready(function () {
    initMap();
    initAutocomplete();
    initFilterRadius();
    initFilterRadiusMain();
    onFetchData();
    onChangeTab();
    onSubmitForm();
    onSubmitSearch();
});