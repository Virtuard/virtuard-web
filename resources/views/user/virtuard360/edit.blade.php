@extends('layouts.user')
@section('content')
    <h2 class="title-bar no-border-bottom">
        Edit Virtuard 360
    </h2>

    <div class="col-md-12">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    @if (!request()->has('id'))
        <div class="alert alert-primary" role="alert">
            <b>You must first create a title for your Virtuard 360!</b>
        </div>

        <form action="{{ route('user.virtuard-360.add-new-service') }}" method="POST">
            @csrf

            <div class="card p-4">
                <div class="row">
                    <div class="col-md-10">
                        <div class="form-group">
                            <input placeholder="Your title..." type="text" name="title" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-2 p-0">
                        <button class="btn btn-primary w-100">
                            Submit
                        </button>
                    </div>
                </div>
            </div>

        </form>
    @else
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <input type="text" id="title" name="title" class="form-control" value="{{ $panorama->title }}" placeholder="Title..">
                </div>
            </div>
        </div>

        <div class="mb-4 d-flex justify-content-end">
            <a href="#" class="btn btn-danger has-icon bravo-video-popup mr-2" data-toggle="modal" data-src="{{ handleVideoUrl('https://www.youtube.com/watch?v=r4btk4OgCJ0') }}" data-target="#modalIpanoramaTutorial">
                <i class="input-icon field-icon fa">
                    <svg height="18px" width="18px" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 24 24" style="enable-background:new 0 0 24 24;" xml:space="preserve">
                        <g fill="#FFFFFF">
                            <path d="M2.25,24C1.009,24,0,22.991,0,21.75V2.25C0,1.009,1.009,0,2.25,0h19.5C22.991,0,24,1.009,24,2.25v19.5
                                c0,1.241-1.009,2.25-2.25,2.25H2.25z M2.25,1.5C1.836,1.5,1.5,1.836,1.5,2.25v19.5c0,0.414,0.336,0.75,0.75,0.75h19.5
                                c0.414,0,0.75-0.336,0.75-0.75V2.25c0-0.414-0.336-0.75-0.75-0.75H2.25z">
                            </path>
                            <path d="M9.857,16.5c-0.173,0-0.345-0.028-0.511-0.084C8.94,16.281,8.61,15.994,8.419,15.61c-0.11-0.221-0.169-0.469-0.169-0.716
                                V9.106C8.25,8.22,8.97,7.5,9.856,7.5c0.247,0,0.495,0.058,0.716,0.169l5.79,2.896c0.792,0.395,1.114,1.361,0.719,2.153
                                c-0.154,0.309-0.41,0.565-0.719,0.719l-5.788,2.895C10.348,16.443,10.107,16.5,9.857,16.5z M9.856,9C9.798,9,9.75,9.047,9.75,9.106
                                v5.788c0,0.016,0.004,0.033,0.011,0.047c0.013,0.027,0.034,0.044,0.061,0.054C9.834,14.998,9.845,15,9.856,15
                                c0.016,0,0.032-0.004,0.047-0.011l5.788-2.895c0.02-0.01,0.038-0.027,0.047-0.047c0.026-0.052,0.005-0.115-0.047-0.141l-5.79-2.895
                                C9.889,9.004,9.872,9,9.856,9z">
                            </path>
                        </g>
                    </svg>
                </i> {{__("Panorama Tutorial")}}
            </a>
            <button class="btn btn-primary add-image " data-toggle="modal" data-target="#modalAddImage" id="btn-image">
                + Add New Image
            </button>
        </div>

        @include('partials.ipanorama.modal-new-image')

        <input type="hidden" id="url_panorama" value="{{ url('/uploads/ipanoramaBuilder?id=' . request('id') . '&user_id=' . auth()->user()->id) }}">
        @if(config('app.env') == 'local')
            <iframe id="ipanorama-frame" src="/uploads/ipanoramaBuilder/?id={{ request('id') }}&user_id={{ auth()->user()->id}}"></iframe>
        @else
            <div id="ipanorama-frame"></div>
        @endif
    @endif
