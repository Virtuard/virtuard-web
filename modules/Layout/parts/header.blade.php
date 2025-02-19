
<div class="bravo_header">
    <div class="{{$container_class ?? 'container'}}">
        <div class="content justify-content-between">
            <div class="header-left">
                <a href="/" class="bravo-logo">
                    @php
                        $logo_id = setting_item("logo_id");
                        if(!empty($row->custom_logo)){
                            $logo_id = $row->custom_logo;
                        }
                    @endphp
                    @if($logo_id)
                        <?php $logo = get_file_url($logo_id,'full') ?>
                        <img loading='lazy'src="{{$logo}}" alt="{{setting_item("site_title")}}">
                    @endif
                </a>
                <div class="bravo-menu">
                    <?php generate_menu('primary') ?>
                </div>
            </div>
            <div class="header-right">
                <?php $header_right_menu = true ?>
                @if(!empty($header_right_menu))
                    <ul class="topbar-items">
                        {{-- @if(setting_item('google_translate_enable'))
                        <li class="menu-hr">
                            <div id="gtranslate-web"></div>
                        </li>
                        @else
                        @include('Language::frontend.switcher')
                        @endif --}}
                        @if (is_enable_multi_lang())
                            @include('Language::frontend.switcher')
                        @endif
                        @include('Core::frontend.currency-switcher')
                        @if(!Auth::check())
                            <li class="login-item">
                                <a href="#login" data-toggle="modal" data-target="#login" class="login">{{__('Login')}}</a>
                            </li>
                            @if(is_enable_registration())
                                <li class="signup-item">
                                    <a href="#register" data-toggle="modal" data-target="#register" class="signup">{{__('Sign Up')}}</a>
                                </li>
                            @endif
                        @else
                            @if(Auth::check() && setting_item('inbox_enable'))
                                <li class="p-0">
                                    <a href="{{route('user.chat')}}">
                                        <i class="fa fa-comments" style="font-size: 14px;"></i>
                                        <sup class="badge badge-danger" style="top: -12px;">{{ auth()->user()->unseen_message_count }}</sup>
                                    </a>
                                </li>
                            @endif
                            @include('Layout::parts.notification')
                            <li class="login-item dropdown">
                                <a href="#" data-toggle="dropdown" class="is_login">
                                    @if($avatar_url = Auth::user()->getAvatarUrl())
                                        <img loading='lazy'class="avatar" src="{{$avatar_url}}" alt="{{ Auth::user()->getDisplayName()}}">
                                    @else
                                        <span class="avatar-text">{{ucfirst( Auth::user()->getDisplayName()[0])}}</span>
                                    @endif
                                    {{__("Hi, :Name",['name'=>Auth::user()->getDisplayName()])}}
                                    <i class="fa fa-angle-down"></i>
                                </a>
                                <ul class="dropdown-menu text-left">

                                    @if(Auth::user()->hasPermission('dashboard_vendor_access'))
                                        <li><a href="{{route('vendor.dashboard')}}"><i class="icon ion-md-analytics"></i> {{__("Vendor Dashboard")}}</a></li>
                                    @endif
                                    <li class="@if(Auth::user()->hasPermission('dashboard_vendor_access')) menu-hr @endif">
                                        <a href="{{route('user.profile.index')}}"><i class="icon ion-md-construct"></i> {{__("My profile")}}</a>
                                    </li>
                                    @if(setting_item('inbox_enable'))
                                    <li class="menu-hr"><a href="{{route('user.chat')}}"><i class="fa fa-comments"></i> {{__("Messages")}}</a></li>
                                    @endif
                                    <li class="menu-hr"><a href="{{route('user.booking_history')}}"><i class="fa fa-clock-o"></i> {{__("Booking History")}}</a></li>
                                    <li class="menu-hr"><a href="{{route('user.change_password')}}"><i class="fa fa-lock"></i> {{__("Change password")}}</a></li>
                                    @if(Auth::user()->hasPermission('dashboard_access'))
                                        <li class="menu-hr"><a href="{{route('admin.index')}}"><i class="icon ion-ios-ribbon"></i> {{__("Admin Dashboard")}}</a></li>
                                    @endif
                                    <li class="menu-hr">
                                        <a  href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fa fa-sign-out"></i> {{__('Logout')}}</a>
                                    </li>
                                </ul>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                            </li>
                        @endif
                        <li class="create-item @auth auth @endauth">
                            <a href="{{ route('create') }}" class="btn">
                                <i class="fa fa-list"></i>
                                {{ __('Create') }}
                            </a>
                        </li>
                    </ul>
                @endif
                <div class="mobile-header">
                    <div class="mobile-icons d-md-none">
                        @if (!Auth::check())
                            <a href="#" data-toggle="modal" data-target="#login" class="mobile-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"><path d="M12 2a5 5 0 1 0 5 5 5 5 0 0 0-5-5zm0 8a3 3 0 1 1 3-3 3 3 0 0 1-3 3zm9 11v-1a7 7 0 0 0-7-7h-4a7 7 0 0 0-7 7v1h2v-1a5 5 0 0 1 5-5h4a5 5 0 0 1 5 5v1z"></path></svg>
                            </a>
                        @else
                            <a href="{{ route('user.profile.index') }}" class="mobile-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"><path d="M7.5 6.5C7.5 8.981 9.519 11 12 11s4.5-2.019 4.5-4.5S14.481 2 12 2 7.5 4.019 7.5 6.5zM20 21h1v-1c0-3.859-3.141-7-7-7h-4c-3.86 0-7 3.141-7 7v1h17z"></path></svg>
                            </a>
                        @endif
                        @if(Auth::check() && setting_item('inbox_enable'))
                        <a href="{{ route('user.chat') }}" class="mobile-icon">
                            <i class="fa fa-comments" style="font-size: 18px;"></i>
                            <span class="badge badge-danger" style="top: -5px; right: -10px;">{{ auth()->user()->unseen_message_count ?? 0 }}</span>
                        </a>
                        @endif
                        
                        @include('Layout::parts.notification')

                        <button class="bravo-more-menu">
                            <i class="fa fa-bars"></i>
                        </button>
                    </div>
                </div>
                
                <style>
                .mobile-header {
                    display: flex;
                    justify-content: flex-end;
                    align-items: center; 
                    padding: 10px; 
                   
                }
                
                .mobile-icons {
                    display: flex;
                    gap: 10px; 
                    align-items: center;
                }
                
                .mobile-icons .mobile-icon {
                    position: relative;
                    font-size: 18px;
                    color: #333;
                    text-decoration: none;
                }
                
                .mobile-icons .badge {
                    position: absolute;
                    top: -5px;
                    right: -10px;
                    font-size: 12px;
                    background-color: red;
                    color: white;
                    border-radius: 50%;
                    padding: 2px 6px;
                }
                
                .bravo-more-menu {
                    font-size: 18px;
                    background: none;
                    border: none;
                    cursor: pointer;
                    color: #333;
                    padding: 5px;
                }
                
                @media (min-width: 768px) {
                    .mobile-header {
                        display: none;
                    }
                }
                </style>
                         
                {{-- <button class="bravo-more-menu">
                    <i class="fa fa-bars"></i>
                </button> --}}
            </div>
        </div>
    </div>
    <div class="bravo-menu-mobile" style="display:none;">
        <div class="user-profile">
            <div class="b-close"><i class="icofont-scroll-left"></i></div>
            <div class="avatar"></div>
            <ul>
                @if(!Auth::check())
                    <li>
                        <a href="#login" data-toggle="modal" data-target="#login" class="login">{{__('Login')}}</a>
                    </li>
                    @if(is_enable_registration())
                        <li>
                            <a href="#register" data-toggle="modal" data-target="#register" class="signup">{{__('Sign Up')}}</a>
                        </li>
                    @endif
                @else
                    <li>
                        <a href="{{route('user.profile.index')}}">
                            <i class="icofont-user-suited"></i> {{__("Hi, :Name",['name'=>Auth::user()->getDisplayName()])}}
                        </a>
                    </li>
                    @if(Auth::user()->hasPermission('dashboard_vendor_access'))
                        <li><a href="{{route('vendor.dashboard')}}"><i class="icon ion-md-analytics"></i> {{__("Vendor Dashboard")}}</a></li>
                    @endif
                    @if(Auth::user()->hasPermission('dashboard_access'))
                        <li>
                            <a href="{{route('admin.index')}}"><i class="icon ion-ios-ribbon"></i> {{__("Admin Dashboard")}}</a>
                        </li>
                    @endif
                    <li>
                        <a href="{{route('user.profile.index')}}">
                            <i class="icon ion-md-construct"></i> {{__("My profile")}}
                        </a>
                    </li>
                    <li>
                        <a  href="#" onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();">
                            <i class="fa fa-sign-out"></i> {{__('Logout')}}
                        </a>
                        <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                        </form>
                    </li>

                @endif
            </ul>
            <ul class="multi-lang">
                {{-- @if(setting_item('google_translate_enable'))
                    <div id="gtranslate-mobile" style="margin-left: 20px;"></div>
                @else
                    @include('Language::frontend.switcher')
                @endif --}}
            </ul>
            {{-- //tes --}}
            <ul class="multi-lang">
                @if (is_enable_multi_lang())
                    @include('Language::frontend.switcher')
                @endif
                @include('Core::frontend.currency-switcher')
            </ul>
        </div>
        <div class="g-menu">
            <?php generate_menu('primary') ?>
            <li class="create-item @auth auth @endauth m-2">
                <a href="{{ route('create') }}" class="btn">
                    <i class="fa fa-list"></i>
                    {{ __('Create') }}
                </a>
            </li>
        </div>
    </div>
</div>

@stack('custom-scripts')
