@push('css')
    <style>
        .bravo-form-search-all {
            height: 110vh;
        }
        
        #mypanorama {
            border: none !important;
            filter: brightness(0.6);
            height: 110vh;
            pointer-events: none;
            position: absolute !important;
            top: 90px !important;
            z-index: -10 !important;
        }

        .bravo-content {
            margin-top: -70px !important;
        }

        @media(max-width: 768px) {
            .bravo-form-search-all, #mypanorama {
                height: 140% !important;
                /* margin-bottom: 250px; */
            }
        }

        @media(max-width: 400px) {
            .bravo-form-search-all, #mypanorama {
                height: 150% !important;
                /* margin-bottom: 150px; */
            }
        }
    </style>    

    <!-- iPanorama -->
    @include('partials.ipanorama.ipanorama-css')
@endpush

{{-- <div class="bravo-form-search-all {{$style}} @if(!empty($style) and $style == "carousel") bravo-form-search-slider @endif" @if(empty($style)) style="background-image: linear-gradient(0deg,rgba(0, 0, 0, 0.2),rgba(0, 0, 0, 0.2)),url('{{$bg_image_url}}') !important" @endif> --}}
<div class="bravo-form-search-all {{$style}} @if(!empty($style) and $style == "carousel") bravo-form-search-slider @endif">
    <div class="bravo-content">
        @if(in_array($style,['carousel','']))
            @include("Template::frontend.blocks.form-search-all-service.style-normal")
        @endif
        @if($style == "carousel_v2")
            @include("Template::frontend.blocks.form-search-all-service.style-slider-ver2")
        @endif
    </div>

    <input type="hidden" id="data-panorama" data-code="{{ $get_hotel->ipanorama->code }}"
    data-user_id="{{ $get_hotel->ipanorama->user_id }}">

    <div id="mypanorama" class="mypanorama-preview"></div>
</div>

@push('js')
    @include('partials.ipanorama.ipanorama-js-no-jquery')
    <script>
        $(document).ready(function() {
            previewPanorama();
        });

        function previewPanorama() {
            let panoramaCode = $('#data-panorama').data('code');
            let userId = $('#data-panorama').data('user_id');
            panoramaCode.auto_rotate = true;
            panoramaCode.autoRotate = true;
            panoramaCode.showFullscreenCtrl = false;
            panoramaCode.showZoomCtrl = false;
            panoramaCode.showSceneNextPrevCtrl = false;
            panoramaCode.hotSpotBelowPopover = false;
            panoramaCode.popover = false;
            panoramaCode.title = false;
            panoramaCode.minFov = 0;
            // console.log(panoramaCode)
            panoramaCode = JSON.stringify(panoramaCode);
            panoramaCode = panoramaCode.replaceAll(`upload/`, `/uploads/ipanoramaBuilder/upload/${userId}/`);
            panoramaCode = panoramaCode.replaceAll(`/uploads/ipanoramaBuilder/upload/${userId}/${userId}/`, `/uploads/ipanoramaBuilder/upload/${userId}/`);
            panoramaCode = JSON.parse(panoramaCode)
            $(`#mypanorama`).ipanorama(panoramaCode);
        }
    </script>
@endpush
