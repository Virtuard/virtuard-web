@extends('layouts.user')
@section('content')
    <h2 class="title-bar no-border-bottom">
        Virtuard 360
    </h2>
    @include('admin.message')
        <div class="d-flex justify-content-end mt-4">
            <a href="/user/add/virtuard-360" class="virtuard-add btn btn-success">
                    Add 360
            </a>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <!-- Content for the left column (6 columns wide on medium-sized screens) -->
                    <table class="table mt-4">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($dataIpanorama as $key => $ipanoramaData)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $ipanoramaData->title }}</td>
                                    <td>
                                        <a href="/user/edit/virtuard-360?id=<?= $ipanoramaData->id ?>"
                                            class="virtuard-edit btn btn-primary">Edit</a>
                                        <a href="/user/delete/virtuard-360?id=<?= $ipanoramaData->id ?>"
                                                class="virtuard-delete btn btn-danger">Delete</a>
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
@endsection
