

@php
    $translation = $row->translate();
@endphp

{{-- <div class="item-loop {{$wrap_class ?? ''}} mobile-only">
    @if($row->is_featured == "1")
        <div class="featured">
            {{__("Featured")}}
        </div>
    @endif
    <div class="thumb-image">
        @if($row->discount_percent)
            <div class="sale_info">{{$row->discount_percent}}</div>
        @endif
        <a @if(!empty($blank)) target="_blank" @endif href="{{$row->getDetailUrl($include_param ?? true)}}">
            @if($row->image_url)
                @if(!empty($disable_lazyload))
                    <img src="{{$row->image_url}}" class="img-responsive" alt="{{$location->name ?? ''}}">
                @else
                    {!! get_image_tag($row->image_id,'medium',['class'=>'img-responsive','alt'=>$row->title]) !!}
                @endif
            @endif
        </a>
        <div class="service-wishlist {{$row->isWishList()}}" data-id="{{$row->id}}" data-type="{{$row->type}}">
            <i class="fa fa-heart"></i>
        </div>
        @if(auth()->check() && auth()->id() === $row->author_id)
        <div class="service-actions">
            <a href="" class="edit-icon">
                <i class="fa fa-edit"></i>
            </a>
            <form action="" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="delete-icon" onclick="return confirm('{{ __('Are you sure you want to delete this item?') }}')">
                    <i class="fa fa-trash"></i>
                </button>
            </form>
        </div>
    @endif
      
    </div> --}}
    {{-- <div class="location">
        @if(!empty($row->location->name))
            @php $location =  $row->location->translate() @endphp
            <i class="icofont-paper-plane"></i>
            {{$location->name ?? ''}}
        @endif
    </div>
    <div class="item-title">
        <a @if(!empty($blank)) target="_blank" @endif href="{{$row->getDetailUrl($include_param ?? true)}}">
            {{$translation->title}}
        </a>
    </div>
</div> --}}



<div class="item-loop {{$wrap_class ?? ''}}">
    @if($row->is_featured == "1")
        <div class="featured">
            {{__("Featured")}}
        </div>
    @endif
    <div class="thumb-image ">
        <a @if(!empty($blank)) target="_blank" @endif href="{{$row->getDetailUrl()}}">
            @if($row->image_url)
                @if(!empty($disable_lazyload))
                    <img src="{{$row->image_url}}" class="img-responsive" alt="">
                @else
                    {!! get_image_tag($row->image_id,'medium',['class'=>'img-responsive','alt'=>$translation->title]) !!}
                @endif
            @endif
        </a>
        @if($row->star_rate)
            <div class="star-rate">
                <div class="list-star">
                    <ul class="booking-item-rating-stars">
                        @for ($star = 1 ;$star <= $row->star_rate ; $star++)
                            <li><i class="fa fa-star"></i></li>
                        @endfor
                    </ul>
                </div>
            </div>
        @endif
        <div class="service-wishlist {{$row->isWishList()}}" data-id="{{$row->id}}" data-type="{{$row->type}}">
            <i class="fa fa-heart"></i>
        </div>
        

    </div>
    <div class="item-title">
        <a @if(!empty($blank)) target="_blank" @endif href="{{$row->getDetailUrl()}}">
            @if($row->is_instant)
                <i class="fa fa-bolt d-none"></i>
            @endif
                {{$translation->title}}
        </a>
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
    @if(setting_item('hotel_enable_review'))
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
    @if ($row->display_price > 0)
    <div class="info">
        <div class="g-price">
            <div class="prefix">
                <span class="fr_text">{{__("from")}}</span>
            </div>
            <div class="price">
                <span class="text-price">{{ $row->display_price }} <span class="unit">{{__("/night")}}</span></span>
            </div>
        </div>
    </div>
    @endif
    @if(auth()->check() && auth()->id() === $row->author_id)
    <div class="service-actions" style="display: flex; gap: 10px; align-items: center; padding: 10px 10px;">
        <span class="badge" style="background-color: #007bff; color: white; padding: 5px 10px; border-radius: 5px;">
            <a href="{{ route("hotel.vendor.edit",[$row->id]) }}" class="edit-icon" style="color: white; text-decoration: none;">
                <i class="fa fa-edit"></i> Edit
            </a>
        </span>
        <span class="badge" style="background-color: #dc3545; color: white; padding: 5px 10px; border-radius: 5px;">
            <form action="" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <a href="{{ route("hotel.vendor.delete",[$row->id]) }}" class="edit-icon" style="color: white; text-decoration: none;">
                    <i class="fa fa-trash"></i> Delete
                </a>
            </form>
        </span>
    </div>
@endif

</div>
