<?php
if(!auth()->check()) return;
[$notifications,$countUnread] = getNotify();
?>

<div class="mobile-header">
    <a href="#" class="mobile-icon" data-toggle="dropdown">
        <i class="fa fa-bell"></i>
        <span class="badge badge-danger position-absolute top-0 start-100 translate-middle">{{$countUnread}}</span>
    </a>
    <ul class="dropdown-menu notify-items dropdown-container dropdown-menu-right dropdown-large">
        <div class="dropdown-toolbar">
            <div class="dropdown-toolbar-actions">
                <a href="#" class="markAllAsRead">{{__('Mark all as read')}}</a>
            </div>
            <h3 class="dropdown-toolbar-title">{{__('Notifications')}} (<span class="notif-count">{{$countUnread}}</span>)</h3>
        </div>
        <ul class="dropdown-list-items p-0">
            @if(count($notifications) > 0)
                @foreach($notifications as $oneNotification)
                    @php
                        $active = $class = '';
                        $data = json_decode($oneNotification['data']);

                        $idNotification = @$data->id;
                        $title = @$data->notification->message;
                        $name = @$data->notification->name;
                        $avatar = @$data->notification->avatar;
                        $link = @$data->notification->link;

                        if(empty($oneNotification->read_at)){
                            $class = 'markAsRead';
                            $active = 'active';
                        }
                    @endphp
                    <li class="notification {{$active}}">
                        <a class="{{$class}} p-0" data-id="{{$idNotification}}" href="{{$link}}">
                            <div class="media">
                                <div class="media-left">
                                    <div class="media-object">
                                        @if($avatar)
                                            <img loading='lazy'class="image-responsive" src="{{$avatar}}" alt="{{$name}}">
                                        @else
                                            <span class="avatar-text">{{ucfirst($name[0])}}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="media-body">
                                    {!! $title !!}
                                    <div class="notification-meta">
                                        <small class="timestamp">{{format_interval($oneNotification->created_at)}}</small>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </li>
                @endforeach
            @else
                <li class="text-center py-3 text-muted">
                    <small>{{__('No notifications yet')}}</small>
                </li>
            @endif
        </ul>
        <div class="dropdown-footer text-center">
            <a href="{{route('core.notification.loadNotify')}}" class="text-decoration-none text-white">{{__('View More')}}</a>
        </div>
    </ul>
</div>



<style>
.notify-items {
    max-height: 800px;
    overflow-y: auto;
    width: 400px; 
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    font-size: 14px;
    position: absolute;
    right: 0; 
    top: 50px; 
    z-index: 9999; 
    margin-top: 10px;
}

.parent-container {
    position: relative; 
}

@media (max-width: 768px) {
    .notify-items {
        width: 100vw; 
        max-width: 100vw; 
        right: 0; 
        left: auto; 
        top: 60px; 
        margin-top: 0; 
    }
}

@media (min-width: 768px) {
    .notify-items {
        top: 50px; 
    }
}

@media (max-width: 400px) {
    .notify-items {
        width: 100%; 
        left: 0; 
    }
}

.notify-items .dropdown-toolbar {
    padding: 12px;
    border-bottom: 1px solid #ddd;
    text-align: center;
}

.notify-items .dropdown-toolbar-title {
    font-size: 1.1rem;
    font-weight: bold;
    color: #ddd;
}

.notify-items .dropdown-list-items {
    list-style: none;
    padding: 0;
    margin: 0;
}

.notify-items .notification {
    display: flex;
    align-items: center;
    padding: 12px;
    border-bottom: 1px solid #f1f1f1;
    transition: background-color 0.3s ease;
}

.notify-items .notification:hover {
    background-color: #f9f9f9;
}

.notify-items .notification.active {
    background-color: #e9f7fe;
}

.notify-items .notification .media-left {
    margin-right: 10px;
}

.notify-items .notification .media-object {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    overflow: hidden;
}

.notify-items .notification .media-object img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.notify-items .notification .media-body {
    color: #050505;

    flex-grow: 1;
}

.notify-items .notification .notification-meta {
    font-size: 0.8rem;
    color: #888;
}

.notify-items .dropdown-footer {
    padding: 12px;
    text-align: center;
    background-color: #007bff;
    border-top: 1px solid #ddd;
}

.notify-items .dropdown-footer a {
    color: white;
    font-size: 0.9rem;
    text-decoration: none;
}

.notify-items .dropdown-footer a:hover {
    text-decoration: underline;
}

</style>

<li class="dropdown-notifications dropdown p-0 desktop-only">
    <a href="#" data-toggle="dropdown" class="is_login">
        <i class="fa fa-bell mr-2"></i>
        <span class="badge badge-danger notification-icon">{{$countUnread}}</span>
        <i class="fa fa-angle-down"></i>
    </a>
    <ul class="dropdown-menu overflow-auto notify-items dropdown-container dropdown-menu-right dropdown-large">
        <div class="dropdown-toolbar">
            <div class="dropdown-toolbar-actions">
                <a href="#" class="markAllAsRead">{{__('Mark all as read')}}</a>
            </div>
            <h3 class="dropdown-toolbar-title">{{__('Notifications')}} (<span class="notif-count">{{$countUnread}}</span>)</h3>
        </div>
        <ul class="dropdown-list-items p-0">
            @if(count($notifications)> 0)
                @foreach($notifications as $oneNotification)
                    @php
                        $active = $class = '';
                        $data = json_decode($oneNotification['data']);

                        $idNotification = @$data->id;
                        $forAdmin = @$data->for_admin;
                        $usingData = @$data->notification;

                        $services = @$usingData->type;
                        $idServices = @$usingData->id;
                        $title = @$usingData->message;
                        $name = @$usingData->name;
                        $avatar = @$usingData->avatar;
                        $link = @$usingData->link;

                        if(empty($oneNotification->read_at)){
                            $class = 'markAsRead';
                            $active = 'active';
                        }
                    @endphp
                    <li class="notification {{$active}}">
                        <a class="{{$class}} p-0" data-id="{{$idNotification}}" href="{{$link}}">
                            <div class="media">
                                <div class="media-left">
                                    <div class="media-object">
                                        @if($avatar)
                                            <img loading='lazy'class="image-responsive" src="{{$avatar}}" alt="{{$name}}">
                                        @else
                                            <span class="avatar-text">{{ucfirst($name[0])}}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="media-body">
                                    {!! $title !!}
                                    <div class="notification-meta">
                                        <small class="timestamp">{{format_interval($oneNotification->created_at)}}</small>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </li>
                @endforeach
            @endif
        </ul>
        <div class="dropdown-footer text-center">
            <a style="color: whitesmoke" href="{{route('core.notification.loadNotify')}}">{{__('View More')}}</a>
        </div>
    </ul>
</li>


<style>
    @media (max-width: 768px) {
    .desktop-only {
        display: none !important;
    }
}
</style>
