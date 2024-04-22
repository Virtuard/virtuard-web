@extends ('layouts.app')
@section ('content')
        <div class="container-fluid" style="background: #f5f5f5;">
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
                <div class="col-md-12" style="background: #f5f5f5; padding: 0 20px;">
                    <div class="w-100 mt-3 d-flex" style="background: #FFF; border-radius: 8px; padding: 23px 35px;">
                        <div>
                            <a class="m-0" href="{{ route('member.index')}}">
                                <i class="fa fa-globe mr-2"></i>
                                All Members 
                                <span class="badge badge-primary">{{ $memberCount }}</span>
                            </a>
                        </div>
                        @auth
                        <div class="ml-3">
                            <a class="m-0" href="{{ route('member.index', ['type' => 'follower'])}}">
                                <i class="fa fa-signal mr-2"></i>
                                Followers
                                <span class="badge badge-primary">{{ $followerCount }}</span>
                            </a>
                        </div>
                        <div class="ml-3">
                            <a class="m-0" href="{{ route('member.index', ['type' => 'following'])}}">
                                <i class="fa fa-signal mr-2"></i>
                                Following 
                                <span class="badge badge-primary">{{ $followingCount }}</span>
                            </a>
                        </div>
                        @endauth
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
                                <p class="m-0">
                                    <b>{{ $user->name }}</b>
                                </p>
                                <p class="m-0">
                                    {{ $user->business_name }}
                                </p>
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

                        <a href="/profile/<?=$user->id?>">
                            <button class="btn btn-primary w-100">
                                View Site
                            </button>
                        </a>
                    </div>
                </div>  
                @endforeach
            </div>
        </div>
@endsection
