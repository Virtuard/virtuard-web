@extends('layouts.app')

@section('content')
    
<div class="page-profile-content page-template-content">
    <div class="container">
        <div class="">
            <div class="row">
                {{-- <div class="col-md-3">
                    @include('User::frontend.profile.sidebar')
                </div> --}}
                <div class="col-md-12">
                    {{-- <div class="profile-container"> --}}
                        @include('User::frontend.profile.main-profile')
                        @include('User::frontend.profile.services')
                        @include('User::frontend.profile.reviews')
                    {{-- </div> --}}
                </div>
            </div>
        </div>
        </div>
    </div>
@endsection

@push('css')
    @include('vendor.ipanorama.demo.includes.ipanorama-style')
@endpush
@push('js')
    @include('vendor.ipanorama.demo.includes.ipanorama-script')
@endpush
