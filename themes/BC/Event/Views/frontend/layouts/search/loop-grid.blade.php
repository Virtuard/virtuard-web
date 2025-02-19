{{-- <style>
    .desktop-only {
    display: block;
}
.mobile-only {
    display: none;
}

@media (max-width: 768px) {
    .desktop-only {
        display: none;
    }
    .mobile-only {
        display: block;
    }
}
</style> --}}

@php
    $translation = $row->translate();
@endphp




<div class="item-loop {{$wrap_class ?? ''}}">
    @if($row->is_featured == "1")
        <div class="featured">
            {{__("Featured")}}
        </div>
    @endif
    <div class="thumb-image ">
        <a @if(!empty($blank)) target="_blank" @endif href="{{$row->getDetailUrl($include_param ?? true)}}">
            @if($row->image_url)
                @if(!empty($disable_lazyload))
                    <img loading='lazy'src="{{$row->image_url}}" class="img-responsive" alt="">
                @else
                    {!! get_image_tag($row->image_id,'medium',['class'=>'img-responsive','alt'=>$row->title]) !!}
                @endif
            @endif
        </a>
        <div class="service-wishlist {{$row->isWishList()}}" data-id="{{$row->id}}" data-type="{{$row->type}}">
            <i class="fa fa-heart-o"></i>
        </div>
        @if($row->discount_percent)
            <div class="sale_info">{{$row->discount_percent}}</div>
        @endif
    </div>
    <div class="location">
        @if(!empty($row->location->name))
            @php $location =  $row->location->translate() @endphp
            {{$location->name ?? ''}}
        @endif
    </div>
    <div class="item-title">
        <a @if(!empty($blank)) target="_blank" @endif href="{{$row->getDetailUrl($include_param ?? true)}}">
            @if($row->is_instant)
                <i class="fa fa-bolt d-none"></i>
            @endif
                {{$translation->title}}
        </a>
    </div>
    @if(setting_item('space_enable_review'))
    <?php
    $reviewData = $row->getScoreReview();
    $score_total = $reviewData['score_total'];
    ?>
        <div class="service-review">
            <span class="rate">
                @if($reviewData['total_review'] > 0) {{$score_total}}/5 @endif <span class="rate-text">{{$reviewData['review_text']}}</span>
            </span>
            <span class="review">
             @if($reviewData['total_review'] > 1)
                    {{ __(":number Reviews",["number"=>$reviewData['total_review'] ]) }}
                @else
                    {{ __(":number Review",["number"=>$reviewData['total_review'] ]) }}
                @endif
            </span>
        </div>
    @endif
    @if(!empty($time = $row->start_time))
        <div class="start-time">
            {{ __("Start Time: :time",['time'=>$time]) }}
        </div>
    @endif
    <div class="info">
        <div class="duration">
            {{duration_format($row->duration)}}
        </div>
        <div class="g-price">
            <div class="prefix">
                <span class="fr_text">{{__("from")}}</span>
            </div>
            <div class="price">
                <span class="onsale">{{ $row->display_sale_price }}</span>
                <span class="text-price">{{ $row->display_price }}</span>
            </div>
        </div>
    </div>
    @if(auth()->check() && auth()->id() === $row->author_id)
    <div class="service-actions" style="display: flex; gap: 10px; align-items: center; padding: 10px 10px;">
        <span class="badge" style="background-color: #007bff; color: white; padding: 5px 10px; border-radius: 5px;">
            <a href="{{ route("event.vendor.edit",[$row->id]) }}" class="edit-icon" style="color: white; text-decoration: none;">
                <i class="fa fa-edit"></i> Edit
            </a>
        </span>
        <span class="badge" style="background-color: #dc3545; color: white; padding: 5px 10px; border-radius: 5px;">
            <form action="" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <a href="{{ route("event.vendor.delete",[$row->id]) }}" class="edit-icon" style="color: white; text-decoration: none;">
                    <i class="fa fa-trash"></i> Delete
                </a>
            </form>
        </span>
    </div>
@endif
</div>
