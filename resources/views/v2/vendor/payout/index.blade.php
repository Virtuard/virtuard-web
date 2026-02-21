@extends('v2.layouts.vendor')

@section('content')
    {{-- VENDOR PAYOUT PAGE --}}
    {{-- Route: GET /vendor/payouts --}}

    {{--
    ============================================
    DATA TERSEDIA UNTUK FRONTEND:
    ============================================
    $summary → Array berisi ringkasan saldo:
    available_balance: "$150.00" (Sudah diformat mata uang)
    total_pending: "$235.00"
    total_paid: "$328.00"
    raw_available_balance: 150 (Unformatted float, berguna untuk validasi batas maksimal input di frontend)

    $all_methods → Array berisi semua metode payout dari Admin (e.g. Bank Transfer, Paypal, Stripe)
    Berguna untuk di-loop di dalam form "Set Up Payout Account" maupun dropdown "Request Payout".
    id: "bank_transfer"
    name: "Bank Transfer"

    $payout_accounts → Array/Object berisi info akun yang sudah disetup user sebelumnya.
    e.g. $payout_accounts['bank_transfer'] akan berisi string nomor rekening bank user.
    Bisa dipakai untuk pre-fill input form di "Set Up Payout Account" modal.

    ============================================
    AKSI FORM PADA MODAL
    ============================================
    1. MODAL "Set Up Payout Account"
    Gunakan $setup_account_action (/vendor/payouts/setup)
    Method: POST
    Field Format:
    name="payout_accounts[bank_transfer]" -> isinya string text/rekening
    name="payout_accounts[paypal]" -> isinya email
    name="payout_accounts[stripe]" -> isinya konektor/info

    2. MODAL "Request Payout"
    Gunakan $request_payout_action (/vendor/payouts/request)
    Method: POST
    Fields Required:
    - name="amount" (Number, maksimal dibatasi oleh raw_available_balance)
    - name="payout_method" (Value dr dropdown, e.g. "bank_transfer" atau "paypal")
    - name="note" (Teks opsional 0/200 karakter)

    Note: Kedua endpoint di atas bisa menerima request biasa maupun AJAX (akan me-return JSON { status: true|false, message:
    "..." }).
    --}}

    {{-- Placeholder Skeleton Custom UI --}}
    <h1>{{ $page_title }}</h1>

    <div
        style="display:flex; justify-content: space-between; border-bottom: 2px solid #ccc; padding-bottom: 20px; margin-bottom: 20px;">
        <div>
            <p>Available Balance</p>
            <h2>{{ $summary['available_balance'] }}</h2>
        </div>
        <div>
            <p>Total Pending</p>
            <h2>{{ $summary['total_pending'] }}</h2>
        </div>
        <div>
            <p>Total Paid</p>
            <h2>{{ $summary['total_paid'] }}</h2>
        </div>
    </div>

    {{-- ACTIONS --}}
    <div style="display:flex; gap: 20px;">
        {{-- Request Payout Trigger --}}
        <div style="flex:1; border: 1px solid #ddd; padding: 40px; text-align:center;">
            <h3>Request Payout</h3>
            <p>Withdraw your available balance to your registered bank account quickly and securely.</p>
            <button onclick="document.getElementById('modal-request').style.display='block'">Request Payout</button>
        </div>

        {{-- Setup Account Trigger --}}
        <div style="flex:1; border: 1px solid #ddd; padding: 40px; text-align:center;">
            <h3>Set Up Payout Account</h3>
            <p>Add your bank account details to receive payouts from your earnings.</p>
            <button onclick="document.getElementById('modal-setup').style.display='block'">Setup Account</button>
        </div>
    </div>

    {{-- =================================== --}}
    {{-- MOCK MODALS (FOR REFERENCE) --}}
    {{-- =================================== --}}
    <div id="modal-setup" style="display:none; background: #f4f4f4; padding: 20px; margin-top:20px;">
        <h4>Set Up Payout Account</h4>
        <form action="{{ $setup_account_action }}" method="POST">
            @csrf
            {{-- Loop available methods from DB settings --}}
            @foreach($all_methods as $method)
                <label>{{ $method['name'] ?? $method['id'] }}</label><br>
                <input type="text" name="payout_accounts[{{ $method['id'] }}]"
                    value="{{ $payout_accounts[$method['id']] ?? '' }}"
                    placeholder="Enter {{ $method['name'] ?? $method['id'] }} account info"><br><br>
            @endforeach
            <button type="submit">Save Changes</button>
        </form>
    </div>

    <div id="modal-request" style="display:none; background: #f4f4f4; padding: 20px; margin-top:20px;">
        <h4>Request Payout</h4>
        <form action="{{ $request_payout_action }}" method="POST">
            @csrf
            <label>Payout Amount * (Max: {{ $summary['raw_available_balance'] }})</label><br>
            <input type="number" name="amount" max="{{ $summary['raw_available_balance'] }}" required><br><br>

            <label>Payout Method *</label><br>
            <select name="payout_method" required>
                <option value="">Select payout method</option>
                @foreach($all_methods as $method)
                    <option value="{{ $method['id'] }}">{{ $method['name'] ?? $method['id'] }}</option>
                @endforeach
            </select><br><br>

            <label>Note</label><br>
            <textarea name="note" maxlength="200"></textarea><br><br>

            <button type="submit">Request Payout</button>
        </form>
    </div>

@endsection