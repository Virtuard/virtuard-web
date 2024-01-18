<?php
$dataUser = Auth::user();
$menus = [
    'dashboard' => [
        'url' => route('vendor.dashboard'),
        'title' => __('Dashboard'),
        'icon' => 'fa fa-home',
        'permission' => 'dashboard_vendor_access',
        'position' => 1,
    ],
    'booking-history' => [
        'url' => route('user.booking_history'),
        'title' => __('Booking History'),
        'icon' => 'fa fa-clock-o',
        'position' => 4,
    ],
    'wishlist' => [
        'url' => route('user.wishList.index'),
        'title' => __('Wishlist'),
        'icon' => 'fa fa-heart-o',
        'position' => 3,
    ],
    'profile-setting' => [
        'url' => route('user.profile.setting'),
        'title' => __('Profile Setting'),
        'icon' => 'fa fa-cogs',
        'position' => 100,
    ],
    'profile' => [
        'url' => route('user.profile.index'),
        'title' => __('My Profile'),
        'icon' => 'fa fa-user',
        'position' => 2,
    ],
    'password' => [
        'url' => route('user.change_password'),
        'title' => __('Change password'),
        'icon' => 'fa fa-lock',
        'position' => 100,
    ],
    'admin' => [
        'url' => route('admin.index'),
        'title' => __('Admin Dashboard'),
        'icon' => 'icon ion-ios-ribbon',
        'permission' => 'dashboard_access',
        'position' => 110,
    ],
    'virtuard360' => [
        'url' => route('user-virtuard'),
        'title' => 'Virtuard 360',
        'icon' => 'fa fa-camera',
        'permission' => 'dashboard_vendor_access',
        'position' => 5,
    ],
    // 'listing' => [
    //     'url' => '#',
    //     'title' => 'Listing',
    //     'icon' => 'fa fa-list',
    //     'position' => 6,
    //     'children' => [],
    // ],
];

// Modules
$custom_modules = \Modules\ServiceProvider::getModules();
if (!empty($custom_modules)) {
    foreach ($custom_modules as $module) {
        $moduleClass = '\\Modules\\' . ucfirst($module) . '\\ModuleProvider';
        if (class_exists($moduleClass)) {
            $menuConfig = call_user_func([$moduleClass, 'getUserMenu']);
            if (!empty($menuConfig)) {
                $menus = array_merge($menus, $menuConfig);
            }
            $menuSubMenu = call_user_func([$moduleClass, 'getUserSubMenu']);
            if (!empty($menuSubMenu)) {
                foreach ($menuSubMenu as $k => $submenu) {
                    $submenu['id'] = $submenu['id'] ?? '_' . $k;
                    if (!empty($submenu['parent']) and isset($menus[$submenu['parent']])) {
                        $menus[$submenu['parent']]['children'][$submenu['id']] = $submenu;
                        $menus[$submenu['parent']]['children'] = array_values(
                            \Illuminate\Support\Arr::sort($menus[$submenu['parent']]['children'], function ($value) {
                                return $value['position'] ?? 100;
                            }),
                        );
                    }
                }
            }
        }
    }
}

// Plugins Menu
$plugins_modules = \Plugins\ServiceProvider::getModules();
if (!empty($plugins_modules)) {
    foreach ($plugins_modules as $module) {
        $moduleClass = '\\Plugins\\' . ucfirst($module) . '\\ModuleProvider';
        if (class_exists($moduleClass)) {
            $menuConfig = call_user_func([$moduleClass, 'getUserMenu']);
            if (!empty($menuConfig)) {
                $menus = array_merge($menus, $menuConfig);
            }
            $menuSubMenu = call_user_func([$moduleClass, 'getUserSubMenu']);
            if (!empty($menuSubMenu)) {
                foreach ($menuSubMenu as $k => $submenu) {
                    $submenu['id'] = $submenu['id'] ?? '_' . $k;
                    if (!empty($submenu['parent']) and isset($menus[$submenu['parent']])) {
                        $menus[$submenu['parent']]['children'][$submenu['id']] = $submenu;
                        $menus[$submenu['parent']]['children'] = array_values(
                            \Illuminate\Support\Arr::sort($menus[$submenu['parent']]['children'], function ($value) {
                                return $value['position'] ?? 100;
                            }),
                        );
                    }
                }
            }
        }
    }
}

