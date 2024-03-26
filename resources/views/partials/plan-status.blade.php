<div class="border rounded text-center p-4">
    @if (isset($user_plan))
        @if ($user_plan->status === 0)
            <div>
                <span class="icon text-center" style="font-size: 5rem;"><i class="fa fa-lock"></i></span>
                <h1>Locked feature</h1>
                <div class="alert alert-warning" role="alert">
                    Your service is <b>PENDING</b>, please activate the service by making a payment.
                    <a href="{{ route('user.plan') }}">Click here</a> to check our plan.
                </div>
            </div>
        @elseif ($user_plan->status === 1 && !$user_plan->is_valid)
            <div>
                <span class="icon text-center" style="font-size: 5rem;"><i class="fa fa-lock"></i></span>
                <h1>Locked feature</h1>
                <div class="alert alert-danger" role="alert">
                    Your service is <b>EXPIRED</b> at {{ $user_plan->end_date }}, please 
                    <a href="{{ route('user.plan') }}">Click here</a> to subscribe our plan.

                </div>
                <a href="{{ route('user.plan') }}" class="btn btn-primary">Subscribe</a>
            </div>
        @elseif ($user_plan->status === 1 && $user_plan->is_valid)
            <div>
                <span class="icon text-center" style="font-size: 5rem;"><i class="fa fa-unlock"></i></span>
                <h1>Unlocked feature</h1>
                <div class="alert alert-success" role="alert">
                    Your service is <b>ACTIVE</b> until {{ $user_plan->end_date }}
                </div>
            </div>
        @else
            <div>
                <span class="icon text-center" style="font-size: 5rem;"><i class="fa fa-lock"></i></span>
                <h1>Locked feature</h1>
                <p>Please activate the service by making a payment</p>
                <div class="alert alert-danger" role="alert">
                    Your service is not active yet, please
                    <a href="{{ route('user.plan') }}">Click here</a> to subscribe our plan.
                </div>
                <a href="{{ route('user.plan') }}" class="btn btn-primary">Subscribe</a>
            </div>
        @endif
    @else
        <div>
            <span class="icon text-center" style="font-size: 5rem;"><i class="fa fa-lock"></i></span>
            <h1>Locked feature</h1>
            <p>Please activate the service by making a payment</p>
            <div class="alert alert-danger" role="alert">
                Your service is not active yet, please
                <a href="{{ route('user.plan') }}">Click here </a> to subscribe our plan.
            </div>
            <a href="{{ route('user.plan') }}" class="btn btn-primary">Subscribe</a>
        </div>
    @endif

    <!-- Modal -->
    <div class="modal fade" id="modalSubscribe" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Activate the service</h5>
                    <button type="button" class="close" user_plan-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('user.virtuard-360.submission-service') }}" method="POST" enctype="multipart/form-user_plan">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="exampleFormControlFile1">Proof of payment</label>
                            <input type="file" name="proof" class="form-control-file" id="exampleFormControlFile1">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" user_plan-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Subscribe</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
