@extends('Layout::empty')

{{-- VENDOR BOOKING INVOICE (PDF) --}}
{{-- Route: GET /vendor/booking-history/{id}/invoice --}}

@push('css')
    <style type="text/css">
        html,
        body {
            background: #fff;
            color: #333;
            font-family: inherit;
        }

        .bravo_topbar,
        .bravo_header,
        .bravo_footer {
            display: none !important;
        }

        #invoice-print-zone {
            padding: 30px;
            max-width: 800px;
            margin: 0 auto;
        }

        .invoice-title {
            font-size: 32px;
            font-weight: bold;
            text-align: right;
            text-transform: uppercase;
            margin: 0 0 10px 0;
        }

        .text-right {
            text-align: right;
        }

        .mt-4 {
            margin-top: 1.5rem;
        }

        .mb-4 {
            margin-bottom: 1.5rem;
        }

        .box-info {
            border: 1px solid #0056b3;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .box-title {
            color: #0056b3;
            font-weight: 600;
            background: #fff;
            display: inline-block;
            margin-top: -30px;
            padding: 0 10px;
            font-size: 14px;
        }

        .data-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .data-row span:last-child {
            font-weight: 500;
            color: #111;
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact;
                margin: 0;
                padding: 0;
            }

            #invoice-print-zone {
                width: 100%;
                border: none;
            }
        }
    </style>

    <script>
        window.addEventListener('load', function () {
            setTimeout(function () {
                window.print();
            }, 500);
        });
    </script>
@endpush

@section('content')
    <div id="invoice-print-zone">
        @dd($invoice)
        {{-- HEADER --}}
        <!-- <table width="100%" cellspacing="0" cellpadding="0" class="mb-4">
            <tr>
                <td width="50%" valign="top">
                    @if(!empty($logo = setting_item('logo_invoice_id') ?? setting_item('logo_id')))
                        <img loading='lazy' style="max-height: 50px;" src="{{ get_file_url($logo, 'full') }}"
                            alt="Virtuard Logo">
                    @else
                        <h2>Virtuard</h2> {{-- Fallback if no logo --}}
                    @endif
                    <div class="mt-4" style="font-size: 13px; line-height: 1.6;">
                        <strong>Virtuard Reality Design</strong><br>
                        Copyright © Virtuard Reality Design. Company nr AHU-0175648.AH.01.11,<br>
                        registered in Gianyar, Bali, Indonesia.<br>
                        virtuard.com
                    </div>
                </td>
                <td width="50%" valign="top" class="text-right">
                    <h1 class="invoice-title">INVOICE</h1>
                    <div style="font-size: 14px;">
                        Invoice #: <strong>{{ $invoice['id'] }}</strong><br>
                        Created: <strong>{{ $invoice['created_at'] }}</strong>
                    </div>
                </td>
            </tr>
        </table>

        {{-- GUEST INFORMATION --}}
        <div class="box-info">
            <div class="box-title">Guest Information</div>
            <div class="data-row"><span>Booking ID</span> <span>{{ $invoice['booking_id_transaction'] }}</span></div>
            <div class="data-row"><span>Name</span> <span>{{ $invoice['customer_name'] }}</span></div>
            <div class="data-row"><span>Email</span> <span>{{ $invoice['customer_email'] }}</span></div>
            <div class="data-row"><span>Phone Number</span> <span>{{ $invoice['customer_phone'] }}</span></div>
        </div>

        {{-- BOOKING DETAILS --}}
        <div class="box-info">
            <div class="box-title">Booking Details</div>

            <h3 style="margin: 5px 0 0 0; font-size: 16px;">{{ $invoice['service_title'] }}</h3>
            <p style="margin: 0 0 15px 0; color: #666; font-size: 13px;">{{ $invoice['service_address'] }}</p>

            <div class="data-row"><span>Number of Rooms</span> <span>{{ $invoice['rooms'] }}</span></div>
            <div class="data-row"><span>No. Bed</span> <span>{{ $invoice['bed'] }}</span></div>
            <div class="data-row"><span>No. Bathroom</span> <span>{{ $invoice['bathroom'] }}</span></div>
            <div class="data-row"><span>Nights</span> <span>{{ $invoice['nights'] }}</span></div>
            <div class="data-row"><span>Adults</span> <span>{{ $invoice['adults'] }}</span></div>
            <div class="data-row"><span>Status</span> <span style="color: #F5A623;">{{ $invoice['status'] }}</span></div>
            <div class="data-row"><span>Check in Date</span> <span>{{ $invoice['check_in'] }}</span></div>
            <div class="data-row"><span>Check out Date</span> <span>{{ $invoice['check_out'] }}</span></div>
        </div>

        {{-- BOOKING TRANSACTION --}}
        <div class="box-info">
            <div class="box-title">Booking Transaction</div>
            <div class="data-row"><span>Total</span> <span>{{ $invoice['total'] }}</span></div>
            <div class="data-row"><span>Paid</span> <span>{{ $invoice['paid'] }}</span></div>
            <div class="data-row"><span>Remain</span> <span style="color: #0056b3;">{{ $invoice['remain'] }}</span></div>
        </div> -->

    </div>
@endsection