// Custom Menu
$custom_modules = \Custom\ServiceProvider::getModules();
if (!empty($custom_modules)) {
    foreach ($custom_modules as $module) {
        $moduleClass = '\\Custom\\' . ucfirst($module) . '\\ModuleProvider';
        if (class_exists($moduleClass)) {
            $menuConfig = call_user_func([$moduleClass, 'getUserMenu']);
            if (!empty($menuConfig)) {
                $menus = array_merge($menus, $menuConfig);
            }
            $menuSubMenu = call_user_func([$moduleClass, 'getUserSubMenu']);
            if (!empty($menuSubMenu)) {
                foreach ($menuSubMenu as $k => $submenu) {
                    $submenu['id'] = $submenu['id'] ?? '_' . $k;
                    if (!empty($submenu['parent']) and isset($menus[$submenu['parent']])) {
                        $menus[$submenu['parent']]['children'][$submenu['id']] = $submenu;
                        $menus[$submenu['parent']]['children'] = array_values(
                            \Illuminate\Support\Arr::sort($menus[$submenu['parent']]['children'], function ($value) {
                                return $value['position'] ?? 100;
                            }),
                        );
                    }
                }
            }
        }
    }
}

// check list menu
foreach ($menus as $key => $menu) {
    $menus[$key]['id'] = $key;
    $menus[$key]['position'] = setMenuPosition($menus[$key]);
    if (in_array($key, menu_listing())) {
        $menus[$key]['category'] = 'listing';
        // $menus['listing']['children'][] = $menu;
        // unset($menus[$key]);
    }
    if (!in_array($key, menu_user())) {
        unset($menus[$key]);
    }
}
// dd($menus);

$currentUrl = url(Illuminate\Support\Facades\Route::current()->uri());
if (!empty($menus)) {
    $menus = array_values(
        \Illuminate\Support\Arr::sort($menus, function ($value) {
            return $value['position'] ?? 100;
        }),
    );
}

foreach ($menus as $k => $menuItem) {
    if (!empty($menuItem['permission']) and !Auth::user()->hasPermission($menuItem['permission'])) {
        unset($menus[$k]);
        continue;
    }
    $menus[$k]['class'] = $currentUrl == url($menuItem['url']) ? 'active' : '';
    if (!empty($menuItem['children'])) {
        $menus[$k]['class'] .= ' has-children';
        foreach ($menuItem['children'] as $k2 => $menuItem2) {
            if (!empty($menuItem2['permission']) and !Auth::user()->hasPermission($menuItem2['permission'])) {
                unset($menus[$k]['children'][$k2]);
                continue;
            }
            $menus[$k]['children'][$k2]['class'] = $currentUrl == url($menuItem2['url']) ? 'active active_child' : '';
        }
    }
}

