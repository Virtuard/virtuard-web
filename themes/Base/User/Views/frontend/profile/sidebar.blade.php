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
</style>
<div class="profile-summary mb-2">
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
    <ul class="meta-info style2">
        @auth
            <form action="{{ route('member.store') }}" method="POST">
            @csrf
                <input type="hidden" name="param" value="{{ !is_following($user->id) ? 'follow' : 'unfollow' }}">
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
        <a href="@auth {{ route('user.chat', ['user_id' => $user->id]) }} @else javascript:void(0) @endauth" class="btn btn-primary w-100"
            @guest
            onclick="showModalLogin()"
            @endguest
            >
            Message
        </a>
    </ul>

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
