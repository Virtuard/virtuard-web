@extends('layouts.app')
@push('css')
    <link href="{{ asset('dist/frontend/module/space/css/space.css?_ver='.config('app.asset_version')) }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset("libs/ion_rangeslider/css/ion.rangeSlider.min.css") }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset("libs/fotorama/fotorama.css") }}"/>
    
    <!-- iPanorama -->
    <link href="{{ asset('libs/ipanorama/src/ipanorama.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/ipanorama/src/ipanorama.theme.default.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/ipanorama/src/ipanorama.theme.modern.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/ipanorama/src/ipanorama.theme.dark.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/ipanorama/src/effect.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/ipanorama/src/style.css') }}" rel="stylesheet">
@endpush
@section('content')
    <div class="bravo_detail_space">
        @include('Layout::parts.bc')
        @include('Business::frontend.layouts.details.business-banner')
        <input type="hidden" id="panId" value="{{$ipanorama}}">
        <div class="bravo_content">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 col-lg-9">
                        @php $review_score = $row->review_data @endphp
                        @if($ipanorama)
                        <div id="panorama"></div>
                        @endif
                        @include('Business::frontend.layouts.details.business-detail')
                        @include('Business::frontend.layouts.details.business-review')
                    </div>
                    <div class="col-md-12 col-lg-3">
                        @include('Tour::frontend.layouts.details.vendor')
                        @include('Business::frontend.layouts.details.business-form-book')
                    </div>
                </div>
                <div class="row end_tour_sticky">
                    <div class="col-md-12">
                        @include('Business::frontend.layouts.details.business-related')
                    </div>
                </div>
            </div>
        </div>
        @include('Business::frontend.layouts.details.business-form-book-mobile')
    </div>
@endsection

@push('js')
    {!! App\Helpers\MapEngine::scripts() !!}
    <script>
        jQuery(function ($) {
            @if($row->map_lat && $row->map_lng)
            new BravoMapEngine('map_content', {
                disableScripts: true,
                fitBounds: true,
                center: [{{$row->map_lat}}, {{$row->map_lng}}],
                zoom:{{$row->map_zoom ?? "8"}},
                ready: function (engineMap) {
                    engineMap.addMarker([{{$row->map_lat}}, {{$row->map_lng}}], {
                        icon_options: {
                            iconUrl:"{{get_file_url(setting_item("business_icon_marker_map"),'full') ?? url('images/icons/png/pin.png') }}"
                        }
                    });
                }
            });
            @endif
        })
    </script>
    <script>
        var bravo_booking_data = {!! json_encode($booking_data) !!}
        var bravo_booking_i18n = {
			no_date_select:'{{__('Please select Start and End date')}}',
            no_guest_select:'{{__('Please select at least one guest')}}',
            load_dates_url:'{{route('business.vendor.availability.loadDates')}}',
            name_required:'{{ __("Name is Required") }}',
            email_required:'{{ __("Email is Required") }}',
        };
    </script>
    <script type="text/javascript" src="{{ asset("libs/ion_rangeslider/js/ion.rangeSlider.min.js") }}"></script>
    <script type="text/javascript" src="{{ asset("libs/fotorama/fotorama.js") }}"></script>
    <script type="text/javascript" src="{{ asset("libs/sticky/jquery.sticky.js") }}"></script>
    <script type="text/javascript" src="{{ asset('module/space/js/single-space.js?_ver='.config('app.asset_version')) }}"></script>

    <script src="{{ asset('libs/ipanorama/src/lib/jquery.min.js') }}"></script>
    <script src="{{ asset('libs/ipanorama/src/jquery.ipanorama.js') }}"></script>
    <script src="{{ asset('libs/ipanorama/src/lib/three.min.js') }}"></script>
    <script src="{{ asset('libs/ipanorama/src/main.js') }}"></script>
@endpush
