<style>
    ul.meta-info.style1 {
        list-style: none;
        padding: 0;
        margin: 0;
        max-width: 100%;
        /* Limit the maximum width of the list */
        overflow: hidden;
        /* Hide overflow content */
    }

    ul.meta-info.style1 li {
        margin: 10px 0;
        /* Add spacing between list items as needed */
    }

    ul.meta-info.style1 .label {
        font-weight: bold;
    }

    ul.meta-info.style1 .val {
        display: block;
        word-wrap: break-word;
        /* Wrap long words or URLs to the next line */
        max-width: 100%;
        /* Limit the maximum width of the value */
    }

    .follow-stats {
    display: flex;
    justify-content: center;
    gap: 20px; /* Jarak antara followers dan following */
    text-align: center;
    font-family: Arial, sans-serif; /* Sesuaikan dengan font Anda */
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
</style>
<div class="profile-summary mb-2">
    @if (auth()->check() && auth()->user()->id == $user->id)
        <div class="text-center mb-1">
            <a href="{{ route('user.profile.setting') }}" class="badge badge-warning">Edit Profile</a>
        </div>
    @endif
    <div class="profile-avatar">
        @if ($avatar = $user->getAvatarUrl())
            <div class="avatar-img avatar-cover" style="background-image: url('{{ $user->getAvatarUrl() }}')">
            </div>
        @else
            <span class="avatar-text">{{ $user->getDisplayName()[0] }}</span>
        @endif
    </div>
    <div class="text-center mb-1"><span class="role-name  badge badge-primary">{{ $user->role_name }}</span></div>
    <h3 class="display-name">{{ $user->getDisplayName() }}
        @if ($user->is_verified)
            <img data-toggle="tooltip" data-placement="top" src="{{ asset('icon/ico-vefified-1.svg') }}"
                title="{{ __('Verified') }}" alt="ico-vefified-1">
        @else
            <img data-toggle="tooltip" data-placement="top" src="{{ asset('icon/ico-not-vefified-1.svg') }}"
                title="{{ __('Not verified') }}" alt="ico-vefified-1">
        @endif
    </h3>

    <p class="profile-since">{{ __('Member Since :time', ['time' => date('M Y', strtotime($user->created_at))]) }}</p>
    <div class="follow-stats mb-2">
        <div class="stat">
            <p class="label">Followers</p>
            <p class="number">{{ $followerCount }}</p>
        </div>
        <div class="stat">
            <p class="label">Following</p>
            <p class="number">{{ $followingCount }}</p>
        </div>
    </div>
    <ul class="meta-info style2">
        @auth
            <form action="{{ route('member.store') }}" method="POST">
                @csrf
                <input type="hidden" name="action" value="{{ !is_following($user->id) ? 'follow' : 'unfollow' }}">
                <input type="hidden" name="follower_id" value="{{ $user->id }}">
                <button class="btn btn-{{ !is_following($user->id) ? 'primary' : 'secondary' }} w-100 mb-2">
                    {{ !is_following($user->id) ? 'Follow' : 'Unfollow' }}
                </button>
            </form>
        @else
            <button class="btn btn-primary w-100 mb-2" onclick="showModalLogin()">
                Follow
            </button>
        @endauth
        <a href="@auth {{ route('user.chat', ['user_id' => $user->id]) }} @else javascript:void(0) @endauth"
            class="btn btn-primary w-100" @guest
onclick="showModalLogin()" @endguest>
            Message
        </a>
        <a href="#social-share-modal" data-toggle="modal" class="btn btn-transparent text-primary w-100 mt-2"
            style="background-color: transparent; border: none;">
            <i class="icofont-share"></i> <span>{{ __('Share') }}</span>
        </a>
    </ul>
    <!-- Modal Social Share -->
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
                                <a class="telegram"
                                    href="https://telegram.me/share/url?url={{ urlencode($profileUrl) }}"
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


    @if ($user->hasPermission('dashboard_vendor_access'))
        <hr>
        <ul class="meta-info style2">
            <li class="is_vendor">
                <i class="icon ion-ios-ribbon"></i>
                {{ __('Vendor') }}
            </li>
            <li class="review_count">
                <i class="icon ion-ios-thumbs-up"></i>
                @if ($user->review_count <= 1)
                    {{ __(':count review', ['count' => $user->review_count]) }}
                @else
                    {{ __(':count reviews', ['count' => $user->review_count]) }}
                @endif
            </li>
        </ul>
    @endif
    @if (setting_item('vendor_show_email') or setting_item('vendor_show_phone'))
        <hr>
        <ul class="meta-info style1">
            @if (setting_item('vendor_show_email'))
                <li class="user_email">
                    <span class="label">{{ __('Email:') }}</span>
                    <span class="val">{{ $user->email }}</span>
                </li>
            @endif

            @if (setting_item('vendor_show_phone'))
                <li class="user_phone">
                    <span class="label">{{ __('Phone:') }}</span>
                    <span class="val">{{ $user->phone }}</span>
                </li>
            @endif
        </ul>
    @endif
    @if (empty(setting_item('user_disable_verification_feature')))
        <hr>
        <h4 class="summary-title">{{ __('Verifications') }}</h4>
        <ul class="verification-lists">
            @if (!empty($user->verification_fields))
                @foreach ($user->verification_fields as $field)
                    <li> <span class="left-icon">
                            @if ($field['is_verified'])
                                <img src="{{ asset('icon/success.svg') }}" alt="success">
                            @else
                                <img src="{{ asset('icon/x.svg') }}" alt="success">
                            @endif
                        </span> <span>{{ $field['name'] }}</span>

                    </li>
                @endforeach
            @endif
        </ul>
    @endif
</div>
