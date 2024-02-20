<div class="tab-pane fade" id="{{ $key }}" role="tabpanel" aria-labelledby="{{ $key }}-tab">
    <div class="row">
        <div class="col-6">
            @if(in_array($key, get_listing_book()))
                @include('explore.partial.type-1')
            @else
                @include('explore.partial.type-2')
            @endif
        </div>
        <div class="col-6">
            <div class="card" style="height: 600px; overflow-y: auto;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <i class="fa fa-lg fa-arrow-left"></i>
                        <span>Showing {{ $listing->count() }} Result</span>
                        <i class="fa fa-lg fa-arrow-right"></i>
                    </div>
                    @foreach ($listing as $list)
                        <div class="col-12 mb-4">
                            <div class="card">
                                <a href="{{ url('hotel/' . $list->slug) }}">
                                    <div class="card card-custom card-has-bg click-col"
                                        style="background-image:url('{{ url('uploads/' . ($list->image->file_path ?? 'demo/hotel/gallery/hotel-gallery-1.jpg')) }}'); height: 250px;">
                                        <div class="card-img-overlay d-flex align-items-end">
                                            <img src="https://source.unsplash.com/600x900/?house" alt="rumah"
                                                style="width:50px; height:50px;border-radius:50%">
                                            <div class="ml-2">
                                                <h4 class="card-title mt-0 mb-0"><a class="text-white"
                                                        herf="{{ url('hotel/' . $list->slug) }}">{{ $list->title ?? 'Hotel' }}</a>
                                                </h4>
                                                <span class="text-white"> <i class="fa fa-map-marker"></i>
                                                    {{ $list->address ?? 'No address' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                <div class="card-footer">
                                    <div class="d-flex justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <img src="https://source.unsplash.com/600x900/?house" alt="rumah"
                                                style="width:25px; height:25px;border-radius:50%">
                                            <div class="ml-2">
                                                <p class="card-title mt-0 mb-0 text-dark">
                                                    {{ $list->user->name ?? 'Virtuard' }}</p>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center">
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
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
