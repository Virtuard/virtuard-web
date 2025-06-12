@extends ('layouts.app')
@section ('content')
        <div class="container">
            <div class="row p-3">
                <div class="col-md-12 text-center">
                    @if (session('status'))
                        <div class="alert alert-{{session('status')}}" role="alert">
                            {{ session('message') }}
                        </div>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <h2>{{ $pageTitle }}</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12" style="background: #f5f5f5;">
                    <div class="member-box">
                        <div class="label-member">
                            <a class="m-0" href="{{ route('member.index')}}">
                                <i class="fa fa-globe mr-2"></i>
                                All Members 
                                <span class="badge badge-primary">{{ $memberCount }}</span>
                            </a>
                        </div>
                        @auth
                        <div class="label-member">
                            <a class="m-0" href="{{ route('member.index', ['type' => 'follower'])}}">
                                <i class="fa fa-users mr-2"></i>
                                Followers
                                <span class="badge badge-primary">{{ $followerCount }}</span>
                            </a>
                        </div>
                        <div class="label-member">
                            <a class="m-0" href="{{ route('member.index', ['type' => 'following'])}}">
                                <i class="fa fa-user-plus mr-2"></i>
                                Following 
                                <span class="badge badge-primary">{{ $followingCount }}</span>
                            </a>
                        </div>
                        @endauth
                        <div class="label-member label-search">
                            <form action="{{ route('member.index') }}" class="form-search-member">
                                <input type="text" name="search" placeholder="search">
                                <button type="submit"><i class="fa fa-search"></i></button>
                            </form>
                        </div>
                    </div>  
                </div>  
                @foreach($users as $user)
                {{-- <div class="col-md-4" style="background: #f5f5f5; padding: 0 20px;">
                    <div class="w-100 mt-3" style="background: #FFF; border-radius: 8px; padding: 23px 35px;">
                        <div class="d-flex align-items-center">
                            <div class="image_box">
                                <img loading='lazy' class="mr-4" src="{{ $user->getAvatarUrl() }}" alt="img" style="width: 60px; height: 60px; object-fit: cover; border-radius: 100px;">
                            </div>
                        
                            <div>
                                <a class="m-0" href="{{ route('user.profile', $user->user_name ?? $user->id) }}">
                                    <b>{{ $user->name }}</b>
                                </a>
                                <div class="mb-1"><span class="role-name  badge badge-primary">{{ $user->role_name }}</span></div>
                            </div>
                        </div>

                        <hr>

                        <p style="color: #a3a3a3;">
                            {!! $user->bio !!}
                        </p>

                        <hr>
                        @auth
                                <form action="{{ route('member.store') }}" class="mb-4" method="POST">
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

                        <a href="@auth {{ route('user.chat', ['user_id' => $user->id]) }} @else javascript:void(0) @endauth" class="btn btn-primary w-100"
                            @guest
                            onclick="showModalLogin()"
                            @endguest
                            >
                            Message
                        </a>
                    </div>
                </div>   --}}
                <div class="col-md-4 d-flex justify-content-center">
                    <div class="w-100 mt-3" style="background: #FFF; border-radius: 12px; padding: 20px; box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="image_box">
                                    <img loading='lazy' src="{{ $user->getAvatarUrl() }}" alt="img" style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;">
                                </div>
                                <div class="ml-3">
                                    <a href="{{ route('user.profile', $user->user_name ?? $user->id) }}" style="text-decoration: none; color: #262626; font-weight: bold;">
                                        {{ $user->name }}
                                    </a>
                                    <div class="small text-muted">{{ $user->role_name }}</div>
                                </div>
                            </div>
                
                            <div class="d-flex">
                                <a href="@auth {{ route('user.chat', ['user_id' => $user->id]) }} @else javascript:void(0) @endauth" 
                                    style="border-radius: 8px; font-weight: bold;   margin-right: 10px; margin-top: 5px;"
                                    @guest onclick="showModalLogin()" @endguest>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-chat" viewBox="0 0 16 16">
                                        <path d="M2.678 11.894a1 1 0 0 1 .287.801 11 11 0 0 1-.398 2c1.395-.323 2.247-.697 2.634-.893a1 1 0 0 1 .71-.074A8 8 0 0 0 8 14c3.996 0 7-2.807 7-6s-3.004-6-7-6-7 2.808-7 6c0 1.468.617 2.83 1.678 3.894m-.493 3.905a22 22 0 0 1-.713.129c-.2.032-.352-.176-.273-.362a10 10 0 0 0 .244-.637l.003-.01c.248-.72.45-1.548.524-2.319C.743 11.37 0 9.76 0 8c0-3.866 3.582-7 8-7s8 3.134 8 7-3.582 7-8 7a9 9 0 0 1-2.347-.306c-.52.263-1.639.742-3.468 1.105"/>
                                      </svg>
                                </a>

                                @auth
                                    <form action="{{ route('member.store') }}" method="POST" class="mr-2">
                                        @csrf
                                        <input type="hidden" name="action" value="{{ !is_following($user->id) ? 'follow' : 'unfollow' }}">
                                        <input type="hidden" name="follower_id" value="{{ $user->id }}">
                                        <button class="btn btn-sm btn-{{ !is_following($user->id) ? 'primary' : 'secondary' }}" style="border-radius: 8px; font-weight: bold;">
                                            {{ !is_following($user->id) ? 'Follow' : 'Unfollow' }}
                                        </button>
                                    </form>
                                @else
                                    <button class="btn btn-sm btn-primary mr-2" onclick="showModalLogin()" style="border-radius: 8px; font-weight: bold;">Follow</button>
                                @endauth
                
                               
                            </div>
                        </div>
                        <p class="text-muted mt-2" style="font-size: 14px; margin-bottom: 8px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            {!! \Illuminate\Support\Str::words(strip_tags($user->bio), 100, '...') !!}
                        </p>
                    </div>
                </div>
                
                
                @endforeach
            </div>
            <div class="d-flex justify-content-center mt-5">
                {{ $users->onEachSide(0)->links() }}
            </div>
        </div>
@endsection
@push('css')
<style>
    body {
        background-color: #f5f5f5;
    }
    .member-box {
        display: flex;
        background: #FFF;
        border-radius: 8px;
        padding: 23px 35px;
        gap: 5px;
    }
    .label-member {
       background-color: #f5f5f5;
       padding: 10px;
       border-radius: 10px;
       display: flex;
       align-items: center;
    }
    * {
  box-sizing: border-box;
}

    /* Style the search field */
    .form-search-member input[type=text] {
        padding: 5px;
        font-size: 15px;
        border: 1px solid grey;
        float: left;
        width: 80%;
        background: #f1f1f1;
    }

    /* Style the submit button */
    .form-search-member button {
        float: left;
        width: 20%;
        padding: 5px;
        font-size: 15px;
        background: #2196F3;
        color: white;
        border: 1px solid grey;
        border-left: none; /* Prevent double borders */
        cursor: pointer;
    }

    .form-search-member button:hover {
        background: #0b7dda;
    }

    /* Clear floats */
    .form-search-member::after {
        content: "";
        clear: both;
        display: table;
    }

    @media(max-width: 768px) {
        .member-box {
            flex-direction: column;
        }
        .label-member {
            width: 100%;
        }

        .label-member form {
            width: 100%;
        }
    }
</style>
@endpush