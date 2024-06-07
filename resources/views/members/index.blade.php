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
                <div class="col-md-4" style="background: #f5f5f5; padding: 0 20px;">
                    <div class="w-100 mt-3" style="background: #FFF; border-radius: 8px; padding: 23px 35px;">
                        <div class="d-flex align-items-center">
                            <div class="image_box">
                                <img class="mr-4" src="{{ $user->getAvatarUrl() }}" alt="img" style="width: 60px; height: 60px; object-fit: cover; border-radius: 100px;">
                            </div>
                        
                            <div>
                                <a class="m-0" href="{{ route('user.profile', $user->id) }}">
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
                            @if(!is_following($user->id))
                                <form action="{{ route('member.store') }}" class="mb-4" method="POST">
                                @csrf
                                    <input type="hidden" name="param" value="Follow">
                                    <input type="hidden" name="id_follow" value="<?=$user->id?>">
                                    <button class="btn btn-primary w-100 mb-2">
                                        Follow
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('member.store') }}" class="mb-4" method="POST">
                                @csrf
                                    <input type="hidden" name="param" value="Unfollow">
                                    <input type="hidden" name="id_follow" value="<?=$user->id?>">
                                    <button class="btn btn-secondary w-100 mb-2">
                                        Unfollow
                                    </button>
                                </form>
                            @endif
                        @else
                            <button class="btn btn-primary w-100 mb-2" onclick="showModalLogin()">
                                Follow
                            </button>
                        @endauth

                        {{-- <a href="{{ route('user.profile', $user->id) }}">
                            <button class="btn btn-primary w-100">
                                View Profile
                            </button>
                        </a> --}}
                    </div>
                </div>  
                @endforeach
            </div>
            <div class="d-flex justify-content-center mt-5">
                {{ $users->links() }}
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