@extends('v2.layouts.vendor')

@section('content')
    {{-- VENDOR VIRTUARD 360 PAGE --}}
    {{-- Route: GET /vendor/virtuard360 --}}

    {{--
    ============================================
    DATA TERSEDIA UNTUK FRONTEND:
    ============================================
    $panoramas → Paginator objects. Tiap baris tabel berisi:
    - id: 18
    - uuid: "abcd-1234-xyz"
    - title: "Skyline Residence 360"
    - status: "publish" | "draft"
    - usage_count: 5 (Berarti asset ini diembed/dipakai di 5 listing)
    - detail_url: route URL mem-preview asset
    - edit_url: route URL mengedit asset dan konfig iPanorama builder-nya
    - delete_url: route URL untuk hapus
    - share_url: route URL preview publik
    - publish_url: route URL change status action
    - draft_url: route URL change status action

    $filters → Filter aktif saat ini yang dipasang di URL GET parameters:
    - search: "",
    - status: "all" | "publish" | "draft"
    - sort: "newest" | "oldest" | "title_az" | "title_za" | "most_used"
    --}}

    {{-- FILTER FORM & ACTIONS --}}
    <form method="GET" action="" style="display:flex; justify-content: space-between; margin-bottom: 25px;">
        <div style="display:flex; gap: 15px;">
            <select name="sort" onchange="this.form.submit()">
                <option value="newest" {{ $filters['sort'] == 'newest' ? 'selected' : '' }}>Sort by: Newest</option>
                <option value="oldest" {{ $filters['sort'] == 'oldest' ? 'selected' : '' }}>Sort by: Oldest</option>
                <option value="title_az" {{ $filters['sort'] == 'title_az' ? 'selected' : '' }}>Sort by: Title (A-Z)</option>
                <option value="title_za" {{ $filters['sort'] == 'title_za' ? 'selected' : '' }}>Sort by: Title (Z-A)</option>
                <option value="most_used" {{ $filters['sort'] == 'most_used' ? 'selected' : '' }}>Sort by: Most Used</option>
            </select>

            <select name="status" onchange="this.form.submit()">
                <option value="all" {{ $filters['status'] == 'all' ? 'selected' : '' }}>Status: All Status</option>
                <option value="publish" {{ $filters['status'] == 'publish' ? 'selected' : '' }}>Status: Published</option>
                <option value="draft" {{ $filters['status'] == 'draft' ? 'selected' : '' }}>Status: Draft</option>
            </select>

            <input type="text" name="search" value="{{ $filters['search'] }}" placeholder="Search by title..."
                style="padding: 5px;" onblur="this.form.submit()">
        </div>

        <div style="display:flex; gap: 15px;">
            {{-- This wizard URL can point to the V1 wizard component if required, or generic # placeholder per mockup
            request --}}
            <a href="#"
                style="padding: 10px; border: 1px solid #ddd; background: #fff; text-decoration: none; border-radius: 5px; color: black;">⚙
                Start Tour Creation Wizard</a>
            <a href="{{ route('vendor2.virtuard360.add') }}"
                style="padding: 10px; border: none; background: #007bff; color: white; text-decoration: none; border-radius:5px;">
                + Add New Asset 360°
            </a>
        </div>
    </form>

    {{-- NOTIFICATIONS --}}
    @if(session('success'))
        <div style="padding: 10px; background: #d4edda; color: #155724; border-radius: 5px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="padding: 10px; background: #f8d7da; color: #721c24; border-radius: 5px; margin-bottom: 20px;">
            {{ session('error') }}
        </div>
    @endif

    {{-- TABLE SECTION --}}
    <table border="1" style="width: 100%; text-align: left; border-collapse: collapse;">
        <thead>
            <tr style="background: #f9f9f9;">
                <th style="padding: 10px;">Title</th>
                <th style="padding: 10px;">Status</th>
                <th style="padding: 10px; text-align: center;">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($panoramas as $item)
                <tr>
                    <td style="padding: 10px;">{{ $item['title'] }}</td>
                    <td style="padding: 10px;">
                        @if($item['status'] == 'publish')
                            <span style="color: green; background: #e6ffe6; padding: 3px 8px; border-radius: 3px;">Published</span>
                        @else
                            <span style="color: orange; background: #fff3e6; padding: 3px 8px; border-radius: 3px;">Draft</span>
                        @endif
                    </td>
                    <td style="padding: 10px; text-align: center;">
                        {{-- Copy Link --}}
                        <button onclick="navigator.clipboard.writeText('{{ $item['share_url'] }}'); alert('Link Copied!')"
                            title="Copy Link" style="border: none; background: transparent; cursor: pointer;">
                            🔗
                        </button>
                        {{-- Preview --}}
                        <a href="{{ $item['detail_url'] }}" title="Preview Asset" style="text-decoration: none;">👁</a>
                        {{-- Edit --}}
                        <a href="{{ $item['edit_url'] }}" title="Edit Asset" style="text-decoration: none;">✏</a>
                        {{-- Change Status (Mockup shows a modal popup, here we provide the direct GET action links as fallback)
                        --}}
                        @if($item['status'] == 'publish')
                            <a href="{{ $item['draft_url'] }}" title="Make Draft" style="text-decoration: none;">📦</a>
                        @else
                            <a href="{{ $item['publish_url'] }}" title="Publish Asset" style="text-decoration: none;">🚀</a>
                        @endif
                        {{-- Delete --}}
                        <a href="{{ $item['delete_url'] }}"
                            onclick="return confirm('Are you sure you want to delete this asset?')" title="Delete Asset"
                            style="text-decoration: none; color: red;">🗑</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="padding: 20px; text-align: center;">No 360 Assets found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- PAGINATION --}}
    <div style="margin-top: 30px;">
        {{ $panoramas->links() }}
    </div>

@endsection