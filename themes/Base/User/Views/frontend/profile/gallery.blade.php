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
                    <div class="gallery-item">
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

                        {{-- Tombol Like & Comment --}}
                        <div class="action-btn-wrapper">
                            @auth
                                <a href="javascript:void(0)"
                                   class="action-btn like-btn {{ $liked->count() > 0 ? 'liked' : '' }}"
                                   data-post-id="{{ $post->id }}"
                                   data-like-url="{{ route('post.like', ['id' => $post->id]) }}">
                                    <i class="fa {{ $liked->count() > 0 ? 'fa-heart' : 'fa-heart-o' }}"></i>
                                    <span class="action-count like-count-{{ $post->id }}">{{ $post->likes->count() }}</span>
                                </a>
                            @else
                                <a href="javascript:void(0)"
                                   onclick="alert('You need to login to like this post');"
                                   class="action-btn cursor-pointer">
                                    <i class="fa fa-heart-o"></i>
                                    <span class="action-count">{{ $post->likes->count() }}</span>
                                </a>
                            @endauth

                            <a href="#" class="action-btn comment-btn" data-toggle="modal" data-target="#commentModal{{ $post->id }}">
                                <i class="fa fa-comment-o"></i>
                                <span class="action-count">{{ $post->comments->count() }}</span>
                            </a>
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
                    <div class="gallery-item">
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

                            {{-- Tombol Like & Comment --}}
                            <div class="action-btn-wrapper">
                                @auth
                                    <a href="javascript:void(0)"
                                       class="action-btn like-btn {{ $liked->count() > 0 ? 'liked' : '' }}"
                                       data-post-id="{{ $post->id }}"
                                       data-like-url="{{ route('post.like', ['id' => $post->id]) }}">
                                        <i class="fa {{ $liked->count() > 0 ? 'fa-heart' : 'fa-heart-o' }}"></i>
                                        <span class="action-count like-count-{{ $post->id }}">{{ $post->likes->count() }}</span>
                                    </a>
                                @else
                                    <a href="javascript:void(0)"
                                       onclick="alert('You need to login to like this post');"
                                       class="action-btn cursor-pointer">
                                        <i class="fa fa-heart-o"></i>
                                        <span class="action-count">{{ $post->likes->count() }}</span>
                                    </a>
                                @endauth

                                <a href="#" class="action-btn comment-btn" data-toggle="modal" data-target="#commentModal{{ $post->id }}">
                                    <i class="fa fa-comment-o"></i>
                                    <span class="action-count">{{ $post->comments->count() }}</span>
                                </a>
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
                    <div class="gallery-item">

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


                            {{-- Tombol Like & Comment --}}
                            <div class="action-btn-wrapper">
                                @auth
                                    <a href="javascript:void(0)"
                                       class="action-btn like-btn {{ $liked->count() > 0 ? 'liked' : '' }}"
                                       data-post-id="{{ $post->id }}"
                                       data-like-url="{{ route('post.like', ['id' => $post->id]) }}">
                                        <i class="fa {{ $liked->count() > 0 ? 'fa-heart' : 'fa-heart-o' }}"></i>
                                        <span class="action-count like-count-{{ $post->id }}">{{ $post->likes->count() }}</span>
                                    </a>
                                @else
                                    <a href="javascript:void(0)"
                                       onclick="alert('You need to login to like this post');"
                                       class="action-btn cursor-pointer">
                                        <i class="fa fa-heart-o"></i>
                                        <span class="action-count">{{ $post->likes->count() }}</span>
                                    </a>
                                @endauth

                                <a href="#" class="action-btn comment-btn" data-toggle="modal" data-target="#commentModal{{ $post->id }}">
                                    <i class="fa fa-comment-o"></i>
                                    <span class="action-count">{{ $post->comments->count() }}</span>
                                </a>
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

                {{-- Comment Modal --}}
                <div class="modal fade" id="commentModal{{ $post->id }}" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-dialog-centered modal-xl" role="document" style="max-height: 90vh; margin: 1.75rem auto;">
                        <div class="modal-content" style="height: 90vh; max-width: 1200px;">
                            <div class="modal-header">
                                <h5 class="modal-title">{{ __('Post') }}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body p-0" style="height: calc(100% - 60px); overflow: hidden;">
                                <div class="row no-gutters" style="height: 100%;">
                                    {{-- Left Side - Post Media (FIXED, NO SCROLL) --}}
                                    <div class="col-12 col-md-6 bg-dark d-flex align-items-center justify-content-center"
                                         style="height: 100%;">
                                        @php
                                            $firstMedia = $post->medias->first();
                                        @endphp

                                        @if($firstMedia)
                                            @if($firstMedia->type == 'image')
                                                @if($firstMedia->is_360_media)
                                                    {{-- 360 Image --}}
                                                    <div id="panorama-modal-{{ $post->id }}" style="width: 100%; height: 100%;"></div>
                                                    <script>
                                                        document.addEventListener("DOMContentLoaded", function () {
                                                            pannellum.viewer('panorama-modal-{{ $post->id }}', {
                                                                "type": "equirectangular",
                                                                "panorama": "/uploads/{{ $firstMedia->media }}",
                                                                "autoLoad": true,
                                                                "showZoomCtrl": true
                                                            });
                                                        });
                                                    </script>
                                                @else
                                                    {{-- Regular Image --}}
                                                    <img src="{{ asset('uploads/' . $firstMedia->media) }}"
                                                         alt="Post"
                                                         style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                                @endif
                                            @elseif($firstMedia->type == 'video')
                                                {{-- Video --}}
                                                <div class="video-wrapper" style="width: 100%; height: 100%;">
                                                    <video controls id="video-modal-{{ $post->id }}" preload="auto"
                                                           class="video-js vjs-default-skin"
                                                           style="width: 100%; height: 100%;">
                                                        <source src="{{ url('uploads/' . $firstMedia->media) }}" type="video/mp4">
                                                    </video>
                                                </div>
                                                <script>
                                                    document.addEventListener("DOMContentLoaded", function() {
                                                        var playerModal{{ $post->id }} = videojs("video-modal-{{ $post->id }}", {
                                                            controls: true,
                                                            autoplay: false,
                                                            fluid: false,
                                                            controlBar: {
                                                                fullscreenToggle: true,
                                                            }
                                                        });

                                                        @if($firstMedia->is_360_media)
                                                        playerModal{{ $post->id }}.vr({
                                                            projection: "360",
                                                        });
                                                        @endif
                                                    });
                                                </script>
                                            @endif
                                        @else
                                            <p class="text-white">{{ __('No media available') }}</p>
                                        @endif
                                    </div>

                                    {{-- Right Side - Comments Section --}}
                                    <div class="col-12 col-md-6 d-flex flex-column bg-white" style="height: 100%;">

                                        {{-- Comments List (SCROLLABLE AREA) --}}
                                        <div id="commentsList{{ $post->id }}" class="p-3 bg-white"
                                             style="overflow-y: auto; flex: 1 1 auto; height: 0;">

                                            @forelse($post->comments as $comment)
                                                <div class="mb-3 comment-item">
                                                    <div class="d-flex">
                                                        <img src="{{ $comment->user->getAvatarUrl() ?? asset('images/avatar.png') }}"
                                                             alt="User"
                                                             style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
                                                        <div class="ml-2 flex-grow-1">
                                                            <div class="bg-light p-2 rounded">
                                                                <p class="m-0 font-weight-bold" style="font-size: 0.9rem;">
                                                                    {{ $comment->user->display_name ?? $comment->user->name }}
                                                                </p>
                                                                <p class="m-0" style="font-size: 0.9rem;">{{ $comment->comment }}</p>
                                                            </div>
                                                            <p class="m-0 mt-1 text-muted" style="font-size: 0.75rem;">
                                                                {{ $comment->created_at->diffForHumans() }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <p id="noComments{{ $post->id }}" class="text-center text-muted">
                                                    {{ __('No comments yet. Be the first to comment!') }}
                                                </p>
                                            @endforelse
                                        </div>

                                        {{-- Like & Comment Count (FIXED) --}}
                                        <div class="px-3 py-2 border-top border-bottom bg-white" style="flex: 0 0 auto;">
                                            <div class="d-flex justify-content-between">
                                <span>
                                    <i class="fa fa-comment"></i> 
                                    <strong class="comment-count-{{ $post->id }}">{{ $post->comments->count() }}</strong> {{ __('comments') }}
                                </span>
                                            </div>
                                        </div>

                                        {{-- Comment Form (FIXED) --}}
                                        <div class="p-3 border-top bg-white" style="flex: 0 0 auto;">
                                            @auth
                                                <form action="{{ route('post.comment.store', $post->id) }}"
                                                      method="POST"
                                                      class="comment-form"
                                                      data-post-id="{{ $post->id }}">
                                                    @csrf
                                                    <div class="input-group">
                                                        <input type="text"
                                                               name="comment"
                                                               class="form-control border-0"
                                                               placeholder="{{ __('Write a comment...') }}"
                                                               style="background: #f0f2f5;"
                                                               required>
                                                        <div class="input-group-append">
                                                            <button class="btn btn-primary" type="submit">
                                                                <i class="fa fa-paper-plane"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            @else
                                                <p class="text-center text-muted mb-0">
                                                    <a href="{{ route('login') }}">{{ __('Login') }}</a> {{ __('to comment') }}
                                                </p>
                                            @endauth
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

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
        
        .action-btn-wrapper {
            position: absolute;
            bottom: 8px;
            left: 8px;
            z-index: 10;
            display: flex;
            gap: 8px;
        }

        .action-btn {
            background: rgba(0, 0, 0, 0.6);
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

        .action-btn:hover {
            background: rgba(0, 0, 0, 0.8);
            transform: scale(1.05);
            color: white;
            text-decoration: none;
        }

        .action-btn.liked {
            background: rgba(255, 20, 147, 0.8);
            color: white;
        }

        .action-btn.liked:hover {
            background: rgba(255, 20, 147, 1);
        }

        .action-btn i {
            font-size: 16px;
        }

        .action-count {
            font-weight: 600;
            font-size: 13px;
        }

        .comment-btn:hover {
            background: rgba(0, 123, 255, 0.8);
        }

        .comment-item {
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Scrollbar styling for comments */
        [id^="commentsList"]::-webkit-scrollbar {
            width: 6px;
        }

        [id^="commentsList"]::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        [id^="commentsList"]::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }

        [id^="commentsList"]::-webkit-scrollbar-thumb:hover {
            background: #555;
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

            .action-btn {
                padding: 4px 8px;
                font-size: 12px;
            }

            .action-btn i {
                font-size: 14px;
            }

            .action-count {
                font-size: 11px;
            }

            .action-btn-wrapper {
                gap: 6px;
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
        $(document).ready(function() {
            handleLikeButtons();
        });

        function handleLikeButtons() {
            $('.action-btn').on('click', function(e) {
                e.preventDefault();

                var btn = $(this);
                var postId = btn.data('post-id');
                var likeUrl = btn.data('like-url');
                console.log("Like URL: " + likeUrl);
                var icon = btn.find('i');
                var likeCount = $('.like-count-' + postId);
                var likeCountModal = $('.like-count-modal-' + postId);

                // Prevent multiple clicks
                if (btn.data('processing')) {
                    return;
                }

                btn.data('processing', true);

                $.ajax({
                    url: likeUrl,
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            if (response.liked) {
                                btn.addClass('liked');
                                icon.removeClass('fa-heart-o').addClass('fa-heart');

                              
                                icon.addClass('like-animation');
                                setTimeout(function() {
                                    icon.removeClass('like-animation');
                                }, 600);
                            } else {
                                btn.removeClass('liked');
                                icon.removeClass('fa-heart').addClass('fa-heart-o');
                            }
                            
                            likeCount.text(response.total_likes);
                            likeCountModal.text(response.total_likes);
                        }

                        btn.data('processing', false);
                    },
                    error: function(xhr) {
                        btn.data('processing', false);

                        if (xhr.status === 401) {
                            alert('You need to login to like this post');
                        } else {
                            alert('Failed to like/unlike post. Please try again.');
                        }
                    }
                });
            });
        }
        
        function auto_grow(element) {
            element.style.height = "5px";
            element.style.height = (element.scrollHeight) + "px";
        }

        // Handle comment form submission via AJAX
        $(document).ready(function() {
            $('.comment-form').on('submit', function(e) {
                e.preventDefault();

                var form = $(this);
                var postId = form.data('post-id');
                var commentInput = form.find('input[name="comment"]');
                var commentText = commentInput.val();

                if (!commentText.trim()) {
                    return;
                }

                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    // Update bagian success di script AJAX yang sudah ada
                    success: function(response) {
                        // Clear input
                        commentInput.val('');

                        // Hide "no comments" message if exists
                        $('#noComments' + postId).hide();

                        // Add new comment to the list
                        var newComment = `
        <div class="mb-3 comment-item">
            <div class="d-flex">
                <img src="${response.user.avatar}" 
                     alt="User" 
                     style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
                <div class="ml-2 flex-grow-1">
                    <div class="bg-light p-2 rounded">
                        <p class="m-0 font-weight-bold" style="font-size: 0.9rem;">${response.user.name}</p>
                        <p class="m-0" style="font-size: 0.9rem;">${response.comment}</p>
                    </div>
                    <p class="m-0 mt-1 text-muted" style="font-size: 0.75rem;">Just now</p>
                </div>
            </div>
        </div>
    `;

                        $('#commentsList' + postId).append(newComment);

                        // Scroll to bottom
                        var commentsList = document.getElementById('commentsList' + postId);
                        commentsList.scrollTop = commentsList.scrollHeight;

                        // Update comment count in the button AND in modal
                        var currentCount = parseInt($('[data-target="#commentModal' + postId + '"] .action-count').text());
                        $('[data-target="#commentModal' + postId + '"] .action-count').text(currentCount + 1);
                        $('.comment-count-' + postId).text(currentCount + 1);
                    },
                    error: function(xhr) {
                        alert('Failed to post comment. Please try again.');
                    }
                });
            });
        });
        
        
    </script>
@endpush