@endsection
@push('css')
    <style>
        #ipanorama-frame {
            width: 100%;
            min-height: 100vh;
        }
        .shepherd-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1000;
            /* Ensure it's above other elements */
            pointer-events: none;
            /* Allow clicking through the overlay */
        }

        .shepherd-blur {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            backdrop-filter: blur(5px);
            /* Adjust the blur amount as needed */
        }

        .shepherd-highlight {
            position: absolute;
            top: 50%;
            /* Adjust the position based on your design */
            left: 50%;
            /* Adjust the position based on your design */
            transform: translate(-50%, -50%);
            /* Add any other styles for highlighting the element */
        }
        #frame-panorama {
            height: 100vh;
        }
    </style>
@endpush
@push('js')
        <script>
            function load360Tutorial(){
                const tour = new Shepherd.Tour({
                    defaultStepOptions: {
                        cancelIcon: {
                            enabled: true
                        },
                        classes: 'shepherd-theme-dark',
                        scrollTo: {
                            behavior: 'smooth',
                            block: 'center'
                        }
                    }
                });

                tour.addStep({
                    title: 'Add 360 Images',
                    text: `This button is used to add some images to your 360 pictures`,
                    attachTo: {
                        element: '.add-image',
                        on: 'bottom'
                    },
                    buttons: [{
                        action() {
                            $('#btn-image').click();

                            setTimeout(() => {
                                this.next();
                            }, 400);
                        },
                        text: 'Next'
                    }],
                    id: 'creating'
                });

                tour.addStep({
                    title: 'Add title for your image',
                    text: `This field is used to give title for your 360 image`,
                    attachTo: {
                        element: '.title-image',
                        on: 'bottom'
                    },
                    buttons: [{
                            action() {
                                return this.back();
                            },
                            classes: 'shepherd-button-secondary',
                            text: 'Back'
                        },
                        {
                            action() {
                                return this.next();
                            },
                            text: 'Next'
                        }
                    ],
                    id: 'editing'
                });

                tour.addStep({
                    title: 'Upload your picture',
                    text: `You may upload your picture by clicking the upload button`,
                    attachTo: {
                        element: '#image',
                        on: 'left'
                    },
                    buttons: [{
                            action() {
                                return this.back();
                            },
                            classes: 'shepherd-button-secondary',
                            text: 'Back'
                        },
                        {
                            action() {
                                $('#modal-close').click();
                                setTimeout(() => {
                                    this.next();
                                }, 400);
                            },
                            text: 'Next'
                        }
                    ],
                    id: 'editing'
                });

                tour.addStep({
                    title: '360 Image Setting Tab',
                    text: `This tab is the tab where you can edit or set up your 360 image`,
                    attachTo: {
                        element: '#ipanorama-frame',
                        on: 'top'
                    },
                    buttons: [{
                            action() {
                                $('#btn-image').click();

                                setTimeout(() => {
                                    this.back();
                                }, 400);
                            },
                            classes: 'shepherd-button-secondary',
                            text: 'Back'
                        },
                        {
                            action() {
                                // updateIsTourField();
                                return this.complete();
                            },
                            text: 'Done'
                        }
                    ],
                    id: 'editing'
                });

                tour.start();
            }

            function updateIsTourField() {
                const csrfToken = $('meta[name="csrf-token"]').attr('content');

                $.ajax({
                    url: '{{ route('user.virtuard-360.update-tour') }}',
                    method: 'POST',
                    data: {
                        _token: csrfToken,
                    },
                    success: function(response) {
                        console.log('AJAX request successful');
                        console.log(response);
                    },
                    error: function(error) {
                        console.error('AJAX request failed');
                        console.error(error);
                        e
                    }
                });
            }

            @if(request()->has('id'))
                load360Tutorial();
            @endif
        </script>
        <script>
            function initPanorama() {
                let urlPan = $('#url_panorama').val();
                var iframe = $('<iframe>').attr({
                    src: urlPan,
                    id: "frame-panorama",
                    width: '100%',
                    height: '100vh'
                });
                $('#ipanorama-frame').append(iframe);
                $('#frame-panorama').on('load', function(){
                    var iframeContent = $('#frame-panorama').contents();
                    iframeContent.find('.ipnrm-ui-cmd-load').trigger('click');
                    iframeContent.find('.ipnrm-ui-cmd-load').trigger('click');
                    iframeContent.find('#frame-load').find('.ipnrm-ui-toggle').trigger('click');
                });
            }

            document.addEventListener("DOMContentLoaded", function(event) { 
                initPanorama();
            });
        </script>
    @endpush
