@extends('Layout::user')
@section('content')
<div class="d-flex mb-2">
        <a href="{{ route('vendor.dashboard') }}" class="btn btn-primary btn-sm">
            <span class="fa fa-home"></span> Dashboard
        </a>
    @if(request('user_id'))
        <a href="{{ route('user.chat') }}" class="btn btn-primary btn-sm ml-2">
           <span class="fa fa-comments"></span> All Chat
        </a>
    @endif
</div>
    <iframe id="chat-iframe" width="100%" style="height: calc(100vh - 30px)" src="{{route(config('chatify.path'),['user_id'=>request('user_id')])}}" frameborder="0"></iframe>
@endsection
