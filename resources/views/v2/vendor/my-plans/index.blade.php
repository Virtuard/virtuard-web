@extends('v2.layouts.vendor')

@section('content')
    {{-- VENDOR MY PLANS PAGE --}}
    {{-- Route: GET /vendor/my-plans --}}

    {{--
    ============================================
    DATA TERSEDIA UNTUK FRONTEND:
    ============================================
    $user_plans → LengthAwarePaginator. Tiap baris berisi:
    id: 12
    plan_name: "Basic Subscription"
    expiry: "31 Dec 2025" (Bisa juga "Lifetime" text)
    total_service: "Unlimited" atau Angka
    total_ipanorama: 3
    price: "$10.00"
    status: "Active" | "Expired" | "Cancelled"

    $all_plans → Daftar lengkap Plan (tersedia di DB) untuk mengisi dropdown filter.
    Bentuk datanya [ {id: 1, title: 'Trial'}, {id: 2, title: 'Basic'} ]

    $filters → Filter aktif saat ini:
    sort: "latest_expiry" | "oldest_expiry"
    status: "all" | "active" | "expired" | "cancelled"
    plan_id: "all" | integer plan id

    $renew_plan_url → Rute global (contoh: /plan atau /pricing) ketika vendor menekan tombol Renew Plan di pojok kanan atas.

    ============================================
    PENGGUNAAN FILTER:
    ============================================
    Gunakan form method GET. Contoh struktur parameter di URL:
    /vendor/my-plans?sort=latest_expiry&status=active&plan_id=all
    --}}

    {{-- Placeholder Skeleton Custom UI --}}
    <h1>{{ $page_title }}</h1>

    <div style="display:flex; justify-content: space-between; align-items: center;">
        <div>
            <form method="GET" action="">
                <label>Sort by:</label>
                <select name="sort" onchange="this.form.submit()">
                    <option value="latest_expiry" {{ $filters['sort'] == 'latest_expiry' ? 'selected' : '' }}>Latest Expiry
                    </option>
                    <option value="oldest_expiry" {{ $filters['sort'] == 'oldest_expiry' ? 'selected' : '' }}>Oldest Expiry
                    </option>
                </select>

                <label>Status:</label>
                <select name="status" onchange="this.form.submit()">
                    <option value="all" {{ $filters['status'] == 'all' ? 'selected' : '' }}>All Status</option>
                    <option value="active" {{ $filters['status'] == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="expired" {{ $filters['status'] == 'expired' ? 'selected' : '' }}>Expired</option>
                    <option value="cancelled" {{ $filters['status'] == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>

                <label>Plan:</label>
                <select name="plan_id" onchange="this.form.submit()">
                    <option value="all" {{ $filters['plan_id'] == 'all' ? 'selected' : '' }}>All Plan</option>
                    @foreach($all_plans as $plan)
                        <option value="{{ $plan->id }}" {{ $filters['plan_id'] == $plan->id ? 'selected' : '' }}>
                            {{ $plan->title }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        <div>
            <a href="{{ $renew_plan_url }}"
                style="padding: 10px 20px; background: blue; color: white; text-decoration: none;">Renew Plan</a>
        </div>
    </div>

    <table border="1" style="width: 100%; margin-top: 20px; text-align: left;">
        <thead>
            <tr>
                <th>Plan Name</th>
                <th>Expiry</th>
                <th>Total Service</th>
                <th>Total ipanorama</th>
                <th>Price</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($user_plans as $row)
                <tr>
                    <td>{{ $row['plan_name'] }}</td>
                    <td>{{ $row['expiry'] }}</td>
                    <td>{{ $row['total_service'] }}</td>
                    <td>{{ $row['total_ipanorama'] }}</td>
                    <td>{{ $row['price'] }}</td>
                    <td>
                        <span style="padding: 5px; 
                                             background: {{ $row['status'] == 'Active' ? 'lightgreen' : 'lightpink' }};">
                            {{ $row['status'] }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top:20px;">
        {{ $user_plans->links() }}
    </div>

@endsection