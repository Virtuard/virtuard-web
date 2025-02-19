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
    <link href="{{ asset('dist/frontend/css/app.css?_ver=' . config('app.asset_version')) }}" rel="stylesheet">
    <style>
        .bravo-list-event .item-loop {
            border: 1px solid #dfdfdf;
            border-radius: 10px;
            margin-bottom: 30px;
            overflow: hidden;
            padding-bottom: 10px;
            position: relative;
        }
    </style>
    @stack('css')
</head>

<body>
    <header>
        <div class="container">
            <div class="profile">
                <div class="profile-image" id="profile-instagram">
                    <img loading='lazy'class="image-demo"
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
        </div>
    </header>

    <main>
        <div class="container">
            @include('User::frontend.profile.gallery')
        </div>
    </main>

    <script src="{{ asset('libs/jquery-3.6.3.min.js') }}"></script>
    <script src="{{ asset('libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('libs/lightbox2/dist/js/lightbox.js') }}"></script>
    @include('vendor.ipanorama.demo.includes.ipanorama-script')
    @stack('js')
</body>

</html>
