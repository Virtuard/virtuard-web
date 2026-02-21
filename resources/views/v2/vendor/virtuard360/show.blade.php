@extends('v2.layouts.vendor')

@section('content')
    <div style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1>{{ $page_title }}</h1>
        <div style="display:flex; gap: 10px;">
            <a href="{{ route('vendor2.virtuard360.index') }}"
                style="padding: 10px; border: 1px solid #ddd; background: #fff; text-decoration: none; color: black; border-radius:5px;">
                Back to Asset Page
            </a>
        </div>
    </div>

    <div style="background: white; padding: 20px; border-radius: 10px; border: 1px solid #eee;">
        <div style="margin-bottom: 20px;">
            <label style="display:block; font-size: 13px; color: #666; margin-bottom: 5px;">Virtuard Asset Title</label>
            <input type="text" value="{{ $panorama->title }}" readonly
                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; background: #f9f9f9;">
        </div>

        <div>
            <label style="display:block; font-size: 13px; color: #666; margin-bottom: 5px;">Virtuard Asset 360°</label>
            <div style="border: 1px solid #ddd; border-radius: 10px; overflow: hidden; height: 600px;">
                {{-- Embed the iPanorama Viewer using the original preview route so functionality remains intact --}}
                <iframe src="{{ route('user.virtuard-360.show', $panorama->id) }}" width="100%" height="100%"
                    frameborder="0" allowfullscreen></iframe>
            </div>
        </div>
    </div>
@endsection