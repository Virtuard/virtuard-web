@extends ('layouts.app')
@section('content')
    <div class="bravo_detail_event">
        @include('Layout::parts.bc')
        <div class="bravo_content">
            <div class="container">
                <div class="row my-2">
                    <div class="col-md-12">
                        <h2>{{ $row->title }}</h2>
                    </div>
                    <div class="col-md-9 mb-5">
                        @if (is_display_panorama_listing($row))
                            <input type="hidden" id="data-panorama" data-code="{{ $panorama['code'] }}"
                                data-user_id="{{ $panorama['user_id'] }}">

                            <div id="mypanorama" class="mypanorama-preview"></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('css')
    <!-- iPanorama -->
    @include('partials.ipanorama.ipanorama-css')
@endpush
@push('js')
    @include('partials.ipanorama.ipanorama-js-no-jquery')
    @if (is_display_panorama_listing($row))
        @include('partials.ipanorama.ipanorama-preview-js')
    @endif
    <script>
        $(document).ready(function() {
            previewPanorama();
        });

        function previewPanorama() {
            let panoramaCode = $('#data-panorama').data('code');
            $(`#mypanorama`).ipanorama(panoramaCode);
        }
    </script>
@endpush
