<!-- Modal -->
<div class="modal fade" id="modalAddImage" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add New Image</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('user.virtuard-360.add-new-image-service') }}" method="POST"
                enctype="multipart/form-data">
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
                        <input type="file" multiple id="image-files" name="images[]" class="form-control-file" id="image" accept="image/jpeg, image/png, image/webp" required>
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

<!-- Modal -->
<div class="modal fade" id="modalIpanoramaTutorial" aria-labelledby="ipanoramaTutorialLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{-- <iframe width="100%" height="350" src="https://www.youtube.com/embed/r4btk4OgCJ0?si=zYydQU50Tv4OxsNA" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe> --}}
                @include('partials.ipanorama.tutorial')
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"
                    id="modal-close">Close</button>
            </div>
        </div>
    </div>
</div>
