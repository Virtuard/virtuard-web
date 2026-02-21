@extends('v2.layouts.vendor')

@section('content')

    {{-- Fallback Check just in case --}}
    @if(empty($panorama))
        <div style="padding: 15px; background: #fff3cd; color: #856404; border-radius: 5px; margin-bottom: 20px;">
            <b>You must first create a title for your Virtuard 360!</b>
        </div>
        {{-- Mocking the form --}}
        <a href="{{ route('vendor2.virtuard360.add') }}">Go back to add title</a>
    @else
        <div style="background: white; padding: 20px; border-radius: 10px; border: 1px solid #eee;">
            {{-- Edit Title Form (V1 natively didn't re-save title here organically, but mockup shows it as editable) --}}
            <div style="margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 20px;">
                <label style="display:block; margin-bottom: 5px; color: #666; font-size: 13px;">Virtuard Asset Title *</label>
                <input type="text" id="title" name="title" value="{{ $panorama->title }}" class="form-control"
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
            </div>

            {{-- Mockup shows Add New Image Button over the iPanorama Builder Box --}}
            <div style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                <label style="margin:0; color: #666; font-size: 13px;">Virtuard Asset 360 *</label>
                {{-- Bootstrap Modal Trigger used by V1 inside V2 layout --}}
                <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#modalAddImage"
                    style="padding: 5px 15px; border-radius: 20px; background: transparent; border: 1px solid #007bff; color: #007bff; cursor: pointer;">
                    Add New Image
                </button>
            </div>

            {{-- Add Image Modal (Included directly from V1 to route to V2 properly) --}}
            <div class="modal fade" id="modalAddImage" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Add New Image</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="{{ route('vendor2.virtuard360.storeImage') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body">
                                <input type="hidden" name="panorama_id" value="{{ $panorama->id }}">
                                <input type="hidden" name="user_id" value="{{ $user_id }}">
                                <input type="hidden" name="page" value="{{ request('page') }}">
                                <input type="hidden" name="wstep" value="{{ request('wstep') }}">

                                <div class="form-group title-image">
                                    <label>Title</label>
                                    <input type="text" id="image-title" name="title" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Upload Images (can upload multiple images)</label>
                                    <input type="file" multiple id="image-files" name="images[]" class="form-control-file"
                                        id="image" accept="image/jpeg, image/png, image/webp" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal"
                                    id="modal-close">Close</button>
                                <button type="submit" id="image-submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- 360 BUILDER IFRAME INTEGRATION --}}
            <div style="border: 1px solid #ddd; border-radius: 10px; overflow: hidden; height: 75vh;">
                @if(config('app.env') == 'local')
                    <iframe id="ipanorama-frame"
                        src="/uploads/ipanoramaBuilder/?id={{ request('id') }}&user_id={{ $user_id }}&page={{ $page }}&wstep={{ $wstep }}"
                        style="width: 100%; height: 100%; border: none;"></iframe>
                @else
                    {{-- Assuming production needs explicit URL or DOM init --}}
                    <input type="hidden" id="url_panorama"
                        value="{{ url('/uploads/ipanoramaBuilder?id=' . request('id') . '&user_id=' . $user_id) }}&page={{ $page }}&wstep={{ $wstep }}">
                    <div id="ipanorama-frame" style="width: 100%; height: 100%;"></div>
                @endif
            </div>

            <div style="display:flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                <a href="{{ route('vendor2.virtuard360.index') }}"
                    style="padding: 10px 20px; border: 1px solid #ddd; background: #fff; text-decoration: none; color: black; border-radius:5px;">
                    Cancel Add
                </a>
                <button
                    style="padding: 10px 20px; border: none; background: #007bff; color: white; border-radius:5px; cursor: pointer;">
                    Add New Virtuard Asset
                </button>
            </div>
        </div>
    @endif
@endsection

@push('js')
    {{-- Integrate the V1 iframe loading mechanics for production environments --}}
    <script>
        function initPanorama() {
            let urlPan = $('#url_panorama').val();
            if (urlPan) {
                var iframe = $('<iframe>').attr({
                    src: urlPan,
                    id: "frame-panorama",
                    width: '100%',
                    height: '100%',
                    style: 'border:none;'
                });
                $('#ipanorama-frame').append(iframe);
                $('#frame-panorama').on('load', function () {
                    var iframeContent = $('#frame-panorama').contents();
                    iframeContent.find('.ipnrm-ui-cmd-load').trigger('click');
                    iframeContent.find('.ipnrm-ui-cmd-load').trigger('click');
                    iframeContent.find('#frame-load').find('.ipnrm-ui-toggle').trigger('click');
                });
            }
        }

        document.addEventListener("DOMContentLoaded", function (event) {
            if (document.getElementById('url_panorama')) {
                initPanorama();
            }
        });
    </script>
@endpush