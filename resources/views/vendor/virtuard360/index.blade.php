@extends('layouts.user')
@section('content')
    <h2 class="title-bar no-border-bottom">
        Virtuard 360
    </h2>
    @include('admin.message')
    @if (!empty($data) && isset($data[0]))
        @if ($data[0]->status === 'PENDING' || $data[0]->status === 'REJECTED' || $data[0]->status === 'STOP')
            <div class="alert alert-danger" role="alert">
                Your service is not active yet, please subscribe to our plan. <a href="{{ route('user.plan') }}">Click
                    here</a> to
                subscribe.
            </div>
        @endif
    @else
        <div class="alert alert-danger" role="alert">
            Your service is not active yet, please subscribe to our plan. <a href="{{ route('user.plan') }}">Click
                here</a> to
            subscribe.
        </div>
    @endif
    <div class="border rounded text-center p-4">

        @if (!empty($data) && isset($data[0]))
            @if ($data[0]->status === 'PENDING')
                <span class="icon text-center" style="font-size: 5rem;"><i class="fa fa-lock"></i></span>
                <h1>Locked feature</h1>
                <p>Your service is being processed for validation</p>
            @elseif($data[0]->status === 'SUCCESS')
                <span class="icon text-center" style="font-size: 5rem;"><i class="fa fa-unlock"></i></span>
                <h1>Unlocked feature</h1>
                <p>Your service is active until {{ $data[0]->expired_date }}</p>
            @elseif($data[0]->status === 'REJECTED')
                <span class="icon text-center" style="font-size: 5rem;"><i class="fa fa-lock"></i></span>
                <h1>Locked feature</h1>
                <p>Your application was rejected, please reapply</p>

                <a href="{{ route('user.plan') }}" class="btn btn-primary">
                    Subscribe
                </a>
            @elseif($data[0]->status === 'STOP')
                <span class="icon text-center" style="font-size: 5rem;"><i class="fa fa-lock"></i></span>
                <h1>Locked feature</h1>
                <p>Please activate the service by making a payment</p>

                <a href="{{ route('user.plan') }}" class="btn btn-primary">
                    Subscribe
                </a>
            @endif
        @else
            <span class="icon text-center" style="font-size: 5rem;"><i class="fa fa-lock"></i></span>
            <h1>Locked feature</h1>
            <p>Please activate the service by making a payment</p>

            <a href="{{ route('user.plan') }}" class="btn btn-primary">
                Subscribe
            </a>
        @endif

        <!-- Modal -->
        <div class="modal fade" id="modalSubscribe" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Activate the service</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('submission-service') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="exampleFormControlFile1">Proof of payment</label>
                                <input type="file" name="proof" class="form-control-file" id="exampleFormControlFile1">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Subscribe</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    @if (!empty($data) && isset($data[0]) && $data[0]->status === 'SUCCESS')
        <div class="d-flex justify-content-end mt-4">
            <a href="/user/add/virtuard-360" class="virtuard-add">
                <button class="btn btn-primary">
                    Add Virtuard 360
                </button>
            </a>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <!-- Content for the left column (6 columns wide on medium-sized screens) -->
                    <table class="table mt-4">
                        <thead class="thead-dark">
                            <tr>
                                <th class="col-md-9" scope="col">Title</th>
                                <!-- <th scope="col">Code</th> -->
                                <th class="col-md-3 text-center" scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dataIpanorama as $ipanoramaData)
                                <tr>
                                    <td>{{ $ipanoramaData->title }}</td>
                                    <td>
                                        <div class="text-center">
                                            <a href="/user/edit/virtuard-360?id=<?= $ipanoramaData->id ?>"
                                                class="virtuard-edit">
                                                <button class="btn btn-warning">
                                                    Edit
                                                </button>
                                            </a>
                                            <a href="/user/delete/virtuard-360?id=<?= $ipanoramaData->id ?>"
                                                class="virtuard-edit">
                                                <button class="btn btn-danger">
                                                    Delete
                                                </button>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="col-md-6">
                    <!-- Content for the right column (6 columns wide on medium-sized screens) -->
                    <!-- Add your content for the right column here -->
                </div>
            </div>
        </div>
    @endif

@endsection
