<div class="card card-explore">
    <div class="card-body">
        <div class="bravo_search_tour">
            <div class="bravo_filter">

                <div class="all-tabs">
                    <ul class="nav nav-tabs justify-content-start" id="filterAllTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link @if (request('term_type') == '' || request('term_type') == 'category') active @endif"
                                id="all-category-tab" data-toggle="tab" data-target="#all-category" type="button"
                                role="tab" aria-controls="all-category" aria-selected="true">Category</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link @if (request('term_type') == 'typology') active @endif"
                                id="all-typology-tab" data-toggle="tab" data-target="#all-typology" type="button"
                                role="tab" aria-controls="all-typology" aria-selected="false">Typology</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="filterAllTabContent">
                        <div class="tab-pane fade @if (request('term_type') == '' || request('term_type') == 'category') show active @endif"
                            id="all-category" role="tabpanel" aria-labelledby="all-category-tab">
                            <form id="form_explore_all" class="bravo_form_filter" action="{{ route('explore.index') }}">
                                <input type="hidden" name="term_type" value="category">
                                <div class="g-filter-item">
                                    <div class="item-title">
                                        <h3>{{ __('Sort by') }}</h3>
                                    </div>
                                    <div class="item-content">
                                        <div class="form-group">
                                            <select name="orderby" class="form-control orderby">
                                                <option value="created_at">
                                                    {{ __('Last') }}
                                                </option>
                                                <option value="rate_high_low">
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
                                <!-- Attributes -->
                                @php
                                    $category = \Modules\Core\Models\Attributes::query()
                                        ->whereIn('service', menu_listing())
                                        ->where('slug', 'like', '%type%')
                                        ->orderBy('name', 'asc')
                                        ->with([
                                            'terms' => function($q) {
                                                $q->orderBy('name');
                                            }, 
                                            'translation'
                                        ])
                                        ->get();
                                    $category_selected = (array) Request::query('terms');
                                @endphp
                                @foreach ($category as $item)
                                    @if (empty($item['hide_in_filter_search']))
                                        @php
                                            $translate = $item->translate();
                                        @endphp
                                        <div class="g-filter-item">
                                            <div class="item-title">
                                                <h3 class="text-capitalize"> {{ menu_listing_as($item->service) }} </h3>
                                                <label class="text-capitalize">{{ $translate->name }} </label>
                                                <i class="fa fa-angle-up" aria-hidden="true"></i>
                                            </div>
                                            <div class="item-content">
                                                <ul>
                                                    @foreach ($item->terms as $key => $term)
                                                        @php $translate = $term->translate(); @endphp
                                                        <li @if ($key > 2 and empty($category_selected)) class="hide" @endif>
                                                            <div class="bravo-checkbox">
                                                                <label>
                                                                    <input
                                                                        @if (in_array($term->id, $category_selected)) checked @endif
                                                                        type="checkbox" name="terms[]"
                                                                        value="{{ $term->id }}">
                                                                    {!! $translate->name !!}
                                                                    <span class="checkmark"></span>
                                                                </label>
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                                @if (count($item->terms) > 3 and empty($category_selected))
                                                    <button type="button"
                                                        class="btn btn-link btn-more-item">{{ __('More') }} <i
                                                            class="fa fa-caret-down"></i></button>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                                <!-- #Attributes -->

                                <div class="form-group">
                                    <button type="submit"
                                        class="btn btn-dark form-control p-0">{{ __('Search') }}</button>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade @if (request('term_type') == 'typology') show active @endif"
                            id="all-typology" role="tabpanel" aria-labelledby="all-typology-tab">
                            <form class="bravo_form_filter" action="{{ route('explore.index') }}">
                                <input type="hidden" name="term_type" value="typology">
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

                                <!-- Attributes -->
                                @php
                                    $typology = \Modules\Core\Models\Attributes::query()
                                        ->whereIn('service', menu_listing())
                                        ->where('slug', 'like', '%typology%')
                                        ->orderBy('name', 'asc')
                                        ->with([
                                            'terms' => function($q) {
                                                $q->orderBy('name');
                                            }, 
                                            'translation'
                                        ])
                                        ->get();
                                    $typology_selected = (array) Request::query('terms');
                                @endphp
                                @foreach ($typology as $item)
                                    @if (empty($item['hide_in_filter_search']))
                                        @php
                                            $translate = $item->translate();
                                        @endphp
                                        <div class="g-filter-item">
                                            <div class="item-title">
                                                <h3 class="text-capitalize"> {{ menu_listing_as($item->service) }} </h3>
                                                <label class="text-capitalize">{{ $translate->name }} </label>
                                                <i class="fa fa-angle-up" aria-hidden="true"></i>
                                            </div>
                                            <div class="item-content">
                                                <ul>
                                                    @foreach ($item->terms as $key => $term)
                                                        @php $translate = $term->translate(); @endphp
                                                        <li @if ($key > 2 and empty($typology_selected)) class="hide" @endif>
                                                            <div class="bravo-checkbox">
                                                                <label>
                                                                    <input
                                                                        @if (in_array($term->id, $typology_selected)) checked @endif
                                                                        type="checkbox" name="terms[]"
                                                                        value="{{ $term->id }}">
                                                                    {!! $translate->name !!}
                                                                    <span class="checkmark"></span>
                                                                </label>
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                                @if (count($item->terms) > 3 and empty($typology_selected))
                                                    <button type="button"
                                                        class="btn btn-link btn-more-item">{{ __('More') }} <i
                                                            class="fa fa-caret-down"></i></button>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                                <!-- #Attributes -->

                                <div class="form-group">
                                    <button type="submit"
                                        class="btn btn-dark form-control p-0">{{ __('Search') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
