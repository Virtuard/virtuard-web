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
{{--                         Tombol Delete --}}
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

{{--                         Tombol Like & Comment --}}
                        <div class="action-btn-wrapper">
                            @auth
                                <a href="javascript:void(0)"
                                   class="action-btn virtual-like-btn {{ $liked->count() > 0 ? 'liked' : '' }}"
                                   data-post-id="{{ $post->id }}"
                                   data-like-url="{{ route('post.like', ['id' => $post->id]) }}">
                                    <i class="fa {{ $liked->count() > 0 ? 'fa-heart' : 'fa-heart-o' }}"></i>
                                    <span class="action-count virtual-like-count-{{ $post->id }}">{{ $post->likes->count() }}</span>
                                </a>
                            @else
                                <a href="javascript:void(0)"
                                   onclick="alert('You need to login to like this post');"
                                   class="action-btn cursor-pointer">
                                    <i class="fa fa-heart-o"></i>
                                    <span class="action-count">{{ $post->likes->count() }}</span>
                                </a>
                            @endauth

                            <a href="#" class="action-btn comment-btn"
                               data-toggle="modal"
                               data-target="#commentModal{{ $post->id }}"
                               data-target-mobile="#commentModalMobile{{ $post->id }}">
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

{{--                 Comment Modal for Desktop --}}
                <div class="modal fade comment-modal-desktop" id="commentModal{{ $post->id }}" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                        <div class="modal-content" style="height: 90vh; max-width: 1200px;">
                            <div class="modal-header">
                                <h5 class="modal-title">{{ __('Post') }}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body p-0" style="height: calc(100% - 60px); overflow: hidden;">
                                <div class="row no-gutters" style="height: 100%;">
                                     Left Side - 360 Panorama (DESKTOP ONLY) 
                                    <div class="col-12 col-md-6 bg-dark d-flex align-items-center justify-content-center panorama-side">
                                        <div id="panorama-modal-{{ $post->id }}" style="width: 100%; height: 100%;"></div>
                                    </div>

                                     Right Side - Comments Section 
                                    <div class="col-12 col-md-6 d-flex flex-column bg-white comments-side">
                                         Comments List (SCROLLABLE AREA) 
                                        <div id="commentsList{{ $post->id }}" class="p-3 bg-white"
                                             style="overflow-y: auto; flex: 1 1 auto; height: 0;">

                                            @forelse($post->comments as $comment)
                                                <div class="mb-3 comment-item" data-comment-id="{{ $comment->id }}">
                                                    <div class="d-flex">
                                                        <img src="{{ $comment->user->getAvatarUrl() ?? asset('images/avatar.png') }}"
                                                            alt="User"
                                                            style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
                                                        <div class="ml-2 flex-grow-1">
                                                            <!-- View Mode -->
                                                            <div class="comment-view-mode">
                                                                <div class="d-flex justify-content-between align-items-start">
                                                                    <div class="bg-light p-2 rounded flex-grow-1">
                                                                        <p class="mb-1 font-weight-bold" style="font-size: 1rem;">
                                                                            {{ $comment->user->display_name ?? $comment->user->name }}
                                                                        </p>
                                                                        <p class="m-0 comment-text" style="font-size: 0.9rem;">{{ $comment->comment }}</p>
                                                                    </div>
                                                                    @if(auth()->check() && auth()->id() === $comment->user_id)
                                                                        <div class="comment-actions ml-2 d-flex flex-column">
                                                                            <button class="btn btn-sm edit-comment-btn mb-2" 
                                                                                    data-comment-id="{{ $comment->id }}"
                                                                                    title="Edit"
                                                                                    style="background-color: #e3f2fd; color: #1976d2; border: none; padding: 4px 8px; width:100% !important; height: 100% !important; border-radius: 4px; font-size: 0.85rem; transition: all 0.2s;">
                                                                                <i class="fa fa-pencil"></i>
                                                                            </button>
                                                                            <button class="btn btn-sm delete-comment-btn" 
                                                                                    data-comment-id="{{ $comment->id }}"
                                                                                    data-post-id="{{ $post->id }}"
                                                                                    title="Delete"
                                                                                    style="background-color: #ffebee; color: #c62828; border: none; padding: 4px 8px; width:100% !important; height: 100% !important; border-radius: 4px; font-size: 0.85rem; transition: all 0.2s;">
                                                                                <i class="fa fa-trash"></i>
                                                                            </button>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                                <p class="m-0 mt-1 text-muted" style="font-size: 0.75rem;">
                                                                    {{ $comment->created_at->diffForHumans() }}
                                                                    @if($comment->created_at != $comment->updated_at)
                                                                        <span class="text-muted">(edited)</span>
                                                                    @endif
                                                                </p>
                                                            </div>
                                        
                                                            <!-- Edit Mode (Hidden by default) -->
                                                            <div class="comment-edit-mode" style="display: none;">
                                                                <form class="update-comment-form" data-comment-id="{{ $comment->id }}" data-post-id="{{ $post->id }}">
                                                                    @csrf
                                                                    <div class="d-flex align-items-center">
                                                                        <input type="text" 
                                                                            name="comment" 
                                                                            class="form-control form-control-sm comment-edit-input" 
                                                                            value="{{ $comment->comment }}"
                                                                            style="border: 2px solid #1976d2; border-radius: 6px; padding: 8px 12px;"
                                                                            required>
                                                                        <div class="comment-actions ml-2 d-flex flex-column">
                                                                            <button type="submit" 
                                                                                    class="btn btn-sm mb-2" 
                                                                                    title="Save"
                                                                                    style="background-color: #4caf50; color: white; border: none; padding: 4px 8px; width:100% !important; height: 100% !important; border-radius: 4px; font-size: 0.85rem; transition: all 0.2s;">
                                                                                <i class="fa fa-check"></i>
                                                                            </button>
                                                                            <button type="button" 
                                                                                    class="btn btn-sm cancel-edit-btn" 
                                                                                    title="Cancel"
                                                                                    style="background-color: #9e9e9e; color: white; border: none; padding: 4px 8px; width:100% !important; height: 100% !important; border-radius: 4px; font-size: 0.85rem; transition: all 0.2s;">
                                                                                <i class="fa fa-times"></i>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <p id="noComments{{ $post->id }}" class="text-center text-muted">
                                                    {{ __('No comments yet. Be the first to comment!') }}
                                                </p>
                                            @endforelse
                                        </div>

                                         Like & Comment Count (FIXED) 
                                        <div class="px-3 py-2 border-top border-bottom bg-white" style="flex: 0 0 auto;">
                                            <div class="d-flex justify-content-between">
                                            <span>
                                                <i class="fa fa-comment"></i> 
                                                <strong class="comment-count-{{ $post->id }}">{{ $post->comments->count() }}</strong> {{ __('comments') }}
                                            </span>
                                            </div>
                                        </div>

                                         Comment Form (FIXED) 
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

