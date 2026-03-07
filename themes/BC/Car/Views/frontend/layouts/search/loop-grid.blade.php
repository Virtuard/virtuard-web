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
                    <img loading='lazy' src="{{$row->image_url}}" class="img-responsive" alt="">
                @else
                    {!! get_image_tag($row->image_id,'medium',['class'=>'img-responsive','alt'=>$row->title]) !!}
                @endif
            @endif
        </a>
        <div class="service-wishlist {{$row->isWishList()}}" data-id="{{$row->id}}" data-type="{{$row->type}}">
            <i class="fa fa-heart-o"></i>
        </div>
    </div>
    <div class="item-title">
        <a @if(!empty($blank)) target="_blank" @endif href="{{$row->getDetailUrl($include_param ?? true)}}">
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
    <div class="amenities">
        @if($row->passenger)
            <span class="amenity total" data-toggle="tooltip"  title="{{ __("Passenger") }}">
                <i class="input-icon field-icon icon-passenger  "></i>
                <span class="text">
                    {{$row->passenger}}
                </span>
            </span>
        @endif
        @if($row->gear)
            <span class="amenity bed" data-toggle="tooltip" title="{{__("Gear Shift")}}">
                <i class="input-icon field-icon icon-gear"></i>
                <span class="text">
                    {{$row->gear}}
                </span>
            </span>
        @endif
        @if($row->baggage)
            <span class="amenity bath" data-toggle="tooltip" title="{{__("Baggage")}}" >
                <i class="input-icon field-icon icon-baggage"></i>
                <span class="text">
                    {{$row->baggage}}
                </span>
            </span>
        @endif
        @if($row->door)
            <span class="amenity size" data-toggle="tooltip" title="{{__("Door")}}" >
                <i class="input-icon field-icon icon-door"></i>
                <span class="text">
                    {{$row->door}}
                </span>
            </span>
        @endif
    </div>
    @if ($row->display_price > 0)
    <div class="info">
        <div class="g-price">
            <div class="prefix">
                <span class="fr_text">{{__("from")}}</span>
            </div>
            <div class="price">
                <span class="onsale">{{ $row->display_sale_price }}</span>
                <span class="text-price">{{ $row->display_price }} <span class="unit">{{__("/day")}}</span></span>
            </div>
        </div>
    </div>
    @endif
    @if(auth()->check() && auth()->id() === $row->author_id)
    <div class="service-actions" style="display: flex; gap: 10px; align-items: center; padding: 10px 10px;">
        <span class="badge" style="background-color: #007bff; color: white; padding: 5px 10px; border-radius: 5px;">
            <a href="{{ route("car.vendor.edit",[$row->id]) }}" class="edit-icon" style="color: white; text-decoration: none;">
                <i class="fa fa-edit"></i> Edit
            </a>
        </span>
        <span class="badge" style="background-color: #dc3545; color: white; padding: 5px 10px; border-radius: 5px;">
            <form action="" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <a href="{{ route("car.vendor.delete",[$row->id]) }}" class="edit-icon" style="color: white; text-decoration: none;">
                    <i class="fa fa-trash"></i> Delete
                </a>
            </form>
        </span>
    </div>
@endif
</div>
