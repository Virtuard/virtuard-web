@extends ('layouts.app')
@section('content')
    <div class="" style="background: #f5f5f5; padding: 120px 60px;">
        <div class="row">
            <div class="card">
                <div class="p-4">
                    <i class="fa fa-globe" aria-hidden="true"></i>
                    <span>All</span>
                </div>
            </div>
        </div>
        <div class="row" style="margin-top: -5px;">
            <div class="col-12 card">
                <form action="">
                    <div class="row py-3">
                        <div class="col-3">
                            <div class="form-group mt-3">
                                <input type="text" aria-label='location' class='form-control autocomplete' id='location'
                                    name='location' placeholder="Location"
                                    style="border-top: none;border-left:none;border-right:none;">
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group mt-3">
                                <div class="form-content">
                                    <label class="mb-2 font-weight-bold">Proximity 10km</label>
                                    <div class="input-search">
                                        <input type="range" class="w-100" value="700000" step="100000" min="0"
                                            max="1000000" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group mt-3">
                                <input type="text" class="form-control" name="keyword" id="keyword research"
                                    placeholder="keyword research"
                                    style="border-top: none;border-left:none;border-right:none;">
                            </div>
                        </div>
                        <div class="col-3">
                            <button type="submit" class="btn btn-md btn-dark mt-3 w-100">
                                <i class="fa fa-search"></i>
                                Search
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="row mt-4">
            <div class="container col-12">
                <ul class="nav nav-tabs d-flex justify-content-between" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link text-capitalize disabled" id="#" data-toggle="tab" data-target="#"
                            type="button" role="tab" aria-controls="#" aria-selected="true">What are you looking for?
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link text-capitalize active" id="all-tab" data-toggle="tab" data-target="#all"
                            type="button" role="tab" aria-controls="all" aria-selected="true">
                            <i class="fa fa-sm mr-2 fa-globe"></i> All
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link text-capitalize" id="business-tab" data-toggle="tab" data-target="#business"
                            type="button" role="tab" aria-controls="business" aria-selected="false">
                            <i class="fa fa-sm mr-2 fa-shopping-bag"></i>
                            Business</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link text-capitalize" id="properties-tab" data-toggle="tab"
                            data-target="#properties" type="button" role="tab" aria-controls="properties"
                            aria-selected="false"><i class="fa fa- mr-2 fa-home"></i> properties</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link text-capitalize" id="accomodation-tab" data-toggle="tab"
                            data-target="#accomodation" type="button" role="tab" aria-controls="accomodation"
                            aria-selected="false"> <i class="fa fa- mr-2 fa-industry"></i> accomodation</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link text-capitalize" id="cultural-tab" data-toggle="tab"
                            data-target="#cultural" type="button" role="tab" aria-controls="cultural"
                            aria-selected="false"><i class="fa fa- mr-2 fa-leaf"></i> cultural heritage</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link text-capitalize" id="rendering-tab" data-toggle="tab"
                            data-target="#rendering" type="button" role="tab" aria-controls="rendering"
                            aria-selected="false"><i class="fa fa- mr-2 fa-laptop"></i> Rendering and Art</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link text-capitalize" id="vehicles-tab" data-toggle="tab"
                            data-target="#vehicle" type="button" role="tab" aria-controls="vehicle"
                            aria-selected="false"><i class="fa fa- mr-2 fa-car"></i> Vehicles (Boat and Car)</button>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
                        <div class="row">
                            <div class="col-3">
                                <div class="card" style="height: 600px; overflow-y: auto;">
                                    <ul class="nav nav-tabs justify-content-start p-3" id="myTab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="categories-tab" data-toggle="tab"
                                                data-target="#categories" type="button" role="tab"
                                                aria-controls="categories" aria-selected="true">Categories</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="typology-tab" data-toggle="tab"
                                                data-target="#typology" type="button" role="tab"
                                                aria-controls="typology" aria-selected="false">Typology</button>
                                        </li>
                                    </ul>
                                    <div class="tab-content" id="myTabContent">
                                        <div class="tab-pane fade show active" id="categories" role="tabpanel"
                                            aria-labelledby="categories-tab">
                                            @foreach ($businessCategories as $businessCategory)
                                                <div class="col-12 mb-4">
                                                    <div class="card card-custom card-has-bg click-col"
                                                        style="background-image:url('https://source.unsplash.com/600x900/?{{ $businessCategory->title }}');">
                                                        <div
                                                            class="card-img-overlay d-flex flex-column justify-content-between">
                                                            <i class="fa fa-lg fa-shopping-bag text-white"></i>
                                                            <div>
                                                                <h4 class="card-title mt-0 mb-0"><a class="text-white"
                                                                        herf="https://creativemanner.com">{{ $businessCategory->title }}</a>
                                                                </h4>
                                                                {{-- <small class="text-white">1.344 listings</small> --}}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="tab-pane fade" id="typology" role="tabpanel"
                                            aria-labelledby="typology-tab">
                                            <div class="col-12 mb-4">
                                                <div class="card card-custom card-has-bg click-col"
                                                    style="background-image:url('https://source.unsplash.com/600x900/?tech');">
                                                    <div
                                                        class="card-img-overlay d-flex flex-column justify-content-between">
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
                                                    <div
                                                        class="card-img-overlay d-flex flex-column justify-content-between">
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
                                                    <div
                                                        class="card-img-overlay d-flex flex-column justify-content-between">
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
                                                    <div
                                                        class="card-img-overlay d-flex flex-column justify-content-between">
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
                                                    <div
                                                        class="card-img-overlay d-flex flex-column justify-content-between">
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
                                                    <div
                                                        class="card-img-overlay d-flex flex-column justify-content-between">
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
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="card" style="height: 600px; overflow-y: auto;">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-4">
                                            <i class="fa fa-lg fa-arrow-left cursor-pointer"></i>
                                            <span>Showing {{ count($business) }} Result</span>
                                            <i class="fa fa-lg fa-arrow-right cursor-pointer"></i>
                                        </div>
                                        @foreach ($business as $businessItem)
                                            <div class="col-12 mb-4">
                                                <div class="card">
                                                    <div class="card card-custom card-has-bg click-col"
                                                        style="background-image: url('/uploads/{{ $businessItem->image->file_path }}'); height: 250px;">
                                                        <div class="card-img-overlay d-flex align-items-end">
                                                            @if ($businessItem->image && $businessItem->image->file_path)
                                                                <img src="/uploads/{{ $businessItem->image->file_path }}"
                                                                    alt="rumah"
                                                                    style="width:50px; height:50px;border-radius:50%">
                                                            @else
                                                                <img src="https://source.unsplash.com/600x900/?avatar {{ $businessItem->title }}"
                                                                    alt="rumah"
                                                                    style="width:50px; height:50px;border-radius:50%">
                                                            @endif
                                                            <div class="ml-2">
                                                                <h4 class="card-title mt-0 mb-0"
                                                                    style="text-overflow: ellipsis; overflow:hidden; white-space: nowrap;">
                                                                    <a class="text-white">{{ $businessItem->title }}</a>
                                                                </h4>
                                                                <span class="text-white"> <i class="fa fa-map-marker"></i>
                                                                    {{ $businessItem->address }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card-footer">
                                                        <div class="d-flex justify-content-between">
                                                            <div class="d-flex align-items-center">
                                                                <img src="https://source.unsplash.com/600x900/?house"
                                                                    alt="rumah"
                                                                    style="width:25px; height:25px;border-radius:50%">
                                                                <div class="ml-2">
                                                                    <p class="card-title mt-0 mb-0 text-dark">sangrogria
                                                                    </p>
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
                            <div class="col-5">
                                <iframe
                                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3949.5190519312455!2d112.57141607497284!3d-8.150342891880014!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e789f9fb747015b%3A0x41e82b7170971b0!2sKanjuruhan%20Stadium!5e0!3m2!1sen!2sid!4v1695985217982!5m2!1sen!2sid"
                                    class="w-100" height="600" style="border:0;" allowfullscreen="" loading="lazy"
                                    referrerpolicy="no-referrer-when-downgrade"></iframe>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="business" role="tabpanel" aria-labelledby="busines-tab">
                        <div class="row">
                            <div class="col-3">
                                <div class="card" style="height: 600px; overflow-y: auto;">
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
                                            <input type="text" class='form-control' placeholder="Franchising"
                                                style="border-top: none;border-left:none;border-right:none;">
                                        </div>
                                        <div class="form-group mt-3">
                                            <label for="services">Services</label>
                                            <div class="form-group">
                                                <input class="" type="checkbox" value="" id="defaultCheck1">
                                                <label class="form-check-label" for="defaultCheck1">
                                                    Air Conditioning
                                                </label>
                                            </div>
                                            <div class="form-group">
                                                <input class="" type="checkbox" value="" id="defaultCheck2">
                                                <label class="form-check-label" for="defaultCheck2">
                                                    TV
                                                </label>
                                            </div>
                                            <div class="form-group">
                                                <input class="" type="checkbox" value="" id="defaultCheck3">
                                                <label class="form-check-label" for="defaultCheck3">
                                                    Free Wi-Fi
                                                </label>
                                            </div>
                                            <div class="form-group">
                                                <input class="" type="checkbox" value="" id="defaultCheck4">
                                                <label class="form-check-label" for="defaultCheck4">
                                                    Internet
                                                </label>
                                            </div>
                                            <div class="form-group">
                                                <input class="" type="checkbox" value="" id="defaultCheck5">
                                                <label class="form-check-label" for="defaultCheck5">
                                                    Outside Pool
                                                </label>
                                            </div>
                                            <div class="form-group">
                                                <input class="" type="checkbox" value="" id="defaultCheck6">
                                                <label class="form-check-label" for="defaultCheck6">
                                                    Free Parking
                                                </label>
                                            </div>
                                        </div>
                                        <div class="form-group mt-3">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <input type="text" aria-label='location'
                                                    class='form-control autocomplete' id='place-business'
                                                    name='place-business' placeholder="Place"
                                                    style="border-top: none;border-left:none;border-right:none;">
                                                <button class="btn btn-sm" id="get-location" type="button">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none">
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
                                                        <input type="radio" name="options" id="option1"
                                                            autocomplete="off" checked> All
                                                    </label>
                                                    <label class="btn btn-outline-secondary btn-toggle">
                                                        <input type="radio" name="options" id="option2"
                                                            autocomplete="off"> Open Now
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group mt-3">
                                            <div class="form-content">
                                                <label class="mb-2 font-weight-bold">Range</label>
                                                <div class="input-search">
                                                    <input type="range" class="w-100" value="700000" step="100000"
                                                        min="0" max="1000000" />
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
                            </div>
                            <div class="col-4">
                                <div class="card" style="height: 600px; overflow-y: auto;">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-4">
                                            <i class="fa fa-lg fa-arrow-left"></i>
                                            <span>Showing {{ $hotels->count() }} Result</span>
                                            <i class="fa fa-lg fa-arrow-right"></i>
                                        </div>
                                        @foreach ($hotels as $hotel)
                                            <div class="col-12 mb-4">
                                                <div class="card">
                                                    <a href="{{ url('hotel/' . $hotel->slug) }}">
                                                        <div class="card card-custom card-has-bg click-col"
                                                            style="background-image:url('{{ url('uploads/' . ($hotel->image->file_path ?? 'demo/hotel/gallery/hotel-gallery-1.jpg')) }}'); height: 250px;">
                                                            <div class="card-img-overlay d-flex align-items-end">
                                                                <img src="https://source.unsplash.com/600x900/?house"
                                                                    alt="rumah"
                                                                    style="width:50px; height:50px;border-radius:50%">
                                                                <div class="ml-2">
                                                                    <h4 class="card-title mt-0 mb-0"><a class="text-white"
                                                                            herf="{{ url('hotel/' . $hotel->slug) }}">{{ $hotel->title ?? 'Hotel' }}</a>
                                                                    </h4>
                                                                    <span class="text-white"> <i
                                                                            class="fa fa-map-marker"></i>
                                                                        {{ $hotel->address ?? 'No address' }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </a>
                                                    <div class="card-footer">
                                                        <div class="d-flex justify-content-between">
                                                            <div class="d-flex align-items-center">
                                                                <img src="https://source.unsplash.com/600x900/?house"
                                                                    alt="rumah"
                                                                    style="width:25px; height:25px;border-radius:50%">
                                                                <div class="ml-2">
                                                                    <p class="card-title mt-0 mb-0 text-dark">
                                                                        {{ $hotel->user->name ?? 'Virtuard' }}</p>
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
                            <div class="col-5">
                                <iframe
                                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3949.5190519312455!2d112.57141607497284!3d-8.150342891880014!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e789f9fb747015b%3A0x41e82b7170971b0!2sKanjuruhan%20Stadium!5e0!3m2!1sen!2sid!4v1695985217982!5m2!1sen!2sid"
                                    class="w-100" height="600" style="border:0;" allowfullscreen="" loading="lazy"
                                    referrerpolicy="no-referrer-when-downgrade"></iframe>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="properties" role="tabpanel" aria-labelledby="properties-tab">
                        <div class="row">
                            <div class="col-3">
                                <div class="card" style="height: 600px; overflow-y: auto;">
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
                                            <input type="text" class='form-control' placeholder="Franchising"
                                                style="border-top: none;border-left:none;border-right:none;">
                                        </div>
                                        <div class="form-group mt-3">
                                            <label for="services">Services</label>
                                            <div class="form-group">
                                                <input class="" type="checkbox" value="" id="defaultCheck1">
                                                <label class="form-check-label" for="defaultCheck1">
                                                    Air Conditioning
                                                </label>
                                            </div>
                                            <div class="form-group">
                                                <input class="" type="checkbox" value="" id="defaultCheck2">
                                                <label class="form-check-label" for="defaultCheck2">
                                                    TV
                                                </label>
                                            </div>
                                            <div class="form-group">
                                                <input class="" type="checkbox" value="" id="defaultCheck3">
                                                <label class="form-check-label" for="defaultCheck3">
                                                    Free Wi-Fi
                                                </label>
                                            </div>
                                            <div class="form-group">
                                                <input class="" type="checkbox" value="" id="defaultCheck4">
                                                <label class="form-check-label" for="defaultCheck4">
                                                    Internet
                                                </label>
                                            </div>
                                            <div class="form-group">
                                                <input class="" type="checkbox" value="" id="defaultCheck5">
                                                <label class="form-check-label" for="defaultCheck5">
                                                    Outside Pool
                                                </label>
                                            </div>
                                            <div class="form-group">
                                                <input class="" type="checkbox" value="" id="defaultCheck6">
                                                <label class="form-check-label" for="defaultCheck6">
                                                    Free Parking
                                                </label>
                                            </div>
                                        </div>
                                        <div class="form-group mt-3">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <input type="text" aria-label='location'
                                                    class='form-control autocomplete' id='place-properties'
                                                    name='place-properties' placeholder="Place"
                                                    style="border-top: none;border-left:none;border-right:none;">
                                                <button class="btn btn-sm" id="get-location" type="button">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none">
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
                                                        <input type="radio" name="options" id="option1"
                                                            autocomplete="off" checked> All
                                                    </label>
                                                    <label class="btn btn-outline-secondary btn-toggle">
                                                        <input type="radio" name="options" id="option2"
                                                            autocomplete="off"> Open Now
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group mt-3">
                                            <div class="form-content">
                                                <label class="mb-2 font-weight-bold">Range</label>
                                                <div class="input-search">
                                                    <input type="range" class="w-100" value="700000" step="100000"
                                                        min="0" max="1000000" />
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
                            </div>
                            <div class="col-4">
                                <div class="card" style="height: 600px; overflow-y: auto;">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-4">
                                            <i class="fa fa-lg fa-arrow-left"></i>
                                            <span>Showing {{ $properties->count() }} Result</span>
                                            <i class="fa fa-lg fa-arrow-right"></i>
                                        </div>
                                        @foreach ($properties as $property)
                                            <div class="col-12 mb-4">
                                                <div class="card">
                                                    <div class="card card-custom card-has-bg click-col"
                                                        style="background-image:url('{{ url('uploads/' . ($property->image->file_path ?? 'demo/hotel/gallery/hotel-gallery-1.jpg')) }}'); height: 250px;">
                                                        <div class="card-img-overlay d-flex align-items-end">
                                                            <img src="https://source.unsplash.com/600x900/?house"
                                                                alt="rumah"
                                                                style="width:50px; height:50px;border-radius:50%">
                                                            <div class="ml-2">
                                                                <h4 class="card-title mt-0 mb-0"><a class="text-white"
                                                                        href="{{ url('space/' . $property->slug) }}">{{ $property->title }}</a>
                                                                </h4>
                                                                <span class="text-white"> <i class="fa fa-map-marker"></i>
                                                                    {{ $property->address }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card-footer">
                                                        <div class="d-flex justify-content-between">
                                                            <div class="d-flex align-items-center">
                                                                <img src="https://source.unsplash.com/600x900/?house"
                                                                    alt="rumah"
                                                                    style="width:25px; height:25px;border-radius:50%">
                                                                <div class="ml-2">
                                                                    <p class="card-title mt-0 mb-0 text-dark">
                                                                        {{ $property->user->business_name ?? 'Virtuard' }}
                                                                    </p>
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
                            <div class="col-5">
                                <iframe
                                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3949.5190519312455!2d112.57141607497284!3d-8.150342891880014!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e789f9fb747015b%3A0x41e82b7170971b0!2sKanjuruhan%20Stadium!5e0!3m2!1sen!2sid!4v1695985217982!5m2!1sen!2sid"
                                    class="w-100" height="600" style="border:0;" allowfullscreen="" loading="lazy"
                                    referrerpolicy="no-referrer-when-downgrade"></iframe>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="accomodation" role="tabpanel" aria-labelledby="accomodation-tab">
                        <div class="row">
                            <div class="col-3">
                                <div class="card" style="height: 600px; overflow-y: auto;">
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
                                            <input type="text" class='form-control' placeholder="Franchising"
                                                style="border-top: none;border-left:none;border-right:none;">
                                        </div>
                                        <div class="form-group mt-3">
                                            <label for="services">Services</label>
                                            <div class="form-group">
                                                <input class="" type="checkbox" value="" id="defaultCheck1">
                                                <label class="form-check-label" for="defaultCheck1">
                                                    Air Conditioning
                                                </label>
                                            </div>
                                            <div class="form-group">
                                                <input class="" type="checkbox" value="" id="defaultCheck2">
                                                <label class="form-check-label" for="defaultCheck2">
                                                    TV
                                                </label>
                                            </div>
                                            <div class="form-group">
                                                <input class="" type="checkbox" value="" id="defaultCheck3">
                                                <label class="form-check-label" for="defaultCheck3">
                                                    Free Wi-Fi
                                                </label>
                                            </div>
                                            <div class="form-group">
                                                <input class="" type="checkbox" value="" id="defaultCheck4">
                                                <label class="form-check-label" for="defaultCheck4">
                                                    Internet
                                                </label>
                                            </div>
                                            <div class="form-group">
                                                <input class="" type="checkbox" value="" id="defaultCheck5">
                                                <label class="form-check-label" for="defaultCheck5">
                                                    Outside Pool
                                                </label>
                                            </div>
                                            <div class="form-group">
                                                <input class="" type="checkbox" value="" id="defaultCheck6">
                                                <label class="form-check-label" for="defaultCheck6">
                                                    Free Parking
                                                </label>
                                            </div>
                                        </div>
                                        <div class="form-group mt-3">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <input type="text" aria-label='location'
                                                    class='form-control autocomplete' id='place-accomodation'
                                                    name='place-accomodation' placeholder="Place"
                                                    style="border-top: none;border-left:none;border-right:none;">
                                                <button class="btn btn-sm" id="get-location" type="button">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none">
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
                                                        <input type="radio" name="options" id="option1"
                                                            autocomplete="off" checked> All
                                                    </label>
                                                    <label class="btn btn-outline-secondary btn-toggle">
                                                        <input type="radio" name="options" id="option2"
                                                            autocomplete="off"> Open Now
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group mt-3">
                                            <div class="form-content">
                                                <label class="mb-2 font-weight-bold">Range</label>
                                                <div class="input-search">
                                                    <input type="range" class="w-100" value="700000" step="100000"
                                                        min="0" max="1000000" />
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
                            </div>
                            <div class="col-4">
                                <div class="card" style="height: 600px; overflow-y: auto;">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-4">
                                            <i class="fa fa-lg fa-arrow-left"></i>
                                            <span>Showing {{ $cars->count() }} Result</span>
                                            <i class="fa fa-lg fa-arrow-right"></i>
                                        </div>
                                        @foreach ($cars as $car)
                                            <div class="col-12 mb-4">
                                                <div class="card">
                                                    <div class="card card-custom card-has-bg click-col"
                                                        style="background-image:url('{{ url('uploads/' . ($car->image->file_path ?? 'demo/hotel/gallery/hotel-gallery-1.jpg')) }}'); height: 250px;">
                                                        <div class="card-img-overlay d-flex align-items-end">
                                                            <img src="https://source.unsplash.com/600x900/?house"
                                                                alt="rumah"
                                                                style="width:50px; height:50px;border-radius:50%">
                                                            <div class="ml-2">
                                                                <h4 class="card-title mt-0 mb-0"><a class="text-white"
                                                                        href="{{ url('car/' . $car->slug) }}">{{ $car->title }}</a>
                                                                </h4>
                                                                <span class="text-white"> <i class="fa fa-map-marker"></i>
                                                                    {{ $car->address }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card-footer">
                                                        <div class="d-flex justify-content-between">
                                                            <div class="d-flex align-items-center">
                                                                <img src="https://source.unsplash.com/600x900/?house"
                                                                    alt="rumah"
                                                                    style="width:25px; height:25px;border-radius:50%">
                                                                <div class="ml-2">
                                                                    <p class="card-title mt-0 mb-0 text-dark">
                                                                        {{ $car->user->business_name }}
                                                                    </p>
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
                            <div class="col-5">
                                <iframe
                                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3949.5190519312455!2d112.57141607497284!3d-8.150342891880014!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e789f9fb747015b%3A0x41e82b7170971b0!2sKanjuruhan%20Stadium!5e0!3m2!1sen!2sid!4v1695985217982!5m2!1sen!2sid"
                                    class="w-100" height="600" style="border:0;" allowfullscreen="" loading="lazy"
                                    referrerpolicy="no-referrer-when-downgrade"></iframe>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="cultural" role="tabpanel" aria-labelledby="cultural-tab">
                        <div class="row">
                            <div class="col-3">
                                <div class="card" style="height: 600px; overflow-y: auto;">
                                    <ul class="nav nav-tabs justify-content-start p-3" id="myTab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="filters-cultural-tab" data-toggle="tab"
                                                data-target="#filters-cultural" type="button" role="tab"
                                                aria-controls="filters-cultural" aria-selected="true">Filters</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="categories-cultural-tab" data-toggle="tab"
                                                data-target="#categories-cultural" type="button" role="tab"
                                                aria-controls="categories-cultural"
                                                aria-selected="false">Categories</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="region-cultural-tab" data-toggle="tab"
                                                data-target="#region-cultural" type="button" role="tab"
                                                aria-controls="region-cultural" aria-selected="false">Region</button>
                                        </li>
                                    </ul>
                                    <div class="tab-content" id="myTabContent">
                                        <div class="tab-pane fade show active" id="filter-cultural" role="tabpanel"
                                            aria-labelledby="filter-cultural-tab">
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
                                                        <input type="text" aria-label='location'
                                                            class='form-control autocomplete' id='place-cultural'
                                                            name='place-cultural' placeholder="Place"
                                                            style="border-top: none;border-left:none;border-right:none;">
                                                        <button class="btn btn-sm" id="get-location" type="button">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                height="24" viewBox="0 0 24 24" fill="none">
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
                                                        <div class="btn-group w-100 btn-group-toggle"
                                                            data-toggle="buttons">
                                                            <label class="btn btn-outline-secondary btn-toggle active">
                                                                <input type="radio" name="options" id="option1"
                                                                    autocomplete="off" checked> All
                                                            </label>
                                                            <label class="btn btn-outline-secondary btn-toggle">
                                                                <input type="radio" name="options" id="option2"
                                                                    autocomplete="off"> Open Now
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group mt-3">
                                                    <div class="form-content">
                                                        <label class="mb-2 font-weight-bold">Range</label>
                                                        <div class="input-search">
                                                            <input type="range" class="w-100" value="700000"
                                                                step="100000" min="0" max="1000000" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group mt-3">
                                                    <input type="text" class='form-control' placeholder="Text Search"
                                                        style="border-top: none;border-left:none;border-right:none;">
                                                </div>
                                                <div class="form-group mt-3">
                                                    <input type="text" class='form-control'
                                                        placeholder="General Search Box"
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
                                        <div class="tab-pane fade" id="categories-cultural" role="tabpanel"
                                            aria-labelledby="categories-cultural-tab">
                                            <div class="col-12 mb-4">
                                                <div class="card card-custom card-has-bg click-col"
                                                    style="background-image:url('https://source.unsplash.com/600x900/?tech');">
                                                    <div
                                                        class="card-img-overlay d-flex flex-column justify-content-between">
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
                                                    <div
                                                        class="card-img-overlay d-flex flex-column justify-content-between">
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
                                                    <div
                                                        class="card-img-overlay d-flex flex-column justify-content-between">
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
                                                    <div
                                                        class="card-img-overlay d-flex flex-column justify-content-between">
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
                                                    <div
                                                        class="card-img-overlay d-flex flex-column justify-content-between">
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
                                                    <div
                                                        class="card-img-overlay d-flex flex-column justify-content-between">
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
                                        <div class="tab-pane fade" id="region-cultural" role="tabpanel"
                                            aria-labelledby="region-cultural-tab">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="card" style="height: 600px; overflow-y: auto;">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-4">
                                            <i class="fa fa-lg fa-arrow-left"></i>
                                            <span>Showing {{ $events->count() }} Result</span>
                                            <i class="fa fa-lg fa-arrow-right"></i>
                                        </div>
                                        @foreach ($events as $event)
                                            <div class="col-12 mb-4">
                                                <div class="card">
                                                    <div class="card card-custom card-has-bg click-col"
                                                        style="background-image:url('{{ url('uploads/' . ($event->image->file_path ?? 'demo/hotel/gallery/hotel-gallery-1.jpg')) }}'); height: 250px;">
                                                        <div class="card-img-overlay d-flex align-items-end">
                                                            <img src="https://source.unsplash.com/600x900/?event"
                                                                alt="rumah"
                                                                style="width:50px; height:50px;border-radius:50%">
                                                            <div class="ml-2">
                                                                <h4 class="card-title mt-0 mb-0"><a class="text-white"
                                                                        href="{{ url('event/' . $event->slug) }}">{{ $event->title }}</a>
                                                                </h4>
                                                                <span class="text-white"> <i class="fa fa-map-marker"></i>
                                                                    {{ $event->address }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card-footer">
                                                        <div class="d-flex justify-content-between">
                                                            <div class="d-flex align-items-center">
                                                                <img src="https://source.unsplash.com/600x900/?event"
                                                                    alt="rumah"
                                                                    style="width:25px; height:25px;border-radius:50%">
                                                                <div class="ml-2">
                                                                    <p class="card-title mt-0 mb-0 text-dark">
                                                                        {{ $event->user->business_name ?? 'Virtuard' }}
                                                                    </p>
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
                            <div class="col-5">
                                <iframe
                                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3949.5190519312455!2d112.57141607497284!3d-8.150342891880014!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e789f9fb747015b%3A0x41e82b7170971b0!2sKanjuruhan%20Stadium!5e0!3m2!1sen!2sid!4v1695985217982!5m2!1sen!2sid"
                                    class="w-100" height="600" style="border:0;" allowfullscreen=""
                                    loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="rendering" role="tabpanel" aria-labelledby="rendering-tab">
                        <div class="row">
                            <div class="col-3">
                                <div class="card" style="height: 600px; overflow-y: auto;">
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
                                            <label for="services">Services</label>
                                            <div class="form-group">
                                                <input class="" type="checkbox" value=""
                                                    id="defaultCheck1">
                                                <label class="form-check-label" for="defaultCheck1">
                                                    Air Conditioning
                                                </label>
                                            </div>
                                            <div class="form-group">
                                                <input class="" type="checkbox" value=""
                                                    id="defaultCheck2">
                                                <label class="form-check-label" for="defaultCheck2">
                                                    TV
                                                </label>
                                            </div>
                                            <div class="form-group">
                                                <input class="" type="checkbox" value=""
                                                    id="defaultCheck3">
                                                <label class="form-check-label" for="defaultCheck3">
                                                    Free Wi-Fi
                                                </label>
                                            </div>
                                            <div class="form-group">
                                                <input class="" type="checkbox" value=""
                                                    id="defaultCheck4">
                                                <label class="form-check-label" for="defaultCheck4">
                                                    Internet
                                                </label>
                                            </div>
                                            <div class="form-group">
                                                <input class="" type="checkbox" value=""
                                                    id="defaultCheck5">
                                                <label class="form-check-label" for="defaultCheck5">
                                                    Outside Pool
                                                </label>
                                            </div>
                                            <div class="form-group">
                                                <input class="" type="checkbox" value=""
                                                    id="defaultCheck6">
                                                <label class="form-check-label" for="defaultCheck6">
                                                    Free Parking
                                                </label>
                                            </div>
                                        </div>
                                        <div class="form-group mt-3">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <input type="text" aria-label='location'
                                                    class='form-control autocomplete' id='place-rendering'
                                                    name='place-rendering' placeholder="Place"
                                                    style="border-top: none;border-left:none;border-right:none;">
                                                <button class="btn btn-sm" id="get-location" type="button">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                        height="24" viewBox="0 0 24 24" fill="none">
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
                                                        <input type="radio" name="options" id="option1"
                                                            autocomplete="off" checked> All
                                                    </label>
                                                    <label class="btn btn-outline-secondary btn-toggle">
                                                        <input type="radio" name="options" id="option2"
                                                            autocomplete="off"> Open Now
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group mt-3">
                                            <div class="form-content">
                                                <label class="mb-2 font-weight-bold">Range</label>
                                                <div class="input-search">
                                                    <input type="range" class="w-100" value="700000"
                                                        step="100000" min="0" max="1000000" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group mt-3">
                                            <input type="text" class='form-control' placeholder="Text Search"
                                                style="border-top: none;border-left:none;border-right:none;">
                                        </div>
                                        <div class="form-group mt-3">
                                            <input type="text" class='form-control'
                                                placeholder="General Search Box"
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
                            </div>
                            <div class="col-4">
                                <div class="card" style="height: 600px; overflow-y: auto;">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-4">
                                            <i class="fa fa-lg fa-arrow-left"></i>
                                            <span>Showing {{ $flights->count() }} Result</span>
                                            <i class="fa fa-lg fa-arrow-right"></i>
                                        </div>
                                        @foreach ($flights as $flight)
                                            <div class="col-12 mb-4">
                                                <div class="card">
                                                    <div class="card card-custom card-has-bg click-col"
                                                        style="background-image:url('https://source.unsplash.com/600x900/?flight'); height: 250px;">
                                                        <div class="card-img-overlay d-flex align-items-end">
                                                            <img src="https://source.unsplash.com/600x900/?flight"
                                                                alt="rumah"
                                                                style="width:50px; height:50px;border-radius:50%">
                                                            <div class="ml-2">
                                                                <h4 class="card-title mt-0 mb-0"><a class="text-white"
                                                                        href="{{ url('flight') }}">{{ $flight->title }}</a>
                                                                </h4>
                                                                <span class="text-white">
                                                                    {{ $flight->code }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card-footer">
                                                        <div class="d-flex justify-content-between">
                                                            <div class="d-flex align-items-center">
                                                                <img src="https://source.unsplash.com/600x900/?flight"
                                                                    alt="rumah"
                                                                    style="width:25px; height:25px;border-radius:50%">
                                                                <div class="ml-2">
                                                                    <p class="card-title mt-0 mb-0 text-dark">
                                                                        {{ $flight->user->business_name ?? 'Virtuard' }}
                                                                    </p>
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
                            <div class="col-5">
                                <iframe
                                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3949.5190519312455!2d112.57141607497284!3d-8.150342891880014!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e789f9fb747015b%3A0x41e82b7170971b0!2sKanjuruhan%20Stadium!5e0!3m2!1sen!2sid!4v1695985217982!5m2!1sen!2sid"
                                    class="w-100" height="600" style="border:0;" allowfullscreen=""
                                    loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="vehicle" role="tabpanel" aria-labelledby="vehicle-tab">
                        <div class="row">
                            <div class="col-3">
                                <div class="card" style="height: 600px; overflow-y: auto;">
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
                                            <label for="services">Services</label>
                                            <div class="form-group">
                                                <input class="" type="checkbox" value=""
                                                    id="defaultCheck1">
                                                <label class="form-check-label" for="defaultCheck1">
                                                    Air Conditioning
                                                </label>
                                            </div>
                                            <div class="form-group">
                                                <input class="" type="checkbox" value=""
                                                    id="defaultCheck2">
                                                <label class="form-check-label" for="defaultCheck2">
                                                    TV
                                                </label>
                                            </div>
                                            <div class="form-group">
                                                <input class="" type="checkbox" value=""
                                                    id="defaultCheck3">
                                                <label class="form-check-label" for="defaultCheck3">
                                                    Free Wi-Fi
                                                </label>
                                            </div>
                                            <div class="form-group">
                                                <input class="" type="checkbox" value=""
                                                    id="defaultCheck4">
                                                <label class="form-check-label" for="defaultCheck4">
                                                    Internet
                                                </label>
                                            </div>
                                            <div class="form-group">
                                                <input class="" type="checkbox" value=""
                                                    id="defaultCheck5">
                                                <label class="form-check-label" for="defaultCheck5">
                                                    Outside Pool
                                                </label>
                                            </div>
                                            <div class="form-group">
                                                <input class="" type="checkbox" value=""
                                                    id="defaultCheck6">
                                                <label class="form-check-label" for="defaultCheck6">
                                                    Free Parking
                                                </label>
                                            </div>
                                        </div>
                                        <div class="form-group mt-3">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <input type="text" aria-label='location'
                                                    class='form-control autocomplete' id='place-rendering'
                                                    name='place-rendering' placeholder="Place"
                                                    style="border-top: none;border-left:none;border-right:none;">
                                                <button class="btn btn-sm" id="get-location" type="button">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                        height="24" viewBox="0 0 24 24" fill="none">
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
                                                        <input type="radio" name="options" id="option1"
                                                            autocomplete="off" checked> All
                                                    </label>
                                                    <label class="btn btn-outline-secondary btn-toggle">
                                                        <input type="radio" name="options" id="option2"
                                                            autocomplete="off"> Open Now
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group mt-3">
                                            <div class="form-content">
                                                <label class="mb-2 font-weight-bold">Range</label>
                                                <div class="input-search">
                                                    <input type="range" class="w-100" value="700000"
                                                        step="100000" min="0" max="1000000" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group mt-3">
                                            <input type="text" class='form-control' placeholder="Text Search"
                                                style="border-top: none;border-left:none;border-right:none;">
                                        </div>
                                        <div class="form-group mt-3">
                                            <input type="text" class='form-control'
                                                placeholder="General Search Box"
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
                            </div>
                            <div class="col-4">
                                <div class="card" style="height: 600px; overflow-y: auto;">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-4">
                                            <i class="fa fa-lg fa-arrow-left"></i>
                                            <span>Showing {{$cars->count() + $boats->count()}} Result</span>
                                            <i class="fa fa-lg fa-arrow-right"></i>
                                        </div>
                                        @foreach ($cars as $car)
                                            <div class="col-12 mb-4">
                                                <div class="card">
                                                    <div class="card card-custom card-has-bg click-col"
                                                        style="background-image:url('{{ url('uploads/' . ($car->image->file_path ?? 'demo/hotel/gallery/hotel-gallery-1.jpg')) }}'); height: 250px;">
                                                        <div class="card-img-overlay d-flex align-items-end">
                                                            <img src="https://source.unsplash.com/600x900/?car"
                                                                alt="Mobil"
                                                                style="width:50px; height:50px;border-radius:50%">
                                                            <div class="ml-2">
                                                                <h4 class="card-title mt-0 mb-0"><a class="text-white"
                                                                        href="{{ url('car/' . $car->slug) }}">{{ $car->title }}</a>
                                                                </h4>
                                                                <span class="text-white"> <i
                                                                        class="fa fa-map-marker"></i>
                                                                    {{ $car->address }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card-footer">
                                                        <div class="d-flex justify-content-between">
                                                            <div class="d-flex align-items-center">
                                                                <img src="https://source.unsplash.com/600x900/?car"
                                                                    alt="rumah"
                                                                    style="width:25px; height:25px;border-radius:50%">
                                                                <div class="ml-2">
                                                                    <p class="card-title mt-0 mb-0 text-dark">
                                                                        {{ $car->user->business_name }}
                                                                    </p>
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

                                        @foreach ($boats as $boat)
                                            <div class="col-12 mb-4">
                                                <div class="card">
                                                    <div class="card card-custom card-has-bg click-col"
                                                        style="background-image:url('{{ url('uploads/' . ($boat->image->file_path ?? 'demo/hotel/gallery/hotel-gallery-1.jpg')) }}'); height: 250px;">
                                                        <div class="card-img-overlay d-flex align-items-end">
                                                            <img src="https://source.unsplash.com/600x900/?boat"
                                                                alt="Mobil"
                                                                style="width:50px; height:50px;border-radius:50%">
                                                            <div class="ml-2">
                                                                <h4 class="card-title mt-0 mb-0"><a class="text-white"
                                                                        href="{{ url('boat/' . $boat->slug) }}">{{ $boat->title }}</a>
                                                                </h4>
                                                                <span class="text-white"> <i
                                                                        class="fa fa-map-marker"></i>
                                                                    {{ $boat->address }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card-footer">
                                                        <div class="d-flex justify-content-between">
                                                            <div class="d-flex align-items-center">
                                                                <img src="https://source.unsplash.com/600x900/?boat"
                                                                    alt="rumah"
                                                                    style="width:25px; height:25px;border-radius:50%">
                                                                <div class="ml-2">
                                                                    <p class="card-title mt-0 mb-0 text-dark">
                                                                        {{ $boat->user->business_name }}
                                                                    </p>
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
                            <div class="col-5">
                                <iframe
                                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3949.5190519312455!2d112.57141607497284!3d-8.150342891880014!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e789f9fb747015b%3A0x41e82b7170971b0!2sKanjuruhan%20Stadium!5e0!3m2!1sen!2sid!4v1695985217982!5m2!1sen!2sid"
                                    class="w-100" height="600" style="border:0;" allowfullscreen=""
                                    loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <style>
        .autocomplete-items {
            position: absolute;
            z-index: 99;
            left: 0;
            right: 0;
        }

        .form-control:focus {
            outline: none;
            box-shadow: none;
        }

        input[type="range"]:focus {
            outline: none;
        }

        input[type="range"] {
            position: relative;
            -webkit-appearance: none;
            margin-right: 15px;
            width: 100%;
            height: 8px;
            background: rgba(241, 241, 241, 1);
            border-radius: 5px;
            background-image: linear-gradient(180deg,
                    rgb(0, 0, 0) 0%,
                    #000000 100%);
            background-size: 70% 100%;
            background-repeat: no-repeat;
        }

        input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            height: 22px;
            width: 22px;
            border-radius: 50%;
            background: #fff;
            cursor: ew-resize;
            border: 3.5px solid #000000;
            transition: background 0.3s ease-in-out;
        }

        input[type="range"]::-moz-range-thumb {
            -webkit-appearance: none;
            height: 22px;
            width: 22px;
            border-radius: 50%;
            background: #fff;
            cursor: ew-resize;
            border: 3.5px solid #000000;
            transition: background 0.3s ease-in-out;
        }

        input[type="range"]::-ms-thumb {
            -webkit-appearance: none;
            height: 22px;
            width: 22px;
            border-radius: 50%;
            background: #fff;
            cursor: ew-resize;
            border: 3.5px solid #000000;
            transition: background 0.3s ease-in-out;
        }

        input[type="range"]::-webkit-slider-runnable-track {
            -webkit-appearance: none;
            box-shadow: none;
            border: none;
            background: transparent;
        }

        input[type="range"]::-moz-range-track {
            -webkit-appearance: none;
            box-shadow: none;
            border: none;
            background: transparent;
        }

        input[type="range"]::-ms-track {
            -webkit-appearance: none;
            box-shadow: none;
            border: none;
            background: transparent;
        }

        .card-custom {
            border: none;
            overflow: hidden;
            transition: all 500ms cubic-bezier(0.19, 1, 0.22, 1);
            border-radius: 5px;
            min-height: 150px;
            box-shadow: 0 0 12px 0 rgba(0, 0, 0, 0.2);
        }

        @media (max-width: 768px) {
            .card-custom {
                min-height: 350px;
            }
        }

        @media (max-width: 420px) {
            .card-custom {
                min-height: 300px;
            }
        }

        .card-custom.card-has-bg {
            transition: all 500ms cubic-bezier(0.19, 1, 0.22, 1);
            background-size: 120%;
            background-repeat: no-repeat;
            background-position: center center;
        }

        .card-custom.card-has-bg:before {
            content: "";
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
        }

        .card-custom.card-has-bg:hover {
            transform: scale(0.98);
            box-shadow: 0 0 5px -2px rgba(0, 0, 0, 0.3);
            background-size: 130%;
            transition: all 500ms cubic-bezier(0.19, 1, 0.22, 1);
        }

        .card-custom.card-has-bg:hover .card-img-overlay {
            transition: all 800ms cubic-bezier(0.19, 1, 0.22, 1);
            background: rgba(22, 22, 22, 0.8);
            background: linear-gradient(0deg, rgba(22, 22, 22, 0.8) 0%, #1a1b1b 100%);
        }

        .card-custom .card-title {
            font-weight: 800;
        }

        .card-custom .card-meta {
            text-transform: uppercase;
            font-weight: 500;
            letter-spacing: 2px;
        }

        .card-custom .card-body {
            transition: all 500ms cubic-bezier(0.19, 1, 0.22, 1);
        }

        .card-custom:hover {
            cursor: pointer;
            transition: all 800ms cubic-bezier(0.19, 1, 0.22, 1);
        }

        .card-custom .card-img-overlay {
            transition: all 800ms cubic-bezier(0.19, 1, 0.22, 1);
            background: rgba(22, 22, 22, 0.3);
            background: linear-gradient(0deg, rgba(22, 22, 22, 0.3) 0%, #1a1b1b 100%);
        }
    </style>
@endpush

@push('js')
    <script>
        let autocomplete = (inp, arr) => {
            let currentFocus;
            inp.addEventListener("input", function(e) {
                let a,
                    b,
                    i,
                    val = this.value;

                closeAllLists();

                if (!val) {
                    return false;
                }

                currentFocus = -1;

                a = document.createElement("DIV");

                a.setAttribute("id", this.id + " autocomplete-list");
                a.setAttribute("class", " autocomplete-items list-group text-left");

                this.parentNode.appendChild(a);

                for (i = 0; i < arr.length; i++) {
                    if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
                        b = document.createElement("DIV");
                        b.setAttribute("class", "list-group-item list-group-item-action");
                        b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
                        b.innerHTML += arr[i].substr(val.length);
                        b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
                        b.addEventListener("click", function(e) {
                            inp.value = this.getElementsByTagName("input")[0].value;
                            closeAllLists();
                        });
                        a.appendChild(b);
                    }
                }
            });

            inp.addEventListener("keydown", function(e) {
                var x = document.getElementById(this.id + "autocomplete-list");
                if (x) x = x.getElementsByTagName("div");
                if (e.keyCode == 40) {
                    currentFocus++;
                    addActive(x);
                } else if (e.keyCode == 38) {
                    currentFocus--;
                    addActive(x);
                } else if (e.keyCode == 13) {
                    e.preventDefault();
                    if (currentFocus > -1) {
                        if (x) x[currentFocus].click();
                    }
                }
            });

            let addActive = (x) => {
                if (!x) return false;
                removeActive(x);
                if (currentFocus >= x.length) currentFocus = 0;
                if (currentFocus < 0) currentFocus = x.length - 1;
                x[currentFocus].classList.add("active");
            };

            let removeActive = (x) => {
                for (let i = 0; i < x.length; i++) {
                    x[i].classList.remove("active");
                }
            };

            let closeAllLists = (elmnt) => {
                var x = document.getElementsByClassName("autocomplete-items");
                for (var i = 0; i < x.length; i++) {
                    if (elmnt != x[i] && elmnt != inp) {
                        x[i].parentNode.removeChild(x[i]);
                    }
                }
            };

            document.addEventListener("click", function(e) {
                closeAllLists(e.target);
            });
        };

        let data = [
            "Afghanistan",
            "Albania",
            "Algeria",
            "Andorra",
            "Angola",
            "Anguilla",
            "Antigua & Barbuda",
            "Argentina",
            "Armenia",
            "Aruba",
            "Australia",
            "Austria",
            "Azerbaijan",
            "Bahamas",
            "Bahrain",
            "Bangladesh",
            "Barbados",
            "Belarus",
            "Belgium",
            "Belize",
            "Benin",
            "Bermuda",
            "Bhutan",
        ];

        autocomplete(document.getElementById("location"), data);
        autocomplete(document.getElementById("place-business"), data);
        autocomplete(document.getElementById("place-properties"), data);
        autocomplete(document.getElementById("place-accomodation"), data);
        autocomplete(document.getElementById("place-cultural"), data);
        autocomplete(document.getElementById("place-natural"), data);
        autocomplete(document.getElementById("place-rendering"), data);
    </script>
    <script>
        document.getElementById('get-location').addEventListener('click', function() {
            if ("geolocation" in navigator) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    alert("Lokasi berhasil diizinkan. Latitude: " + position.coords.latitude +
                        ", Longitude: " + position.coords.longitude);
                });
            } else {
                alert("Geolokasi tidak didukung di peramban Anda.");
            }
        });
    </script>
    {{-- <script>
        const rangeInputs = document.querySelectorAll('input[type="range"]')

        function handleInputChange(e) {
            let target = e.target
            if (e.target.type !== 'range') {
                target = document.getElementById('range')
            }
            const min = target.min
            const max = target.max
            const val = target.value

            let percentage = (val - min) * 100 / (max - min)
            target.style.backgroundSize = percentage + '% 100%'
        }

        rangeInputs.forEach(input => {
            input.addEventListener('input', handleInputChange)
        })
    </script> --}}
@endpush
