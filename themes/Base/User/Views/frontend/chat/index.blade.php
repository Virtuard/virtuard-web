@extends('Layout::user')
@section('content')
@if(request('user_id'))
<div class="row">
    <div class="col-md-12 py-2">
        <a href="{{ route('user.chat') }}" class="btn btn-primary">
           <span class="fa fa-arrow-left"></span> Back
        </a>
    </div>
</div>
@endif
    <iframe id="chat-iframe" width="100%" style="height: calc(100vh - 30px)" src="{{route(config('chatify.path'),['user_id'=>request('user_id')])}}" frameborder="0"></iframe>
@endsection
