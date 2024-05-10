@extends('admin.layouts.app')
@section('content')
    <div class="container-fluid">
        @if (!request()->has('id'))
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-primary" role="alert">
                        <b>You must first create a title for your Virtuard 360!</b>
                    </div>

                    <form action="{{ route('admin.virtuard360.store') }}" method="POST">
                        @csrf
                        <div class="card p-4">
                            <div class="row">
                                <div class="col-md-10">
                                    <div class="form-group">
                                        <input placeholder="Your title..." type="text" name="title"
                                            class="form-control">
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
                </div>
            </div>
        @else
            <div class="row">
                <div class="col-md-9">
                    <div class="d-flex justify-content-between mb20">
                        <div class="title-bar"></div>
                        <div class="title-actions">
                            <button class="btn btn-primary w-100 add-image" data-toggle="modal" data-target="#modalAddImage"
                                id="btn-image">
                                + Add New Image
                            </button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 p-0 mb-4">
                            <iframe id="ipanorama-frame"></iframe>
                            {{-- <iframe id="ipanorama-frame" src="/uploads/ipanoramaBuilder/?idItem={{ request()->id }}"></iframe> --}}
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <form action="{{ route('admin.virtuard360.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <input type="hidden" name="id" value="{{ request()->input('id') }}">
                        </div>
                        <div class="panel">
                            <div class="panel-title"><strong>{{ __('Author Setting') }}</strong></div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <?php
                                    $user = $row->author;
                                    \App\Helpers\AdminForm::select2(
                                        'id_user',
                                        [
                                            'configs' => [
                                                'ajax' => [
                                                    'url' => route('user.admin.getForSelect2'),
                                                    'dataType' => 'json',
                                                ],
                                                'allowClear' => true,
                                                'placeholder' => __('-- Select User --'),
                                            ],
                                        ],
                                        !empty($user->id) ? [$user->id, $user->getDisplayName() . ' (#' . $user->id . ')'] : false,
                                    );
                                    ?>
                                </div>
                                <div class="text-right">
                                    <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i>
                                        {{ __('Save Changes') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif

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
                    <form action="{{ route('user.virtuard-360.add-new-image-service') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group title-image">
                                <label for="exampleFormControlFile1">Title Image</label>
                                <input type="text" name="title" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="exampleFormControlFile1">Image 360</label>
                                <input type="file" name="image" class="form-control-file" id="image">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal"
                                id="modal-close">Close</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/shepherd.js@10.0.1/dist/css/shepherd.css" />
    <style>
        #ipanorama-frame {
            width: 100%;
            height: 850px;
        }

        .shepherd-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1000;
            pointer-events: none;
        }

        .shepherd-blur {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            backdrop-filter: blur(5px);
        }

        .shepherd-highlight {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
    </style>
@endpush
@push('js')
    <script src="https://cdn.jsdelivr.net/npm/shepherd.js@10.0.1/dist/js/shepherd.min.js"></script>
    <script>
        function load360Tutorial() {
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
                            return this.complete();
                        },
                        text: 'Done'
                    }
                ],
                id: 'editing'
            });

            tour.start();
        }

        @if (request()->has('id'))
            load360Tutorial();
        @endif
    </script>
    <script>
        $(function() {
            var iframe = $('<iframe>').attr({
                src: "{{ url('/uploads/ipanoramaBuilder/?idItem=' . request('id')) }}",
                id: "frame-panorama",
                width: '100%',
                style: 'height: 310vh'
            });
            $('#ipanorama-frame').append(iframe);
            $('#frame-panorama').on('load', function() {
                var iframeContent = $('#frame-panorama').contents();
                iframeContent.find('.ipnrm-ui-cmd-load').trigger('click');
                iframeContent.find('.ipnrm-ui-cmd-load').trigger('click');
                iframeContent.find('#frame-load').find('.ipnrm-ui-toggle').trigger('click');
            });
        });
    </script>
@endpush
