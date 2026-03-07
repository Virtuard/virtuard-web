<title>{{ config('chatify.name') }}</title>

{{-- Meta tags --}}
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="id" content="{{ $id }}">
<meta name="type" content="{{ $type }}">
<meta name="messenger-color" content="{{ $messengerColor }}">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="url" content="{{ url('').'/'.config('chatify.routes.prefix') }}" data-user="{{ Auth::user()->id }}">

<!-- Fonts -->
<link rel="dns-prefetch" href="//fonts.gstatic.com">
<link rel='stylesheet' id='google-font-css-css'  href='https://fonts.googleapis.com/css?family=Poppins%3A400%2C500%2C600' type='text/css' media='all' />

{{-- scripts --}}
<script
  src="https://code.jquery.com/jquery-3.6.3.min.js"></script>
<script src="{{ asset('js/chatify/font.awesome.min.js') }}"></script>
<script src="{{ asset('js/chatify/autosize.js') }}"></script>
<script src='https://unpkg.com/nprogress@0.2.0/nprogress.js'></script>

{{-- styles --}}
<link rel='stylesheet' href='https://unpkg.com/nprogress@0.2.0/nprogress.css'/>
<link href="{{ asset('css/chatify/style.css') }}" rel="stylesheet" />
<link href="{{ asset('css/chatify/'.$dark_mode.'.mode.css') }}" rel="stylesheet" />
<style>
    .d-none {
        display: none;
    }
    .message-card .message-card-content {
        display: flex;
        flex-direction: column;
        gap: 4px;
        max-width: 60%;
    }

    .message-card .message {
        background: #fff;
        color: #656b75;
        box-shadow: 0px 6px 11px rgba(18, 67, 105, 0.03);
        margin: 0;
        padding: 6px 15px;
        padding-bottom: 6px;
        padding-bottom: 5px;
        width: fit-content;
        width: -webkit-fit-content;
        border-radius: 20px;
        word-break: break-word;
        display: table-cell;
    }
    .message-card .message-time {
        display: inline-block;
        font-size: 11px;
    }

    .message-card.mc-sender .message {
        direction: ltr;
        color: #fff !important;
        background: #2180f3 !important;
    }

    .message-card .image-wrapper .image-file > div {
        display: none;
        position: absolute;
        bottom: 0;
        right: 0;
        left: 0;
        background: linear-gradient( 0deg, rgba(0, 0, 0, 1) 0%, rgba(0, 0, 0, 0.5) 100% );
        padding: 0.5rem;
        font-size: 11px;
        color: #fff;
    }
    .message-card .image-file, .attachment-preview .image-file {
        cursor: pointer;
        width: 140px;
        height: 70px;
        border-radius: 6px;
        width: 260px;
        height: 170px;
        overflow: hidden;
        background-color: #f7f7f7;
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center center;
    }
    .message-card .actions .delete-btn {
    cursor: pointer;
    color: #333333;
    }
</style>

{{-- Messenger Color Style--}}
@include('Chatify::layouts.messengerColor')
