<div class="card card-explore">
    <div class="card-body">
        <div class="bravo_search_tour">
            <div class="bravo_filter">
                <form class="bravo_form_filter" action="{{ route('explore.index') }}">
                    <div class="all-tabs">
                        <ul class="nav nav-tabs justify-content-start" id="filterAllTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="all-category-tab" data-toggle="tab"
                                    data-target="#all-category" type="button" role="tab"
                                    aria-controls="all-category" aria-selected="true">Category</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="all-typology-tab" data-toggle="tab"
                                    data-target="#all-typology" type="button" role="tab"
                                    aria-controls="all-typology" aria-selected="false">Typology</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="filterAllTabContent">
                            <div class="tab-pane fade show active" id="all-category" role="tabpanel"
                                aria-labelledby="all-category-tab">
                                @php
                                    $allCategories = get_all_categories();
                                @endphp
                                @foreach ($allCategories as $val)
                                    <div class="col-md-12 mb-4 p-0">
                                        <div class="card card-custom card-has-bg click-col nav-link sub-nav-link"
                                            style="background-image:url('');">
                                            <div class="card-img-overlay d-flex flex-column justify-content-between"
                                                data-toggle="tab" data-target="#sub">
                                                {{-- <i class="fa fa-lg fa-shopping-bag text-white"></i> --}}
                                                <div>
                                                    <h4 class="card-title mt-0 mb-0 text-white ">{{ $val->name }}
                                                    </h4>
                                                    {{-- <small class="text-white">1.344 listings</small> --}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="tab-pane fade" id="all-typology" role="tabpanel"
                                aria-labelledby="all-typology-tab">
                                @php
                                    $allTypologies = get_all_typologies();
                                @endphp
                                @foreach ($allTypologies as $val)
                                    <div class="col-md-12 mb-4 p-0">
                                        <div class="card card-custom card-has-bg click-col nav-link sub-nav-link"
                                            style="background-image:url('');">
                                            <div class="card-img-overlay d-flex flex-column justify-content-between"
                                                data-toggle="tab" data-target="#sub">
                                                {{-- <i class="fa fa-lg fa-shopping-bag text-white"></i> --}}
                                                <div>
                                                    <h4 class="card-title mt-0 mb-0 text-white">{{ $val->name }}</h4>
                                                    {{-- <small class="text-white">1.344 listings</small> --}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="tab-pane fade panel-all-back" id="sub" role="tabpanel"
                                aria-labelledby="all-typology-tab">
                                <div class="col-md-12 mb-4 p-0">
                                    <a class="nav-link btn btn-secondary btn-all-back" data-toggle="tab"
                                        href="#all-category" role="tab" aria-controls="activity"
                                        aria-selected="false">Back</a>
                                </div>
                                <div class="col-md-12 mb-4 p-0">
                                    <div class="card card-explore">
                                        <div class="card-body">
                                            <div class="bravo_search_tour">
                                                <div class="bravo_filter">
                                                    <form class="bravo_form_filter"
                                                        action="{{ route('explore.index') }}">
                                                        <div class="g-filter-item">
                                                            <div class="item-title">
                                                                <h3>{{ __('Sort by') }}</h3>
                                                            </div>
                                                            <div class="item-content">
                                                                <div class="form-group">
                                                                    <select name="orderby" class="form-control orderby">
                                                                        <option value="created_at"
                                                                            {{ request('orderby') == 'created_at' ? 'selected' : '' }}>
                                                                            {{ __('Last') }}
                                                                        </option>
                                                                        <option value="rate_high_low"
                                                                            {{ request('orderby') == 'rate_high_low' ? 'selected' : '' }}>
                                                                            {{ __('Top Rated') }}
                                                                        </option>
                                                                        <option value="">
                                                                            {{ __('Random') }}
                                                                        </option>
                                                                        <option value="title">
                                                                            {{ __('A-Z') }}
                                                                        </option>
                                                                        <option value="ipanorama_id">
                                                                            {{ __('360') }}
                                                                        </option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <button type="submit" class="btn btn-dark form-control p-0">{{ __('Search') }}</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
