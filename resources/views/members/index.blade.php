@extends ('layouts.app')
@section ('content')
        <div class="container" style="padding-top: 115px;padding-bottom: 40px; background: #f5f5f5;">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-12" style="background: #f5f5f5; padding: 0 20px;">
                        <div class="w-100 mt-3 d-flex" style="background: #FFF; border-radius: 8px; padding: 23px 35px;">
                            <div>
                                <p class="m-0">
                                    <i class="fa fa-globe mr-2"></i>
                                    All Members 
                                    <span class="badge badge-primary">{{ count($users) }}</span>
                                </p>
                            </div>
                            <div class="ml-3">
                                <p class="m-0">
                                    <i class="fa fa-signal mr-2"></i>
                                    Following 
                                    <span class="badge badge-primary">{{ $myFollowerCount }}</span>
                                </p>
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
                            
                            <?php
                            if($user->isFollow == 0) { ?>
                                <form action="{{ route('user.add.follow.member') }}" class="mb-4" method="POST">
                                @csrf
                                    <input type="hidden" name="param" value="Follow">
                                    <input type="hidden" name="id_follow" value="<?=$user->id?>">
                                    <button class="btn btn-primary w-100 mb-2">
                                        Follow
                                    </button>
                                </form>
                            <?php }else{ ?>
                                <form action="{{ route('user.add.follow.member') }}" class="mb-4" method="POST">
                                @csrf
                                    <input type="hidden" name="param" value="Unfollow">
                                    <input type="hidden" name="id_follow" value="<?=$user->id?>">
                                    <button class="btn btn-secondary w-100 mb-2">
                                        Unfollow
                                    </button>
                                </form>
                            <?php } ?>

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
        </div>
@endsection
