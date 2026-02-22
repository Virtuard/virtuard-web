@extends('v2.layouts.vendor')

@section('content')
    <div style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1>Business Details</h1>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('vendor2.business.edit', ['id' => $business->id]) }}"
                style="padding: 10px 15px; border: none; background: #fff8e1; color: #f2a600; text-decoration: none; border-radius:5px; font-weight: bold; border: 1px solid #f2a600;">
                Edit Business
            </a>
            <a href="{{ route('vendor2.business.index') }}"
                style="padding: 10px 15px; border: none; background: #f0f0f0; color: #333; text-decoration: none; border-radius:5px; font-weight: bold; border: 1px solid #ddd;">
                Back to List
            </a>
        </div>
    </div>

    <div style="background: white; padding: 30px; border-radius: 10px; border: 1px solid #eee;">
        <h3 style="border-bottom: 2px solid #f0f0f0; padding-bottom: 15px; margin-bottom: 20px;">
            {{ $business->title }}
            <span
                style="font-size: 14px; background: #e6f7ef; color: #28a745; padding: 5px 10px; border-radius: 5px; margin-left: 10px; font-weight: normal;">{{ ucfirst($business->status) }}</span>
        </h3>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
            <div>
                <h4 style="color: #666; margin-bottom: 10px;">Content & Description</h4>
                <div
                    style="background: #fafafa; padding: 15px; border-radius: 5px; border: 1px solid #eee; margin-bottom: 20px;">
                    {!! nl2br(e($business->content)) !!}
                </div>

                <h4 style="color: #666; margin-bottom: 10px;">Location Details</h4>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li style="margin-bottom: 10px;"><strong>Address:</strong> {{ $business->address ?? 'N/A' }}</li>
                    <li style="margin-bottom: 10px;"><strong>Latitude:</strong> {{ $business->map_lat ?? '-' }}</li>
                    <li style="margin-bottom: 10px;"><strong>Longitude:</strong> {{ $business->map_lng ?? '-' }}</li>
                </ul>

                <h4 style="color: #666; margin-bottom: 10px; margin-top: 20px;">Extra Info</h4>
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 10px 0; color: #555;"><strong>Franchising Company</strong></td>
                        <td style="padding: 10px 0; text-align: right;">{{ $business->franchising ?? 'N/A' }}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 10px 0; color: #555;"><strong>Phone</strong></td>
                        <td style="padding: 10px 0; text-align: right;">{{ $business->phone ?? 'N/A' }}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 10px 0; color: #555;"><strong>Website</strong></td>
                        <td style="padding: 10px 0; text-align: right;">
                            @if($business->website)
                                <a href="{{ $business->website }}" target="_blank">{{ $business->website }}</a>
                            @else
                                N/A
                            @endif
                        </td>
                    </tr>
                </table>
            </div>

            <div>
                <h4 style="color: #666; margin-bottom: 10px;">Products (Items)</h4>
                @if(is_array($business->items) && count($business->items) > 0)
                    <ul style="list-style-type: disc; margin-left: 20px; color: #555;">
                        @foreach($business->items as $item)
                            <li>
                                <strong>{{ $item['title'] ?? 'Unnamed Product' }}</strong>
                                @if(isset($item['price']))
                                    - ${{ number_format($item['price'], 2) }}
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p style="color: #888; font-style: italic;">No products attached.</p>
                @endif

                <h4 style="color: #666; margin-bottom: 10px; margin-top: 20px;">Asset References</h4>
                <ul style="list-style: none; padding: 0; margin: 0; color: #888; font-size: 14px;">
                    <li><strong>Ipanorama 360 ID:</strong> {{ $business->ipanorama_id ?? 'None Attached' }}</li>
                </ul>
            </div>
        </div>
    </div>
@endsection