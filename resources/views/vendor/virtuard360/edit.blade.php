@extends('layouts.user')
@section('content')
    <h2 class="title-bar no-border-bottom">
        Edit Virtuard 360
    </h2>

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
    @endif

    @if (request()->has('id'))
        <div class="col-md-12 p-0 mb-4">
            <button class="btn btn-primary w-100 add-image" data-toggle="modal" data-target="#modalAddImage" id="btn-image">
                + Add New Image
            </button>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="modalAddImage" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Insert New Image</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('user.virtuard-360.add-new-image-service') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group title-image">
                                <label for="exampleFormControlFile1">Title Image</label>
                                <input type="text" name="title" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="exampleFormControlFile1">Image 360</label>
                                <input type="file" name="image" id="image" class="form-control-file"
                                    id="exampleFormControlFile1">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="modal-close" class="btn btn-secondary"
                                data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @if(config('app.env') == 'local')
            <iframe id="ipanorama-frame" src="/uploads/ipanoramaBuilder/?id={{ request()->id }}&user_id={{ auth()->user()->id}}"></iframe>
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
            // $(function() {
            //     var iframe = $('<iframe>').attr({
            //         src: "{{ url('/uploads/ipanoramaBuilder/?id=' . request('id')) }}",
            //         id: "ipanorama-frame",
            //         width: '100%',
            //         height: '100vh'
            //     });
            //     $('#ipanorama-frame').append(iframe);
            //     $('#ipanorama-frame').on('load', function(){
            //         var iframeContent = $('#ipanorama-frame').contents();
            //         iframeContent.find('.ipnrm-ui-cmd-load').trigger('click');
            //         iframeContent.find('.ipnrm-ui-cmd-load').trigger('click');
            //         iframeContent.find('#frame-load').find('.ipnrm-ui-toggle').trigger('click');
            //     });
            // });
        </script>
    @endpush
