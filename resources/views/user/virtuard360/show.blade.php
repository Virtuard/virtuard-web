@extends('layouts.user')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <h3>{{ $panorama->title }}</h3>
        </div>
        <div class="col-md-12 mt-3">
            <input type="hidden" id="data-panorama" data-code="{{ $panorama->code ?? '' }}">
            <div id="mypanorama"></div>
        </div>
        <div class="col-md-12 mt-3">
            <a href="{{ route('user.virtuard-360.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
@endsection
@push('css')
    @include('partials.ipanorama.ipanorama-css')
    <style>
        #mypanorama {
            width: 100%;
            height: 450px;
        }
    </style>
@endpush
@push('js')
    @include('partials.ipanorama.ipanorama-js')
    <script>
        $(document).ready(function() {
            previewPanorama();
        });

        function previewPanorama() {
            let panoramaCode = $('#data-panorama').data('code');
            panoramaCode = JSON.stringify(panoramaCode);
            panoramaCode = panoramaCode.replaceAll(`upload/`, '/uploads/ipanoramaBuilder/upload/');
            panoramaCode = JSON.parse(panoramaCode)
            $(`#mypanorama`).ipanorama(panoramaCode);
        }
    </script>
@endpush
