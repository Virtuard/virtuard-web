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
                <div class="modal-footer">
                    <a href="javascript:void(0)" class="btn btn-warning btn-sm btn-panorama-tutorial" data-toggle="modal" data-src="{{ handleVideoUrl('https://www.youtube.com/watch?v=r4btk4OgCJ0') }}" data-target="#modalIpanoramaTutorial">
                        <i class="fa fa-info-circle"></i> {{__("360 Tutorial")}}
                    </a>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" id="title" name="title" class="form-control" value="{{ $panorama->title }}" placeholder="Title..">
                </div>
            </div>
        </div>

        <div class="mb-4 d-flex justify-content-end">
            <button class="btn btn-primary btn-sm add-image " data-toggle="modal" data-target="#modalAddImage" id="btn-image">
               Add New Image
            </button>
        </div>

        @include('partials.ipanorama.modal-new-image')

        <input type="hidden" id="url_panorama" value="{{ url('/uploads/ipanoramaBuilder?id=' . request('id') . '&user_id=' . auth()->user()->id) }}&page={{ request('page') }}&wstep={{ request('wstep') }}">
        @if(config('app.env') == 'local')
            <iframe id="ipanorama-frame" src="/uploads/ipanoramaBuilder/?id={{ request('id') }}&user_id={{ auth()->user()->id}}&page={{ request('page') }}&wstep={{ request('wstep') }}"></iframe>
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
        .btn-primary {
            background-color: #0073aa !important;
        }

        .btn-panorama-tutorial{
            position: fixed;
            z-index: 9999;
        }
    </style>
