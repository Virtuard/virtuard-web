<!-- HTML -->
<div class="container">
    <div class="row mt-2">
        @if (auth()->check() && auth()->user()->id == $user->id)
            <div class="col-md-4 col-6 mb-2 cursor-pointer" data-toggle="modal"
                 data-target="#modalGallery360">
                <div class="gallery-item">
                    <div class="text-dark">
                        <i class="fa fa-plus" style="font-size: 40px"></i>
                    </div>
                </div>
            </div>
        @endif

        @foreach ($userPanoramas as $post)
            @if ($post->ipanorama)
                @php
                    $liked = \App\Models\PostLike::where('post_id', $post->id)->where('user_id', Auth::id());
                @endphp

                <div class="col-md-4 col-6 mb-2">
                    <div class="gallery-item">
                        {{-- Tombol Delete --}}
                        @if (auth()->check() && auth()->user()->id == $post->ipanorama->user_id)
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

                        <a class="preview-panorama cursor-pointer" data-id="{{ $post->ipanorama->id }}"
                           data-code="{{ $post->ipanorama->code }}" data-user_id="{{ $post->user_id }}">
                            <img loading='lazy' src="{{ getThumbPanorama($post->ipanorama) }}" class="gallery-image thumb-panorama"
                                 alt="image">
                        </a>
                    </div>
                </div>

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
                                        {{-- Left Side - 360 Panorama (FIXED, NO SCROLL) --}}
                                        <div class="col-12 col-md-6 bg-dark d-flex align-items-center justify-content-center"
                                             style="height: 100%;">
                                            <div id="panorama-modal-{{ $post->id }}" style="width: 100%; height: 100%;"></div>
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
                                                <span>
                                                    <i class="fa fa-comment"></i> 
                                                    <strong class="comment-count-{{ $post->id }}">{{ $post->comments->count() }}</strong> {{ __('comments') }}
                                                </span>
                                            </div>

                                            {{-- Comment Form (FIXED) --}}
                                            <div class="p-3 border-top bg-white" style="flex: 0 0 auto;">
                                                @auth
                                                    <form action="{{ route('post.comment.store', $post->id) }}"
                                                          method="POST"
                                                          class="virtual-tour-comment-form"
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
            @endif
        @endforeach
    </div>
</div>

<section class="section-modal">
    <!-- panoramaModal -->
    <div class="modal fade" id="panoramaModal" tabindex="-1" role="dialog" aria-labelledby="panoramaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="panoramaModalLabel">Preview</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="mypanorama" class="load-panorama"
                         style=" position: relative; width: 100%; height: 450px; z-index: 1;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="modalGallery360" class="modal fade">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('post.store') }}" method="POST"
                  enctype="multipart/form-data" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Add 360 Post') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="mb-4">
                        <label for="" class="form-label">{{ __('Caption') }}</label>
                        <textarea style="width: 100%; padding: 10px;" name="message" placeholder="{{ __('What\'s new?') }}"></textarea>
                    </div>
                    <div class="mb-4">
                        <label for="panoramaSelect" class="form-label">{{ __('Select 360 Image') }}</label>
                        <select id="panoramaSelect" name="ipanorama_id" class="form-control">
                            <option value="">{{ __('Select your 360') }}</option>
                            @if (isset($dataIpanorama))
                                @foreach ($dataIpanorama as $panorama)
                                    @if ($panorama->code)
                                        <option value="{{ $panorama->id }}">{{ $panorama->title }}</option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
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
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">{{ __('Post') }}</button>
                </div>
            </form>
        </div>
    </div>
</section>

