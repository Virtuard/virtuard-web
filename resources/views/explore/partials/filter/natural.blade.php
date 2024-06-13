<div class="card card-explore">
    <div class="card-body">
        <div class="bravo_search_tour">
            <div class="bravo_filter">
                <form class="bravo_form_filter" action="{{ route('explore.index') }}">
                    <input type="hidden" name="service_type" value="natural">
                    <div class="g-filter-item">
                        <div class="item-title">
                            <h3>{{ __('Sort by') }}</h3>
                        </div>
                        <div class="item-content">
                            <div class="form-group">
                                <select name="order" class="form-control">
                                    <option value="id" selected>Last</option>
                                    <option value="review_score">Top Rated</option>
                                    <option value="">Random</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="g-filter-item">
                        <div class="item-title">
                            <h3>{{ __('Review Score') }}</h3>
                            <i class="fa fa-angle-up" aria-hidden="true"></i>
                        </div>
                        <div class="item-content">
                            <ul>
                                @for ($number = 5; $number >= 1; $number--)
                                    <li>
                                        <div class="bravo-checkbox">
                                            <label>
                                                <input name="review_score[]" type="checkbox" value="{{ $number }}"
                                                    @if (in_array($number, request()->query('review_score', []))) checked @endif>
                                                <span class="checkmark"></span>
                                                @for ($review_score = 1; $review_score <= $number; $review_score++)
                                                    <i class="fa fa-star"></i>
                                                @endfor
                                            </label>
                                        </div>
                                    </li>
                                @endfor
                            </ul>
                        </div>
                    </div>
                    <!-- Attributes -->
                    @php
                        $attributes = \Modules\Core\Models\Attributes::where('service', 'natural')
                            ->orderBy('position', 'desc')
                            ->with(['terms', 'translation'])
                            ->get();
                        $selected = (array) Request::query('terms');
                    @endphp
                    @foreach ($attributes as $item)
                        @if (empty($item['hide_in_filter_search']))
                            @php
                                $translate = $item->translate();
                            @endphp
                            <div class="g-filter-item">
                                <div class="item-title">
                                    <h3> {{ $translate->name }} </h3>
                                    <i class="fa fa-angle-up" aria-hidden="true"></i>
                                </div>
                                <div class="item-content">
                                    <ul>
                                        @foreach ($item->terms as $key => $term)
                                            @php $translate = $term->translate(); @endphp
                                            <li @if ($key > 2 and empty($selected)) class="hide" @endif>
                                                <div class="bravo-checkbox">
                                                    <label>
                                                        <input @if (in_array($term->id, $selected)) checked @endif
                                                            type="checkbox" name="terms[]" value="{{ $term->id }}">
                                                        {!! $translate->name !!}
                                                        <span class="checkmark"></span>
                                                    </label>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                    @if (count($item->terms) > 3 and empty($selected))
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
                            <div class="form-group">
                                <input type="text" name="service_name" placeholder="Keyword search" class="form-control">
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
