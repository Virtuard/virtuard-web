<!-- HTML -->
<div class="container">
    <div class="row mt-2">
        @foreach ($userPanoramas as $panorama)
            <div class="col-4 mb-2">
                <div class="gallery-item">
                    {{-- Tombol Delete --}}
                    @if (auth()->check() && auth()->user()->id == $panorama->user_id)
                        <form action="{{ route('post.destroy', $panorama->id) }}" method="POST"
                            class="delete-btn-wrapper">
                            @csrf
                            @method('delete')
                            <button type="submit" class="delete-btn">
                                <i class="fa fa-trash"></i>
                            </button>
                        </form>
                    @endif

                    <a class="preview-panorama cursor-pointer" data-id="{{ $panorama->id }}"
                        data-code="{{ $panorama->code }}" data-user_id="{{ $panorama->user_id }}">
                        <img loading='lazy'src="{{ getThumbPanorama($panorama) }}" class="gallery-image thumb-panorama"
                            alt="image">
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>

<section class="section-modal">
     <!-- panoramaModal -->
    <div class="modal fade" id="panoramaModal" tabindex="-1" role="dialog" aria-labelledby="panoramaModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="panoramaModalLabel">Preview</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @isset($panorama)
                @if ($panorama->status == 'publish' and $panorama->author->checkUserPlanStatus())
                <div id="mypanorama" class="load-panorama"
                    style=" position: relative; width: 100%; height: 450px; z-index: 1;">
                </div>
                @else
                <div id="mypanorama" class="load-panorama"
                    style=" position: relative; width: 100%; height: 450px; z-index: 1;">
                </div>
                {{-- <p class="text-center">{{ __("If you don't preview the 360 tour. The uploader does not have a subscription plan or the subscription has expired.") }}</p> --}}
                @endif
                @endisset
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
    </div>
</section>

@push('js')
<script>
    $(document).ready(function() {
        previewPanorama();
    });

    function previewPanorama() {
        $('.preview-panorama').click(function() {
            let panoramaCode = $(this).data('code');
            let userId = $(this).data('user_id');
            
            panoramaCode = JSON.stringify(panoramaCode);
            panoramaCode = panoramaCode.replaceAll(`upload/`, `/uploads/ipanoramaBuilder/upload/${userId}/`);
            panoramaCode = panoramaCode.replaceAll(`/uploads/ipanoramaBuilder/upload/${userId}/${userId}/`, `/uploads/ipanoramaBuilder/upload/${userId}/`);
            panoramaCode = JSON.parse(panoramaCode)
            $(`#mypanorama`).ipanorama(panoramaCode);
            $('#panoramaModal').modal('toggle');
        })
    }
</script>
@endpush

<style>
    .delete-btn-wrapper {
        position: absolute;
        top: 8px;
        right: 8px;
        z-index: 10;
    }

    .delete-btn {
        background: rgba(255, 0, 0, 0.7);
        color: white;
        border: none;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease-in-out;
        font-size: 16px;
    }

    .delete-btn:hover {
        background: rgba(255, 0, 0, 1);
        transform: scale(1.1);
        box-shadow: 0px 4px 10px rgba(255, 0, 0, 0.5);
    }

    .gallery-item {
        position: relative;
        overflow: hidden;
        background: #f0f0f0;
        aspect-ratio: 1 / 1;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .gallery-item img {

        height: 100%;
        object-fit: fill;
        transform: scale(0.5);
        transform-origin: center center;
        background-color: #f0f0f0;
    }

    @media (max-width: 768px) {
        .gallery-item img {
            transform: scale(0.15);
        }
    }

    .col-4 {
        padding-left: 5px;
        padding-right: 5px;
    }

    .col-4.mb-2 {
        margin-bottom: 5px;
    }
</style>
