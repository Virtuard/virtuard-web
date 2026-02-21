@extends('v2.layouts.vendor')

@section('content')
    {{-- VENDOR DETAIL ENQUIRY / REPLY PAGE --}}
    {{-- Route: GET /vendor/enquiry-report/{id}/reply --}}

    {{--
    ============================================
    DATA TERSEDIA UNTUK FRONTEND:
    ============================================
    $enquiry → Array berisi info customer yang bertanya:
    id: 82
    name: "Aditya Prayatna"
    email: "aditya...gmail.com"
    phone: "+6281238484005"
    note: "I want to rent a Fortuner. If possible, monthly or yearly."

    $replies → Array (atau object mapped) berisi daftar balasan (Recent Updates):
    List ini sudah di-sort dari yang terbaru. Tiap itemnya berisi:
    id: 123
    user_name: "Michael Anderson"
    user_avatar: "url-gambar" (Bisa null jika tidak ada avatar)
    content: "Hi! Thank you for your enquiry..."
    created_at: "Dec 21, 2025"

    $submit_url → URL endpoint untuk men-submit form balasan vendor:
    Method: POST
    URL: $submit_url (cth: /vendor/enquiry-report/82/reply)
    Headers: X-CSRF-TOKEN
    Fields:
    content (textarea field)

    Note:
    1. Form ini dapat bekerja secara normal (page reload) dengan membungkus <form action="{{$submit_url}}" method="POST">
        2. ATAU dengan AJAX, API `submit_url` akan mengembalikan JSON jika menerima request header AJAX/wantsJson, cth dpt
        feedback:
        { status: true, message: "Reply added", data: { user_name: "...", content: "..." } }
        --}}

        {{-- Placeholder --}}
        <h1>{{ $page_title }}</h1>

        <div style="display:flex; gap: 20px;">
            <div style="flex: 1; border:1px solid #ddd; padding:20px;">
                <h2>Enquiry Customer Info</h2>
                <p><strong>Name:</strong> {{ $enquiry['name'] }}</p>
                <p><strong>Email:</strong> {{ $enquiry['email'] }}</p>
                <p><strong>Phone:</strong> {{ $enquiry['phone'] }}</p>
                <p><strong>Note:</strong> {{ $enquiry['note'] }}</p>

                <form action="{{ $submit_url }}" method="POST">
                    @csrf
                    <p>Reply Enquiry *</p>
                    <textarea name="content" rows="4" style="width:100%"></textarea>
                    <button type="submit">Reply Enquiry</button>
                </form>
            </div>

            <div style="flex: 1; border:1px solid #ddd; padding:20px;">
                <h2>Recent Updates</h2>
                @if(count($replies) > 0)
                    <ul>
                        @foreach($replies as $reply)
                            <li>
                                <strong>{{ $reply['user_name'] }}</strong> ({{ $reply['created_at'] }})<br>
                                {{ $reply['content'] }}
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p>No Reviews Available.</p>
                @endif
            </div>
        </div>
@endsection