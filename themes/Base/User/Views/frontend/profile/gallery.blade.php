<!-- HTML -->
<div class="container">
    <div class="row mt-2">
        @foreach ($userPosts as $post)
            @if ($post->ipanorama)
                <div class="col-4  mb-2">
                    <div class="gallery-item">
                        <a class="preview-panorama cursor-pointer"
                            data-id="{{ $post->ipanorama->id }}" 
                            data-code="{{ $post->ipanorama->code }}"
                            data-user_id="{{ $post->ipanorama->user_id }}">
                            <img 
                                src="{{ getThumbPanorama($post->ipanorama) }}" 
                                class="gallery-image thumb-panorama" 
                                alt="image">
                        </a>
                    </div>
                </div>
            @endif
            @foreach ($post->medias as $media)
                <div class="col-4  mb-2">
                    <div class="gallery-item">
                        <a href="{{ asset('uploads/' . $media->media) }}" data-lightbox="image-1">
                            <img 
                               class="img-responsive lazy loaded"
                                data-src="{{ asset('uploads/' . $media->media) }}" 
                                alt="image" 
                                src="{{ asset('uploads/' . $media->media) }}"
                                data-was-processed="true" />
                        </a>
                    </div>
                </div>
            @endforeach
        @endforeach
    </div>
</div>

<section class="section-modal">
    @include('vendor.ipanorama.demo.includes.ipanorama-modal')
</section>

<style>
    .gallery-item {
        position: relative;
        overflow: hidden;
        background: #f0f0f0;
        aspect-ratio: 1 / 1; 
        display: flex;
        justify-content: center;
        align-items: center;
    }

    

    .col-4 {
        padding-left: 5px;  
        padding-right: 5px;
    }

    .col-4.mb-2 {
        margin-bottom: 5px; 
    }
</style>
