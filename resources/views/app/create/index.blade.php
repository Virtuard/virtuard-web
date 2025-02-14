@extends ('layouts.auth')
@section('content')
    <div id="viewer" style="top: 0; filter: brightness(70%);"></div>
    @include('Layout::parts.header')
    <div class="boards" style="background: #f5f5f5; padding: 120px 60px;">
        <div class="row my-5">
            <div class="col-12">
                <h1 class="text-center" style="color: #fff;">{{ __('What do you want to listing?') }}</h1>
            </div>
        </div>
        <div class="row">
            @forelse ($menus as $key => $menu)
                <div class="col-md-4 mb-3">
                    <a href="{{ $menu['url'] }}" class="card h-100 text-decoration-none" @guest
                    data-toggle="modal"
                    data-target="#login"
                    @endguest>
                        <div class="card-body d-flex align-items-center">
                            <i class="fa fa-lg mr-2 {{ $menu['icon'] }}"></i>
                            {{ __($menu['title']) }}
                        </div>
                    </a>
                </div>
            @empty
            @endforelse
        </div>
    </div>
@endsection

@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@photo-sphere-viewer/core/index.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@photo-sphere-viewer/markers-plugin/index.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@photo-sphere-viewer/virtual-tour-plugin/index.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@photo-sphere-viewer/gallery-plugin/index.css" />
    <script src="https://cdn.jsdelivr.net/npm/photo-sphere-viewer@4/dist/photo-sphere-viewer.js"></script>
    
    <style>
        .bravo_wrap {
            position: absolute;
            width: 100%;
        }
        .bravo-menu a,
        .login-item a,
        .signup-item a,
        .c-grey.f14,
        .term-label,
        .form-title,
        .media-left i,
        .media-heading,
        .container.context p {
            color: #fff !important;
        }
        .children-menu a {
            color: #222 !important;
        }
        .bravo_header {
            z-index: 999 !important;
        }
        #viewer { width: 100vw; height: 100vh; position: fixed; pointer-events: none; }
        .bg-overlay {
            background-color: rgba(0,0,0,.5);
            width: 100%;
            height: 100%;
            z-index: 999 !important;
        }
    </style>
@endpush

@push('js')

<script type="importmap">
    {
        "imports": {
            "three": "https://cdn.jsdelivr.net/npm/three/build/three.module.js",
            "@photo-sphere-viewer/core": "https://cdn.jsdelivr.net/npm/@photo-sphere-viewer/core/index.module.js",
            "@photo-sphere-viewer/autorotate-plugin": "https://cdn.jsdelivr.net/npm/@photo-sphere-viewer/autorotate-plugin@5/index.module.js",
            "@photo-sphere-viewer/virtual-tour-plugin": "https://cdn.jsdelivr.net/npm/@photo-sphere-viewer/virtual-tour-plugin@5/index.module.js",
            "@photo-sphere-viewer/gallery-plugin": "https://cdn.jsdelivr.net/npm/@photo-sphere-viewer/gallery-plugin@5/index.module.js",
            "@photo-sphere-viewer/markers-plugin": "https://cdn.jsdelivr.net/npm/@photo-sphere-viewer/markers-plugin@5/index.module.js"
        }
    }
</script>

