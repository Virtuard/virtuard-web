@extends('v2.layouts.app')

@section('content')
    {{-- PRIVACY POLICY PAGE --}}
    {{-- Route: GET /privacy-policy --}}
    {{-- Available data: $page_title, $page_description --}}

    {{-- Hero Section --}}
    <h1>{{ $page_title }}</h1>
    <p>{{ $page_description }}</p>

    {{-- Content Section with Table of Contents --}}
    {{-- TODO: Frontend will implement sidebar TOC + content sections --}}
@endsection