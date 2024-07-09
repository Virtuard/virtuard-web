@extends ('layouts.app')
@section('content')
    <div id="explore" class="container">
        <div class="row">
            <div class="col-12 card">
                <form action="{{ route('explore.index') }}">
                    <div class="row py-2">
                        <div class="col-md-3">
                            <div class="form-group mt-3">
                                <div class="form-content">
                                    <div class="smart-search d-flex justify-content-between align-items-center">
                                        <input type="text" aria-label='location' class='form-control' id='map_place'
                                            name='map_place' placeholder="Location"
                                            style="border-top: none;border-left:none;border-right:none;">
                                        <button class="btn btn-sm" id="get-location" type="button" onclick="getLocation()">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M11.5397 22.351C11.57 22.3685 11.5937 22.3821 11.6105 22.3915L11.6384 22.4071C11.8613 22.5294 12.1378 22.5285 12.3608 22.4075L12.3895 22.3915C12.4063 22.3821 12.43 22.3685 12.4603 22.351C12.5207 22.316 12.607 22.265 12.7155 22.1982C12.9325 22.0646 13.2388 21.8676 13.6046 21.6091C14.3351 21.0931 15.3097 20.3274 16.2865 19.3273C18.2307 17.3368 20.25 14.3462 20.25 10.5C20.25 5.94365 16.5563 2.25 12 2.25C7.44365 2.25 3.75 5.94365 3.75 10.5C3.75 14.3462 5.76932 17.3368 7.71346 19.3273C8.69025 20.3274 9.66491 21.0931 10.3954 21.6091C10.7612 21.8676 11.0675 22.0646 11.2845 22.1982C11.393 22.265 11.4793 22.316 11.5397 22.351ZM12 13.5C13.6569 13.5 15 12.1569 15 10.5C15 8.84315 13.6569 7.5 12 7.5C10.3431 7.5 9 8.84315 9 10.5C9 12.1569 10.3431 13.5 12 13.5Z"
                                                    fill="#0F172A"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="hidden" id="map_lat" name="map_lat">
                                <input type="hidden" id="map_lgn" name="map_lgn">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mt-3">
                                <div class="form-content">
                                    <label class="mb-2 font-weight-bold">Proximity <span id="proximity_text">0</span>
                                        km</label>
                                    <div class="input-search">
                                        <input type="range" id="search_radius" name="search_radius" min="0"
                                            max="500" class="w-100 cursor-pointer" value="0" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mt-3">
                                <input type="text" class="form-control" id="service_name" name="service_name"
                                    placeholder="keyword research"
                                    style="border-top: none;border-left:none;border-right:none;">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button id="submit-search" type="submit" class="btn btn-md btn-dark mt-3 w-100">
                                <i class="fa fa-search"></i>
                                Search
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="row my-2 nav-tab">
            <div class="col-12 px-0">
                <ul class="nav nav-tabs d-flex justify-content-start" id="myTab" role="tablist"
                    style="gap: 5px; padding: 5px 0;">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link nav-category text-capitalize {{ !request('service_type') ? 'active' : '' }}"
                            id="all-tab" data-toggle="tab" data-target="#all" type="button" role="tab"
                            aria-controls="all" aria-selected="true">
                            <i class="fa fa-sm mr-2 fa-globe"></i> {{ __('All') }}
                        </button>
                    </li>
                    @foreach (get_explore_service() as $menu)
                        <li class="nav-item" role="presentation">
                            <button
                                class="nav-link nav-category text-capitalize {{ request('service_type') == $menu['id'] ? 'active' : '' }}"
                                id="{{ $menu['id'] }}-tab" data-toggle="tab" data-target="#{{ $menu['id'] }}"
                                type="button" role="tab" aria-controls="hotel" aria-selected="false">
                                {!! $menu['icon'] !!}
                                {{ $menu['title'] }}</button>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="row my-2">
            <div class="col-md-3 px-0">
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show {{ !request('service_type') ? 'active' : '' }}" id="all"
                        role="tabpanel" aria-labelledby="all-tab">
                        @include('app.explore.partials.filter.all')
                    </div>
                    @foreach (get_explore_service() as $menu)
                        <div class="tab-pane fade show {{ request('service_type') == $menu['id'] ? 'active' : '' }}"
                            id="{{ $menu['id'] }}" role="tabpanel" aria-labelledby="{{ $menu['id'] }}-tab">
                            @include('app.explore.partials.filter.' . $menu['id'])
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="col-md-4 px-2 md-px-0 col-list-service">
                <div id="list-scroll" class="card card-explore">
                    <div class="card-body">
                        <div class="d-flex justify-content-center align-items-center mb-2">
                            <i class="fa fa-lg fa-arrow-left cursor-pointer d-none"></i>
                            <span id="count-list"></span>
                            <i class="fa fa-lg fa-arrow-right cursor-pointer d-none"></i>
                        </div>
                        <div class="list-item-container">
                            <div id="list-item"></div>
                            <div id="service-loading" class="text-center" style="display: none;">
                                <div class="spinner-border" role="status">
                                  <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-md-5 px-2 md-px-0">
                <div class="card card-explore">
                    <div class="card-body">
                        <div id="gmap"></div>
                    </div>
                </div>
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
            border-radius: unset;
            min-height: 150px;
            box-shadow: 0 0 12px 0 rgba(0, 0, 0, 0.2);
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

        #gmap {
            height: 100%;
        }

        .card-explore {
            height: 500px;
            position: relative;
            overflow: hidden;
            overflow-y: scroll;
        }

        @media (max-width: 768px) {
            .card-custom {
                min-height: 250px;
            }

            .card-explore {
                height: 350px;
            }

            #myTab li {
                font-size: 0.8rem;
            }

            .md-px-0 {
                padding-left: 0 !important;
                padding-right: 0 !important;
            }
        }

        @media (max-width: 420px) {
            .card-custom {
                min-height: 250px;
            }
        }

        .cluster-marker {
            background-color: red;
            /* Set your desired background color */
            color: white;
            /* Set text color */
            border-radius: 50%;
            /* Optional: round the marker */
            text-align: center;
            /* Optional: center the text */
            line-height: 50px;
            /* Optional: vertically center the text */
            width: 50px;
            /* Set the width */
            height: 50px;
            /* Set the height */
        }

        .bravo_search_tour .bravo_filter .g-filter-item {
            border: none;
            border-top-width: medium;
            border-top-style: none;
            border-top-color: currentcolor;
            border-radius: 0;
            border-top: 1px solid #d7dce3;
            margin-bottom: 0;
            padding: 20px;
        }

        .bravo_search_tour .bravo_filter .g-filter-item .item-title {
            cursor: pointer;
            position: relative;
        }

        .bravo_search_tour .bravo_filter .g-filter-item .item-title h3 {
            font-size: 16px;
        }

        .bravo_search_tour .bravo_filter .g-filter-item .item-title .fa {
            color: #1a2b48;
            font-size: 22px;
            position: absolute;
            right: 0;
            top: 2px;
        }

        .bravo_search_tour .bravo_filter .g-filter-item .item-content {
            margin-top: 20px;
        }

        .bravo_search_tour .bravo_filter .g-filter-item .item-content ul {
            list-style: none;
        }

        .bravo_filter .g-filter-item .hide {
            display: none;
        }

        .bravo_search_tour .bravo_filter .g-filter-item .item-content .btn-more-item {
            color: #5191fa;
            font-size: 14px;
            padding: 0;
            text-decoration: none;
        }
    </style>
@endpush

@push('js')
    <script src="https://maps.google.com/maps/api/js?key={{ get_map_gmap_key() }}&libraries=places"></script>
    <script src="https://cdn.jsdelivr.net/npm/@google/markerclusterer@2.0.9/dist/markerclusterer.min.js"></script>
    <script src="{{ asset('assets/js/listing-map.js') }}"></script>
@endpush
