<div class="container">
    <div class="gallery">
        @forelse ($userPosts as $post)
            <!-- Post Panorama -->
            @if ($post->ipanorama)
                <div class="gallery-item" tabindex="0">
                    <a data-id="{{ $post->ipanorama->id }}" data-code="{{ $post->ipanorama->code }}"
                        class="preview-panorama cursor-pointer">
                        <img id="thumb-panorama-{{ $post->ipanorama->id }}" src="{{ getThumbPanorama($post->ipanorama) }}"
                            class="gallery-image thumb-panorama" alt="image">
                    </a>
                </div>
            @endif
            <!-- Post Media -->
            @forelse ($post->medias as $media)
                <div class="gallery-item" tabindex="0">
                    <a href="{{ asset('uploads/' . $media->media) }}" data-lightbox="image-1">
                        <img src="{{ asset('uploads/' . $media->media) }}" class="gallery-image" alt="image">
                    </a>
                    {{-- <div class="gallery-item-info">
                        <ul>
                            <li class="gallery-item-likes">
                                <span class="visually-hidden">Likes:</span>
                                <i class="fas fa-heart" aria-hidden="true"></i> 
                                56
                            </li>
                            <li class="gallery-item-comments">
                                <span class="visually-hidden">Comments:</span>
                                <i class="fas fa-comment" aria-hidden="true"></i>
                                2
                            </li>
                        </ul>
                    </div> --}}
                </div>
            @empty
            @endforelse
        @empty
        @endforelse
    </div>
    <!-- End of gallery -->
    {{-- <div class="loader"></div> --}}
</div>
<!-- End of container -->

<section class="section-modal">
    @include('vendor.ipanorama.demo.includes.ipanorama-modal')
</section>
