<?php
$types = get_bookable_services();
if (empty($types)) return;
$list_service = [];
$userPanoramas = getUserPanoramas($user->id);
$isOwner = auth()->check() && auth()->user()->id == $user->id;
?>
<div class="profile-service-tabs">
    <div class="service-nav-tabs mb-2">
        <ul class="nav nav-tabs w-100 flex-nowrap">
            @php $i = 0; @endphp
            <li class="nav-item">
                <a href="#" class="nav-link @if(!$i) active @endif" data-toggle="tab" data-target="#profile">Profile</a>
            </li>
            @if($isOwner || $userPanoramas->count() > 0)
            <li class="nav-item">
                    <a href="#" class="nav-link" data-toggle="tab" data-target="#profile_360">Virtual Tour</a>
                </li>
            @endif
            @foreach($types as $type=>$moduleClass)
                @php
                    if(!in_array($type, menu_listing()))  continue;
                    if(!$moduleClass::isEnable()) continue;
                    if(!$user->hasPermission($type.'_create')) continue;
                    $services = $moduleClass::getVendorServicesQuery($user->id)->orderBy('id','desc')->paginate(6);
                    if(empty($services->total())) continue;
                    $list_service[$type] = $services;
    
                    $attrType = get_attribute_listing($type);
                    $typeKey = $attrType['old_key'];
                    $typeText = $attrType['new_key'];
                @endphp
                <li class="nav-item">
                    <a href="#" class="nav-link" data-toggle="tab" data-target="#type_{{$typeKey}}">{{ $typeText }}</a>
                </li>
            @php $i++; @endphp
            @endforeach
        </ul>
    </div>
    <div class="tab-content">
        @php 
            $i = 0; 
        @endphp
        <div class="tab-pane fade @if(!$i) show active @endif" id="profile" role="tabpanel" aria-labelledby="pills-home-tab">
            @include('User::frontend.profile.gallery', ['userPosts' => getUserPosts($user->id)])
        </div>
        @if($isOwner || $userPanoramas->count() > 0)
            <div class="tab-pane fade" id="profile_360" role="tabpanel" aria-labelledby="pills-home-tab">
                @include('User::frontend.profile.gallery_360', ['userPanoramas' => $userPanoramas])
            </div>
        @endif
        @foreach($types as $type=>$moduleClass)
            @php
                if($type == "flight")  continue;
                if(!$moduleClass::isEnable()) continue;
                if(empty($list_service[$type])) continue;
            @endphp
                @if(view()->exists(ucfirst($type).'::frontend.profile.service') && $user->hasPermission($type.'_create'))
                    <div class="tab-pane fade" id="type_{{$type}}" role="tabpanel" aria-labelledby="pills-home-tab">
                        @include(ucfirst($type).'::frontend.profile.service',['services'=>$list_service[$type]])
                    </div>
                    @php $i++; @endphp
                @endif
        @endforeach
    </div>
</div>

<style>
    .nav-tabs {
        display: flex;
        flex-wrap: nowrap; 
        gap: 10px; 
    }

    .nav-item {
        flex: 0 0 auto; 
    }

    @media (max-width: 768px) {
        .nav-tabs {
            overflow-x: auto; 
            -webkit-overflow-scrolling: touch; 
        }

        .nav-item {
            flex: 0 0 auto; 
        }
    }
</style>