<script type="module">
    import { Viewer } from '@photo-sphere-viewer/core';
    import { MarkersPlugin } from '@photo-sphere-viewer/markers-plugin';
    import { AutorotatePlugin } from '@photo-sphere-viewer/autorotate-plugin';
    import { VirtualTourPlugin } from '@photo-sphere-viewer/virtual-tour-plugin';
    import { GalleryPlugin } from '@photo-sphere-viewer/gallery-plugin';

    const baseUrl = '/assets/images/';
    const baseUrl2 = 'https://photo-sphere-viewer-data.netlify.app/assets/';

    const container = document.createElement('section'); 
    const caption = 'Deep Blue Villa New <br> <b>&copy; virtuard.com</b>';

    const nodes = [
        {
            id: '1',
            panorama: baseUrl2 + 'tour/key-biscayne-1.jpg',
            thumbnail: baseUrl2 + 'tour/key-biscayne-1-thumb.jpg',
            // name: 'One',
            caption: `[1] ${caption}`,
            links: [
                { 
                    nodeId: '2',
                    position: { yaw: 10.0, pitch: 10.0 },
                }
            ],
            // markers: [markerLighthouse],
            gps: [-80.156479, 25.666725, 3],
            sphereCorrection: { pan: '33deg' },
        },
        {
            id: '2',
            panorama: baseUrl2 + 'tour/key-biscayne-2.jpg',
            thumbnail: baseUrl2 + 'tour/key-biscayne-2-thumb.jpg',
            // name: 'Two',
            caption: `[2] ${caption}`,
            links: [{ nodeId: '3' }, { nodeId: '1' }],
            // markers: [markerLighthouse],
            gps: [-80.156168, 25.666623, 3],
            sphereCorrection: { pan: '42deg' },
        },
        {
            id: '3',
            panorama: baseUrl2 + 'tour/key-biscayne-3.jpg',
            thumbnail: baseUrl2 + 'tour/key-biscayne-3-thumb.jpg',
            // name: 'Three',
            caption: `[3] ${caption}`,
            links: [{ nodeId: '4' }, { nodeId: '2' }, { nodeId: '5' }],
            gps: [-80.155932, 25.666498, 5],
            sphereCorrection: { pan: '50deg' },
        },
        {
            id: '4',
            panorama: baseUrl2 + 'tour/key-biscayne-4.jpg',
            thumbnail: baseUrl2 + 'tour/key-biscayne-4-thumb.jpg',
            // name: 'Four',
            caption: `[4] ${caption}`,
            links: [{ nodeId: '3' }, { nodeId: '5' }],
            gps: [-80.156089, 25.666357, 3],
            sphereCorrection: { pan: '-78deg' },
        },
        {
            id: '5',
            panorama: baseUrl2 + 'tour/key-biscayne-5.jpg',
            thumbnail: baseUrl2 + 'tour/key-biscayne-5-thumb.jpg',
            // name: 'Five',
            caption: `[5] ${caption}`,
            links: [{ nodeId: '6' }, { nodeId: '3' }, { nodeId: '4' }],
            gps: [-80.156292, 25.666446, 2],
            sphereCorrection: { pan: '170deg' },
        },
        {
            id: '6',
            panorama: baseUrl2 + 'tour/key-biscayne-6.jpg',
            thumbnail: baseUrl2 + 'tour/key-biscayne-6-thumb.jpg',
            // name: 'Six',
            caption: `[6] ${caption}`,
            links: [{ nodeId: '5' }, { nodeId: '7' }],
            gps: [-80.156465, 25.666496, 2],
            sphereCorrection: { pan: '65deg' },
        },
        {
            id: '7',
            panorama: baseUrl2 + 'tour/key-biscayne-7.jpg',
            thumbnail: baseUrl2 + 'tour/key-biscayne-7-thumb.jpg',
            // name: 'Seven',
            caption: `[7] ${caption}`,
            links: [{ nodeId: '6' }],
            gps: [-80.15707, 25.6665, 3],
            sphereCorrection: { pan: '110deg', pitch: -3 },
        },
    ];

    const viewer = new Viewer({
        container: 'viewer',
        // panorama: baseUrl + 'tour-example-360.jpg',
        caption: 'Copyright &copy; 2025 virtuard.com. All Right Reserved',
        loadingImg: null,
        // touchmoveTwoFingers: true,
        defaultYaw: 0,
        defaultPitch: 0,
        defaultZoomLvl: 20,
        fisheye: true,
        navbar: [
            "fullscreen"
        ],
        mousewheel: false, // Nonaktifkan zoom via scroll
        touchmoveTwoFingers: false, // Nonaktifkan geser dengan dua jari
        moveInertia: false, // Nonaktifkan efek geser
        keyboard: false, // Nonaktifkan navigasi pakai keyboard
        draggable: false, // Nonaktifkan drag mouse
        plugins: [
            [MarkersPlugin, {
                markers: [
                    {
                        id: 'Register For Free',
                        elementLayer: container,
                        position: { yaw: 0, pitch: 0.2},
                        rotation: { yaw: 0 },
                    },
                ],
            }],
            [AutorotatePlugin, {
                autorotatePitch: '3deg',
            }],
            [GalleryPlugin, {
                thumbnailSize: { width: 100, height: 100 },
            }],
            [VirtualTourPlugin, {
                positionMode: 'gps',
                renderMode: '3d',
                nodes: nodes,
                startNodeId: '1',   
            }],
        ],
    });     

    function handleFullscreenChange() {
        const mainElement = document.getElementById('main');
        if (document.fullscreenElement) {
            mainElement.style.zIndex = '0';
            viewer.setOptions({ navbar: ['fullscreen'] });
        } else {
            mainElement.style.zIndex = '99';
            viewer.setOptions({ navbar: [] });
        }
    }

    // Add event listeners for fullscreen change
    document.addEventListener('fullscreenchange', handleFullscreenChange);
    document.addEventListener('webkitfullscreenchange', handleFullscreenChange);
    document.addEventListener('mozfullscreenchange', handleFullscreenChange);
    document.addEventListener('MSFullscreenChange', handleFullscreenChange);


    document.getElementById("btn-demo").addEventListener("click", function () {
        let viewer = document.getElementById("viewer");

        if (viewer.requestFullscreen) {
            viewer.requestFullscreen();
        } else if (viewer.mozRequestFullScreen) { // Firefox
            viewer.mozRequestFullScreen();
        } else if (viewer.webkitRequestFullscreen) { // Chrome, Safari, Opera
            viewer.webkitRequestFullscreen();
        } else if (viewer.msRequestFullscreen) { // IE/Edge
            viewer.msRequestFullscreen();
        }
    });

    var listMaps = [];
    let map, markerCluster, currentInfoWindow;
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
        // initAutocomplete();

        map = new google.maps.Map(document.getElementById('gmap'), {
            center: { lat: 0, lng: 0 },
            zoom: 3,
            mapTypeId: 'satellite'
        });

        // add merker to map
        // addMarkersToMap(listMaps);
    }

    function fetchMap(attr) {
        $('#map-loading').show();

        $.ajax({
            type: "POST",
            url: "/explore/map/search",
            data: attr,
            success: function(data) {
                let maps = data.data;

                resetMarkers();
                addMarkersToMap(maps);

                $('#map-loading').hide();
            },
            error: function(xhr) {
                $('#map-loading').hide();
            }
        });
    }

    function resetMarkers() {
        if(markerCluster){
            markerCluster.clearMarkers();
            mapMarkers.forEach(marker => marker.setMap(null));
            mapMarkers = [];
        }

        const mapCenter = getCenterMarker([])
        map.setCenter(mapCenter)
    }

    function onFetchData(attr) {
        fetchMap(attr);
    }

    function getCenterMarker(mdata) {
        let map_lat = $('#explore_map_lat').val() ?? 0;
        let map_lgn = $('#explore_map_lgn').val() ?? 0;

        if (mdata.length !== 0) {
            let mdata_lat = mdata[0].map_lat ?? 0;
            let mdata_lgn = mdata[0].map_lgn ?? 0;

            if (mdata_lat) {
                map_lat = mdata_lat
            }
            if (mdata_lgn) {
                map_lat = mdata_lgn
            }
        }

        let center = {
            lat: Number(map_lat),
            lng: Number(map_lgn)
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

            let contentString = getPopupMarker(data);

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

        // map setCenter
        const mapCenter = getCenterMarker(markerData);
        map.setCenter(mapCenter);

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

    initMap()
    addMarkersToMap(listMaps)
    onFetchData()
</script>
@endpush
