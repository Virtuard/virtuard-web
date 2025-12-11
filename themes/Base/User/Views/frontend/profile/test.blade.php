{{-- Comment Modal --}}
<div class="modal fade" id="commentModal{{ $post->id }}" tabindex="-1" role="dialog">
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
                    {{-- Left Side - Post Media (FIXED, NO SCROLL) --}}
                    <div class="col-12 col-md-6 bg-dark d-flex align-items-center justify-content-center panorama-side">
                        @php
                            $firstMedia = $post->medias->first();
                        @endphp

                        @if($firstMedia)
                            @if($firstMedia->type == 'image')
                                @if($firstMedia->is_360_media)
                                    {{-- 360 Image --}}
                                    <div id="panorama-modal-{{ $post->id }}"
                                         data-url="/uploads/{{ $firstMedia->media }}"
                                         style="width: 100%; height: 100%;">
                                    </div>
                                @else
                                    {{-- Regular Image --}}
                                    <img src="{{ asset('uploads/' . $firstMedia->media) }}"
                                         alt="Post"
                                         style="width: 100%; height: 100%; object-fit: cover;">
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
                    <div class="col-12 col-md-6 d-flex flex-column bg-white comments-side">

                        <div id="commentsList{{ $post->id }}" class="p-3 bg-white" style="overflow-y: auto; flex: 1 1 auto; height: 0;">
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
                                                        <p class="m-0 font-weight-bold" style="font-size: 0.9rem;">
                                                            {{ $comment->user->display_name ?? $comment->user->name }}
                                                        </p>
                                                        <p class="m-0 comment-text" style="font-size: 0.9rem;">{{ $comment->comment }}</p>
                                                    </div>
                                                    @if(auth()->check() && auth()->id() === $comment->user_id)
                                                        <div class="comment-actions ml-2 d-flex">
                                                            <button class="btn btn-sm edit-comment-btn" 
                                                                    data-comment-id="{{ $comment->id }}"
                                                                    title="Edit"
                                                                    style="background-color: #e3f2fd; color: #1976d2; border: none; padding: 4px 10px; border-radius: 4px; font-size: 0.85rem; transition: all 0.2s;">
                                                                <i class="fa fa-pencil"></i>
                                                            </button>
                                                            <button class="btn btn-sm delete-comment-btn" 
                                                                    data-comment-id="{{ $comment->id }}"
                                                                    data-post-id="{{ $post->id }}"
                                                                    title="Delete"
                                                                    style="background-color: #ffebee; color: #c62828; border: none; padding: 4px 10px; border-radius: 4px; font-size: 0.85rem; margin-left: 6px; transition: all 0.2s;">
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
                                                        <button type="submit" 
                                                                class="btn btn-sm ml-2" 
                                                                title="Save"
                                                                style="background-color: #4caf50; color: white; border: none; padding: 8px 14px; border-radius: 6px; min-width: 45px; transition: all 0.2s;">
                                                            <i class="fa fa-check"></i>
                                                        </button>
                                                        <button type="button" 
                                                                class="btn btn-sm cancel-edit-btn" 
                                                                title="Cancel"
                                                                style="background-color: #9e9e9e; color: white; border: none; padding: 8px 14px; border-radius: 6px; margin-left: 6px; min-width: 45px; transition: all 0.2s;">
                                                            <i class="fa fa-times"></i>
                                                        </button>
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

@push('css')
<style>
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
</style>
@endpush