?>
<div class="sidebar-user">
    <div class="bravo-close-menu-user"><i class="icofont-scroll-left"></i></div>
    <div class="logo">
        @if ($avatar_url = $dataUser->getAvatarUrl())
            <div class="avatar avatar-cover" style="background-image: url('{{ $dataUser->getAvatarUrl() }}')"></div>
        @else
            <span class="avatar-text">{{ ucfirst($dataUser->getDisplayName()[0]) }}</span>
        @endif
    </div>
    <div class="user-profile-avatar">
        <div class="info-new">
            <span class="role-name badge badge-info">{{ $dataUser->role_name }}</span>
            <h5>{{ $dataUser->getDisplayName() }}</h5>
            <p>{{ __('Member Since :time', ['time' => date('M Y', strtotime($dataUser->created_at))]) }}</p>
        </div>
    </div>
    <div class="user-profile-plan">
        {{-- @if (!Auth::user()->hasPermission('dashboard_vendor_access') and setting_item('vendor_enable')) --}}
        @if (Auth::user()->role_id == '3')
            <a href=" {{ route('user.upgrade_vendor') }}">{{ __('Become a vendor') }}</a>
        @endif
    </div>
    <div class="sidebar-menu">
        <ul class="main-menu">
            @foreach ($menus as $key => $menuItem)

            @if ($key === 5)
                <li id="nav-listing" class="nav-category nav-listing">
                    <a href="#">
                        <span class="icon text-center"><i class="fa fa-list"></i></span>
                        Manage Listing
                    </a>
                    <i class="caret"></i>
                </li>
            @endif

                <li
                    class="{{ $menuItem['class'] }} {{ $menuItem['category'] ?? '' }} {{ isset($menuItem['category']) ? 'd-none' : '' }}"
                    position="{{ $menuItem['position'] ?? '' }}"
                    >
                    <a href="{{ url($menuItem['url']) }}">

                        @if (!empty($menuItem['icon']))
                            <?php
                            $iconMenu = $menuItem['icon'];

                            if ($menuItem['icon'] === 'fa fa-building-o') {
                                $iconMenu = 'fa fa-shopping-bag';
                            } elseif ($menuItem['icon'] === 'icofont-island-alt') {
                                $iconMenu = 'fa fa-tree';
                            } elseif ($menuItem['icon'] === 'icofont-building-alt') {
                                $iconMenu = 'fa fa-home';
                            } elseif ($menuItem['icon'] === 'icofont-ui-flight') {
                                $iconMenu = 'fa fa-laptop';
                            } elseif ($menuItem['icon'] === 'icofont-car') {
                                $iconMenu = 'fa fa-industry';
                            } elseif ($menuItem['icon'] === 'icofont-ticket') {
                                $iconMenu = 'fa fa-leaf';
                            }
                            ?>

                            <span class="icon text-center"><i class="{{ $iconMenu }}"></i></span>
                        @endif

                        <?php
                        $dataTitleName = $menuItem['title'];
                        ?>


                        {!! clean($dataTitleName) !!}

                        @if (checkMenuVendor($menuItem))
                            <span class="icon text-center icon-lock">
                                <i class="fa fa-star"></i>
                            </span>
                        @endif
                    </a>
                    @if (!empty($menuItem['children']))
                        <i class="caret"></i>
                    @endif
                    @if (!empty($menuItem['children']))
                        <ul class="children">
                            @foreach ($menuItem['children'] as $menuItem2)
                                <li class="{{ $menuItem2['class'] }}">
                                    <a href="{{ url($menuItem2['url']) }}">
                                        @if (!empty($menuItem2['icon']))
                                            <i class="{{ $menuItem2['icon'] }}"></i>
                                        @endif
                                        {!! clean($menuItem2['title']) !!}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </li>

                {{-- @if ($menuItem['title'] === 'Dashboard')
                    <li class="" position="9">
                        <a href="/user/virtuard-360">
                            <span class="icon text-center"><i class="fa fa-camera"></i></span>
                            Virtuard 360
                        </a>
                    </li>
                @endif --}}
            @endforeach
        </ul>
    </div>
    <div class="logout">
        <form id="logout-form-vendor" action="{{ route('logout') }}" method="POST" style="display: none;">
            {{ csrf_field() }}
        </form>
        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form-vendor').submit();"><i
                class="fa fa-sign-out"></i> {{ __('Log Out') }}
        </a>
    </div>
    <div class="logout">
        <a href="{{ url('/') }}" style="color: #1ABC9C"><i class="fa fa-long-arrow-left"></i>
            {{ __('Back to Homepage') }}</a>
    </div>
</div>
