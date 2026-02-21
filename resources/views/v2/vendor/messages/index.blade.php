@extends('v2.layouts.vendor')

@section('content')
    {{-- VENDOR MESSAGES PAGE --}}
    {{-- Route: GET /vendor/messages --}}

    {{--
    ============================================
    DATA TERSEDIA UNTUK FRONTEND:
    ============================================
    Sistem chat saat ini menggunakan package `munafio/chatify`. Terdapat dua cara frontend mengimplementasikan halaman ini
    berdasarkan mockup V2:

    OPSI 1: MENGGUNAKAN IFRAME (CARA PALING CEPAT SESUAI V1)
    $ifame_url → URL view messaging default chatify. Tinggal dirender di dalam <iframe src="{{ $iframe_url }}"></iframe>

    OPSI 2: MEMBUAT KUSTOM UI DENGAN AJAX / REST API (SESUAI MOCKUP V2)
    Bila Frontend ingin membuat UI Message custom secara utuh (kiri list chat, kanan isi chat seperti gambar mockup):
    - Dokumentasi API Backend Chatify berada di route-route `/chatify/api/...`
    - Contoh URL API:
    $chatify_api['fetch_contacts'] : Mengambil daftar orang yg ngechat.
    $chatify_api['fetch_messages'] : Mengambil riwayat pesan untuk 1 user_id
    $chatify_api['send_message'] : Mengirim pesan baru ke user_id tertentu
    - Gunakan variabel $user_id (bila ada) sebagai penanda user yang barusan diklik dari halaman lain (contoh: membalas
    Enquiry lalu diarahkan ke Messages page ini).
    - Variabel styling default bila ingin disamakan dengan V1:
    $messenger_color
    $dark_mode
    --}}

    {{-- Placeholder Skeleton Custom UI --}}
    <h1>{{ $page_title }}</h1>

    <div>
        {{-- LEFT PANEL: CONTACT LIST --}}
        <div>  
        </div>

        {{-- CENTER PANEL: MESSAGES --}}
        <div>
        </div>

        {{-- RIGHT PANEL: INFO --}}
        <div>
        </div>
    </div>
@endsection