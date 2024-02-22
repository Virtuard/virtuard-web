@extends ('layouts.app')
@section('content')
    <div class="" style="background: #f5f5f5; padding: 120px 60px;">
        <div class="row">
            <div class="card">
                <div class="p-4">
                    <i class="fa fa-globe" aria-hidden="true"></i>
                    <span>All</span>
                </div>
            </div>
        </div>
        <div class="row" style="margin-top: -5px;">
            <div class="col-12 card">
                <form action="">
                    <div class="row py-3">
                        <div class="col-3">
                            <div class="form-group mt-3">
                                <div class="form-content">
                                    <div class="smart-search d-flex justify-content-between align-items-center">
                                        <input type="text" aria-label='location' class='form-control' 
                                            id='search_location'
                                            name='search_location' 
                                            placeholder="Location"
                                            value="{{ isset($_GET['search_location']) ? $_GET['search_location'] : '' }}"
                                            style="border-top: none;border-left:none;border-right:none;">
                                        <button class="btn btn-sm" id="get-location" type="button" onclick="getLocation()">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M11.5397 22.351C11.57 22.3685 11.5937 22.3821 11.6105 22.3915L11.6384 22.4071C11.8613 22.5294 12.1378 22.5285 12.3608 22.4075L12.3895 22.3915C12.4063 22.3821 12.43 22.3685 12.4603 22.351C12.5207 22.316 12.607 22.265 12.7155 22.1982C12.9325 22.0646 13.2388 21.8676 13.6046 21.6091C14.3351 21.0931 15.3097 20.3274 16.2865 19.3273C18.2307 17.3368 20.25 14.3462 20.25 10.5C20.25 5.94365 16.5563 2.25 12 2.25C7.44365 2.25 3.75 5.94365 3.75 10.5C3.75 14.3462 5.76932 17.3368 7.71346 19.3273C8.69025 20.3274 9.66491 21.0931 10.3954 21.6091C10.7612 21.8676 11.0675 22.0646 11.2845 22.1982C11.393 22.265 11.4793 22.316 11.5397 22.351ZM12 13.5C13.6569 13.5 15 12.1569 15 10.5C15 8.84315 13.6569 7.5 12 7.5C10.3431 7.5 9 8.84315 9 10.5C9 12.1569 10.3431 13.5 12 13.5Z" fill="#0F172A"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group mt-3">
                                <div class="form-content">
                                    <label class="mb-2 font-weight-bold">Proximity 10km</label>
                                    <div class="input-search">
                                        <input type="range" 
                                            id="search_range" 
                                            name="search_range" 
                                            step="100000" 
                                            min="0"
                                            max="1000000" 
                                            class="w-100" 
                                            value="700000"
                                            />
                                    </div>
                                    <div class="d-flex justify-content-between" style="font-size: 12px!important">
                                        <span>0</span>
                                        <span>≥10km</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group mt-3">
                                <input type="text" class="form-control" 
                                    id="search_keyword"
                                    name="search_keyword" 
                                    placeholder="keyword research"
                                    value="{{ isset($_GET['search_keyword']) ? $_GET['search_keyword'] : '' }}"
                                    style="border-top: none;border-left:none;border-right:none;">
                            </div>
                        </div>
                        <div class="col-3">
                            <button type="submit" class="btn btn-md btn-dark mt-3 w-100">
                                <i class="fa fa-search"></i>
                                Search
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="row mt-4 nav-tab">
            <div class="col-12">
                <ul class="nav nav-tabs d-flex justify-content-start" id="myTab" role="tablist" style="gap: 5px; padding: 5px 0;">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link text-capitalize disabled" id="#" data-toggle="tab" data-target="#"
                            type="button" role="tab" aria-controls="#" aria-selected="true">What are you looking for?
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link nav-category text-capitalize active" id="all-tab" data-toggle="tab" data-target="#all"
                            type="button" role="tab" aria-controls="all" aria-selected="true">
                            <i class="fa fa-sm mr-2 fa-globe"></i> All
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link nav-category text-capitalize" id="business-tab" data-toggle="tab" data-target="#business"
                            type="button" role="tab" aria-controls="business" aria-selected="false">
                            <i class="fa fa-sm mr-2 fa-shopping-bag"></i>Business</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link nav-category text-capitalize" id="properties-tab" data-toggle="tab"
                            data-target="#properties" type="button" role="tab" aria-controls="properties"
                            aria-selected="false"><i class="fa fa-sm mr-2 fa-home"></i> Properties</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link nav-category text-capitalize" id="accomodations-tab" data-toggle="tab"
                            data-target="#accomodations" type="button" role="tab" aria-controls="accomodations"
                            aria-selected="false"> <i class="fa fa-sm mr-2 fa-building"></i> Accomodations</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link nav-category text-capitalize" id="vehicles-tab" data-toggle="tab"
                            data-target="#vehicles" type="button" role="tab" aria-controls="vehicles"
                            aria-selected="false"><i class="fa fa-sm mr-2 fa-ship"></i> Vehicles</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link nav-category text-capitalize" id="naturals-tab" data-toggle="tab"
                            data-target="#naturals" type="button" role="tab" aria-controls="naturals"
                            aria-selected="false"><i class="material-icons">landscape</i> Natural and Landscape</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link nav-category text-capitalize" id="culturals-tab" data-toggle="tab"
                            data-target="#culturals" type="button" role="tab" aria-controls="culturals"
                            aria-selected="false"><i class="material-icons">church</i> Cultural Heritage</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link nav-category text-capitalize" id="arts-tab" data-toggle="tab"
                            data-target="#arts" type="button" role="tab" aria-controls="arts"
                            aria-selected="false"><i class="material-icons font-size-inherit">design_services</i> Rendering and Art</button>
                    </li>
                </ul>
            </div>
        </div>
        <div class="row">
            <div class="col-7">
                <div class="tab-content" id="myTabContent">
                    {{-- Tab content all --}}
                    @include('explore.listing.all.content')
                    
                    {{-- Tab content listing category --}}
                    @foreach ($listings as $key => $listing)
                        @include('explore.listing.category.content')
                    @endforeach
                </div>
            </div>
            <div class="col-5">
                <div id="gmap" style="height: 100%;"></div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <style>
        .autocomplete-items {
            position: absolute;
            z-index: 99;
            left: 0;
            right: 0;
        }

        .form-control:focus {
            outline: none;
            box-shadow: none;
        }
        .card-custom {
            border: none;
            overflow: hidden;
            transition: all 500ms cubic-bezier(0.19, 1, 0.22, 1);
            border-radius: 5px;
            min-height: 150px;
            box-shadow: 0 0 12px 0 rgba(0, 0, 0, 0.2);
        }

        @media (max-width: 768px) {
            .card-custom {
                min-height: 350px;
            }
        }

        @media (max-width: 420px) {
            .card-custom {
                min-height: 300px;
            }
        }

        .card-custom.card-has-bg {
            transition: all 500ms cubic-bezier(0.19, 1, 0.22, 1);
            background-size: 120%;
            background-repeat: no-repeat;
            background-position: center center;
        }

        .card-custom.card-has-bg:before {
            content: "";
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
        }

        .card-custom.card-has-bg:hover {
            transform: scale(0.98);
            box-shadow: 0 0 5px -2px rgba(0, 0, 0, 0.3);
            background-size: 130%;
            transition: all 500ms cubic-bezier(0.19, 1, 0.22, 1);
        }

        .card-custom.card-has-bg:hover .card-img-overlay {
            transition: all 800ms cubic-bezier(0.19, 1, 0.22, 1);
            background: rgba(22, 22, 22, 0.8);
            background: linear-gradient(0deg, rgba(22, 22, 22, 0.8) 0%, #1a1b1b 100%);
        }

        .card-custom .card-title {
            font-weight: 800;
        }

        .card-custom .card-meta {
            text-transform: uppercase;
            font-weight: 500;
            letter-spacing: 2px;
        }

        .card-custom .card-body {
            transition: all 500ms cubic-bezier(0.19, 1, 0.22, 1);
        }

        .card-custom:hover {
            cursor: pointer;
            transition: all 800ms cubic-bezier(0.19, 1, 0.22, 1);
        }

        .card-custom .card-img-overlay {
            transition: all 800ms cubic-bezier(0.19, 1, 0.22, 1);
            background: rgba(22, 22, 22, 0.3);
            background: linear-gradient(0deg, rgba(22, 22, 22, 0.3) 0%, #1a1b1b 100%);
        }

        .nav-tab .material-icons {
            font-size: inherit !important;
            margin-right: 2px;
        }
    </style>
    <style>
        input[type="range"]:focus {
        outline: none;
        }

        input[type="range"] {
            position: relative;
            -webkit-appearance: none;
            margin-right: 15px;
            width: 100%;
            height: 8px;
            background: rgba(241, 241, 241, 1);
            border-radius: 5px;
            background-image: linear-gradient(
                180deg,
                rgba(101, 143, 227, 0.8) 0%,
                #6e9cf7 100%
            );
            background-size: 70% 100%;
            background-repeat: no-repeat;
        }

        input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            height: 22px;
            width: 22px;
            border-radius: 50%;
            background: #fff;
            cursor: ew-resize;
            border: 3.5px solid #6e9cf7;
            box-shadow: 0 0 1px 0 #6e9cf7;
            transition: background 0.3s ease-in-out;
        }

        input[type="range"]::-moz-range-thumb {
            -webkit-appearance: none;
            height: 22px;
            width: 22px;
            border-radius: 50%;
            background: #fff;
            cursor: ew-resize;
            border: 3.5px solid #6e9cf7;
            box-shadow: 0 0 1px 0 #6e9cf7;
            transition: background 0.3s ease-in-out;
        }

        input[type="range"]::-ms-thumb {
            -webkit-appearance: none;
            height: 22px;
            width: 22px;
            border-radius: 50%;
            background: #fff;
            cursor: ew-resize;
            border: 3.5px solid #6e9cf7;
            box-shadow: 0 0 1px 0 #6e9cf7;
            transition: background 0.3s ease-in-out;
        }

        input[type="range"]::-webkit-slider-runnable-track {
            -webkit-appearance: none;
            box-shadow: none;
            border: none;
            background: transparent;
        }

        input[type="range"]::-moz-range-track {
            -webkit-appearance: none;
            box-shadow: none;
            border: none;
            background: transparent;
        }

        input[type="range"]::-ms-track {
            -webkit-appearance: none;
            box-shadow: none;
            border: none;
            background: transparent;
        }
    </style>
@endpush

@push('js')
    <script src="https://maps.google.com/maps/api/js?key={{ get_map_gmap_key() }}&libraries=places"></script>
    <script>
        const rangeInputs = document.querySelectorAll('input[type="range"]')

        function handleInputChange(e) {
            let target = e.target
            if (e.target.type !== 'range') {
                target = document.getElementById('range')
            }
            const min = target.min
            const max = target.max
            const val = target.value

            let percentage = (val - min) * 100 / (max - min)
            target.style.backgroundSize = percentage + '% 100%'
        }

        rangeInputs.forEach(input => {
            input.addEventListener('input', handleInputChange)
        })

        // Initialize the map
        const markersData = {{ Js::from($listMaps) }}
        let map;
        let mapMarkers = [];

        function initAutocomplete() {
            let input = document.getElementById('search_location');
            let autoComplete = new google.maps.places.Autocomplete(input);
        }

        function initMap() {
            // Initialize the autocomplete
            initAutocomplete();

            map = new google.maps.Map(document.getElementById('gmap'), {
                center: { lat: 0, lng: 0 },
                zoom: 1
            });

            addMarkersToMap(markersData);
        }

        function addMarkersToMap(markerData) {
            markerData.forEach((data) => {
                const lat = Number(data.map_lat)
                const lng = Number(data.map_lng)
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
            mapMarkers.forEach(marker => marker.setMap(null));

            const filteredMarkers = markersData.filter(condition);

            addMarkersToMap(filteredMarkers);
        }

        function defaultMarkers() {
            mapMarkers.forEach(marker => marker.setMap(null));

            addMarkersToMap(markersData);
        }

        $('.nav-category').on('click', function(e) {
            let id = $(this).attr('id');
            id = id.replace('-tab', '');

            if (id == 'all') return defaultMarkers();
            
            filterMarkers(marker => marker.category == id);
        });
    </script>
    <script>
        $(document).ready(function() {
            initMap();
        });
    </script>
@endpush
