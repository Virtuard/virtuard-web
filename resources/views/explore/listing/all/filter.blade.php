<div class="card card-explore">
    <ul class="nav nav-tabs justify-content-start p-3" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="categories-tab" data-toggle="tab" data-target="#categories" type="button"
                role="tab" aria-controls="categories" aria-selected="true">Categories</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="typology-tab" data-toggle="tab" data-target="#typology" type="button"
                role="tab" aria-controls="typology" aria-selected="false">Typology</button>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="categories" role="tabpanel" aria-labelledby="categories-tab">
            <div class="col-12 mb-4">
                <div class="card card-custom card-has-bg click-col"
                    style="background-image:url('https://source.unsplash.com/600x900/?hotel');">
                    <div class="card-img-overlay d-flex flex-column justify-content-between">
                        <i class="fa fa-lg fa-shopping-bag text-white"></i>
                        <div>
                            <h4 class="card-title mt-0 mb-0"><a class="text-white"
                                    herf="https://creativemanner.com">Hotel</a>
                            </h4>
                            {{-- <small class="text-white">1.344 listings</small> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="typology" role="tabpanel" aria-labelledby="typology-tab">
            <div class="col-12 mb-4">
                <div class="card card-custom card-has-bg click-col"
                    style="background-image:url('https://source.unsplash.com/600x900/?tech');">
                    <div class="card-img-overlay d-flex flex-column justify-content-between">
                        <i class="fa fa-lg fa-shopping-bag text-white"></i>
                        <div>
                            <h4 class="card-title mt-0 mb-0"><a class="text-white"
                                    herf="https://creativemanner.com">Hotels</a></h4>
                            {{-- <small class="text-white">1.344 listings</small> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
