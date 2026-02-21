@extends('v2.layouts.vendor')

@section('content')

    {{-- Title Phase --}}
    @if(empty($panorama))
        <div style="padding: 15px; background: #fff3cd; color: #856404; border-radius: 5px; margin-bottom: 20px;">
            <b>You must first create a title for your Virtuard 360!</b>
        </div>

        <form action="{{ route('vendor2.virtuard360.store') }}" method="POST"
            style="background: white; padding: 20px; border-radius: 10px; border: 1px solid #eee; display:flex; gap: 15px; align-items: flex-end;">
            @csrf
            {{-- Original required V1 inputs for routing callbacks properly --}}
            <input type="hidden" name="page" value="add">
            <input type="hidden" name="wstep" value="2">

            <div style="flex: 1;">
                <label style="display:block; margin-bottom: 5px;">Title</label>
                <input type="text" name="title" placeholder="Enter virtuard asset title..." required
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
            </div>

            <button type="submit"
                style="padding: 10px 20px; border: none; background: #007bff; color: white; border-radius:5px; cursor: pointer;">
                Submit Title
            </button>
        </form>
    @else
        {{-- Render this block if it successfully redirected here via edit logic after title creation --}}
        Fallback template, should not hit this natively as adding a title redirects to the edit view.
    @endif

@endsection