@extends('v2.layouts.vendor')

@section('content')
    <div style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1>Property Details</h1>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('vendor2.property.edit', ['id' => $property->id]) }}"
                style="padding: 10px 15px; border: none; background: #fff8e1; color: #f2a600; text-decoration: none; border-radius:5px; font-weight: bold; border: 1px solid #f2a600;">
                Edit Property
            </a>
            <a href="{{ route('vendor2.property.index') }}"
                style="padding: 10px 15px; border: none; background: #f0f0f0; color: #333; text-decoration: none; border-radius:5px; font-weight: bold; border: 1px solid #ddd;">
                Back to List
            </a>
        </div>
    </div>

    <div style="background: white; padding: 30px; border-radius: 10px; border: 1px solid #eee;">
        <h3 style="border-bottom: 2px solid #f0f0f0; padding-bottom: 15px; margin-bottom: 20px;">
            {{ $property->title }}
            <span
                style="font-size: 14px; background: #e6f7ef; color: #28a745; padding: 5px 10px; border-radius: 5px; margin-left: 10px; font-weight: normal;">{{ ucfirst($property->status) }}</span>
        </h3>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
            <div>
                <h4 style="color: #666; margin-bottom: 10px;">Content & Description</h4>
                <div
                    style="background: #fafafa; padding: 15px; border-radius: 5px; border: 1px solid #eee; margin-bottom: 20px;">
                    {!! nl2br(e($property->content)) !!}
                </div>

                <h4 style="color: #666; margin-bottom: 10px;">Location Details</h4>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li style="margin-bottom: 10px;"><strong>Address:</strong> {{ $property->address ?? 'N/A' }}</li>
                    <li style="margin-bottom: 10px;"><strong>Latitude:</strong> {{ $property->map_lat ?? '-' }}</li>
                    <li style="margin-bottom: 10px;"><strong>Longitude:</strong> {{ $property->map_lng ?? '-' }}</li>
                </ul>
            </div>

            <div>
                <h4 style="color: #666; margin-bottom: 10px;">Space Extra Info</h4>
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 10px 0; color: #555;"><strong>Base Price</strong></td>
                        <td style="padding: 10px 0; text-align: right;">${{ number_format($property->price, 2) }}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 10px 0; color: #555;"><strong>Bed & Bath</strong></td>
                        <td style="padding: 10px 0; text-align: right;">{{ $property->bed ?? 0 }} Bed,
                            {{ $property->bathroom ?? 0 }} Bath</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 10px 0; color: #555;"><strong>Flooring</strong></td>
                        <td style="padding: 10px 0; text-align: right;">{{ $property->flooring ?? 'N/A' }}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 10px 0; color: #555;"><strong>Land / Built Sq. Meters</strong></td>
                        <td style="padding: 10px 0; text-align: right;">{{ $property->square_land ?? 0 }} /
                            {{ $property->square ?? 0 }}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 10px 0; color: #555;"><strong>Agency</strong></td>
                        <td style="padding: 10px 0; text-align: right;">{{ $property->agency ?? 'N/A' }}</td>
                    </tr>
                </table>

                <h4 style="color: #666; margin-bottom: 10px;">Asset References</h4>
                <ul style="list-style: none; padding: 0; margin: 0; color: #888; font-size: 14px;">
                    <li><strong>Ipanorama 360 ID:</strong> {{ $property->ipanorama_id ?? 'None Attached' }}</li>
                </ul>
            </div>
        </div>
    </div>
@endsection