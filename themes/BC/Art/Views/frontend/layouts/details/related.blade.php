@if(count($art_related) > 0)
    <div class="bravo-list-space-related">
        <h2>{{__("You might also like")}}</h2>
        <div class="row">
            @foreach($art_related as $k=>$item)
                <div class="col-md-3">
                    @include('Event::frontend.layouts.search.loop-grid',['row'=>$item,'include_param'=>0])
                </div>
            @endforeach
        </div>
    </div>
@endif
