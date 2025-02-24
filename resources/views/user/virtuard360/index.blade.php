@extends('layouts.user')

@push('css')
    <style>
        .shepherd-header {
            background: none !important;
            border-bottom: 1px solid #ccc;
            padding-bottom: 8px;
        }

        @media (max-width: 600px) {
            .shepherd-element {
                max-width: 80%;
                font-size: 14px;
            }
        }
    </style>
@endpush

@section('content')
    <h2 class="title-bar no-border-bottom">
        Virtuard 360
    </h2>
    @include('admin.message')
    {{-- @include('partials.plan-status') --}}
    {{-- @if(auth()->user()->checkUserPlan()) --}}
        <div class="container">
            <div class="text-right mt-4">
                <a class="btn btn-warning btn-sm mr-2" href="{{ route('user.virtuard-360.index', ['page' => 'index', 'wstep' => '1']) }}"><i class="icon ion-ios-walk"></i> {{ __('Start Tour Creation Wizard') }}</a>
                <a class="btn btn-info btn-sm btn-add-item" id="add-360-btn" href="{{ route('user.virtuard-360.add') }}"><i class="icon ion-ios-add-circle-outline"></i> Add 360 Image</a>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <!-- Content for the left column (6 columns wide on medium-sized screens) -->
                    <table class="table mt-4">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($dataIpanorama as $key => $pan)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $pan->title }}</td>
                                    <td>
                                        <span class="badge badge-{{ $pan->status }}">{{ $pan->status }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('user.virtuard-360.show', $pan->id) }}"
                                            class="virtuard-edit btn btn-info btn-sm">Preview</a>
                                        <a href="{{ route('user.virtuard-360.edit', ['id' => $pan->id, 'user_id' => auth()->user()->id]) }}"
                                            class="virtuard-edit btn btn-primary btn-sm">Edit</a>
                                        <a href="{{ route('user.virtuard-360.destroy', $pan->id) }}"
                                                class="virtuard-delete btn btn-danger btn-sm">Delete</a>
                                        @if($pan->status == 'publish')
                                            <a href="{{ route("user.virtuard-360.bulk_edit",[$pan->id,'action' => "make-hide"]) }}" class="btn btn-warning btn-sm">{{__("Make Draft")}}</a>
                                        @endif
                                        @if($pan->status == 'draft')
                                            <a href="{{ route("user.virtuard-360.bulk_edit",[$pan->id,'action' => "make-publish"]) }}" class="btn btn-success btn-sm    ">{{__("Make Publish")}}</a>
                                        @endif
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
    {{-- @endif --}}
@endsection

@push('js')
    <script>
        function getQueryParams() {
            const params = new URLSearchParams(window.location.search);
            return {
                page: params.get('page'),
                wstep: parseInt(params.get('wstep'), 10)
            };
        }

        const { page, wstep } = getQueryParams();

        $(document).on('click', '.shepherd-header .shepherd-cancel-icon', function() {
            const isCanceled = confirm('Are you sure you want to exit the tour creation wizard?');
            if (isCanceled) {
                window.location.href = '/user/virtuard-360';
            }
        });

        if(page == 'index' && !isNaN(wstep)) {
            $("#add-360-btn").attr('href', '/user/virtuard-360/add?page=add&wstep=2');
        } else {
            $("#add-360-btn").attr('href', '/user/virtuard-360/add');
        }

        const tour = new Shepherd.Tour({
            useModalOverlay: true,
            defaultStepOptions: {
                cancelIcon: { 
                    enabled: true,
                },
                classes: 'shadow-md bg-white',
                scrollTo: { behavior: 'smooth', block: 'center' }
            },
        });

        tour.addStep({
            id: 'step-1',
            title: '1 - Creating a New Tour',
            text: 'Click on the button <b>Add 360 Image</b>',
            attachTo: {
                element: '#add-360-btn',
                on: 'right'
            },
            // buttons: [
            //     {
            //         text: 'Next',
            //         action: tour.next
            //     }
            // ]
        });

        if (page === 'index' && !isNaN(wstep)) {
            tour.start();
            tour.show(wstep);
        }
    </script>
@endpush