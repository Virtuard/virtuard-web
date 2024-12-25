@extends('layouts.app')
@push('css')
    <link href="{{ asset('dist/frontend/module/business/css/space.css?_ver='.config('app.asset_version')) }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset("libs/ion_rangeslider/css/ion.rangeSlider.min.css") }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset("libs/fotorama/fotorama.css") }}"/>
    
    <!-- iPanorama -->
    @include('partials.ipanorama.ipanorama-css')
@endpush
@section('content')
    <div class="bravo_detail_space">
        @include('Layout::parts.bc')
        @include('Business::frontend.layouts.details.business-banner')
        <div class="bravo_content">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 col-lg-9">
                        @include('partials.listing.share-action')
                        @include('user.partials.listing.ipanorama')
                    </div>
                    <div class="col-md-12 col-lg-9">
                        @php $review_score = $row->review_data @endphp
                        @include('Business::frontend.layouts.details.business-detail')
                        @include('Business::frontend.layouts.details.business-review')
                    </div>
                    <div class="col-md-12 col-lg-3">
                        @include('Tour::frontend.layouts.details.vendor')
                        @if(enable_referral_sell($row))
                            @include('partials.listing.sell-button')
                        @endif
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
        {{-- @if($row->author->checkUserPlan()) --}}
        @include('Business::frontend.layouts.details.business-form-book-mobile')
        {{-- @endif --}}
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
                zoom:22,
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
    <script type="text/javascript" src="{{ asset('module/business/js/single-space.js?_ver='.config('app.asset_version')) }}"></script>

    @include('partials.ipanorama.ipanorama-js-no-jquery')
    @if (is_display_panorama_listing($row))
    @include('partials.ipanorama.ipanorama-preview-js')
    @endif
    @if(enable_referral_sell($row))
        @include('partials.listing.copy-referral-script')
    @endif
@endpush
