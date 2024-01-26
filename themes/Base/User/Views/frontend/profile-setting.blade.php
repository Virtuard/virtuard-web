<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Profile of {{ Auth::user()->name }}</title>
    @php
        $favicon = setting_item('site_favicon');
    @endphp
    @if ($favicon)
        @php
            $file = (new \Modules\Media\Models\MediaFile())->findById($favicon);
        @endphp
        @if (!empty($file))
            <link rel="icon" type="{{ $file['file_type'] }}" href="{{ asset('uploads/' . $file['file_path']) }}" />
        @else:
            <link rel="icon" type="image/png" href="{{ url('images/favicon.png') }}" />
        @endif
    @endif
    <link href="{{ asset('libs/bootstrap/css/bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/lightbox2/dist/css/lightbox.css') }}" rel="stylesheet" />
    @include('vendor.ipanorama.demo.includes.ipanorama-style')
    <link rel="stylesheet" href="{{ asset('assets/css/profile-setting-custom.css') }}">
    @stack('css')
</head>

<body>
    <header>
        <div class="container">
            <div class="profile">
                <div class="profile-image" id="profile-instagram">
                    <img class="image-demo"
                        src="{{ get_file_url(old('avatar_id', $dataUser->avatar_id)) ?? ($dataUser->getAvatarUrl() ?? '') }}"
                        style="width:152px;" />
                </div>
                <div class="profile-user-settings">
                    <h1 class="profile-user-name">{{ $dataUser->user_name }}</h1>
                    <a href="{{ route('user.profile.setting') }}" class="btn profile-edit-btn">Edit Profile</a>
                </div>
                <div class="profile-stats">
                    <ul>
                        <li><span class="profile-stat-count">164</span> posts</li>
                        <li><span class="profile-stat-count">{{ $followed }}</span> followers</li>
                        <li><span class="profile-stat-count">{{ $following }}</span> following</li>
                    </ul>
                </div>

                <div class="profile-bio mt-2">
                    <p><span class="profile-real-name">{{ $dataUser->name }}</span> {!! $dataUser->bio !!}</p>
                </div>
            </div>
            <!-- End of profile section -->
        </div>
        <!-- End of container -->
    </header>
    
    <main>
        <div class="container">
            <div class="gallery">
                @forelse ($userPosts as $post)
                    <!-- Post Panorama -->
                    @if ($post->ipanorama)
                        <div class="gallery-item" tabindex="0">
                            <a data-id="{{ $post->ipanorama->id }}" data-code="{{ $post->ipanorama->code }}"
                                class="preview-panorama cursor-pointer">
                                <img id="thumb-panorama-{{ $post->ipanorama->id }}"
                                    src="{{ getThumbPanorama($post->ipanorama) }}" class="gallery-image thumb-panorama"
                                    alt="image">
                            </a>
                        </div>
                    @endif
                    <!-- Post Media -->
                    @forelse ($post->medias as $media)
                        <div class="gallery-item" tabindex="0">
                            <a href="{{ asset('uploads/' . $media->media) }}" data-lightbox="image-1">
                                <img src="{{ asset('uploads/' . $media->media) }}" class="gallery-image"
                                    alt="image">
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
    </main>

    <script src="{{ asset('libs/jquery-3.6.3.min.js') }}"></script>
    <script src="{{ asset('libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('libs/lightbox2/dist/js/lightbox.js') }}"></script>
    @include('vendor.ipanorama.demo.includes.ipanorama-script')
    @stack('js')
</body>

</html>
