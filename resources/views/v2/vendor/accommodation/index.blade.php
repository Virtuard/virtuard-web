@extends('v2.layouts.vendor')

@section('content')
    {{-- FILTER FORM & ACTIONS --}}
    <form method="GET" action="{{ route('vendor2.accommodation.index') }}" style="display:flex; justify-content: space-between; margin-bottom: 25px;">
        <div style="display:flex; gap: 15px;">
            <div style="display: flex; align-items: center; border: 1px solid #ddd; border-radius: 5px; padding: 0 10px;">
                <label style="margin-right: 5px;">Sort by:</label>
                <select name="sort" onchange="this.form.submit()" style="border: none; outline: none; padding: 10px 0; cursor: pointer;">
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest</option>
                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                    <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Rating</option>
                    <option value="popularity" {{ request('sort') == 'popularity' ? 'selected' : '' }}>Popularity</option>
                </select>
            </div>

            <div style="display: flex; align-items: center; border: 1px solid #ddd; border-radius: 5px; padding: 0 10px;">
                <label style="margin-right: 5px;">Price:</label>
                <select name="price" onchange="this.form.submit()" style="border: none; outline: none; padding: 10px 0; cursor: pointer;">
                    <option value="none" {{ request('price', 'none') == 'none' ? 'selected' : '' }}>None</option>
                    <option value="low_to_high" {{ request('price') == 'low_to_high' ? 'selected' : '' }}>Low to High</option>
                    <option value="high_to_low" {{ request('price') == 'high_to_low' ? 'selected' : '' }}>High to Low</option>
                </select>
            </div>

            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name & location"
                style="padding: 10px; border: 1px solid #ddd; border-radius: 5px; min-width: 250px;">
            {{-- Invisible submit button --}}
            <button type="submit" style="display: none;"></button>
        </div>

        <div style="display:flex; gap: 10px;">
            <a href="{{ route('vendor2.accommodation.add') }}"
                style="padding: 10px 15px; border: none; background: #2563ea; color: white; text-decoration: none; border-radius:5px; font-weight: bold;">
                <span style="margin-right: 5px;">+</span> Add New Accommodation
            </a>
        </div>
    </form>

    {{-- TABLE SECTION --}}
    <table border="1" style="width: 100%; text-align: left; border-collapse: collapse; background: white; border: 1px solid #eee;">
        <thead>
            <tr style="background: #fafafa; border-bottom: 1px solid #ddd;">
                <th style="padding: 15px; font-weight: normal; color: #666;">Name</th>
                <th style="padding: 15px; font-weight: normal; color: #666;">Location</th>
                <th style="padding: 15px; font-weight: normal; color: #666;">Status</th>
                <th style="padding: 15px; font-weight: normal; color: #666;">Reviews</th>
                <th style="padding: 15px; font-weight: normal; color: #666;">Views</th>
                <th style="padding: 15px; font-weight: normal; color: #666;">Date</th>
                <th style="padding: 15px; font-weight: normal; color: #666;">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($accommodations as $acc)
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 15px;">{{ $acc->title }}</td>
                    <td style="padding: 15px;">{{ $acc->location->name ?? $acc->address ?? 'N/A' }}</td>
                    <td style="padding: 15px;">
                        @if($acc->status == 'publish')
                            <span style="background: #e6f7ef; color: #28a745; padding: 5px 10px; border-radius: 5px; font-size: 13px;">Publish</span>
                        @elseif($acc->status == 'draft')
                            <span style="background: #fff8e1; color: #ffc107; padding: 5px 10px; border-radius: 5px; font-size: 13px;">Draft</span>
                        @else
                            <span style="background: #eee; color: #666; padding: 5px 10px; border-radius: 5px; font-size: 13px;">{{ ucfirst($acc->status) }}</span>
                        @endif
                    </td>
                    <td style="padding: 15px;">{{ $acc->review_score ?? 0 }}</td>
                    <td style="padding: 15px;">{{ number_format($acc->view_count ?? 0) }}</td>
                    <td style="padding: 15px;">{{ $acc->created_at->format('M d, Y') }}</td>
                    <td style="padding: 15px;">
                        <div style="display: flex; gap: 5px;">
                            {{-- View View --}}
                            <a href="{{ $acc->getDetailUrl() ?? '#' }}" target="_blank" style="padding: 6px; background: #e8f0fe; color: #1a73e8; border-radius: 4px; text-decoration: none;">
                                👁
                            </a>
                            {{-- Edit Button --}}
                            <a href="{{ route('vendor2.accommodation.edit', ['id' => $acc->id]) }}" style="padding: 6px; background: #fff8e1; color: #f2a600; border-radius: 4px; text-decoration: none;">
                                ✏️
                            </a>
                            {{-- Publish/Draft Action --}}
                            <a href="{{ route('vendor2.accommodation.updateStatus', ['id' => $acc->id, 'action' => $acc->status == 'publish' ? 'draft' : 'publish']) }}" style="padding: 6px; background: #e6f7ef; color: #28a745; border-radius: 4px; text-decoration: none;" title="{{ $acc->status == 'publish' ? 'Mark as Draft' : 'Publish' }}">
                                🔄
                            </a>
                            {{-- Delete Action --}}
                            <a href="{{ route('vendor2.accommodation.delete', ['id' => $acc->id]) }}" onclick="return confirm('Are you sure you want to delete this accommodation?')" style="padding: 6px; background: #fce8e6; color: #ea4335; border-radius: 4px; text-decoration: none;">
                                🗑️
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="padding: 20px; text-align: center; color: #888;">No accommodations found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- PAGINATION --}}
    <div style="margin-top: 20px; display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <span style="color: #666;">Showing per page</span>
            <select name="per_page" style="padding: 5px 10px; border: 1px solid #ddd; border-radius: 5px; outline: none;" disabled>
                <option>10</option>
            </select>
        </div>
        
        <div style="display: flex; gap: 5px;">
            {{ $accommodations->appends(request()->query())->links() }}
        </div>
    </div>
@endsection
