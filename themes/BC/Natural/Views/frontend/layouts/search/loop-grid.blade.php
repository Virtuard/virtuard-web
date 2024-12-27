@php
    $translation = $row->translate();
@endphp
<div class="item-tour {{ $wrap_class ?? '' }}">
    @if ($row->is_featured == '1')
        <div class="featured">
            {{ __('Featured') }}
        </div>
    @endif
    <div class="thumb-image">
        @if ($row->discount_percent)
            <div class="sale_info">{{ $row->discount_percent }}</div>
        @endif
        <a @if (!empty($blank)) target="_blank" @endif
            href="{{ $row->getDetailUrl($include_param ?? true) }}">
            @if ($row->image_url)
                @if (!empty($disable_lazyload))
                    <img src="{{ $row->image_url }}" class="img-responsive" alt="{{ $location->name ?? '' }}">
                @else
                    {!! get_image_tag($row->image_id, 'medium', ['class' => 'img-responsive', 'alt' => $row->title]) !!}
                @endif
            @endif
        </a>
        <div class="service-wishlist {{ $row->isWishList() }}" data-id="{{ $row->id }}"
            data-type="{{ $row->type }}">
            <i class="fa fa-heart"></i>
        </div>
    </div>
    <div class="location">
        @if (!empty($row->location->name))
            @php $location =  $row->location->translate() @endphp
            <i class="icofont-paper-plane"></i>
            {{ $location->name ?? '' }}
        @endif
    </div>
    <div class="item-title">
        <a @if (!empty($blank)) target="_blank" @endif
            href="{{ $row->getDetailUrl($include_param ?? true) }}">
            {{ $translation->title }}
        </a>
    </div>
    @if (setting_item('natural_enable_review'))
        <?php
        $reviewData = $row->getScoreReview();
        $score_total = $reviewData['score_total'];
        ?>
        <div class="service-review tour-review-{{ $score_total }}">
            <div class="list-star">
                <ul class="booking-item-rating-stars">
                    <li><i class="fa fa-star-o"></i></li>
                    <li><i class="fa fa-star-o"></i></li>
                    <li><i class="fa fa-star-o"></i></li>
                    <li><i class="fa fa-star-o"></i></li>
                    <li><i class="fa fa-star-o"></i></li>
                </ul>
                <div class="booking-item-rating-stars-active" style="width: {{ $score_total * 2 * 10 ?? 0 }}%">
                    <ul class="booking-item-rating-stars">
                        <li><i class="fa fa-star"></i></li>
                        <li><i class="fa fa-star"></i></li>
                        <li><i class="fa fa-star"></i></li>
                        <li><i class="fa fa-star"></i></li>
                        <li><i class="fa fa-star"></i></li>
                    </ul>
                </div>
            </div>
            <span class="review">
                @if ($reviewData['total_review'] > 1)
                    {{ __(':number Reviews', ['number' => $reviewData['total_review']]) }}
                @else
                    {{ __(':number Review', ['number' => $reviewData['total_review']]) }}
                @endif
            </span>
        </div>
    @endif
    <div class="info">
        <div class="duration">
            <i class="icofont-wall-clock"></i>
            {{ duration_format($row->duration) }}
        </div>
        @if ($row->display_price > 0)
            <div class="g-price">
                <div class="prefix">
                    <i class="icofont-flash"></i>
                    <span class="fr_text">{{ __('from') }}</span>
                </div>
                <div class="price">
                    <span class="onsale">{{ $row->display_sale_price }}</span>
                    <span class="text-price">{{ $row->display_price }}</span>
                </div>
            </div>
        @endif
    </div>
</div>


<style>
    .item-tour {
        display: flex;
        flex-direction: column;
        gap: 10px;
        border: 1px solid #eaeaea;
        border-radius: 8px;
        padding: 16px;
        background-color: #fff;
        margin-bottom: 20px;
    }

    .thumb-image {
        width: 100%;
        aspect-ratio: 16/9;
        border-radius: 8px;
        position: relative;
        overflow: hidden;
    }

    .thumb-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .featured {
        position: absolute;
        top: 10px;
        left: 10px;
        background-color: #ff9800;
        color: #fff;
        padding: 4px 8px;
        font-size: 12px;
        border-radius: 4px;
    }

    .sale_info {
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: red;
        color: #fff;
        padding: 4px 8px;
        font-size: 12px;
        border-radius: 4px;
    }

    .item-title a {
        display: block;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        cursor: grab;
    }

    .item-title a:active {
        cursor: grabbing;
    }

    @media screen and (max-width: 768px) {
        .item-title {
            overflow-x: auto;
        }

        .item-title::-webkit-scrollbar {
            height: 4px;
        }

        .item-title::-webkit-scrollbar-thumb {
            background: #aaa;
            border-radius: 2px;
        }

        .item-title::-webkit-scrollbar-thumb:hover {
            background: #888;
        }
    }

    .service-review {
        font-size: 14px;
    }

    .g-price {
        display: flex;
        align-items: baseline;
        gap: 8px;
    }

    .g-price .onsale {
        font-size: 16px;
        text-decoration: line-through;
        color: red;
    }

    .g-price .text-price {
        font-size: 18px;
        font-weight: bold;
        color: green;
    }

    @media screen and (max-width: 768px) {
        .item-tour {
            padding: 12px;
            gap: 8px;
        }

        .thumb-image {
            aspect-ratio: 4/3;
        }

        .item-title a {
            font-size: 14px;
        }

        .service-review {
            font-size: 12px;
        }

        .g-price .onsale,
        .g-price .text-price {
            font-size: 16px;
        }
    }

    @media screen and (max-width: 480px) {
        .item-tour {
            padding: 8px;
            gap: 6px;
        }

        .thumb-image {
            aspect-ratio: 1;
        }

        .item-title a {
            font-size: 12px;
        }

        .service-review {
            font-size: 10px;
        }

        .g-price .onsale,
        .g-price .text-price {
            font-size: 14px;
        }
    }

    .location {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        cursor: grab;
    }

    .location:active {
        cursor: grabbing;
    }

    @media screen and (max-width: 768px) {
        .location {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .location::-webkit-scrollbar {
            height: 4px;
        }

        .location::-webkit-scrollbar-thumb {
            background: #aaa;
            border-radius: 2px;
        }

        .location::-webkit-scrollbar-thumb:hover {
            background: #888;
        }
    }
</style>
