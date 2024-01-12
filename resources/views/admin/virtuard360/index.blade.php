@extends('admin.layouts.app')
@section('content')
    <h2 class="title-bar no-border-bottom">
        Virtuard 360
    </h2>
    @include('admin.message')
    <div class="border rounded text-center p-4">

        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">No</th>
                    <th scope="col">Name</th>
                    <th scope="col">Status</th>
                    <th scope="col">Start Date</th>
                    <th scope="col">Expired Date</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $data)
                @php
                    $no = 1;
                @endphp
                <tr>
                    <th scope="row">{{ $no }}</th>
                    <td>
                        {{ $data->name }}
                    </td>

                    <td>
                        {{ $data->status }}
                    </td>

                    <td>
                        {{ $data->start_date }}
                    </td>

                    <td>
                        {{ $data->expired_date }}
                    </td>
                    <td>
                        <div class="dropdown">
                          <button class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                            Validate
                          </button>
                          <div class="dropdown-menu">
                            <form class="dropdown-item" action="{{ route('validate-service') }}" method="POST">
                            @csrf
                                <input type="hidden" name="param" value="SUCCESS">
                                <input type="hidden" name="id" value="{{ $data->id }}">
                                <button class="btn btn-success" type="submit">Approve</button>
                            </form>
                            <form class="dropdown-item" action="{{ route('validate-service') }}" method="POST">
                            @csrf
                                <input type="hidden" name="param" value="REJECTED">
                                <input type="hidden" name="id" value="{{ $data->id }}">
                                <button class="btn btn-danger" type="submit">Rejected</button>
                            </form>
                            <form class="dropdown-item" action="{{ route('validate-service') }}" method="POST">
                            @csrf
                                <input type="hidden" name="param" value="STOP">
                                <input type="hidden" name="id" value="{{ $data->id }}">
                                <button class="btn btn-warning" type="submit">Stop</button>
                            </form>
                          </div>
                        </div>
                    </td>
                </tr>
                @php
                    $no++;
                @endphp
                @endforeach
            </tbody>
        </table>

    </div>
@endsection