@endpush
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
                window.location.href = "/user/virtuard-360/edit?id={{ request('id') }}&user_id={{ request('user_id') }}";
            }
        });

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
        
        function load360WizardTutorialPart1() {
            tour.addStep({
                id: 'step-4',
                title: '4 - Add New Image',
                text: 'Click on button <b>Add New Image</b>',
                attachTo: {
                    element: '#btn-image',
                    on: 'right'
                },
            });

            $('#btn-image').click(function() {
                setTimeout(() => {
                    tour.next();
                }, 500)
            });

            tour.addStep({
                id: 'step-5',
                title: '5 - Add New Image',
                text: 'Type the title of your image',
                attachTo: {
                    element: '#modalAddImage #image-title',
                    on: 'top'
                },
                buttons: [
                    {
                        text: 'Next',
                        action: () => {
                            const titleInput = document.querySelector('#modalAddImage #image-title');
                            if (titleInput && titleInput.value.trim() !== '') {
                                tour.next();
                            } else {
                                alert('Please fill in the image title before proceeding!');
                            }
                        }
                    }
                ]
            });

            tour.addStep({
                id: 'step-6',
                title: '6 - Add New Image',
                text: 'Upload your 360 images by clicking the <b>Choose files</b> button',
                attachTo: {
                    element: '#modalAddImage #image-files',
                    on: 'top'
                },
                buttons: [
                    {
                        text: 'Next',
                        action: () => {
                            const fileInput = document.querySelector('#modalAddImage #image-files');
                            if (fileInput && fileInput.files.length > 0) {
                                tour.next();
                            } else {
                                alert('Please upload at least one image before proceeding!');
                            }
                        }
                    }
                ]
            });

            tour.addStep({
                id: 'step-7',
                title: '7 - Add New Image',
                text: 'Click on the <b>Submit</b> button to save your image',
                attachTo: {
                    element: '#modalAddImage #image-submit',
                    on: 'right'
                },
            });

            tour.start();
            // tour.show(wstep);
        }
        
        // function load360WizardTutorialPart2() {
        //     const iframe = document.getElementById('frame-panorama');
            
        //     tour.addStep({
        //         id: 'step-8',
        //         title: '8 - Open Scenes',
        //         text: 'Click on tab <b>Scenes</b>',
        //         attachTo: {
        //             element: '',
        //             on: 'left'
        //         },
        //         buttons: [
        //             {
        //             text: 'Next',
        //             action: () => {
        //                 tour.cancel();
        //             }
        //             }
        //         ]
        //     });

        //     tour.addStep({
        //         id: 'step-9',
        //         title: '9 - Open Scenes',
        //         text: 'Click add button <b>"+"</b> to open scenes form',
        //         attachTo: {
        //             element: '',
        //             on: 'left'
        //         },
        //         buttons: [
        //             {
        //             text: 'Next',
        //             action: () => {
        //                 tour.next();
        //             }
        //             }
        //         ]
        //     });

        //     tour.start();

        //     const iframeDocument = iframe.contentWindow.document;
        //     const scenesElement = iframeDocument.querySelector('#scenes');
        //     const btnAddScene = iframeDocument.querySelector('#btn-add-scene');

        //     scenesElement.addEventListener('click', function() {
        //         console.log('clicked');
        //         tour.next();
        //     });
        // }

        if (page === 'edit' && !isNaN(wstep) && wstep === 3) {
            load360WizardTutorialPart1();
        }

        // function load360Tutorial(){
        //     const tour = new Shepherd.Tour({
        //         defaultStepOptions: {
        //             cancelIcon: {
        //                 enabled: true
        //             },
        //             classes: 'shepherd-theme-dark',
        //             scrollTo: {
        //                 behavior: 'smooth',
        //                 block: 'center'
        //             }
        //         }
        //     });

        //     tour.addStep({
        //         title: 'Add 360 Images',
        //         text: `This button is used to add some images to your 360 pictures`,
        //         attachTo: {
        //             element: '.add-image',
        //             on: 'bottom'
        //         },
        //         buttons: [{
        //             action() {
        //                 $('#btn-image').click();

        //                 setTimeout(() => {
        //                     this.next();
        //                 }, 400);
        //             },
        //             text: 'Next'
        //         }],
        //         id: 'creating'
        //     });

        //     tour.addStep({
        //         title: 'Add title for your image',
        //         text: `This field is used to give title for your 360 image`,
        //         attachTo: {
        //             element: '.title-image',
        //             on: 'bottom'
        //         },
        //         buttons: [{
        //                 action() {
        //                     return this.back();
        //                 },
        //                 classes: 'shepherd-button-secondary',
        //                 text: 'Back'
        //             },
        //             {
        //                 action() {
        //                     return this.next();
        //                 },
        //                 text: 'Next'
        //             }
        //         ],
        //         id: 'editing'
        //     });

        //     tour.addStep({
        //         title: 'Upload your picture',
        //         text: `You may upload your picture by clicking the upload button`,
        //         attachTo: {
        //             element: '#image',
        //             on: 'left'
        //         },
        //         buttons: [{
        //                 action() {
        //                     return this.back();
        //                 },
        //                 classes: 'shepherd-button-secondary',
        //                 text: 'Back'
        //             },
        //             {
        //                 action() {
        //                     $('#modal-close').click();
        //                     setTimeout(() => {
        //                         this.next();
        //                     }, 400);
        //                 },
        //                 text: 'Next'
        //             }
        //         ],
        //         id: 'editing'
        //     });

        //     tour.addStep({
        //         title: '360 Image Setting Tab',
        //         text: `This tab is the tab where you can edit or set up your 360 image`,
        //         attachTo: {
        //             element: '#ipanorama-frame',
        //             on: 'top'
        //         },
        //         buttons: [{
        //                 action() {
        //                     $('#btn-image').click();

        //                     setTimeout(() => {
        //                         this.back();
        //                     }, 400);
        //                 },
        //                 classes: 'shepherd-button-secondary',
        //                 text: 'Back'
        //             },
        //             {
        //                 action() {
        //                     // updateIsTourField();
        //                     return this.complete();
        //                 },
        //                 text: 'Done'
        //             }
        //         ],
        //         id: 'editing'
        //     });

        //     tour.start();
        // }

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

        // @if(request()->has('id'))
        //     load360Tutorial();
        // @endif
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

                // if (page === 'edit' && !isNaN(wstep) && wstep === 4) {
                //     load360WizardTutorialPart2();
                // }
            });
        }

        document.addEventListener("DOMContentLoaded", function(event) { 
            initPanorama();
        });
    </script>
@endpush