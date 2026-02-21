@extends('v2.layouts.vendor')

@section('content')
    {{-- VENDOR REFERRAL PAGE --}}
    {{-- Route: GET /vendor/referral --}}

    {{--
    ============================================
    DATA TERSEDIA UNTUK FRONTEND:
    ============================================
    $referral_link → "https://virtaurd.com/register?ref=12" (dipakai untuk tombol Copy Referral Link)

    $referrals → Paginator objects. Tiap baris tabel berisi:
    - id: 18
    - type: "Plan Subscription" | "Hotel Service" | "Space Service" dll
    - detail: "Basic Plan" | "Oceanview Luxury Villa"
    - user: "John Doe"
    - date: "Mar 05, 2026"
    - commission: "$15.00" (Sudah formatted money)

    $available_plans → Array Object dari DB untuk opsi `<select name="plan_type">`:
        [{id: 1, title: 'Trial'}, {id: 2, title: 'Basic'}]

        $available_services → Array Statis nama layanan untuk opsi `<select name="service_type">`:
            ['custom 360 tour creation', 'vr shooting service', 'hosting extension']

            $filters → Filter aktif saat ini yang dipasang di URL GET parameters:
            - type: "plan" | "service"
            - sort: "latest" | "this_month"
            - plan_type: "all" | (Integer ID)
            - service_type: "all" | "custom 360 tour creation" | dsb

            ============================================
            PENGGUNAAN PENCARIAN & FILTER
            ============================================
            Kirim form GET di bawah dengan parameter yang diperlukan.
            Opsi "Plan Type" hanya akan muncul/relevan jika type = "plan".
            Opsi "Service Type" hanya akan muncul/relevan jika type = "service".
            --}}

            <div style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h1>{{ $page_title }}</h1>
                <button onclick="navigator.clipboard.writeText('{{ $referral_link }}'); alert('Referral Link Copied!')"
                    style="padding: 10px; border: 1px solid #ddd; background: #fff; cursor: pointer; border-radius:5px;">
                    🔗 Copy Referral Link
                </button>
            </div>

            {{-- FILTER FORM --}}
            <form method="GET" action="" style="display:flex; gap: 15px; margin-bottom: 25px;">
                <label>Type:</label>
                <select name="type" onchange="this.form.submit()">
                    <option value="plan" {{ $filters['type'] == 'plan' ? 'selected' : '' }}>Plan</option>
                    <option value="service" {{ $filters['type'] == 'service' ? 'selected' : '' }}>Service</option>
                </select>

                <label>Sort by:</label>
                <select name="sort" onchange="this.form.submit()">
                    <option value="latest" {{ $filters['sort'] == 'latest' ? 'selected' : '' }}>Latest</option>
                    <option value="this_month" {{ $filters['sort'] == 'this_month' ? 'selected' : '' }}>This Month</option>
                </select>

                @if($filters['type'] == 'plan')
                    <label>Plan Type:</label>
                    <select name="plan_type" onchange="this.form.submit()">
                        <option value="all" {{ $filters['plan_type'] == 'all' ? 'selected' : '' }}>All Plans</option>
                        @foreach($available_plans as $p)
                            <option value="{{ $p->id }}" {{ $filters['plan_type'] == $p->id ? 'selected' : '' }}>{{ $p->title }}
                            </option>
                        @endforeach
                    </select>
                @else
                    <label>Service Type:</label>
                    <select name="service_type" onchange="this.form.submit()">
                        <option value="all" {{ $filters['service_type'] == 'all' ? 'selected' : '' }}>All Services</option>
                        @foreach($available_services as $st)
                            {{-- $st is already a string here --}}
                            <option value="{{ $st }}" {{ $filters['service_type'] == $st ? 'selected' : '' }}>{{ ucwords($st) }}
                            </option>
                        @endforeach
                    </select>
                @endif
            </form>

            {{-- TABLE SECTION --}}
            <table border="1" style="width: 100%; text-align: left; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f9f9f9;">
                        <th style="padding: 10px;">ID</th>
                        <th style="padding: 10px;">Type</th>
                        <th style="padding: 10px;">Detail / Title</th>
                        <th style="padding: 10px;">User</th>
                        <th style="padding: 10px;">Date Joined / Booked</th>
                        <th style="padding: 10px;">Commission Earned</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($referrals as $item)
                        <tr>
                            <td style="padding: 10px;">{{ $item['id'] }}</td>
                            <td style="padding: 10px;">{{ $item['type'] }}</td>
                            <td style="padding: 10px;">{{ $item['detail'] }}</td>
                            <td style="padding: 10px;">{{ $item['user'] }}</td>
                            <td style="padding: 10px;">{{ $item['date'] }}</td>
                            <td style="padding: 10px;"><b>{{ $item['commission'] }}</b></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding: 20px; text-align: center;">No referrals found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- PAGINATION --}}
            <div style="margin-top: 30px;">
                {{ $referrals->links() }}
            </div>

@endsection