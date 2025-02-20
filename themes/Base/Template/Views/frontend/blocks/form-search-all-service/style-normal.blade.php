@push('css')
    <style>
        .nav.nav-tabs {
            display: grid !important;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        @media(max-width: 400px) {
            .nav.nav-tabs {
                grid-template-columns: repeat(1, 1fr);
            }
        }

        /* @media(max-width: 350px) {
            .nav.nav-tabs {
                grid-template-columns: repeat(1, 1fr);
            }
        } */
    </style>

    <!-- iPanorama -->
    {{-- @include('partials.ipanorama.ipanorama-css') --}}
@endpush

@if(!empty($style) and $style == "carousel" and !empty($list_slider))
    <div class="effect">
        <div class="owl-carousel">
            @foreach($list_slider as $item)
                @php $img = get_file_url($item['bg_image'],'full') @endphp
                <div class="item">
                    <div class="item-bg" style="background-image: linear-gradient(0deg,rgba(0, 0, 0, 0.2),rgba(0, 0, 0, 0.2)),url('{{$img}}') !important"></div>
                </div>
            @endforeach
        </div>
    </div>
@endif
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="text-heading">{{$title}}</h1>
            <div class="sub-heading">{{$sub_title}}</div>

            {{-- Ipanorama preview --}}
            <div>
                <input type="hidden" id="data-panorama2" data-code="{{ $get_hotel->ipanorama->code }}"
                data-user_id="{{ $get_hotel->ipanorama->user_id }}">
        
                <div id="mypanorama2" class="mypanorama-preview"></div>
            </div>

            <a href="{{ route('user.virtuard-360.index') }}" class="btn btn-primary w-100 mt-2">
                <i class="bi bi-search"></i> {{ __('Create your own for free!') }}
            </a>

            <p class="text-center mt-2 text-white" style="font-size: 16px;">Or</p>

            <button type="button" class="btn btn-info w-100" id="searchButton">
                <i class="bi bi-search"></i> {{ __('Search') }}
            </button>

            @if(empty($hide_form_search))
                <div class="g-form-control">
                    <ul class="nav nav-tabs mb-2" role="tablist">
                        @if(!empty($service_types))
                            @php $number = 0; @endphp
                            @foreach ($service_types as $service_type)
                                @php
                                    $allServices = get_bookable_services();
                                    if(empty($allServices[$service_type])) continue;
                                    $module = new $allServices[$service_type];
                                @endphp
                                <li role="bravo_{{$service_type}}" class="text-center">
                                    <a href="#bravo_{{$service_type}}" class="@if($number == 0) active @endif" aria-controls="bravo_{{$service_type}}" role="tab" data-toggle="tab">
                                        <i class="{{ $module->getServiceIconFeatured() }}"></i>
                                        {{ !empty($modelBlock["title_for_".$service_type]) ? $modelBlock["title_for_".$service_type] : $module->getModelName() }}
                                    </a>
                                </li>
                                @php $number++; @endphp
                            @endforeach
                        @endif
                    </ul>
                    <div class="tab-content">
                        @if(!empty($service_types))
                            @php $number = 0; @endphp
                            @foreach ($service_types as $service_type)
                                @php
                                    $allServices = get_bookable_services();
                                    if(empty($allServices[$service_type])) continue;
                                    $module = new $allServices[$service_type];
                                @endphp
                                <div role="tabpanel" id="scrollTarget" class="tab-pane @if($number == 0) active @endif" id="bravo_{{$service_type}}" style="max-width: 100%;">
                                    @include(ucfirst($service_type).'::frontend.layouts.search.form-search')
                                </div>
                                @php $number++; @endphp
                            @endforeach
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('js')
    {{-- @include('partials.ipanorama.ipanorama-js-no-jquery') --}}
    <script>
        $(document).ready(function() {
            previewPanorama2();

            $('#searchButton').on('click', function() {
                $('html, body').animate({
                    scrollTop: $('#scrollTarget').offset().top
                }, 300); // 800ms for smooth scroll
            });
        });

        function previewPanorama2() {
            let panoramaCode = $('#data-panorama2').data('code');
            let userId = $('#data-panorama2').data('user_id');
            panoramaCode.auto_rotate = true;
            panoramaCode.autoRotate = true;
            // panoramaCode.showFullscreenCtrl = false;
            // panoramaCode.showZoomCtrl = false;
            // panoramaCode.showSceneNextPrevCtrl = false;
            // panoramaCode.hotSpotBelowPopover = false;
            // panoramaCode.popover = false;
            panoramaCode.title = false;

            panoramaCode = JSON.stringify(panoramaCode);
            panoramaCode = panoramaCode.replaceAll(`upload/`, `/uploads/ipanoramaBuilder/upload/${userId}/`);
            panoramaCode = panoramaCode.replaceAll(`/uploads/ipanoramaBuilder/upload/${userId}/${userId}/`, `/uploads/ipanoramaBuilder/upload/${userId}/`);
            panoramaCode = JSON.parse(panoramaCode)
            $(`#mypanorama2`).ipanorama(panoramaCode);
        }
    </script>
@endpush
