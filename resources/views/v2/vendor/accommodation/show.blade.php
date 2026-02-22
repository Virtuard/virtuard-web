@extends('v2.layouts.vendor')

@section('content')
    <div style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1>Accommodation Details</h1>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('vendor2.accommodation.edit', ['id' => $hotel->id]) }}"
                style="padding: 10px 15px; border: none; background: #fff8e1; color: #f2a600; text-decoration: none; border-radius:5px; font-weight: bold; border: 1px solid #f2a600;">
                Edit Accommodation
            </a>
            <a href="{{ route('vendor2.accommodation.index') }}"
                style="padding: 10px 15px; border: none; background: #f0f0f0; color: #333; text-decoration: none; border-radius:5px; font-weight: bold; border: 1px solid #ddd;">
                Back to List
            </a>
        </div>
    </div>

    {{--
    ========================================================================
    FRONTEND GUIDE: ACCOMMODATION DETAIL PAGE (V2)
    ========================================================================

    Data Model Binded: $hotel
    Controller Method: VendorAccommodationController::show

    Tampilkan data preview dari model $hotel yang terikat:

    1. GENERAL INFO
    - Title: {{ $hotel->title }}
    - Status: {{ $hotel->status }}
    - Phone: {{ $hotel->phone ?? '-' }}
    - Website: {{ $hotel->website ?? '-' }}
    - Created At: {{ $hotel->created_at->format('M d, Y') }}

    2. LOCATION
    - Address: {{ $hotel->address ?? 'N/A' }}
    - Map Coordinates: Lat: {{ $hotel->map_lat }}, Lng: {{ $hotel->map_lng }}

    3. PRICING & BOOKING
    - Price: ${{ number_format($hotel->price, 2) }}
    - Check-in Time: {{ $hotel->check_in_time ?? 'Standard' }}
    - Check-out Time: {{ $hotel->check_out_time ?? 'Standard' }}

    4. VIRTUAL 360
    - Ipanorama ID: {{ $hotel->ipanorama_id ?? 'None Attached' }}

    Bungkus dengan layout CSS atau Vue Component untuk *read-only* detail view
    sesuai sistem V2 framework Frontend!
    ========================================================================
    --}}

    <div style="background: white; padding: 30px; border-radius: 10px; border: 1px solid #eee;">
        <h3 style="border-bottom: 2px solid #f0f0f0; padding-bottom: 15px; margin-bottom: 20px;">{{ $hotel->title }} <span
                style="font-size: 14px; background: #e6f7ef; color: #28a745; padding: 5px 10px; border-radius: 5px; margin-left: 10px; font-weight: normal;">{{ ucfirst($hotel->status) }}</span>
        </h3>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
            <div>
                <h4 style="color: #666; margin-bottom: 10px;">Description</h4>
                <div
                    style="background: #fafafa; padding: 15px; border-radius: 5px; border: 1px solid #eee; margin-bottom: 20px;">
                    {!! nl2br(e($hotel->content)) !!}
                </div>

                <h4 style="color: #666; margin-bottom: 10px;">Location Details</h4>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li style="margin-bottom: 10px;"><strong>Address:</strong> {{ $hotel->address ?? 'N/A' }}</li>
                    <li style="margin-bottom: 10px;"><strong>Latitude:</strong> {{ $hotel->map_lat ?? '-' }}</li>
                    <li style="margin-bottom: 10px;"><strong>Longitude:</strong> {{ $hotel->map_lng ?? '-' }}</li>
                </ul>
            </div>

            <div>
                <h4 style="color: #666; margin-bottom: 10px;">Pricing & Info</h4>
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 10px 0; color: #555;"><strong>Base Price</strong></td>
                        <td style="padding: 10px 0; text-align: right;">${{ number_format($hotel->price, 2) }}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 10px 0; color: #555;"><strong>Check-In</strong></td>
                        <td style="padding: 10px 0; text-align: right;">{{ $hotel->check_in_time ?? 'N/A' }}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 10px 0; color: #555;"><strong>Check-Out</strong></td>
                        <td style="padding: 10px 0; text-align: right;">{{ $hotel->check_out_time ?? 'N/A' }}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 10px 0; color: #555;"><strong>Min Days Stay</strong></td>
                        <td style="padding: 10px 0; text-align: right;">{{ $hotel->min_day_stays ?? 1 }}</td>
                    </tr>
                </table>

                <h4 style="color: #666; margin-bottom: 10px;">SEO Settings</h4>
                <ul style="list-style: none; padding: 0; margin: 0; color: #888; font-size: 14px;">
                    <li><strong>SEO Title:</strong> {{ $hotel->seo_title ?? 'Not Set' }}</li>
                    <li><strong>SEO Desc:</strong> {{ $hotel->seo_desc ?? 'Not Set' }}</li>
                </ul>
            </div>
        </div>
    </div>
@endsection