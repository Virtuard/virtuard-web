<!-- HTML -->
<div class="container">
    <div class="row mt-2">
        @if (auth()->check() && auth()->user()->id == $user->id)
            <div class="col-md-4 col-6 mb-2 cursor-pointer" data-toggle="modal"
                 data-target="#modalGallery">
                <div class="gallery-item">
                    <div class="text-dark">
                        <i class="fa fa-plus" style="font-size: 40px"></i>
                    </div>
                </div>
            </div>
        @endif

        @foreach ($userPosts as $post)
            @php
                $galleries = $post->medias->where('type', 'image')->where('is_360_media', false);
                $galleries_360 = $post->medias->where('type', 'image')->where('is_360_media', true);

                $videos = $post->medias->where('type', 'video');
                $videos_360 = $post->medias->where('type', 'video')->where('is_360_media', true);
                
                $liked = \App\Models\PostLike::where('post_id', $post->id)->where('user_id', Auth::id());
            @endphp

            @foreach ($galleries as $media)
                <div class="col-md-4 col-6 mb-2">
                    <div id="Post-{{$post->id}}" class="gallery-item">
                        {{-- Tombol Delete --}}
                        @if (auth()->check() && auth()->user()->id == $post->user_id)
                            <form action="{{ route('post.destroy', $post->id) }}" method="POST"
                                  class="delete-btn-wrapper">
                                @csrf
                                @method('delete')
                                <button type="submit" class="delete-btn">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        @endif

                        {{-- Tombol Like --}}
                        <div class="like-btn-wrapper">
                            @auth
                                @if ($liked->count() > 0)
                                    <a href="{{ route('post.like', ['id' => $post->id]) }}" class="like-btn liked">
                                        <i class="fa fa-heart"></i>
                                        <span class="like-count">{{ $post->likes->count() }}</span>
                                    </a>
                                @else
                                    <a href="{{ route('post.like', ['id' => $post->id]) }}" class="like-btn">
                                        <i class="fa fa-heart-o"></i>
                                        <span class="like-count">{{ $post->likes->count() }}</span>
                                    </a>
                                @endif
                            @else
                                <a onclick="alert('You need to login to like this post');" class="like-btn cursor-pointer">
                                    <i class="fa fa-heart-o"></i>
                                    <span class="like-count">{{ $post->likes->count() }}</span>
                                </a>
                            @endauth
                        </div>

                        <a href="{{ asset('uploads/' . $media->media) }}" data-lightbox="image-1">
                            <img loading='lazy' class="img-responsive lazy loaded" data-src="{{ asset('uploads/' . $media->media) }}"
                                 alt="image" src="{{ asset('uploads/' . $media->media) }}"
                                 data-was-processed="true" />
                        </a>
                    </div>
                </div>
            @endforeach

            @foreach ($galleries_360 as $gallery)
                <div class="col-md-4 col-6 mb-2">
                    <div id="Post-{{$post->id}}" class="gallery-item">
                        @if (auth()->check() && auth()->user()->id == $post->user_id)
                            <form action="{{ route('post.destroy', $post->id) }}" method="POST"
                                  class="delete-btn-wrapper">
                                @csrf
                                @method('delete')
                                <button type="submit" class="delete-btn">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        @endif

                        {{-- Tombol Like --}}
                        <div class="like-btn-wrapper">
                            @auth
                                @if ($liked->count() > 0)
                                    <a href="{{ route('post.like', ['id' => $post->id]) }}" class="like-btn liked">
                                        <i class="fa fa-heart"></i>
                                        <span class="like-count">{{ $post->likes->count() }}</span>
                                    </a>
                                @else
                                    <a href="{{ route('post.like', ['id' => $post->id]) }}" class="like-btn">
                                        <i class="fa fa-heart-o"></i>
                                        <span class="like-count">{{ $post->likes->count() }}</span>
                                    </a>
                                @endif
                            @else
                                <a onclick="alert('You need to login to like this post');" class="like-btn cursor-pointer">
                                    <i class="fa fa-heart-o"></i>
                                    <span class="like-count">{{ $post->likes->count() }}</span>
                                </a>
                            @endauth
                        </div>

                        <div class="panorama-image" style="width: 500px; height: 100%; object-fit: cover;" id="panorama-{{ $gallery->id }}"></div>
                    </div>
                </div>
            @endforeach

            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    @foreach ($galleries_360 as $gallery)
                    pannellum.viewer('panorama-{{ $gallery->id }}', {
                        "type": "equirectangular",
                        "panorama": "/uploads/{{ $gallery->media }}",
                        "autoLoad": true,
                        "showZoomCtrl": true
                    });
                    @endforeach
                });
            </script>

            @foreach ($videos as $vid)
                <div class="col-md-4 col-6 mb-2">
                    <div id="Post-{{$post->id}}" class="gallery-item">

                        @if (auth()->check() && auth()->user()->id == $post->user_id)
                            <form action="{{ route('post.destroy', $post->id) }}" method="POST"
                                  class="delete-btn-wrapper">
                                @csrf
                                @method('delete')
                                <button type="submit" class="delete-btn">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        @endif

                        {{-- Tombol Like --}}
                        <div class="like-btn-wrapper">
                            @auth
                                @if ($liked->count() > 0)
                                    <a href="{{ route('post.like', ['id' => $post->id]) }}" class="like-btn liked">
                                        <i class="fa fa-heart"></i>
                                        <span class="like-count">{{ $post->likes->count() }}</span>
                                    </a>
                                @else
                                    <a href="{{ route('post.like', ['id' => $post->id]) }}" class="like-btn">
                                        <i class="fa fa-heart-o"></i>
                                        <span class="like-count">{{ $post->likes->count() }}</span>
                                    </a>
                                @endif
                            @else
                                <a onclick="alert('You need to login to like this post');" class="like-btn cursor-pointer">
                                    <i class="fa fa-heart-o"></i>
                                    <span class="like-count">{{ $post->likes->count() }}</span>
                                </a>
                            @endauth
                        </div>

                        <div class="video-container">
                            <div class="video-wrapper">
                                <video controls id="video-{{ $vid->id }}" preload="auto" class="video-js vjs-default-skin">
                                    <source src="{{ url('uploads/' . $vid->media) }}" type="video/mp4">
                                </video>
                            </div>
                        </div>

                        <button class="custom-fullscreen-btn" data-video="video-{{ $vid->id }}">
                            ⛶
                        </button>
                    </div>
                </div>
            @endforeach

            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    @foreach ($videos as $vid)
                    var player{{ $vid->id }} = videojs("video-{{ $vid->id }}", {
                        controls: true,
                        autoplay: false,
                        fluid: true,
                        controlBar: {
                            fullscreenToggle: true,
                        }
                    });

                    @if($vid->is_360_media)
                    player{{ $vid->id }}.vr({
                        projection: "360",
                    });
                    @endif

                    document.addEventListener("keydown", function(event) {
                        if (event.key === "f") {
                            player{{ $vid->id }}.requestFullscreen();
                        }
                    });

                    document.querySelector("[data-video='video-{{ $vid->id }}']").addEventListener("click", function() {
                        if (player{{ $vid->id }}.requestFullscreen) {
                            player{{ $vid->id }}.requestFullscreen();
                        } else if (player{{ $vid->id }}.webkitEnterFullscreen) {
                            player{{ $vid->id }}.webkitEnterFullscreen();
                        } else if (player{{ $vid->id }}.mozRequestFullScreen) {
                            player{{ $vid->id }}.mozRequestFullScreen();
                        } else if (player{{ $vid->id }}.msRequestFullscreen) {
                            player{{ $vid->id }}.msRequestFullscreen();
                        }
                    });

                    @endforeach
                });
            </script>
        @endforeach
    </div>

    <div id="modalGallery" class="modal fade">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('post.store') }}" method="POST"
                  enctype="multipart/form-data" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Add Post') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="mb-4">
                        <label for="" class="form-label">{{ __('Caption') }}</label>
                        <textarea style="width: 100%; padding: 10px;" name="message" placeholder="{{ __('What\'s new?') }}"
                                  oninput="auto_grow(this)"></textarea>
                    </div>
                    <div class="mb-4">
                        <label for="" class="form-label">{{ __('Select Media') }}</label>
                        <input type="file" id="fileInput" class="m-0" name="media_user[]"
                               accept="image/*, video/*"
                               multiple>
                    </div>
                    <div class="">
                        <label for="is_360_media" class="form-label d-block">{{ __('Is it 360 media?') }}</label>
                        <label class="switch">
                            <input name="is_360_media" id="is_360_media" type="checkbox">
                            <div class="slider">
                                <div class="circle">
                                    <svg class="cross" xml:space="preserve" style="enable-background:new 0 0 512 512" viewBox="0 0 365.696 365.696" y="0" x="0" height="6" width="6" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" xmlns="http://www.w3.org/2000/svg">
                                        <g>
                                            <path data-original="#000000" fill="currentColor" d="M243.188 182.86 356.32 69.726c12.5-12.5 12.5-32.766 0-45.247L341.238 9.398c-12.504-12.503-32.77-12.503-45.25 0L182.86 122.528 69.727 9.374c-12.5-12.5-32.766-12.5-45.247 0L9.375 24.457c-12.5 12.504-12.5 32.77 0 45.25l113.152 113.152L9.398 295.99c-12.503 12.503-12.503 32.769 0 45.25L24.48 356.32c12.5 12.5 32.766 12.5 45.247 0l113.132-113.132L295.99 356.32c12.503 12.5 32.769 12.5 45.25 0l15.081-15.082c12.5-12.504 12.5-32.77 0-45.25zm0 0"></path>
                                        </g>
                                    </svg>
                                    <svg class="checkmark" xml:space="preserve" style="enable-background:new 0 0 512 512" viewBox="0 0 24 24" y="0" x="0" height="10" width="10" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" xmlns="http://www.w3.org/2000/svg">
                                        <g>
                                            <path class="" data-original="#000000" fill="currentColor" d="M9.707 19.121a.997.997 0 0 1-1.414 0l-5.646-5.647a1.5 1.5 0 0 1 0-2.121l.707-.707a1.5 1.5 0 0 1 2.121 0L9 14.171l9.525-9.525a1.5 1.5 0 0 1 2.121 0l.707.707a1.5 1.5 0 0 1 0 2.121z"></path>
                                        </g>
                                    </svg>
                                </div>
                            </div>
                        </label>
                    </div>
                    <div class="">
                        <label for="" class="form-label d-block">{{ __('Status') }}</label>
                        <select class="h-100" id="filter-post" name="type_post"
                                style="
                            padding: 5px 16px;
                            background: #f5f5f5;
                            border: 0;
                            border-radius: 100px;
                            font-weight: 600;
                            outline: none;
                        ">
                            <option value="">{{ __('Public') }}</option>
                            <option value="{{ auth()->check() ? 'me' : 'login' }}" {{ request('filter') == 'me' ? 'selected' : '' }}>{{ __('Only Me') }}</option>
                            <option value="{{ auth()->check() ? 'friend' : 'login' }}" {{ request('filter') == 'friend' ? 'selected' : '' }}>{{ __('My Friends') }}</option>
                        </select>
                        <a class="cursor-pointer d-none">
                            <i class="fa fa-lg fa-smile-o ml-3"></i>
                        </a>
                        <div class="cursor-pointer d-none" id="toogle-tag" onclick="showSelect()">
                            <i class="fa fa-lg fa-tags ml-3"></i>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">{{ __('Post') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('css')
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

        /* Like Button Styles */
        .like-btn-wrapper {
            position: absolute;
            bottom: 8px;
            left: 8px;
            z-index: 10;
        }

        .like-btn {
            background: rgba(0, 0, 0, 0.2);
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
            transition: all 0.3s ease-in-out;
            font-size: 14px;
            text-decoration: none;
        }

        .like-btn:hover {
            background: rgba(0, 0, 0, 0.5);
            transform: scale(1.05);
            color: white;
            text-decoration: none;
        }

        .like-btn.liked {
            background: rgba(255, 20, 147, 0.8);
            color: white;
        }

        .like-btn.liked:hover {
            background: rgba(255, 20, 147, 1);
        }

        .like-btn i {
            font-size: 16px;
        }

        .like-count {
            font-weight: 600;
            font-size: 13px;
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

            .like-btn {
                padding: 4px 8px;
                font-size: 12px;
            }

            .like-btn i {
                font-size: 14px;
            }

            .like-count {
                font-size: 11px;
            }
        }

        .col-4 {
            padding-left: 5px;
            padding-right: 5px;
        }

        .col-4.mb-2 {
            margin-bottom: 5px;
        }

        .switch {
            --switch-width: 46px;
            --switch-height: 24px;
            --switch-bg: rgb(131, 131, 131);
            --switch-checked-bg: rgb(0, 218, 80);
            --switch-offset: calc((var(--switch-height) - var(--circle-diameter)) / 2);
            --switch-transition: all .2s cubic-bezier(0.27, 0.2, 0.25, 1.51);
            --circle-diameter: 18px;
            --circle-bg: #fff;
            --circle-shadow: 1px 1px 2px rgba(146, 146, 146, 0.45);
            --circle-checked-shadow: -1px 1px 2px rgba(163, 163, 163, 0.45);
            --circle-transition: var(--switch-transition);
            --icon-transition: all .2s cubic-bezier(0.27, 0.2, 0.25, 1.51);
            --icon-cross-color: var(--switch-bg);
            --icon-cross-size: 6px;
            --icon-checkmark-color: var(--switch-checked-bg);
            --icon-checkmark-size: 10px;
            --effect-width: calc(var(--circle-diameter) / 2);
            --effect-height: calc(var(--effect-width) / 2 - 1px);
            --effect-bg: var(--circle-bg);
            --effect-border-radius: 1px;
            --effect-transition: all .2s ease-in-out;
        }

        .switch input {
            display: none;
        }

        .switch {
            display: inline-block;
        }

        .switch svg {
            -webkit-transition: var(--icon-transition);
            -o-transition: var(--icon-transition);
            transition: var(--icon-transition);
            position: absolute;
            height: auto;
        }

        .switch .checkmark {
            width: var(--icon-checkmark-size);
            color: var(--icon-checkmark-color);
            -webkit-transform: scale(0);
            -ms-transform: scale(0);
            transform: scale(0);
        }

        .switch .cross {
            width: var(--icon-cross-size);
            color: var(--icon-cross-color);
        }

        .slider {
            -webkit-box-sizing: border-box;
            box-sizing: border-box;
            width: var(--switch-width);
            height: var(--switch-height);
            background: var(--switch-bg);
            border-radius: 999px;
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
            position: relative;
            -webkit-transition: var(--switch-transition);
            -o-transition: var(--switch-transition);
            transition: var(--switch-transition);
            cursor: pointer;
        }

        .circle {
            width: var(--circle-diameter);
            height: var(--circle-diameter);
            background: var(--circle-bg);
            border-radius: inherit;
            -webkit-box-shadow: var(--circle-shadow);
            box-shadow: var(--circle-shadow);
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
            -webkit-box-pack: center;
            -ms-flex-pack: center;
            justify-content: center;
            -webkit-transition: var(--circle-transition);
            -o-transition: var(--circle-transition);
            transition: var(--circle-transition);
            z-index: 1;
            position: absolute;
            left: var(--switch-offset);
        }

        .slider::before {
            content: "";
            position: absolute;
            width: var(--effect-width);
            height: var(--effect-height);
            left: calc(var(--switch-offset) + (var(--effect-width) / 2));
            background: var(--effect-bg);
            border-radius: var(--effect-border-radius);
            -webkit-transition: var(--effect-transition);
            -o-transition: var(--effect-transition);
            transition: var(--effect-transition);
        }

        .switch input:checked+.slider {
            background: var(--switch-checked-bg);
        }

        .switch input:checked+.slider .checkmark {
            -webkit-transform: scale(1);
            -ms-transform: scale(1);
            transform: scale(1);
        }

        .switch input:checked+.slider .cross {
            -webkit-transform: scale(0);
            -ms-transform: scale(0);
            transform: scale(0);
        }

        .switch input:checked+.slider::before {
            left: calc(100% - var(--effect-width) - (var(--effect-width) / 2) - var(--switch-offset));
        }

        .switch input:checked+.slider .circle {
            left: calc(100% - var(--circle-diameter) - var(--switch-offset));
            -webkit-box-shadow: var(--circle-checked-shadow);
            box-shadow: var(--circle-checked-shadow);
        }

        .video-container {
            position: relative;
            display: inline-block;
            width: 100%;
            max-width: 800px;
        }

        .video-wrapper {
            position: relative;
            width: 100%;
        }

        .custom-fullscreen-btn {
            position: absolute;
            bottom: 15px;
            right: 15px;
            width: 40px;
            height: 40px;
            background: rgba(0, 0, 0, 0.5);
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 24px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.3s;
        }

        .custom-fullscreen-btn:hover {
            background: rgba(0, 0, 0, 0.8);
        }
    </style>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.css"/>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js"></script>

    <link href="https://unpkg.com/video.js/dist/video-js.css" rel="stylesheet">
@endpush

@push('js')
    <script src="https://unpkg.com/video.js/dist/video.js"></script>
    <script src="https://unpkg.com/videojs-vr/dist/videojs-vr.min.js"></script>

    <script>
        function auto_grow(element) {
            element.style.height = "5px";
            element.style.height = (element.scrollHeight) + "px";
        }
    </script>
@endpush