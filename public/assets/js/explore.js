$(document).ready(function () {
    initMap();
    initAutocomplete();
    initAutocompleteByCategory();
    initFilterRadius();
    initFilterRadiusMain();
    onFetchData();
    onChangeTab();
    onSubmitForm();
    onSubmitSearch();
});

var isResetSearch = false;
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
            url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
                <svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="10" fill="#3B82F6" stroke="#1E40AF" stroke-width="2"/>
                </svg>
            `),
            height: 24,
            width: 24,
            textSize: 12
        },
        {
            textColor: 'white',
            url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
                <svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="10" fill="#F59E0B" stroke="#D97706" stroke-width="2"/>
                </svg>
            `),
            height: 24,
            width: 24,
            textSize: 12
        },
        {
            textColor: 'white',
            url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
                <svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="10" fill="#EF4444" stroke="#DC2626" stroke-width="2"/>
                </svg>
            `),
            height: 24,
            width: 24,
            textSize: 12
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

function initAutocompleteByCategory() {
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

            $(`#${id}_map_lat`).val(lat);
            $(`#${id}_map_lgn`).val(lng);
            
            updateMapCenter(lat, lng);
        }
    });

}

function initMap() {
    initAutocomplete();

    let initialLat = parseFloat($('#explore_map_lat').val()) || -6.2088;
    let initialLng = parseFloat($('#explore_map_lgn').val()) || 106.8456;

    if (!parseFloat($('#explore_map_lat').val()) && !parseFloat($('#explore_map_lgn').val())) {
        getUserCurrentLocation();
    }

    map = new google.maps.Map(document.getElementById('gmap'), {
        center: { lat: initialLat, lng: initialLng },
        zoom: 10,
        mapTypeControl: true,
        streetViewControl: true,
        fullscreenControl: true,
        zoomControl: true
    });

    initClusterZoomListener();
}

function getUserCurrentLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                
                $('#explore_map_lat').val(lat);
                $('#explore_map_lgn').val(lng);
                
                updateMapCenter(lat, lng);
            },
            function(error) {
                console.log("Error getting location:", error);
            }
        );
    }
}

function getCenterMarker(mdata) {
    let map_lat = parseFloat($('#explore_map_lat').val()) || -6.2088;
    let map_lng = parseFloat($('#explore_map_lgn').val()) || 106.8456;

    if (mdata.length !== 0) {
        let mdata_lat = parseFloat(mdata[0].map_lat) || 0;
        let mdata_lng = parseFloat(mdata[0].map_lng) || 0;

        if (mdata_lat !== 0) {
            map_lat = mdata_lat;
        }
        if (mdata_lng !== 0) {
            map_lng = mdata_lng;
        }
    }

    let center = {
        lat: Number(map_lat),
        lng: Number(map_lng)
    };

    return center;
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

    const mapCenter = getCenterMarker(markerData);
    map.setCenter(mapCenter);

    markerCluster = new MarkerClusterer(map, mapMarkers, clusterConfig);
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

    mapCenter = getCenterMarker([])
    map.setCenter(mapCenter)
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

    $('#explore_map_lat').val(lat);
    $('#explore_map_lgn').val(lng);
    
    updateMapCenter(lat, lng);
}

function updateMapCenter(lat, lng) {
    if (map) {
        const newCenter = { lat: parseFloat(lat), lng: parseFloat(lng) };
        map.setCenter(newCenter);
        map.setZoom(12);
    }
}

function onChangeTab() {
    $('.nav-category').on('click', function (e) {
        let id = $(this).attr('id');
        id = id.replace('-tab', '');

        const attr = {
            service_type: id,
            map_place: $('#explore_map_place').val(),
            map_lat: $('#explore_map_lat').val(),
            map_lgn: $('#explore_map_lgn').val(),
        };

        tabActive = id;
        document.getElementById("explore-form").reset();
        resetServiceList();

        setTimeout(() => {
            onFetchData(attr)
        }, 500);

    });
}

