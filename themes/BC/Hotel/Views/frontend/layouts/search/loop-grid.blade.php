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
</div>

<style>
    .item-tour {
     border: 1px solid #ddd;
     border-radius: 5px;
     overflow: hidden;
     margin-bottom: 20px;
     transition: box-shadow 0.3s ease-in-out;
     box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
 }
 
 .item-tour:hover {
     box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
 }
 
 .item-tour .thumb-image {
     position: relative;
 }
 
 .item-tour .thumb-image img {
     width: 100%;
     height: auto;
     object-fit: cover;
 }
 
 .item-tour .featured {
     position: absolute;
     top: 10px;
     left: 10px;
     background: #ff5a5f;
     color: white;
     padding: 5px 10px;
     font-size: 12px;
     border-radius: 3px;
 }
 
 .item-tour .sale_info {
     position: absolute;
     top: 10px;
     right: 10px;
     background: #ff5a5f;
     color: white;
     padding: 5px 10px;
     font-size: 12px;
     border-radius: 3px;
 }
 
 .item-tour .service-wishlist {
     position: absolute;
     bottom: 10px;
     right: 10px;
     background: white;
     color: #ff5a5f;
     padding: 5px;
     border-radius: 50%;
     box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
 }
 
 .item-tour .location {
     font-size: 14px;
     color: #555;
     margin: 10px 0;
 }
 
 .item-tour .item-title {
     font-size: 16px;
     font-weight: bold;
     margin: 5px 0;
     color: #333;
 }
 
 .item-tour .info {
     display: flex;
     justify-content: space-between;
     align-items: center;
     margin: 10px 0;
     font-size: 14px;
 }
 
 .item-tour .g-price {
     text-align: right;
 }
 
 .item-tour .g-price .onsale {
     font-size: 14px;
     color: #ff5a5f;
     text-decoration: line-through;
     margin-right: 5px;
 }
 
 .item-tour .g-price .text-price {
     font-size: 16px;
     font-weight: bold;
     color: #333;
 }
 
 @media screen and (max-width: 768px) {
     .item-tour {
         margin-bottom: 15px;
     }
 
     .item-tour .featured,
     .item-tour .sale_info,
     .item-tour .service-wishlist {
         font-size: 10px;
         padding: 3px 7px;
     }
 
     .item-tour .item-title {
         font-size: 14px;
     }
 
     .item-tour .info {
         flex-direction: column;
         align-items: flex-start;
     }
 
     .item-tour .g-price .onsale,
     .item-tour .g-price .text-price {
         font-size: 14px;
     }
 }
 
 @media screen and (max-width: 480px) {
     .item-tour {
         padding: 10px;
     }
 
     .item-tour .location {
         font-size: 12px;
     }
 
     .item-tour .item-title {
         font-size: 14px;
     }
 
     .item-tour .info {
         font-size: 12px;
     }
 
     .item-tour .g-price .onsale,
     .item-tour .g-price .text-price {
         font-size: 12px;
     }
 }
 </style>