@push('js')
    <script>
        $(document).ready(function() {
            previewPanorama();
            initPanoramaModals();
            handleCommentForms();
            handleLikeButtons();
        });

        function previewPanorama() {
            $('.preview-panorama').click(function() {
                let panoramaCode = $(this).data('code');
                let userId = $(this).data('user_id');
                let panoramaId = $(this).data('id');

                panoramaCode = JSON.stringify(panoramaCode);
                panoramaCode = panoramaCode.replaceAll(`upload/`, `/uploads/ipanoramaBuilder/upload/${userId}/`);
                panoramaCode = panoramaCode.replaceAll(`/uploads/ipanoramaBuilder/upload/${userId}/${userId}/`, `/uploads/ipanoramaBuilder/upload/${userId}/`);
                panoramaCode = JSON.parse(panoramaCode)

                $(`#mypanorama`).ipanorama(panoramaCode);
                $('#panoramaModal').modal('toggle');
            })
        }

        // Initialize panorama in comment modals
        function initPanoramaModals() {
            $('[id^="commentModal"]').on('shown.bs.modal', function () {
                let modalId = $(this).attr('id');
                let postId = modalId.replace('commentModal', '');
                let panoramaContainer = $('#panorama-modal-' + postId);

                if (panoramaContainer.length && !panoramaContainer.data('initialized')) {
                    // Get panorama data from the preview link
                    let previewLink = $('[data-target="#' + modalId + '"]').closest('.gallery-item').find('.preview-panorama');
                    let panoramaCode = previewLink.data('code');
                    let userId = previewLink.data('user_id');

                    panoramaCode = JSON.stringify(panoramaCode);
                    panoramaCode = panoramaCode.replaceAll(`upload/`, `/uploads/ipanoramaBuilder/upload/${userId}/`);
                    panoramaCode = panoramaCode.replaceAll(`/uploads/ipanoramaBuilder/upload/${userId}/${userId}/`, `/uploads/ipanoramaBuilder/upload/${userId}/`);
                    panoramaCode = JSON.parse(panoramaCode);

                    panoramaContainer.ipanorama(panoramaCode);
                    panoramaContainer.data('initialized', true);
                }
            });
        }
        
        function handleLikeButtons() {
            $('.like-btn').on('click', function(e) {
                e.preventDefault();

                var btn = $(this);
                var postId = btn.data('post-id');
                var likeUrl = btn.data('like-url');
                var icon = btn.find('i');
                var likeCount = $('.like-count-' + postId);
                var likeCountModal = $('.like-count-modal-' + postId);
                
                if (btn.data('processing')) {
                    return;
                }

                btn.data('processing', true);

                $.ajax({
                    url: likeUrl,
                    method: 'GET',
                    success: function(response) {
                        // Toggle liked state
                        if (btn.hasClass('liked')) {
                            // Unlike
                            btn.removeClass('liked');
                            icon.removeClass('fa-heart').addClass('fa-heart-o');

                            // Decrease count
                            var currentCount = parseInt(likeCount.text());
                            likeCount.text(currentCount - 1);
                            likeCountModal.text(currentCount - 1);
                        } else {
                            // Like
                            btn.addClass('liked');
                            icon.removeClass('fa-heart-o').addClass('fa-heart');

                            // Increase count
                            var currentCount = parseInt(likeCount.text());
                            likeCount.text(currentCount + 1);
                            likeCountModal.text(currentCount + 1);

                            // Add animation
                            icon.addClass('like-animation');
                            setTimeout(function() {
                                icon.removeClass('like-animation');
                            }, 600);
                        }

                        btn.data('processing', false);
                    },
                    error: function(xhr) {
                        btn.data('processing', false);
                        alert('Failed to like/unlike post. Please try again.');
                    }
                });
            });
        }

        // Handle comment form submission via AJAX
        function handleCommentForms() {
            $('.virtual-tour-comment-form').on('submit', function(e) {
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

    /* Like animation */
    .like-animation {
        animation: likeHeart 0.6s ease-in-out;
    }

    @keyframes likeHeart {
        0% {
            transform: scale(1);
        }
        25% {
            transform: scale(1.3);
        }
        50% {
            transform: scale(0.9);
        }
        75% {
            transform: scale(1.1);
        }
        100% {
            transform: scale(1);
        }
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
</style>