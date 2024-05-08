@extends ('layouts.app')
<link href="{{ asset('libs/ipanorama/src/ipanorama.css') }}" rel="stylesheet">
<link href="{{ asset('libs/ipanorama/src/ipanorama.theme.default.css') }}" rel="stylesheet">
<link href="{{ asset('libs/ipanorama/src/ipanorama.theme.modern.css') }}" rel="stylesheet">
<link href="{{ asset('libs/ipanorama/src/ipanorama.theme.dark.css') }}" rel="stylesheet">
<link href="{{ asset('libs/ipanorama/src/effect.css') }}" rel="stylesheet">
<link href="{{ asset('libs/ipanorama/src/style.css') }}" rel="stylesheet">
@section('content')
    <div style="padding: 40px 0; background: #f5f5f5;">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-9" style="background: #f5f5f5; padding: 0 20px;">
                    <form action="{{ route('user.post.status') }}" class="mb-4" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="w-100" style="background: #FFF; border-radius: 8px; padding: 23px 35px;">
                            <ul class="d-flex flex-wrap md-flex-column" style="list-style: none; ">
                                <li
                                    style="background: #FFF;
                                    padding: 3px 18px;
                                    border-radius: 8px;
                                    margin-right: 16px;">
                                    <i class="fa fa-comments"></i> Status
                                </li>
                                    <li style="background: #f5f5f5; padding: 3px 18px; border-radius: 8px; margin-right: 16px; cursor: pointer;"
                                        @auth
                                            onclick="document.getElementById('fileInput').click();
                                        @else
                                            onclick="showModalLogin()"
                                        @endauth
                                        ">
                                        <input type="file" id="fileInput" style="display: none;" name="media_user[]"
                                            accept="image/*, video/*"
                                            multiple>
                                        <i class="fa fa-picture-o"></i> Media
                                    </li>
                                    <li style="background: #f5f5f5; padding: 3px 18px; border-radius: 8px; margin-right: 16px; cursor: pointer;"
                                        @auth
                                            onclick="document.getElementById('ipanoramaModal').click();"
                                        @else
                                            onclick="showModalLogin()"
                                        @endauth
                                        >
                                        <i class="fa fa-picture-o"></i> 360 Media
                                        <button id="ipanoramaModal" type="button" class="d-none" data-toggle="modal"
                                            data-target="#modalPanorama"></button>
                                    </li>
                            </ul>

                            <hr>

                            <div class="d-flex align-items-center">
                                <img class="mr-4"
                                    src="https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_960_720.png"
                                    style="width: 60px; height: 60px; object-fit: cover; border-radius: 100px;"
                                    alt="">
                                @auth
                                    <textarea style="width: 100%; border: 0; outline: none;" name="message" placeholder="What's new?"
                                        oninput="auto_grow(this)"></textarea>
                                @else
                                    <input style="width: 100%; border: 0; outline: none;" placeholder="Please register or login to write status" onclick="showModalLogin()">
                                @endauth
                            </div>

                            <div id="search-tag" class="w-100 mt-4 position-relative d-none"
                                style="padding: 15px;background-color: #f4f4f4;border-top: 1px solid #eee; border-bottom: 1px solid #eee;">
                                <select class="form-control position-relative p-2 select-search" name="state"
                                    style="padding-left:2.5em;">
                                    <option value="search">Search your friends!</option>
                                    <option value="WY">Wyoming</option>
                                </select>
                                {{-- <input type="text" class="form-control position-relative" placeholder="Search your friends!" style="padding-left:2.5em;">
                                <i class="fa fa-search"
                                   style="position: absolute; left: 25px;
                                       top:50%; transform: translateY(-50%); z-index:10; color: #BDBDBD"></i> --}}
                            </div>

                            <hr>

                            <div class="w-100 d-flex justify-content-between md-flex-column">
                                <div class="d-flex align-items-center">
                                    <select class="h-100" name="type_post"
                                        style="
                                        padding: 0 13px;
                                        background: #f5f5f5;
                                        border: 0;
                                        border-radius: 100px;
                                        font-weight: 600;
                                        outline: none;
                                    ">
                                        <option value="Public">Public</option>
                                        <option value="Only Me">Only Me</option>
                                        <option value="My Friend">My Friend</option>
                                        <option value="Members">Members</option>
                                    </select>
                                    <a class="cursor-pointer d-none">
                                        <i class="fa fa-lg fa-smile-o ml-3"></i>
                                    </a>
                                    <div class="cursor-pointer" id="toogle-tag" onclick="showSelect()">
                                        <i class="fa fa-lg fa-tags ml-3"></i>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span>Posts in :</span>
                                    <select class="h-100 ml-3" name="type_post"
                                        style="
                                        padding: 0 13px;
                                        background: #f5f5f5;
                                        border: 0;
                                        border-radius: 100px;
                                        font-weight: 600;
                                        outline: none;
                                    ">
                                        <option value="My Profile">My Profile</option>
                                    </select>
                                    <button type="submit" class="btn btn-primary btn-submit ml-3"
                                        style="border-radius: 100px; outline: none;"
                                    @guest
                                        disabled
                                    @endguest
                                    >POST</button>
                                </div>
                            </div>
                        </div>
                        <div id="modalPanorama" class="modal fade">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Select 360</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <label for="panoramaSelect" class="form-label">Select 360</label>
                                        <select id="panoramaSelect" name="ipanorama_id" class="form-control">
                                            <option value="">Select your 360</option>
                                            @foreach ($dataIpanorama as $panorama)
                                                @if ($panorama->code)
                                                    <option value="{{ $panorama->id }}">{{ $panorama->title }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary" data-dismiss="modal">Save</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div id="content_status_post" style="display: block;">
                        @foreach ($posts as $post)
                            <div class="w-100 mt-3" style="background: #FFF; border-radius: 8px; padding: 23px 35px;" id="Post-{{ $post->id }}">
                                <div style="display: flex; align-items: center;">
                                    <img class="mr-4"
                                        src="{{ $post->user->getAvatarUrl() }}"
                                        style="width: 60px; height: 60px; object-fit: cover; border-radius: 100px;"
                                        alt="">
                                    <div>
                                        <a href="{{ route('user.profile', $post->user_id) }}">
                                            <p class="m-0" style="font-weight: 600;">
                                                {{ $post->user->name }}
                                            </p>
                                        </a>
                                        <p class="m-0" style="font-size: 0.7rem; font-weight: 500; color: #9b9b9b;">
                                            {{ $post->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                                <hr>

                                <div class="card-file">
                                    <div class="g-ipanorama">
                                        @if ($post->ipanorama)
                                            <a data-id="{{ $post->ipanorama->id }}"  data-code="{{ $post->ipanorama->code }}" class="preview-panorama cursor-pointer">
                                                <img id="thumb-panorama-{{ $post->ipanorama->id }}" src='{{ getThumbPanorama($post->ipanorama) }}' alt="" class="thumb-panorama">
                                            </a>
                                        @endif
                                    </div>

                                    <div class="g-gallery">
                                        <div class="fotorama" data-width="100%" data-thumbwidth="135" data-thumbheight="135" data-thumbmargin="15" data-nav="thumbs" data-allowfullscreen="true">
                                            @foreach($post->medias->where('type', 'image') as $img)
                                                <a href="{{url('uploads/'.$img['media'])}}" data-thumb="{{url('uploads/'.$img['media'])}}" data-alt="{{ __("Media") }}"></a>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="g-video">
                                        @foreach ($post->medias->where('type', 'video') as $vid)
                                            <div class="mt-2">
                                                <video>
                                                    <source src="{{ url('uploads/' . $vid->media) }}">
                                                </video>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <p class="mt-3"
                                    style="
                                            font-size: 0.9rem;
                                            font-weight: 500;
                                            color: #9b9b9b;
                                        ">
                                    {{ $post->message }}
                                </p>
                                <hr>
                                @php
                                    $comments = $post->comments;
                                @endphp
                                {{-- COMMENT --}}
                                @if ($comments->count() > 0)
                                    <div class="w-100 mt-3"
                                        style="background: #FFF; border-radius: 8px; padding: 23px 35px;">
                                        @foreach ($comments as $comment)
                                            <div style="display: flex; align-items: center;">
                                                <img class="mr-4"
                                                    src="{{ $comment->user->getAvatarUrl() }}"
                                                    style="width: 60px; height: 60px; object-fit: cover; border-radius: 100px;"
                                                    alt="">
                                                <div>
                                                    <a href="{{ route('user.profile', $comment->user_id) }}">
                                                    <p class="m-0" style="font-weight: 600;">
                                                        {{ $comment->user->name }}
                                                    </p>
                                                    </a>
                                                    <p class="m-0"
                                                        style="font-size: 0.7rem; font-weight: 500; color: #9b9b9b;">
                                                        {{ $comment->created_at->diffForHumans() }}
                                                    </p>
                                                </div>
                                            </div>
                                            <hr>

                                            <p class="mt-3"
                                                style="
                                            font-size: 0.9rem;
                                            font-weight: 500;
                                            color: #9b9b9b;
                                        ">
                                                {{ $comment->comment }}
                                            </p>
                                            <hr />
                                        @endforeach
                                    </div>
                                @endif
                                {{-- END COMMENT --}}
                                
                                <div class="d-flex mt-4">
                                    <div class="d-flex">
                                        <img src="https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_960_720.png"
                                            style="width: 20px; height: 20px; object-fit: cover; border-radius: 100px;"
                                            alt="">
                                        <img src="https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_960_720.png"
                                            style="width: 20px; height: 20px; object-fit: cover; border-radius: 100px;margin-left: -5px;">
                                    </div>

                                    <p class="m-0 ml-2" style="color: #9b9b9b;">and
                                        {{ $post->likes->count() }} Like This</p>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-around align-items-center">
                                    @php
                                        $liked = \App\Models\PostLike::where('post_id', $post->id)->where('user_id', Auth::id());
                                    @endphp

                                    @auth
                                        @if ($liked->count() > 0)
                                            <a href="{{ route('user.post.like', ['id' => $post->id]) }}"
                                                class="cursor-pointer" style="color: pink;">
                                                <i class="fa fa-heart"></i> Liked
                                            </a>
                                        @else
                                            <a href="{{ route('user.post.like', ['id' => $post->id]) }}"
                                                class="cursor-pointer" style="color: #9b9b9b;">
                                                <i class="fa fa-heart-o"></i> Like
                                            </a>
                                        @endif
                                    @else
                                        @if ($liked->count() > 0)
                                            <a class="cursor-pointer" style="color: pink;" onclick="alert('You need to login to like this post');">
                                                <i class="fa fa-heart"></i> Liked
                                            </a>
                                        @else
                                            <a class="cursor-pointer" style="color: #9b9b9b;" onclick="alert('You need to login to like this post');">
                                                <i class="fa fa-heart-o"></i> Like
                                            </a>
                                        @endif
                                    @endauth
                                    <a class="cursor-pointer" style="color: #9b9b9b;"
                                        onclick="toggleCommentInput({{ $post->id }})"><i
                                            class="fa fa-commenting-o"></i>
                                        Comment</a>
                                    <div class="btn-group">
                                        <button type="button" class="btn" data-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false" style="color: #9b9b9b;">
                                            <i class="fa fa-share-square-o"></i> Share
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item"
                                                href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url('/user/follow-boards#Post-' . $post->id)) }}"
                                                target="_blank">
                                                <i class="fa fa-facebook"></i> Share on Facebook
                                            </a>
                                        </div>
                                    </div>

                                </div>
                                @auth
                                    <div id="commentInput_{{ $post->id }}" style="display:none;" class="mt-2">
                                        <form action="{{ route('user.post.comment', ['id' => $post->id]) }}" method="POST">
                                            @csrf
                                            <div class="form-group" style="display: flex;">
                                                <textarea class="form-control" name="comment" placeholder="Write your comment here" rows="3"
                                                    style="flex: 1;"></textarea>
                                                <button type="submit" class="btn btn-primary btn-submit">Comment</button>
                                            </div>
                                        </form>
                                    </div>
                                @else
                                    <div id="commentInput_{{ $post->id }}" style="display:none;" class="mt-2">
                                        <form id="commentForm">
                                            <div class="form-group" style="display: flex;">
                                                <textarea class="form-control" name="comment" rows="3" style="flex: 1;"
                                                    placeholder="You need to log in to comment" disabled></textarea>
                                                <button type="button" class="btn btn-primary" onclick="submitComment()"
                                                    disabled>Comment</button>
                                            </div>
                                        </form>
                                    </div>
                                @endauth
                            </div>
                        @endforeach
                    </div>
                    <div id="content_feed_post" class="w-100 mt-3 grid-container"
                        style="background: #FFF; border-radius: 8px; display: none; padding: 23px 35px;">
                        @foreach ($dataFeedMe as $feedMe)
                            <div class="grid-item">
                                <img src="/uploads/<?= $feedMe->media ?>" alt="">
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-md-3" style="background: #f5f5f5; padding: 0 20px;">
                    <div class="w-100" style="background: #FFF; border-radius: 8px; padding: 23px 35px;">
                        <div>
                            <a class="m-0" href="{{ route('member.index')}}">
                                <i class="fa fa-globe mr-2"></i>
                                All Members
                                <span class="badge badge-primary">{{ $memberCount }}</span>
                            </a>
                        </div>
                    </div>
                    @auth
                    <div class="w-100 mt-3" style="background: #FFF; border-radius: 8px; padding: 23px 35px;">
                        <div>
                            <a class="m-0" href="{{ route('member.index', ['type' => 'follower'])}}">
                                <i class="fa fa-users mr-2"></i>
                                Followers
                                <span class="badge badge-primary">{{ $followerCount }}</span>
                            </a>
                        </div>
                    </div>
                    <hr class="mx-4">
                    <div class="w-100 mt-3" style="background: #FFF; border-radius: 8px; padding: 23px 35px;">
                        <div>
                            <a class="m-0" href="{{ route('member.index', ['type' => 'following'])}}">
                                <i class="fa fa-user-plus mr-2"></i>
                                Following
                                <span class="badge badge-primary">{{ $followingCount }}</span>
                            </a>
                        </div>
                    </div>
                    <hr class="mx-4">
                    <div class="w-100" onclick="feedShow()" id="feed_post"
                        style="cursor: pointer; background: #FFF; border-radius: 8px; padding: 23px 35px;">
                        <div>
                            <p class="m-0">
                                <i class="fa fa-picture-o mr-2"></i>
                                My Feed
                            </p>
                        </div>
                    </div>
                    <div class="w-100" onclick="statusShow()" id="status_post"
                        style="cursor: pointer; background: #FFF; border-radius: 8px; padding: 23px 35px; display: none;">
                        <div>
                            <p class="m-0">
                                <i class="fa fa-comments mr-2"></i>
                                My Post
                            </p>
                        </div>
                    </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <div class="section-modal">
        @include('vendor.ipanorama.demo.includes.ipanorama-modal')
    </div>
@endsection

@push('css')
    <link rel="stylesheet" type="text/css" href="{{asset('libs/fotorama/fotorama.css')}}"/>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .g-gallery .fotorama .fotorama__fullscreen-icon {
            background: none;
            bottom: 30px;
            height: 40px;
            right: 30px;
            top: auto;
            width: 40px;
        }
        .g-gallery .fotorama .fotorama__fullscreen-icon:before {
            background: url(/images/ico_full_3.svg);
            content: "";
            height: 24px;
            left: 50%;
            margin-left: -11px;
            position: absolute;
            top: 7px;
            width: 24px;
            z-index: 1;
        }
        .g-gallery .fotorama .fotorama__fullscreen-icon:after {
            background: rgba(26, 43, 72, .5);
            border-radius: 3px;
            content: "";
            height: 100%;
            left: 0;
            position: absolute;
            top: 0;
            width: 100%;
        }
        .g-gallery .fotorama .fotorama__arr {
            background: none;
            background-color: rgba(26, 43, 72, .6);
            border-radius: 3px;
            height: 40px;
            width: 40px;
        }
        .g-gallery .fotorama .fotorama__arr:after {
            height: 24px;
            left: 50%;
            margin-left: -13px;
            position: absolute;
            top: 7px;
            width: 24px;
        }
        .g-gallery .fotorama .fotorama__arr.fotorama__arr--prev {
            left: 30px;
        }
        .g-gallery .fotorama .fotorama__arr.fotorama__arr--prev:after {
            background: url(/images/ico_pre.svg);
            content: "";
        }
        .g-gallery .fotorama .fotorama__arr.fotorama__arr--next {
            right: 30px;
        }
        .g-gallery .fotorama .fotorama__arr.fotorama__arr--next:after {
            background: url(/images/ico_next.svg);
            content: "";
        }
        .g-gallery .fotorama .fotorama__stage {
            width: 100%;
            height: auto;
            max-height: 420px;
        }
        .g-gallery .fotorama .fotorama__stage .fotorama__img {
            top: 0 !important;
        }
        .g-video video {
            width: 100%;
            height: 450px;
        }
        .thumb-panorama {
            width: 100%;
            height: 450px;
        }
        #search-tag span.select2.select2-container {
            width: 100% !important;
        }

        textarea {
            resize: none;
            overflow: hidden;
            min-height: 30px;
            max-height: auto;
        }

        .grid-container {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
            width: 80%;
            margin: 0 auto;
        }

        .grid-item {
            background-color: #f5f5f5;
            color: #fff;
            text-align: center;
        }

        .grid-item img {
            width: 100%;
            height: 20vw;
            object-fit: cover;
        }
    
        @media(max-width: 768px) {
            .md-flex-column {
                gap: 5px;
                flex-direction: column;
            }

            .btn-submit {
                padding: 5px 10px;
            }
        }
    </style>
@endpush

@push('js')
    <script defer src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    {{-- <script src="{{ asset('libs/ipanorama/src/lib/jquery.min.js') }}"></script> --}}
    <script src="{{ asset('libs/ipanorama/src/jquery.ipanorama.js') }}"></script>
    <script src="{{ asset('libs/ipanorama/src/lib/three.min.js') }}"></script>
    <script type="text/javascript" src="{{asset('libs/fotorama/fotorama.js')}}"></script>
    <script>
        function feedShow() {
            document.getElementById('content_status_post').style.display = "none"
            document.getElementById('feed_post').style.display = "none"
            document.getElementById('status_post').style.display = "block"
            document.getElementById('content_feed_post').style.display = "grid"
        }

        function statusShow() {
            document.getElementById('content_status_post').style.display = "block"
            document.getElementById('content_feed_post').style.display = "none"
            document.getElementById('feed_post').style.display = "block"
            document.getElementById('status_post').style.display = "none"
        }

        function auto_grow(element) {
            element.style.height = "5px";
            element.style.height = (element.scrollHeight) + "px";
        }

        function showSelect() {
            const searchTag = document.getElementById("search-tag");
            if (searchTag.classList.contains("d-block")) {
                searchTag.classList.remove("d-block");
                searchTag.classList.add("d-none");
            } else {
                searchTag.classList.remove("d-none");
                searchTag.classList.add("d-block");
            }
        }

        $(document).ready(function() {
            $('.select-search').select2();
        });
    </script>
    <script>
        lightbox.option({
            'resizeDuration': 200,
            'wrapAround': false,
            'disableScrolling': true,
        })
    </script>
    <script>
        function toggleCommentInput(postId) {
            var commentInput = document.getElementById('commentInput_' + postId);
            commentInput.style.display = (commentInput.style.display === 'none' || commentInput.style.display === '') ?
                'block' : 'none';
        }

        function submitComment(postId) {
            // Add your logic to submit the comment (e.g., AJAX request)
            var commentText = document.getElementById('commentText_' + postId).value;
            console.log('Submitted Comment for Post ' + postId + ':', commentText);

            // Optionally, hide the comment input after submission
            document.getElementById('commentInput_' + postId).style.display = 'none';
        }
    </script>
@endpush
