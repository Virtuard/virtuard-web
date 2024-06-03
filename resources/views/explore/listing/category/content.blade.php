<div class="tab-pane fade" id="{{ $key }}" role="tabpanel" aria-labelledby="{{ $key }}-tab">
    <div class="row">
        <div class="col-md-6 p-1">
            @if(in_array($key, get_listing_book()))
                @include('explore.listing.category.filter-1')
            @else
                @include('explore.listing.category.filter-2')
            @endif
        </div>
        <div class="col-md-6 p-1">
            <div class="card card-explore">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <i class="fa fa-lg fa-arrow-left"></i>
                        <span>Showing {{ $listing->count() }} Result</span>
                        <i class="fa fa-lg fa-arrow-right"></i>
                    </div>
                    @foreach ($listMaps as $list)
                        @if($list['category'] == $key)
                            <div class="col-12 mb-4">
                                <div class="card" style="overflow: hidden;">
                                    <div class="card card-custom card-has-bg click-col"
                                        style="background-image: url({{ $list['banner_image_id'] }}); height: 250px;">
                                        <div class="card-img-overlay d-flex align-items-end">
                                            <img src="{{ asset($list['image']) }}" alt=""
                                                style="width:50px; height:50px;border-radius:50%">
                                            <div class="ml-2">
                                                <h5 class="card-title mt-0 mb-0"
                                                    style="text-overflow: ellipsis; overflow:hidden; font-size: 16px;">
                                                    <a class="text-white" href="{{ $list['url'] }}">{{ $list['title'] }}</a>
                                                </h5>
                                                <span class="text-white"> <i class="fa fa-map-marker"></i>
                                                    {{ $list['address'] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="d-flex justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $list['author']['image'] }}" alt=""
                                                    style="width:25px; height:25px;border-radius:50%; font-size: 12px;"
                                                    >
                                                <div class="ml-2">
                                                    <p class="card-title mt-0 mb-0 text-dark">{{ $list['author']['name'] }}</p>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center" style="display: none !important;">
                                                <button class="btn btn-sm" type="button">
                                                    <i class="fa fa-search-plus"></i>
                                                </button>
                                                <button class="btn btn-sm" type="button">
                                                    <i class="fa fa-heart-o"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
