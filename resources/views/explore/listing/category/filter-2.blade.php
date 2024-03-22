<div class="card card-explore">
    <ul class="nav nav-tabs justify-content-start" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="filters-arts-tab" data-toggle="tab" data-target="#filters-arts"
                type="button" role="tab" aria-controls="filters-arts" aria-selected="true">Filters</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="categories-arts-tab" data-toggle="tab" data-target="#categories-arts"
                type="button" role="tab" aria-controls="categories-arts" aria-selected="false">Categories</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="region-arts-tab" data-toggle="tab" data-target="#region-arts" type="button"
                role="tab" aria-controls="region-arts" aria-selected="false">Region</button>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="filter-arts" role="tabpanel" aria-labelledby="filter-arts-tab">
            <div class="card-body">
                <div class="form-group">
                    <label for="exampleFormControlSelect1">Sort by</label>
                    <select class="form-control" id="exampleFormControlSelect1">
                        <option>Last</option>
                        <option>Top Rated</option>
                        <option>Random</option>
                    </select>
                </div>
                <div class="form-group mt-3">
                    <input type="text" class='form-control' placeholder="Categories"
                        style="border-top: none;border-left:none;border-right:none;">
                </div>
                <div class="form-group mt-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <input type="text" aria-label='location' class='form-control autocomplete' id='place-arts'
                            name='place-arts' placeholder="Place"
                            style="border-top: none;border-left:none;border-right:none;">
                        <button class="btn btn-sm" id="get-location" type="button">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M11.5397 22.351C11.57 22.3685 11.5937 22.3821 11.6105 22.3915L11.6384 22.4071C11.8613 22.5294 12.1378 22.5285 12.3608 22.4075L12.3895 22.3915C12.4063 22.3821 12.43 22.3685 12.4603 22.351C12.5207 22.316 12.607 22.265 12.7155 22.1982C12.9325 22.0646 13.2388 21.8676 13.6046 21.6091C14.3351 21.0931 15.3097 20.3274 16.2865 19.3273C18.2307 17.3368 20.25 14.3462 20.25 10.5C20.25 5.94365 16.5563 2.25 12 2.25C7.44365 2.25 3.75 5.94365 3.75 10.5C3.75 14.3462 5.76932 17.3368 7.71346 19.3273C8.69025 20.3274 9.66491 21.0931 10.3954 21.6091C10.7612 21.8676 11.0675 22.0646 11.2845 22.1982C11.393 22.265 11.4793 22.316 11.5397 22.351ZM12 13.5C13.6569 13.5 15 12.1569 15 10.5C15 8.84315 13.6569 7.5 12 7.5C10.3431 7.5 9 8.84315 9 10.5C9 12.1569 10.3431 13.5 12 13.5Z"
                                    fill="#0F172A" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="form-group mt-3">
                    <label for="open">Open Now</label>
                    <div class="form-group">
                        <div class="btn-group w-100 btn-group-toggle" data-toggle="buttons">
                            <label class="btn btn-outline-secondary btn-toggle active">
                                <input type="radio" name="options" id="option1" autocomplete="off" checked> All
                            </label>
                            <label class="btn btn-outline-secondary btn-toggle">
                                <input type="radio" name="options" id="option2" autocomplete="off"> Open Now
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-group mt-3">
                    <div class="form-content">
                        <label class="mb-2 font-weight-bold">Range <span id="proximity_text">0</span> km</label>
                        <div class="input-search">
                            <input type="range" id="search_radius" name="search_radius" min="0" max="500"
                                class="w-100 cursor-pointer" value="0" />
                        </div>
                    </div>
                </div>
                <div class="form-group mt-3">
                    <input type="text" class='form-control' placeholder="Text Search"
                        style="border-top: none;border-left:none;border-right:none;">
                </div>
                <div class="form-group mt-3">
                    <input type="text" class='form-control' placeholder="General Search Box"
                        style="border-top: none;border-left:none;border-right:none;">
                </div>
                <button class="btn btn-secondary w-100 mt-3">
                    <i class="fa fa-search mr-2"></i>
                    Search
                </button>
                <button class="btn btn-outline-secondary w-100 mt-3">
                    <i class="fa fa-undo mr-2"></i>
                    Reset
                </button>
            </div>
        </div>
        <div class="tab-pane fade" id="categories-arts" role="tabpanel" aria-labelledby="categories-culturals-tab">
            <div class="col-12 mb-4">
                <div class="card card-custom card-has-bg click-col"
                    style="background-image:url('https://source.unsplash.com/600x900/?tech');">
                    <div class="card-img-overlay d-flex flex-column justify-content-between">
                        <i class="fa fa-lg fa-shopping-bag text-white"></i>
                        <div>
                            <h4 class="card-title mt-0 mb-0"><a class="text-white"
                                    herf="https://creativemanner.com">Hotels</a></h4>
                            <small class="text-white">1.344 listings</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 mb-4">
                <div class="card card-custom card-has-bg click-col"
                    style="background-image:url('https://source.unsplash.com/600x900/?tech');">
                    <div class="card-img-overlay d-flex flex-column justify-content-between">
                        <i class="fa fa-lg fa-shopping-bag text-white"></i>
                        <div>
                            <h4 class="card-title mt-0 mb-0"><a class="text-white"
                                    herf="https://creativemanner.com">Hotels</a></h4>
                            <small class="text-white">1.344 listings</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 mb-4">
                <div class="card card-custom card-has-bg click-col"
                    style="background-image:url('https://source.unsplash.com/600x900/?tech');">
                    <div class="card-img-overlay d-flex flex-column justify-content-between">
                        <i class="fa fa-lg fa-shopping-bag text-white"></i>
                        <div>
                            <h4 class="card-title mt-0 mb-0"><a class="text-white"
                                    herf="https://creativemanner.com">Hotels</a></h4>
                            <small class="text-white">1.344 listings</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 mb-4">
                <div class="card card-custom card-has-bg click-col"
                    style="background-image:url('https://source.unsplash.com/600x900/?tech');">
                    <div class="card-img-overlay d-flex flex-column justify-content-between">
                        <i class="fa fa-lg fa-shopping-bag text-white"></i>
                        <div>
                            <h4 class="card-title mt-0 mb-0"><a class="text-white"
                                    herf="https://creativemanner.com">Hotels</a></h4>
                            <small class="text-white">1.344 listings</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 mb-4">
                <div class="card card-custom card-has-bg click-col"
                    style="background-image:url('https://source.unsplash.com/600x900/?tech');">
                    <div class="card-img-overlay d-flex flex-column justify-content-between">
                        <i class="fa fa-lg fa-shopping-bag text-white"></i>
                        <div>
                            <h4 class="card-title mt-0 mb-0"><a class="text-white"
                                    herf="https://creativemanner.com">Hotels</a></h4>
                            <small class="text-white">1.344 listings</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 mb-4">
                <div class="card card-custom card-has-bg click-col"
                    style="background-image:url('https://source.unsplash.com/600x900/?tech');">
                    <div class="card-img-overlay d-flex flex-column justify-content-between">
                        <i class="fa fa-lg fa-shopping-bag text-white"></i>
                        <div>
                            <h4 class="card-title mt-0 mb-0"><a class="text-white"
                                    herf="https://creativemanner.com">Hotels</a></h4>
                            <small class="text-white">1.344 listings</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="region-arts" role="tabpanel" aria-labelledby="region-arts-tab">
        </div>
    </div>
</div>
