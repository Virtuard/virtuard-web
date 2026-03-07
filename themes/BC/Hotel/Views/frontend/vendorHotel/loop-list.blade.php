<div class="item-list">
    @if($row->discount_percent)
        <div class="sale_info">{{$row->discount_percent}}</div>
    @endif
    <div class="row">
        <div class="col-md-3">
            @if($row->is_featured == "1")
                <div class="featured">
                    {{__("Featured")}}
                </div>
            @endif
            <div class="thumb-image">
                <a href="{{$row->getDetailUrl()}}" target="_blank">
                    @if($row->image_url)
                        <img loading='lazy' src="{{$row->image_url}}" class="img-responsive" alt="">
                    @endif
                </a>
                <div class="service-wishlist {{$row->isWishList()}}" data-id="{{$row->id}}" data-type="{{$row->type}}">
                    <i class="fa fa-heart"></i>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="item-title">
                <a href="{{$row->getDetailUrl()}}" target="_blank">
                    {{$row->title}}
                </a>
            </div>
            <div class="location">
                @if(!empty($row->location->name))
                    <i class="icofont-paper-plane"></i>
                    {{__("Location")}}: <span>{{$row->location->name ?? ''}}</span>
                @endif
            </div>
            <div class="location">
                <i class="icofont-money"></i>
                {{__("Price")}}: <span class="sale-price">{{ $row->display_sale_price_admin }}</span> <span class="price">{{ $row->display_price_admin }}</span>
            </div>
            <div class="location">
                <i class="icofont-ui-settings"></i>
                {{__("Status")}}: <span class="badge badge-{{ $row->status }}">{{ $row->status_text }}</span>
            </div>
            <div class="location">
                <i class="fa fa-eye"></i>
                {{__("Views")}}: <span>{{ number_format($row->view_count ?? 0) }}</span>
            </div>
            <div class="location">
                <i class="icofont-wall-clock"></i>
                {{__("Last Updated")}}: <span>{{ display_datetime($row->updated_at ?? $row->created_at) }}</span>
            </div>
            <div class="control-action">
                @if(!empty($recovery))
                    <a href="{{ route("hotel.vendor.restore",[$row->id]) }}" class="btn btn-recovery btn-primary" data-confirm="{{__('"Do you want to recovery?"')}}">{{__("Recovery")}}</a>
                    @if(Auth::user()->hasPermission('hotel_delete'))
                        <a href="{{ route("hotel.vendor.delete",['id'=>$row->id,'permanently_delete'=>1]) }}" class="btn btn-danger" data-confirm="{{__('"Do you want to permanently delete?"')}}">{{__("Del")}}</a>
                    @endif
                @else
                    @include('user.partials.listing.btn-copy-link')
                    <a href="{{$row->getDetailUrl()}}" target="_blank" class="btn btn-info">{{__("View")}}</a>
                    @if(Auth::user()->hasPermission('hotel_update'))
                        <a href="{{ route("hotel.vendor.edit",[$row->id]) }}" class="btn btn-warning">{{__("Edit")}}</a>
                    @endif
                    @if(Auth::user()->hasPermission('hotel_delete'))
                        <a href="{{ route("hotel.vendor.delete",[$row->id]) }}" class="btn btn-danger" data-confirm="{{__('"Do you want to delete?"')}}">{{__("Del")}}</a>
                    @endif
                    @include('user.partials.listing.btn-status-group')
                @endif
            </div>
        </div>
    </div>
</div>
