<div class="bravo-list-event layout_normal">
    <div class="sub-title"></div>
    <div class="list-item">
        <div class="row">
            @forelse ($userPosts as $post)
                @if ($post->ipanorama)
                    <div class="col-lg-4 col-md-6">
                        <div class="item-loop" style="padding-bottom: 0;">
                            <div class="thumb-image">
                                <a data-id="{{ $post->ipanorama->id }}" data-code="{{ $post->ipanorama->code }}"
                                    class="preview-panorama cursor-pointer">
                                    <img id="thumb-panorama-{{ $post->ipanorama->id }}" src="{{ getThumbPanorama($post->ipanorama) }}"
                                        class="gallery-image thumb-panorama" alt="image">
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
                @forelse ($post->medias as $media)
                    <div class="col-lg-4 col-md-6">
                        <div class="item-loop" style="padding-bottom: 0;">
                            <div class="thumb-image">
                                <a href="{{ asset('uploads/' . $media->media) }}" data-lightbox="image-1">
                                    <img class="img-responsive lazy loaded"
                                        data-src="{{ asset('uploads/' . $media->media) }}" alt="image" src="{{ asset('uploads/' . $media->media) }}"
                                        data-was-processed="true" />
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                @endforelse
            @empty
            @endforelse
        </div>
    </div>
</div>


<section class="section-modal">
    @include('vendor.ipanorama.demo.includes.ipanorama-modal')
</section>
