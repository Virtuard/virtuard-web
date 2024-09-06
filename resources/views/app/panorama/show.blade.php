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
                        <div id="service-loading" class="text-center my-5" style="display: none;">
                            <div class="spinner-border" role="status">
                              <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                        @if (is_display_panorama_listing($row))
                            <input type="hidden" id="panorama_id" value="{{ $row->ipanorama_id }}">

                            <div id="mypanorama" class="mypanorama-preview" style="display: none;"></div>
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
        <script>
            $(document).ready(function() {
                previewPanorama();
            });
            
            function previewPanorama() {
                $('#service-loading').show();

                let id = $('#panorama_id').val();
                $.ajax({
                    url: `/panorama/compress/${id}`,
                    success: function(res) {
                        if (res.data) {
                            let code = JSON.parse(res.data.code);
                            $(`#mypanorama`).show();
                            $(`#mypanorama`).ipanorama(code);
                            $('#service-loading').hide();
                        }
                    },
                    error: function(xhr) {
                        //
                    }
                });
            }
        </script>
    @endif
@endpush