function resetServiceList() {
    countService = 0;
    pagePaginate = 1;
    isResetSearch = true;

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
        isResetSearch = false;
        
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

        isResetSearch = true;
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

function fetchService(attr = {}) {
    isLoadingScroll = true;
    $('#service-loading').show();

    $.ajax({
        url: `/explore/service/search?page=${pagePaginate}`,
        data: attr,
        success: function(data) {
            countService += data.data.length;

            if (data.data) {
                if (isResetSearch) {
                    $("#list-item").html(data.html);
                } else {
                    $("#list-item").append(data.html);
                }
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
            is_ipanorama: $('#explore_is_ipanorama').val(),
            service_type: tabActive,
        };

        isResetSearch = true;
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

    $("#explore-form select").change(function(e) {
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

function createDynamicClusterConfig(categoryColors = {}) {
    const defaultColors = {
        'hotel': '#3B82F6',
        'space': '#EF4444',
        'business': '#F59E0B',
        'default': '#6B7280'
    };
    
    const colors = { ...defaultColors, ...categoryColors };
    
    return {
        imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m',
        gridSize: 60,
        minimumClusterSize: 4,
        styles: [
            {
                textColor: 'white',
                url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
                    <svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="12" r="10" fill="${colors.default}" stroke="#374151" stroke-width="2"/>
                    </svg>
                `),
                height: 24,
                width: 24,
                textSize: 12
            },
            {
                textColor: 'white',
                url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
                    <svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="12" r="10" fill="${colors.default}" stroke="#374151" stroke-width="2"/>
                    </svg>
                `),
                height: 24,
                width: 24,
                textSize: 12
            },
            {
                textColor: 'white',
                url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
                    <svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="12" r="10" fill="${colors.default}" stroke="#374151" stroke-width="2"/>
                    </svg>
                `),
                height: 24,
                width: 24,
                textSize: 12
            }
        ],
        calculator: function(markers, numStyles) {
            const categories = {};
            markers.forEach(marker => {
                const category = marker.getTitle() || 'default';
                categories[category] = (categories[category] || 0) + 1;
            });
            
            const dominantCategory = Object.keys(categories).reduce((a, b) => 
                categories[a] > categories[b] ? a : b
            );
            
            const color = colors[dominantCategory] || colors.default;
            
            let styleIndex = 0;
            if (markers.length >= 100) styleIndex = 2;
            else if (markers.length >= 10) styleIndex = 1;
            
            const style = this.styles[styleIndex];
            const svgTemplate = `
                <svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="10" fill="${color}" stroke="#374151" stroke-width="2"/>
                </svg>
            `;
            
            style.url = 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(svgTemplate);
            
            return {
                text: markers.length.toString(),
                index: styleIndex
            };
        }
    };
}

function updateClusterColors(categoryColors) {
    if (markerCluster) {
        markerCluster.clearMarkers();
    }
    
    const dynamicConfig = createDynamicClusterConfig(categoryColors);
    markerCluster = new MarkerClusterer(map, mapMarkers, dynamicConfig);
}

function updateClusterByCategory() {
    const categoryColors = {
        'hotel': '#3B82F6',
        'space': '#EF4444',
        'business': '#F59E0B'
    };
    
    updateClusterColors(categoryColors);
}

function updateClusterByTime() {
    const hour = new Date().getHours();
    let colorScheme;
    
    if (hour >= 6 && hour < 12) {
        colorScheme = {
            'default': '#FBBF24',
            'hotel': '#3B82F6',
            'space': '#EF4444',
            'business': '#F59E0B'
        };
    } else if (hour >= 12 && hour < 18) {
        colorScheme = {
            'default': '#F97316',
            'hotel': '#1E40AF',
            'space': '#DC2626',
            'business': '#D97706'
        };
    } else {
        colorScheme = {
            'default': '#6B7280',
            'hotel': '#1E3A8A',
            'space': '#B91C1C',
            'business': '#B45309'
        };
    }
    
    updateClusterColors(colorScheme);
}

function updateClusterByZoom() {
    const zoom = map.getZoom();
    let colorScheme;
    
    if (zoom >= 15) {
        colorScheme = {
            'default': '#10B981',
            'hotel': '#3B82F6',
            'space': '#EF4444',
            'business': '#F59E0B'
        };
    } else if (zoom >= 12) {
        colorScheme = {
            'default': '#F59E0B',
            'hotel': '#1E40AF',
            'space': '#DC2626',
            'business': '#D97706'
        };
    } else {
        colorScheme = {
            'default': '#6B7280',
            'hotel': '#1E3A8A',
            'space': '#B91C1C',
            'business': '#B45309'
        };
    }
    
    updateClusterColors(colorScheme);
}

function initClusterZoomListener() {
    google.maps.event.addListener(map, 'zoom_changed', function() {
        updateClusterByZoom();
    });
}