@extends('admin.layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <h3>{{ $panorama->title }}</h3>
            </div>
            <div class="col-md-12 mt-3">
                @include('partials.ipanorama.ipanorama-init')
            </div>
            <div class="col-md-12 mt-3">
                <a href="{{ route('admin.virtuard360.index') }}" class="btn btn-secondary">Back</a>
            </div>
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
    @include('partials.ipanorama.ipanorama-preview-js')
@endpush
