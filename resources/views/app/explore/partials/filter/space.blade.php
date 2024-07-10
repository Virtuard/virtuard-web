<div class="card card-explore">
    <div class="card-body">
        <div class="bravo_search_tour">
            <div class="bravo_filter">
                <form id="form_explore_space" class="bravo_form_filter" action="{{ route('explore.index') }}">
                    <input type="hidden" name="service_type" value="space">
                    <div class="g-filter-item">
                        <div class="item-content">
                            <label>{{ __('Sort by') }}</label>
                            <div class="form-group">
                                <select name="orderby" class="form-control orderby">
                                    <option value="created_at">{{ __('Last') }}
                                    </option>
                                    <option value="rate_high_low">
                                        {{ __('Top Rated') }}
                                    </option>
                                    <option value="">{{ __('Random') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- Attributes -->
                    @php
                        $space_attributes = \Modules\Core\Models\Attributes::where('service', 'space')
                            ->orderBy('position', 'desc')
                            ->with(['terms', 'translation'])
                            ->get();
                        $space_attributes_selected = (array) Request::query('terms');
                    @endphp
                    @foreach ($space_attributes as $item)
                        @if (empty($item['hide_in_filter_search']))
                            @php
                                $translate = $item->translate();
                            @endphp
                            <div class="g-filter-item">
                                <div class="item-title">
                                    <label> {{ $translate->name }} </label>
                                    <i class="fa fa-angle-up" aria-hidden="true"></i>
                                </div>
                                <div class="item-content">
                                    <ul>
                                        @foreach ($item->terms as $key => $term)
                                            @php $translate = $term->translate(); @endphp
                                            <li @if ($key > 2 and empty($space_attributes_selected)) class="hide" @endif>
                                                <div class="bravo-checkbox">
                                                    <label>
                                                        <input @if (in_array($term->id, $space_attributes_selected)) checked @endif
                                                            type="checkbox" name="terms[]" value="{{ $term->id }}">
                                                        {!! $translate->name !!}
                                                        <span class="checkmark"></span>
                                                    </label>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                    @if (count($item->terms) > 3 and empty($space_attributes_selected))
                                        <button type="button" class="btn btn-link btn-more-item">{{ __('More') }} <i
                                                class="fa fa-caret-down"></i></button>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach
                    <!-- #Attributes -->
                    <div class="g-filter-item">
                        <div class="item-content">
                            <div class="form-group mt-3">
                                @php
                                    $space_ex = \Modules\Space\Models\Space::query()
                                        ->select('agency')
                                        ->where([['agency', '!=', ''], ['status', 'publish']])
                                        ->groupBy('agency')
                                        ->get();
                                @endphp
                                <label>{{ __('Agency') }}</label>
                                <div class="form-group">
                                    <select name="agency" class="form-control">
                                        <option value="">{{ __('--Select Agency--') }}</option>
                                        @foreach ($space_ex as $val)
                                            <option value="{{ $val->agency }}">{{ __($val->agency) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group mt-3">
                                @php
                                    $space_floorings = \Modules\Space\Models\Space::query()
                                        ->select('flooring')
                                        ->where([['flooring', '!=', ''], ['status', 'publish']])
                                        ->groupBy('flooring')
                                        ->get();
                                @endphp
                                <label>{{ __('Flooring') }}</label>
                                <select name="flooring" class="form-control">
                                    <option value="">{{ __('--Select Flooring--') }}</option>
                                    @foreach ($space_floorings as $val)
                                        <option value="{{ $val->flooring }}">{{ __($val->flooring) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mt-3">
                                @php
                                    $space_squares = \Modules\Space\Models\Space::query()
                                        ->select('square')
                                        ->where([['square', '!=', ''], ['status', 'publish']])
                                        ->groupBy('square')
                                        ->orderBy('square')
                                        ->get();
                                @endphp
                                <label>{{ __('Square Meters') }}</label>
                                <select name="square" class="form-control">
                                    <option value="">{{ __('--Select Square Meters--') }}</option>
                                    @foreach ($space_squares as $val)
                                        <option value="{{ $val->square }}">{{ __($val->square) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mt-3">
                                @php
                                    $space_rooms = \Modules\Space\Models\Space::query()
                                        ->select('room')
                                        ->where([['room', '!=', ''], ['status', 'publish']])
                                        ->groupBy('room')
                                        ->orderBy('room')
                                        ->get();
                                @endphp
                                <label>{{ __('Rooms') }}</label>
                                <select name="room" class="form-control">
                                    <option value="">{{ __('--Select Rooms--') }}</option>
                                    @foreach ($space_rooms as $val)
                                        <option value="{{ $val->room }}">{{ __($val->room) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mt-3">
                                @php
                                    $space_beds = \Modules\Space\Models\Space::query()
                                        ->select('bed')
                                        ->where([['bed', '!=', ''], ['status', 'publish']])
                                        ->groupBy('bed')
                                        ->orderBy('bed')
                                        ->get();
                                @endphp
                                <label>{{ __('Bedrooms') }}</label>
                                <select name="bed" class="form-control">
                                    <option value="">{{ __('--Select Bedrooms--') }}</option>
                                    @foreach ($space_beds as $val)
                                        <option value="{{ $val->bed }}">{{ __($val->bed) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mt-3">
                                @php
                                    $space_bathrooms = \Modules\Space\Models\Space::query()
                                        ->select('bathroom')
                                        ->where([['bathroom', '!=', ''], ['status', 'publish']])
                                        ->groupBy('bathroom')
                                        ->orderBy('bathroom')
                                        ->get();
                                @endphp
                                <label>{{ __('Bathrooms') }}</label>
                                <select name="bathroom" class="form-control">
                                    <option value="">{{ __('--Select Bathrooms--') }}</option>
                                    @foreach ($space_bathrooms as $val)
                                        <option value="{{ $val->bathroom }}">{{ __($val->bathroom) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mt-3">
                                <div class="smart-search d-flex justify-content-between align-items-center">
                                    <input type="text" class="form-control filter_map_place" id="space_map_place"
                                        name="map_place" placeholder="Place" data-id="space"
                                        style="border-top: none;border-left:none;border-right:none;">
                                    <button class="btn btn-sm" type="button" onclick="getLocation()">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M11.5397 22.351C11.57 22.3685 11.5937 22.3821 11.6105 22.3915L11.6384 22.4071C11.8613 22.5294 12.1378 22.5285 12.3608 22.4075L12.3895 22.3915C12.4063 22.3821 12.43 22.3685 12.4603 22.351C12.5207 22.316 12.607 22.265 12.7155 22.1982C12.9325 22.0646 13.2388 21.8676 13.6046 21.6091C14.3351 21.0931 15.3097 20.3274 16.2865 19.3273C18.2307 17.3368 20.25 14.3462 20.25 10.5C20.25 5.94365 16.5563 2.25 12 2.25C7.44365 2.25 3.75 5.94365 3.75 10.5C3.75 14.3462 5.76932 17.3368 7.71346 19.3273C8.69025 20.3274 9.66491 21.0931 10.3954 21.6091C10.7612 21.8676 11.0675 22.0646 11.2845 22.1982C11.393 22.265 11.4793 22.316 11.5397 22.351ZM12 13.5C13.6569 13.5 15 12.1569 15 10.5C15 8.84315 13.6569 7.5 12 7.5C10.3431 7.5 9 8.84315 9 10.5C9 12.1569 10.3431 13.5 12 13.5Z"
                                                fill="#0F172A"></path>
                                        </svg>
                                    </button>
                                </div>
                                <div class="form-group">
                                    <input type="hidden" id="space_map_lat" name="map_lat"
                                        class="form-control filter_map_lat">
                                    <input type="hidden" id="space_map_lgn" name="map_lgn"
                                        class="form-control filter_map_lgn">
                                </div>
                            </div>
                            <div class="form-group mt-3">
                                <div class="form-content">
                                    <label class="mb-2">{{ __('Proximity') }} <span
                                            id="space_proximity_text">0</span>
                                        mil</label>
                                    <div class="input-search">
                                        <input type="range" id="space_search_radius" name="search_radius"
                                            min="0" max="500"
                                            class="filter-search-radius w-100 cursor-pointer" value="0"
                                            data-id="space" />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mt-3">
                                <label>{{ __('Open') }}</label>
                                <select name="open" class="form-control opennow">
                                    <option value="all" {{ request('open') == 'all' ? 'selected' : '' }}>
                                        {{ __('All') }}
                                    </option>
                                    <option value="now" {{ request('open') == 'now' ? 'selected' : '' }}>
                                        {{ __('Now') }}
                                    </option>
                                </select>
                            </div>
                            <div class="form-group mt-3">
                                <label>{{ __('Keyword search') }}</label>
                                <input type="text" id="space_service_name" name="service_name"
                                    placeholder="Keyword search" class="form-control filter_service_name">
                            </div>
                            <div class="form-group mt-3">
                                <button type="submit"
                                    class="btn btn-dark form-control p-0">{{ __('Search') }}</button>
                                <a class="btn btn-secondary form-control p-0"
                                    href="{{ route('explore.index', ['service_type' => 'space']) }}"
                                    style="line-height: 2">{{ __('Reset Filter') }}</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
