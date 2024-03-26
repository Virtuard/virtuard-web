@extends('layouts.user')
@section('content')
    <h2 class="title-bar no-border-bottom">
        Virtuard 360
    </h2>
    @include('admin.message')
    @include('partials.plan-status')
    @if(auth()->user()->checkUserPlan())
        <div class="container">
            <div class="text-right mt-4">
                <a class="btn btn-info btn-sm btn-add-item" href="{{ route('user.virtuard-360.add') }}"><i class="icon ion-ios-add-circle-outline"></i> Add item</a>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <!-- Content for the left column (6 columns wide on medium-sized screens) -->
                    <table class="table mt-4">
                        <thead class="thead-dark">
                            <tr>
                                <th width="10%">#</th>
                                <th>Title</th>
                                <th width=20%>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($dataIpanorama as $key => $pan)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $pan->title }}</td>
                                    <td>
                                        <a href="{{ route('user.virtuard-360.edit', ['id' => $pan->id]) }}"
                                            class="virtuard-edit btn btn-warning btn-sm">Edit</a>
                                        <a href="{{ route('user.virtuard-360.destroy', $pan->id) }}"
                                                class="virtuard-delete btn btn-danger btn-sm">Delete</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3">{{ __('No Data') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
@endsection
