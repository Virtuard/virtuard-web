@extends('v2.layouts.vendor')

@section('content')
    <div class="d-flex justify-content-between my-4">
        <h2>Property Management</h2>
        <a href="{{ route('vendor2.property.add') }}" class="btn btn-primary"
            style="background:#0d6efd; color:#fff; border:none; padding:10px 20px; border-radius:5px;text-decoration:none;">Add
            New Property</a>
    </div>

    {{-- Filters Section (Matching Accommodation Index but for Properties) --}}
    <div style="background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
        <form action="" method="GET" style="display: flex; gap: 15px; align-items: center;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Property..."
                style="padding: 10px; border: 1px solid #ddd; border-radius: 5px; min-width: 250px;">

            <select name="sort" style="padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest</option>
                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Highest Rated</option>
                <option value="popularity" {{ request('sort') == 'popularity' ? 'selected' : '' }}>Most Popular</option>
            </select>

            <select name="price" style="padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                <option value="none">Price Filter</option>
                <option value="low_to_high" {{ request('price') == 'low_to_high' ? 'selected' : '' }}>Low to High</option>
                <option value="high_to_low" {{ request('price') == 'high_to_low' ? 'selected' : '' }}>High to Low</option>
            </select>

            <button type="submit"
                style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer;">Apply
                Filter</button>
        </form>
    </div>

    {{-- Data Table --}}
    <div style="background: white; border-radius: 10px; overflow: hidden; border: 1px solid #eee;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead style="background: #f8f9fa;">
                <tr>
                    <th style="padding: 15px; border-bottom: 2px solid #eee;">Property Name</th>
                    <th style="padding: 15px; border-bottom: 2px solid #eee;">Location</th>
                    <th style="padding: 15px; border-bottom: 2px solid #eee;">Status</th>
                    <th style="padding: 15px; border-bottom: 2px solid #eee;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($properties as $property)
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 15px;">
                            <strong>{{ $property->title }}</strong><br>
                            <small style="color: #6c757d;">Price: ${{ $property->price }}</small>
                        </td>
                        <td style="padding: 15px;">{{ $property->address ?? 'N/A' }}</td>
                        <td style="padding: 15px;">
                            @if($property->status == 'publish')
                                <span
                                    style="background: #e6f7ef; color: #28a745; padding: 5px 10px; border-radius: 20px; font-size: 12px;">Active</span>
                            @elseif($property->status == 'draft')
                                <span
                                    style="background: #fdf5e6; color: #fd7e14; padding: 5px 10px; border-radius: 20px; font-size: 12px;">Draft</span>
                            @else
                                <span
                                    style="background: #f8f9fa; color: #6c757d; padding: 5px 10px; border-radius: 20px; font-size: 12px;">{{ ucfirst($property->status) }}</span>
                            @endif
                        </td>
                        <td style="padding: 15px; display:flex; gap:10px;">
                            <a href="{{ route('vendor2.property.show', $property->id) }}" style="color: #6c757d; text-decoration:none;"
                                title="View Detail">👁️</a>
                            <a href="{{ route('vendor2.property.edit', $property->id) }}" style="color: #0d6efd; text-decoration:none;"
                                title="Edit">✏️</a>
                            <a href="{{ route('vendor2.property.updateStatus', ['id' => $property->id, 'action' => $property->status == 'publish' ? 'draft' : 'publish']) }}"
                                style="color: #198754; text-decoration:none;" title="Toggle Status">🔄</a>
                            <a href="{{ route('vendor2.property.delete', $property->id) }}"
                                onclick="return confirm('Delete this property?')" style="color: #dc3545; text-decoration:none;"
                                title="Delete">🗑️</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="padding: 30px; text-align: center; color: #6c757d;">No properties found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="padding: 15px;">
            {{ $properties->links() }}
        </div>
    </div>
@endsection