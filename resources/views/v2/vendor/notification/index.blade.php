@extends('v2.layouts.vendor')

@section('content')
    {{-- VENDOR NOTIFICATION PAGE --}}
    {{-- Route: GET /vendor/notification --}}

    {{--
    ============================================
    DATA TERSEDIA UNTUK FRONTEND:
    ============================================

    $current_type → string ('all' | 'unread' | 'read')
    $notifications → LengthAwarePaginator (Gunakan $notifications->links() untuk UI pagination standard Laravel)

    Tiap item di dalam $notifications:
    {
    id: "abc-def...",
    avatar: "url-foto-pengirim-atau-icon",
    name: "Nama Pengirim (opsional)",
    title: "Pesan Notifikasi",
    link: "/url-terkait-notifikasi",
    type: "Tipe notifikasi (misal: booking, system, dll)",
    is_read: true | false,
    created_at: 2025-10-10 10:00:00,
    time_ago: "2 hours ago"
    }

    ============================================
    FILTER NOTIFICATIONS:
    ============================================
    Ubah filter dengan mengirim param `type` atau `search`:
    - All:     /vendor/notification?type=all
    - Unread:  /vendor/notification?type=unread
    - Read:    /vendor/notification?type=read
    - Search:  /vendor/notification?search=keyword&type=all

    ============================================
    MARK AS READ API:
    ============================================

    A. Mark single notification as read (AJAX POST)
    Endpoint: POST /vendor/notification/mark-read
    Fields: id (string UUID dari notifikasi tersebut)
    Headers: X-CSRF-TOKEN
    Response: { status: true, message: "..." }

    B. Mark ALL notifications as read (AJAX POST)
    Endpoint: POST /vendor/notification/mark-all-read
    Headers: X-CSRF-TOKEN
    Response: { status: true, message: "..." }

    ============================================
    KASUS PENGGUNAAN
    ============================================
    - Header: Dropdown "All Notification" -> bisa klik filter Unread/Read (redirect URL `?type=...`)
    - Search Bar: Submit form dengan GET method menyertakan `name="search"` (redirect URL `?search=keyword`)
    - List Item:
    Jika is_read == false, tambahkan class indicator unread.
    Klik judul / item untuk buka link notifikasi tujuan. Klik "Mark as Read" trigger AJAX (A).
    --}}

    {{-- Placeholder --}}
    <h1>{{ $page_title }}</h1>
    <p>Viewing: {{ $current_type }} notifications</p>
@endsection