{{--                 Comment Modal for Mobile (COMMENTS ONLY) --}}
                <div class="modal fade comment-modal-mobile" id="commentModalMobile{{ $post->id }}" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 100%; margin: 0; height: 100vh;">
                        <div class="modal-content" style="height: 100vh; border-radius: 0;">
                            <div class="modal-header">
                                <h5 class="modal-title">{{ __('Comments') }}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body p-0 d-flex flex-column" style="height: calc(100vh - 60px);">
                                 Comments List - scrollable area 
                                <div id="commentsListMobile{{ $post->id }}" class="p-3 bg-white" style="overflow-y: auto; flex: 1 1 auto; min-height: 0;">
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
                                        <p id="noCommentsMobile{{ $post->id }}" class="text-center text-muted">
                                            {{ __('No comments yet. Be the first to comment!') }}
                                        </p>
                                    @endforelse
                                </div>

                                 Comment Count - fixed 
                                <div class="px-3 py-2 border-top border-bottom bg-white comment-count-section" style="flex: 0 0 auto;">
                                    <div class="d-flex justify-content-between">
                                            <span>
                                                <i class="fa fa-comment"></i> 
                                                <strong class="comment-count-mobile-{{ $post->id }}">{{ $post->comments->count() }}</strong> {{ __('comments') }}
                                            </span>
                                    </div>
                                </div>

                                 Comment Form - fixed at bottom 
                                <div class="comment-form-section bg-white" style="flex: 0 0 auto; padding: 20px 15px;">
                                    @auth
                                        <form action="{{ route('post.comment.store', $post->id) }}"
                                              method="POST"
                                              class="virtual-tour-comment-form-mobile"
                                              data-post-id="{{ $post->id }}">
                                            @csrf
                                            <div class="input-group">
                                                <input type="text"
                                                       name="comment"
                                                       class="form-control border-0"
                                                       placeholder="{{ __('Write a comment...') }}"
                                                       style="background: #f0f2f5; font-size: 16px; min-height: 44px; padding: 12px 15px;"
                                                       required>
                                                <div class="input-group-append">
                                                    <button class="btn btn-primary" type="submit" style="min-height: 44px; min-width: 54px;">
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

        [id^="commentsList"]::-webkit-scrollbar-thumb{
            background: #888;
            border-radius: 3px;
        }

        [id^="commentsList"]::-webkit-scrollbar-thumb:hover{
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
        .modal-dialog.modal-dialog-centered.modal-xl {
            max-height: 100vh; 
            margin: auto !important;
        }
        @media (max-width: 767px) {
            .modal .modal-content {
                height: 100vh;
            }
            .modal .panorama-side {
                display: none; 
                height: 0px;
            }
            .modal .comments-side {
                height: calc(90vh - 65px) !important;
            }
            .modal-dialog.modal-dialog-centered.modal-xl {
                margin: 0 auto !important;
            }
        }

        /* Comment Actions Styling */

        .comment-edit-input:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }
        /* Hover effects untuk tombol edit dan delete */
        .edit-comment-btn:hover {
            background-color: #bbdefb !important;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .delete-comment-btn:hover {
            background-color: #ffcdd2 !important;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        /* Hover effects untuk tombol save dan cancel di edit mode */
        .update-comment-form button[type="submit"]:hover {
            background-color: #45a049 !important;
            transform: translateY(-1px);
            box-shadow: 0 2px 6px rgba(76, 175, 80, 0.3);
        }

        .cancel-edit-btn:hover {
            background-color: #757575 !important;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        /* Active state */
        .edit-comment-btn:active,
        .delete-comment-btn:active {
            transform: translateY(0);
        }

        /* Focus state untuk input edit */
        .comment-edit-input:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(25, 118, 210, 0.1);
        }

        /* Smooth transition */
        .comment-actions button,
        .update-comment-form button {
            transition: all 0.2s ease;
}
        /* Mobile specific */
        @media (max-width: 767px) {
            .comment-actions .btn {
                font-size: 16px;
                min-width: 32px;
                min-height: 32px;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .comment-edit-input {
                font-size: 16px !important;
                min-height: 40px;
            }
        }
    </style>
@endpush

@push('js')
    <script>
        $(document).ready(function() {
            previewPanorama();
            initPanoramaModals();
            handleCommentForms();
            handleLikeButtons();
            handleCommentButtons();
            handleEditComment();
            handleCancelEdit();
            handleUpdateComment(); 
            handleDeleteComment();
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

        // Initialize panorama in comment modals (DESKTOP ONLY)
        function initPanoramaModals() {
            $('[id^="commentModal"]:not([id*="Mobile"])').on('shown.bs.modal', function () {
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

        function handleEditComment() {
        $(document).on('click', '.edit-comment-btn', function() {
            var commentItem = $(this).closest('.comment-item');
            commentItem.find('.comment-view-mode').hide();
            commentItem.find('.comment-edit-mode').show();
            commentItem.find('.comment-edit-input').focus();
        });
    }

    function handleCancelEdit() {
        $(document).on('click', '.cancel-edit-btn', function() {
            var commentItem = $(this).closest('.comment-item');
            commentItem.find('.comment-edit-mode').hide();
            commentItem.find('.comment-view-mode').show();
            
            var originalText = commentItem.find('.comment-text').text();
            commentItem.find('.comment-edit-input').val(originalText);
        });
    }

    function handleUpdateComment() {
        $(document).on('submit', '.update-comment-form', function(e) {
            e.preventDefault();
            
            var form = $(this);
            var commentItem = form.closest('.comment-item');
            var commentId = commentItem.attr('data-comment-id');
            var postId = form.data('post-id');
            var newComment = form.find('input[name="comment"]').val();
            
            if (!commentId || commentId === 'undefined') {
                console.error('Comment ID is undefined!');
                alert('Error: Comment ID not found. Please refresh the page.');
                return;
            }
            
            if (!newComment.trim()) {
                return;
            }
            
            $.ajax({
                url: '/post/' + commentId + '/comment',
                method: 'PUT',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    comment: newComment
                },
                success: function(response) {
                    commentItem.find('.comment-text').text(response.comment);
                    
                    var timeText = commentItem.find('.comment-view-mode p.text-muted').first();
                    if (!timeText.find('span:contains("edited")').length) {
                        timeText.append(' <span class="text-muted">(edited)</span>');
                    }
                    
                    commentItem.find('.comment-edit-input').val(response.comment);
                    
                    commentItem.find('.comment-edit-mode').hide();
                    commentItem.find('.comment-view-mode').show();
                },
                error: function(xhr) {
                    console.error('Update error:', xhr);
                    alert('Failed to update comment. Please try again.');
                }
            });
        });
    }

    function handleDeleteComment() {
        $(document).on('click', '.delete-comment-btn', function() {
            if (!confirm('Are you sure you want to delete this comment?')) {
                return;
            }
            
            var btn = $(this);
            var commentItem = btn.closest('.comment-item');
            var commentId = commentItem.attr('data-comment-id');
            var postId = btn.data('post-id');
            
            if (!commentId || commentId === 'undefined') {
                console.error('Comment ID is undefined!');
                alert('Error: Comment ID not found. Please refresh the page.');
                return;
            }
            
            $.ajax({
                url: '/post/' + commentId + '/comment',
                method: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log('Delete success:', response);
                    
                    commentItem.fadeOut(300, function() {
                        $(this).remove();
                        
                        var currentCount = parseInt($('[data-target="#commentModal' + postId + '"] .action-count').text());
                        var newCount = Math.max(0, currentCount - 1);
                        
                        $('[data-target="#commentModal' + postId + '"] .action-count').text(newCount);
                        $('.comment-count-' + postId).text(newCount);
                        
                        if (newCount === 0) {
                            $('#noComments' + postId).show();
                        }
                    });
                },
                error: function(xhr) {
                    console.error('Delete error:', xhr);
                    alert('Failed to delete comment. Please try again.');
                }
            });
        });
    }

    function handleCommentFormSubmission() {
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
                success: function(response) {
                    console.log('Comment created:', response);
                    
                    if (!response.comment_id) {
                        console.error('Response missing comment_id:', response);
                        alert('Error: Invalid server response.');
                        return;
                    }
                    
                    commentInput.val('');
                    $('#noComments' + postId).hide();
                    
                    var escapedComment = $('<div>').text(response.comment).html();
                    var escapedName = $('<div>').text(response.user.name).html();
                    
                    var newComment = `
                    <div class="mb-3 comment-item" data-comment-id="${response.comment_id}">
                        <div class="d-flex">
                            <img src="${response.user.avatar}" 
                                alt="User" 
                                style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
                            <div class="ml-2 flex-grow-1">
                                <div class="comment-view-mode">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="bg-light p-2 rounded flex-grow-1">
                                            <p class="mb-1 font-weight-bold" style="font-size: 1rem;">${escapedName}</p>
                                            <p class="m-0 comment-text" style="font-size: 0.9rem;">${escapedComment}</p>
                                        </div>
                                        <div class="comment-actions ml-2 d-flex flex-column">
                                            <button class="btn btn-sm edit-comment-btn mb-2"
                                                    title="Edit"
                                                    style="background-color: #e3f2fd; color: #1976d2; border: none; padding: 4px 8px; width:100% !important; height: 100% !important; border-radius: 4px; font-size: 0.85rem; transition: all 0.2s;">
                                                <i class="fa fa-pencil"></i>
                                            </button>
                                            <button class="btn btn-sm delete-comment-btn"
                                                    title="Delete" 
                                                    data-post-id="${postId}"
                                                    style="background-color: #ffebee; color: #c62828; border: none; padding: 4px 8px; width:100% !important; height: 100% !important; border-radius: 4px; font-size: 0.85rem; transition: all 0.2s;">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <p class="m-0 mt-1 text-muted" style="font-size: 0.75rem;">Just now</p>
                                </div>
                                <div class="comment-edit-mode" style="display: none;">
                                    <form class="update-comment-form" data-post-id="${postId}">
                                        <div class="d-flex align-items-center">
                                            <input type="text" 
                                                name="comment" 
                                                class="form-control form-control-sm comment-edit-input" 
                                                value="${escapedComment}"
                                                style="border: 2px solid #1976d2; border-radius: 6px; padding: 8px 12px;"
                                                required>
                                            <div class="comment-actions ml-2 d-flex flex-column">
                                                <button type="submit" 
                                                        class="btn btn-sm mb-2" 
                                                        title="Save"
                                                        style="background-color: #4caf50; color: white; border: none; padding: 4px 8px; width:100% !important; height: 100% !important; border-radius: 4px; font-size: 0.85rem; transition: all 0.2s;">
                                                    <i class="fa fa-check"></i>
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-sm cancel-edit-btn" 
                                                        title="Cancel"
                                                        style="background-color: #9e9e9e; color: white; border: none; padding: 4px 8px; width:100% !important; height: 100% !important; border-radius: 4px; font-size: 0.85rem; transition: all 0.2s;">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    `;
                    
                    var $newComment = $(newComment);
                    $('#commentsList' + postId).append($newComment);
                    
                    var commentsList = document.getElementById('commentsList' + postId);
                    if (commentsList) {
                        commentsList.scrollTop = commentsList.scrollHeight;
                    }

                    var currentCount = parseInt($('[data-target="#commentModal' + postId + '"] .action-count').text()) || 0;
                    $('[data-target="#commentModal' + postId + '"] .action-count').text(currentCount + 1);
                    $('.comment-count-' + postId).text(currentCount + 1);
                },
                error: function(xhr) {
                    console.error('Submit error:', xhr);
                    alert('Failed to post comment. Please try again.');
                }
            });
        });
    }
        function handleLikeButtons() {
            $('.virtual-like-btn').on('click', function(e) {
                e.preventDefault();

                var btn = $(this);
                var postId = btn.data('post-id');
                var likeUrl = btn.data('like-url');
                var icon = btn.find('i');
                var likeCount = $('.virtual-like-count-' + postId);
                var likeCountModal = $('.like-count-modal-' + postId);

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

                            likeCount.text(response.like_count);
                            likeCountModal.text(response.like_count);
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
            $('.virtual-tour-comment-form-mobile').on('submit', function(e) {
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
                        $('#noCommentsMobile' + postId).hide();

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

                        $('#commentsListMobile' + postId).append(newComment);

                        // Scroll to bottom
                        var commentsListMobile = document.getElementById('commentsListMobile' + postId);
                        if (commentsListMobile) {
                            commentsListMobile.scrollTop = commentsListMobile.scrollHeight;
                        }

                        // Update comment count in both button and mobile modal
                        var currentCount = parseInt($('[data-target="#commentModal' + postId + '"] .action-count').text());
                        $('[data-target="#commentModal' + postId + '"] .action-count').text(currentCount + 1);
                        $('.comment-count-' + postId).text(currentCount + 1);
                        $('.comment-count-mobile-' + postId).text(currentCount + 1);
                    },
                    error: function(xhr) {
                        alert('Failed to post comment. Please try again.');
                    }
                });
            });
        }
    </script>
@endpush