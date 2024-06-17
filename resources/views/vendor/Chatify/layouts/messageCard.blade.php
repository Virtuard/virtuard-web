@php
    $viewType = $isSender ? 'sender' : 'default';
    $fullTime = $created_at;
    $time = $timeAgo;

    $seenIcon = (!!$seen ? 'check-double' : 'check');
    $timeAndSeen = "<span data-time='$created_at' class='message-time'>
            ".($isSender ? "<span class='fas fa-$seenIcon' seen'></span>" : '' )." <span class='time'>$timeAgo</span>
        </span>";
@endphp
{{-- -------------------- The default card (white) -------------------- --}}
@if($viewType == 'default')
    @if($from_id != $to_id)
    <div class="message-card" data-id="{{ $id }}">
        <p>{!! ($message == null && $attachment != null && @$attachment->type != 'file') ? $attachment->title : nl2br($message) !!}
            <sub title="{{ $fullTime }}">{{ $time }}</sub>
            {{-- If attachment is a file --}}
            @if(@$attachment->type == 'file')
            <a href="{{ route(config('chatify.attachments.download_route_name'),['fileName'=>$attachment->file]) }}" style="color: #595959;" class="file-download">
                <span class="fas fa-file"></span> {{$attachment->title}}</a>
            @endif
        </p>
    </div>
    {{-- If attachment is an image --}}
    @if(@$attachment->type == 'image')
    <div>
        <div class="message-card">
            <div class="image-file chat-image" style="width: 250px; height: 150px;background-image: url('{{ asset('storage/'.config('chatify.attachments.folder').'/'.$attachment->file) }}')">
            </div>
        </div>
    </div>
    @endif
    @endif
@endif

{{-- -------------------- Sender card (owner) -------------------- --}}
@if($viewType == 'sender')
    <div class="message-card mc-sender" data-id="{{ $id }}">
        <p>{!! ($message == null && $attachment != null && @$attachment->type != 'file') ? $attachment->title : nl2br($message) !!}
            <sub title="{{ $fullTime }}" class="message-time">
                <span class="fas fa-{{ $seen > 0 ? 'check-double' : 'check' }} seen"></span> {{ $time }}</sub>
                {{-- If attachment is a file --}}
            @if(@$attachment->type == 'file')
            <a href="{{ route(config('chatify.attachments.download_route_name'),['fileName'=>$attachment->file]) }}" class="file-download">
                <span class="fas fa-file"></span> {{$attachment->title}}</a>
            @endif
        </p>
    </div>
    {{-- If attachment is an image --}}
    @if(@$attachment->type == 'image')
    <div>
        <div class="message-card mc-sender">
            <div class="image-file chat-image" style="width: 250px; height: 150px;background-image: url('{{ asset('storage/'.config('chatify.attachments.folder').'/'.$attachment->file) }}')">
            </div>
        </div>
    </div>
    @endif
@endif
