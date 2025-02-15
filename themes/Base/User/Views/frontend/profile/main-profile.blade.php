<style>
    @media (max-width: 768px) {
        .profile-header {
            flex-direction: row;
            align-items: center;
        }

        .profile-avatar {
            margin-bottom: 0;
        }

        .profile-details {
            text-align: left;
        }

        .follow-stats {
            flex-direction: row;
            justify-content: flex-start;
            align-items: center;
        }

        .ig-buttons {
            flex-direction: row;
            align-items: center;
            gap: 10px;
        }

        .ig-buttons a,
        .ig-buttons button {
            width: auto;
            margin-bottom: 0;
        }
    }

    @media (max-width: 576px) {
        .profile-header {
            padding: 0;
        }

        .role-name,
        .profile-since,
        .stat {
            font-size: 16px;
        }

        .follow-stats {
            gap: 20px;
        }
    }

    .profile-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 10px;
    }

    .profile-header {
        display: flex;
        align-items: center;
    }

    .profile-avatar .avatar-img {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background-size: cover;
        background-position: center;
        border: 2px solid #ddd;
    }

    .profile-details {
        flex-grow: 1;
    }

    .display-name {
        font-size: 24px;
        font-weight: bold;
    }

    .verified-icon {
        width: 20px;
        height: 20px;
    }

    .follow-stats {
        display: flex;
        justify-content: flex-start;
        gap: 20px;
        align-items: center;
    }

    .follow-stats .stat {
        text-align: center;
    }

    .label {
        font-size: 14px;
        color: #8e8e8e;
    }

    .number {
        font-size: 16px;
        font-weight: bold;
        color: #262626;
    }

    .profile-bio {
        font-size: 14px;
        color: #666;
    }

    ul.meta-info.style1 {
        list-style: none;
        padding: 0;
        margin: 0;
        max-width: 100%;
        overflow: hidden;
    }

    ul.meta-info.style1 li {
        margin: 10px 0;
    }

    ul.meta-info.style1 .label {
        font-weight: bold;
    }

    ul.meta-info.style1 .val {
        display: block;
        word-wrap: break-word;
        max-width: 100%;
    }


    .stat {
        display: flex;
        flex-direction: column;
    }

    .number {
        font-size: 20px;
        font-weight: bold;
        margin: 0;
    }

    .label {
        font-size: 14px;
        color: #555;
        margin: 0;
    }

    .quick-listing-actions li,
    .quick-listing-actions-share-social li {
        list-style: none;
        padding: 0 10px 0 0;
    }

    .quick-listing-actions>ul>li>a {
        padding: 10px 15px;
        display: -webkit-box;
        display: flex;
        -webkit-box-align: center;
        align-items: center;
        border-radius: 50px;
        font-size: 14px;
        -webkit-transition: .2s ease;
        transition: .2s ease;
        background: transparent;
        border: 1.5px solid rgba(0, 0, 0, .15);
        border-radius: 100px solid #eee;
        color: #000;
    }

    .quick-listing-actions>ul>li>a:hover {
        text-decoration: none;
    }

    .quick-listing-actions>ul>li>a>i {
        margin-right: 10px;
    }

    .social-share-modal .share-options li {
        width: 33.3%;
        text-align: center;
        margin-top: 12px;
        margin-bottom: 12px;
    }

    .social-share-modal .share-options li a i {
        display: block;
        width: 40px;
        height: 40px;
        line-height: 40px;
        position: relative;
        background: #eee;
        border-radius: 50%;
        text-align: center;
        margin: auto;
    }

    .social-share-modal .share-options li a p {
        color: #000;
    }

    .quick-listing-actions .service-wishlist.active .fa::before {
        content: "\f004";
        color: red;
    }

    .btn-transparent {
        background-color: transparent;
        border: none;
        box-shadow: none;
    }

    .btn-transparent .text-primary {
        color: #007bff;
    }

    /*ig*/
    .ig-buttons {
        display: flex;
        gap: 10px;
        align-items: stretch;

    }

    .ig-buttons .btn {
        flex: 1;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 10px 20px;
        font-size: 16px;
        font-weight: bold;
        border-radius: 8px;
        text-align: center;
        transition: all 0.3s ease;
        height: 100%;
    }

    .ig-follow-btn {
        /* background-color: #0095f6; */
        color: white;
        border: none;
    }

    .ig-follow-btn:hover {
        background-color: #007ace;
    }

    .ig-message-btn {
        background-color: white;
        color: #262626;
        border: 1px solid #dbdbdb;
    }

    .ig-message-btn:hover {
        background-color: #f9f9f9;
    }

    .ig-share-btn {
        background-color: white;
        color: #262626;
        border: 1px solid #dbdbdb;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
    }

    .ig-share-btn:hover {
        background-color: #f9f9f9;
    }

    .ig-share-btn i {
        font-size: 16px;
    }

    .profile-bio {
        position: relative;
        /* Membuat posisi relatif pada kontainer */
    }

    .bio-content {
        display: block;
    }

    #bio-text {
        display: inline-block;
        max-width: 100%;
    }

    #myBtn {
        cursor: pointer;
        font-size: 14px;
        color: #007bff;
        text-decoration: underline;
        position: absolute;
        right: 0;
        /* Posisikan ke kanan */
        bottom: 0;
        /* Posisikan ke bawah */
        font-size: 13px;
    }

    #myBtn.read-less {
        display: inline-block;
    }


    .contact-info {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
    }

    .contact-item {
        display: flex;
        flex-direction: column;
    }

    .contact-item .label {
        font-weight: 600;
        color: #555;
    }

    .contact-item .val {
        font-size: 1rem;
        color: #333;
    }

    .contact-item+.contact-item {
        margin-left: 20px;
    }

    .contact-item span {
        line-height: 1.4;
    }

    .contact-info .contact-item {
        flex: 1;
    }

    .contact-item .label {
        font-weight: 600;
        color: #555;
    }

    .contact-item .val {
        font-size: 1rem;
        color: #333;
    }

    .contact-item span {
        line-height: 1.4;
    }

    .clamp-text {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    @media (max-width: 768px) {
        .clamp-text {
            -webkit-line-clamp: 2;
        }
    }

    .avatar-info {
        display: flex;
        align-items: center;
        width: 100%;
    }

    .avatar {
        width: 40px;
        height: 40px;
        object-fit: cover;
    }

    .name {
        font-size: 14px;
        font-weight: bold;
    }

    .list-group-item {
        padding: 10px;
        border: none;
        background-color: #fff;
    }

    .list-group-item .btn {
        font-size: 12px;
        padding: 5px 10px;
        border-radius: 20px;
    }


    .btn-primary {
        background-color: #0095f6;
        border-color: #0095f6;
    }

    .btn-secondary {
        background-color: #dbdbdb;
        border-color: #dbdbdb;
    }


    .modal-header {
        background-color: #fafafa;
        border-bottom: 1px solid #ddd;
    }

    .modal-body {
        padding: 20px;
    }

    .modal-title {
        font-weight: bold;
    }

    #searchFollowers,
    #searchFollowing {
        font-size: 14px;
        padding: 10px;
        margin-bottom: 10px;
        border-radius: 20px;
        border: 1px solid #ddd;
    }

    #searchFollowers::placeholder,
    #searchFollowing::placeholder {
        font-style: italic;
        color: #888;
    }


    .modal-dialog {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        margin: 0 auto;
        padding: 0;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
    }


    .modal-content {
        width: 100%;
        max-width: 500px;
        margin: 0 auto;
    }


    @media (min-width: 768px) {
        .modal-dialog {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
    }
</style>

<div class="profile-header d-flex flex-column flex-md-row align-items-center">
    <div class="profile-avatar">
        <div class="avatar-img" style="background-image: url('{{ $user->getAvatarUrl() }}');">
        </div>
    </div>
    <div class="profile-details ml-4 text-center text-md-left">
        <h3 class="display-name d-flex align-items-center justify-content-center justify-content-md-start">
            {{ $user->getDisplayName() }}
            <div class="ml-2">
                @if ($user->is_verified)
                    <img data-toggle="tooltip" data-placement="top" src="{{ asset('icon/ico-vefified-1.svg') }}"
                        title="{{ __('Verified') }}" alt="ico-vefified-1">
                @else
                    <img data-toggle="tooltip" data-placement="top" src="{{ asset('icon/ico-not-vefified-1.svg') }}"
                        title="{{ __('Not verified') }}" alt="ico-vefified-1">
                @endif
            </div>
        </h3>
        <div class="d-flex flex-column flex-md-row align-items-center">
            <p class="profile-since mb-0 mr-3">Member Since
                {{ date('M Y', strtotime($user->created_at)) }}</p>

            <div class="text-center mb-1 mr-3">
                <span class="role-name badge badge-primary">{{ $user->role_name }}</span>
            </div>

            @if (auth()->check() && auth()->user()->id == $user->id)
                <div class="text-center mb-1">
                    <a href="{{ route('user.profile.setting') }}" class="role-name badge badge-warning">Edit
                        Profile</a>
                </div>
            @endif
        </div>

        <!-- Followers Section -->
        <div class="follow-stats d-flex justify-content-center justify-content-md-start">
            <div class="stat text-center">
                <p class="number cursor-pointer" id="show-followers" data-toggle="modal" data-target="#followersModal">
                    {{ $followerCount }}</p>
                <p class="label">Followers</p>
            </div>
            <div class="stat text-center ml-4">
                <p class="number cursor-pointer" id="show-following" data-toggle="modal" data-target="#followingModal">
                    {{ $followingCount }}</p>
                <p class="label">Following</p>
            </div>
        </div>

        <!-- Followers Modal -->
        <div class="modal fade" id="followersModal" tabindex="-1" role="dialog" aria-labelledby="followersModalLabel"
            aria-hidden="true">
            <div class="modal-dialog d-flex justify-content-center align-items-center" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="followersModalLabel">Followers</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" style="max-height: 400px; overflow-y: auto;">
                        <ul class="list-group" id="followersList">
                            @foreach ($followers as $follower)
                                <li class="list-group-item d-flex align-items-center"
                                    data-name="{{ strtolower($follower->name) }}">
                                    <div class="avatar-info d-flex align-items-center w-100">
                                        <img src="{{ $follower->avatar_url }}" alt="{{ $follower->name }}'s avatar"
                                            class="avatar rounded-circle">

                                        <a href="{{ url('profile/' . $follower->user_name) }}"
                                            style="color: black; text-decoration: none;"
                                            class="name ml-3">{{ $follower->name }}</a>

                                        @auth
                                            <form action="{{ route('member.store') }}" method="POST" class="ml-auto">
                                                @csrf
                                                <input type="hidden" name="action"
                                                    value="{{ !is_following($follower->id) ? 'follow' : 'unfollow' }}">
                                                <input type="hidden" name="follower_id" value="{{ $follower->id }}">
                                                <button
                                                    class="btn btn-{{ !is_following($follower->id) ? 'primary' : 'secondary' }}">
                                                    {{ !is_following($follower->id) ? 'Follow' : 'Unfollow' }}
                                                </button>
                                            </form>
                                        @else
                                            <button class="btn btn-primary ml-auto"
                                                onclick="showModalLogin()">Follow</button>
                                        @endauth
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Following Modal -->
        <div class="modal fade" id="followingModal" tabindex="-1" role="dialog" aria-labelledby="followingModalLabel"
            aria-hidden="true">
            <div class="modal-dialog d-flex justify-content-center align-items-center" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="followingModalLabel">Following</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" style="max-height: 400px; overflow-y: auto;">
                        <ul class="list-group" id="followingList">
                            @foreach ($following as $follow)
                                <li class="list-group-item d-flex align-items-center"
                                    data-name="{{ strtolower($follow->name) }}">
                                    <div class="avatar-info d-flex align-items-center w-100">
                                        <img src="{{ $follow->avatar_url }}" alt="{{ $follow->name }}'s avatar"
                                            class="avatar rounded-circle">
                                        <a href="{{ url('profile/' . $follow->user_name) }}"
                                            style="color: black; text-decoration: none;"
                                            class="name ml-3">{{ $follow->name }}</a>

                                        @auth
                                            <form action="{{ route('member.store') }}" method="POST" class="ml-auto">
                                                @csrf
                                                <input type="hidden" name="action"
                                                    value="{{ !is_following($follow->id) ? 'follow' : 'unfollow' }}">
                                                <input type="hidden" name="follower_id" value="{{ $follow->id }}">
                                                <button
                                                    class="btn btn-{{ !is_following($follow->id) ? 'primary' : 'secondary' }}">
                                                    {{ !is_following($follow->id) ? 'Follow' : 'Unfollow' }}
                                                </button>
                                            </form>
                                        @else
                                            <button class="btn btn-primary ml-auto"
                                                onclick="showModalLogin()">Follow</button>
                                        @endauth
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        @if (setting_item('vendor_show_email') || setting_item('vendor_show_phone'))
            <div class="contact-info d-flex justify-content-between mt-3 flex-wrap">
                @if (setting_item('vendor_show_email'))
                    <div class="contact-item d-flex align-items-center">
                        <i class="bi-envelope"></i>
                        <span class="label ms-2">{{ $user->email }}</span>
                    </div>
                @endif

                @if (setting_item('vendor_show_phone'))
                    <div class="contact-item d-flex align-items-center">
                        <i class="bi-phone"></i>
                        <span class="label ms-2">{{ $user->phone }}</span>
                    </div>
                @endif
            </div>
        @endif

        <div class="ig-buttons d-flex justify-content-between mt-4 flex-wrap">
            @auth
                <form action="{{ route('member.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="action" value="{{ !is_following($user->id) ? 'follow' : 'unfollow' }}">
                    <input type="hidden" name="follower_id" value="{{ $user->id }}">
                    <button class="btn btn-{{ !is_following($user->id) ? 'primary' : 'secondary' }} ig-follow-btn">
                        {{ !is_following($user->id) ? 'Follow' : 'Unfollow' }}
                    </button>
                </form>
            @else
                <button class="btn btn-primary w-100 mb-2" onclick="showModalLogin()">
                    Follow
                </button>
            @endauth
            <a href="@auth {{ route('user.chat', ['user_id' => $user->id]) }} @else javascript:void(0) @endauth"
                class="btn ig-message-btn" @guest onclick="showModalLogin()" @endguest>
                Message
            </a>
            <a href="#social-share-modal" data-toggle="modal" class="btn ig-share-btn">
                <i class="icofont-share"></i> <span>{{ __('Share') }}</span>
            </a>
            <div class="position-relative">
                <a href="#referral-share-modal" data-toggle="modal" class="btn ig-share-btn text-nowrap">
                    <i class="icofont-share"></i> <span>{{ __('Referral Link') }}</span>
                </a>
                <a href="#referral-information-modal" data-toggle="modal" class="position-absolute" style="right: -10px; top: -10px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8z"></path><path d="M11 11h2v6h-2zm0-4h2v2h-2z"></path></svg>
                </a>
            </div>
        </div>
    </div>
</div>
<div class="profile-bio mt-4">
    <div class="bio-content">
        <p id="bio-text">
            {!! $user->bio !!}
        </p>
        <span id="myBtn" class="show-more-btn" style="cursor: pointer; color: #007bff; font-size: 14px;">Read
            more</span>
    </div>
</div>



<div class="modal fade social-share-modal" id="social-share-modal" tabindex="-1" role="dialog" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="quick-listing-actions-share-social">
                    <ul class="share-options d-flex flex-wrap justify-content-center">
                        <li>
                            <a class="facebook"
                                href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($profileUrl) }}&amp;"
                                target="_blank" rel="noopener" original-title="{{ __('Facebook') }}">
                                <i class="fa fa-facebook fa-lg"></i>
                                <p>{{ __('Facebook') }}</p>
                            </a>
                        </li>
                        <li>
                            <a class="xtwitter" href="https://x.com/share?url={{ urlencode($profileUrl) }}&amp;"
                                target="_blank" rel="noopener">
                                <i class="fa fa-times"></i>
                                <p>{{ __('X-Twitter') }}</p>
                            </a>
                        </li>
                        <li>
                            <a class="whatsapp"
                                href="https://api.whatsapp.com/send?text={{ urlencode($profileUrl) }}"
                                target="_blank" rel="noopener">
                                <i class="fa fa-whatsapp fa-lg"></i>
                                <p>{{ __('Whatsapp') }}</p>
                            </a>
                        </li>
                        <li>
                            <a class="telegram" href="https://telegram.me/share/url?url={{ urlencode($profileUrl) }}"
                                target="_blank" rel="noopener">
                                <i class="fa fa-telegram fa-lg"></i>
                                <p>{{ __('Telegram') }}</p>
                            </a>
                        </li>
                        <li>
                            <a class="pinterest"
                                href="https://pinterest.com/pin/create/button/?url={{ urlencode($profileUrl) }}"
                                target="_blank" rel="noopener">
                                <i class="fa fa-pinterest fa-lg"></i>
                                <p>{{ __('Pinterest') }}</p>
                            </a>
                        </li>
                        <li>
                            <a class="linkedin"
                                href="http://www.linkedin.com/shareArticle?mini=true&amp;url={{ urlencode($profileUrl) }}"
                                target="_blank" rel="noopener">
                                <i class="fa fa-linkedin fa-lg"></i>
                                <p>{{ __('Linkedin') }}</p>
                            </a>
                        </li>
                        <li>
                            <a class="tumblr"
                                href="https://www.tumblr.com/share?v=3&amp;u={{ urlencode($profileUrl) }}"
                                target="_blank" rel="noopener">
                                <i class="fa fa-tumblr fa-lg"></i>
                                <p>{{ __('Tumblr') }}</p>
                            </a>
                        </li>
                        <li>
                            <a class="vk" href="http://vk.com/share.php?url={{ urlencode($profileUrl) }}"
                                target="_blank" rel="noopener">
                                <i class="fa fa-vk fa-lg"></i>
                                <p>{{ __('VKontakte') }}</p>
                            </a>
                        </li>
                        <li>
                            <a class="email" href="mailto:?subject={{ urlencode($profileUrl) }}" target="_blank"
                                rel="noopener">
                                <i class="fa fa-envelope fa-lg"></i>
                                <p>{{ __('Email') }}</p>
                            </a>
                        </li>
                        <li>
                            <span id="share-copy-text" class="d-none">{{ $profileUrl }}</span>
                            <a id="share-copy-btn" data-toggle="tooltip" data-placement="top"
                                title="Copy to clipboard" onclick="copyToClipboard('share-copy-text')"
                                onmouseout="outCopyFunc()" href="javascript:void(0)">
                                <i class="fa fa-copy fa-lg"></i>
                            </a>
                            <span>{{ __('Copy link') }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade referral-share-modal" id="referral-share-modal" tabindex="-1" role="dialog" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="quick-listing-actions-share-social">
                    <ul class="share-options d-flex flex-wrap justify-content-center">
                        <li>
                            <a class="facebook"
                                href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($referralUrl) }}&amp;"
                                target="_blank" rel="noopener" original-title="{{ __('Facebook') }}">
                                <i class="fa fa-facebook fa-lg"></i>
                                <p>{{ __('Facebook') }}</p>
                            </a>
                        </li>
                        <li>
                            <a class="xtwitter" href="https://x.com/share?url={{ urlencode($referralUrl) }}&amp;"
                                target="_blank" rel="noopener">
                                <i class="fa fa-times"></i>
                                <p>{{ __('X-Twitter') }}</p>
                            </a>
                        </li>
                        <li>
                            <a class="whatsapp"
                                href="https://api.whatsapp.com/send?text={{ urlencode($referralUrl) }}"
                                target="_blank" rel="noopener">
                                <i class="fa fa-whatsapp fa-lg"></i>
                                <p>{{ __('Whatsapp') }}</p>
                            </a>
                        </li>
                        <li>
                            <a class="telegram" href="https://telegram.me/share/url?url={{ urlencode($referralUrl) }}"
                                target="_blank" rel="noopener">
                                <i class="fa fa-telegram fa-lg"></i>
                                <p>{{ __('Telegram') }}</p>
                            </a>
                        </li>
                        <li>
                            <a class="pinterest"
                                href="https://pinterest.com/pin/create/button/?url={{ urlencode($referralUrl) }}"
                                target="_blank" rel="noopener">
                                <i class="fa fa-pinterest fa-lg"></i>
                                <p>{{ __('Pinterest') }}</p>
                            </a>
                        </li>
                        <li>
                            <a class="linkedin"
                                href="http://www.linkedin.com/shareArticle?mini=true&amp;url={{ urlencode($referralUrl) }}"
                                target="_blank" rel="noopener">
                                <i class="fa fa-linkedin fa-lg"></i>
                                <p>{{ __('Linkedin') }}</p>
                            </a>
                        </li>
                        <li>
                            <a class="tumblr"
                                href="https://www.tumblr.com/share?v=3&amp;u={{ urlencode($referralUrl) }}"
                                target="_blank" rel="noopener">
                                <i class="fa fa-tumblr fa-lg"></i>
                                <p>{{ __('Tumblr') }}</p>
                            </a>
                        </li>
                        <li>
                            <a class="vk" href="http://vk.com/share.php?url={{ urlencode($referralUrl) }}"
                                target="_blank" rel="noopener">
                                <i class="fa fa-vk fa-lg"></i>
                                <p>{{ __('VKontakte') }}</p>
                            </a>
                        </li>
                        <li>
                            <a class="email" href="mailto:?subject={{ urlencode($referralUrl) }}" target="_blank"
                                rel="noopener">
                                <i class="fa fa-envelope fa-lg"></i>
                                <p>{{ __('Email') }}</p>
                            </a>
                        </li>
                        <li>
                            <span id="referral-copy-text" class="d-none">{{ $referralUrl }}</span>
                            <a id="referral-copy-btn" data-toggle="tooltip" data-placement="top"
                                title="Copy to clipboard" onclick="copyToClipboard('referral-copy-text')"
                                onmouseout="outCopyFunc()" href="javascript:void(0)">
                                <i class="fa fa-copy fa-lg"></i>
                            </a>
                            <span>{{ __('Copy link') }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade referral-information-modal" id="referral-information-modal" tabindex="-1" role="dialog" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="">
                    <svg class="mb-2" xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8z"></path><path d="M11 11h2v6h-2zm0-4h2v2h-2z"></path></svg>

                    <p>On Every person who registers you through your referral you will have 10% of the Virtuard revenues generated by him forever and you can Monitor on <a href="https://virtuard.com/vendor/referral">Referral page</a></p>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    document.addEventListener("DOMContentLoaded", function() {
        const bioText = document.getElementById("bio-text");
        const readMoreBtn = document.getElementById("myBtn");

        const originalText = bioText.textContent;
        let maxChars;

        function setMaxChars() {
            if (window.innerWidth <= 768) {
                maxChars = 150;
            } else {
                maxChars = 300;
            }
        }

        setMaxChars();

        function truncateText() {
            if (originalText.length > maxChars) {
                bioText.textContent = originalText.substring(0, maxChars) + "...";
                readMoreBtn.style.display = "inline";
            } else {
                bioText.textContent = originalText;
                readMoreBtn.style.display = "none";
            }
        }

        window.addEventListener("resize", function() {
            setMaxChars();
            truncateText();
        });

        readMoreBtn.addEventListener("click", function() {
            if (bioText.textContent.endsWith("...")) {
                bioText.textContent = originalText;
                readMoreBtn.textContent = "Read less";
            } else {
                truncateText();
                readMoreBtn.textContent = "Read more";
            }
        });

        truncateText();
    });
</script>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
{{-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<!-- jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script> --}}
