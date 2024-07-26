<form action="{{ route("space.search") }}" class="form bravo_form" method="get">
    <div class="g-field-search">
        {{-- <div class="row d-flex align-items-center">
            @php $space_search_fields = setting_item_array('space_search_fields');
            $space_search_fields = array_values(\Illuminate\Support\Arr::sort($space_search_fields, function ($value) {
                return $value['position'] ?? 0;
            }));
            @endphp
            @if(!empty($space_search_fields))
                @foreach($space_search_fields as $field)
                    @php $field['title'] = $field['title_'.app()->getLocale()] ?? $field['title'] ?? "" @endphp
                    <div class="col-md-{{ $field['size'] ?? "4" }} border-right">
                        @switch($field['field'])
                            @case ('service_name')
                                @include('Space::frontend.layouts.search.fields.service_name')
                            @break
                            @case ('location')
                                @include('Space::frontend.layouts.search-map.fields.location')
                            @break
                            @case ('date')
                                @include('Space::frontend.layouts.search.fields.date')
                            @break
                            @case ('guests')
                                @include('Space::frontend.layouts.search.fields.range')
                            @break
                        @endswitch
                    </div>
                @endforeach
            @endif
        </div> --}}

        <div class="row d-flex align-items-center">
            <div class="col-md-2 border-right">
                @include('Space::frontend.layouts.search.fields.service_name')
            </div>
            <div class="col-md-3 border-right">
                @include('partials.search.fields.location')
            </div>
            <div class="col-md-2 border-right">
                @include('Space::frontend.layouts.search.fields.range')
            </div>
            <div class="col-md-3 border-right">
                @include('Space::frontend.layouts.search.fields.category')
            </div>
            <div class="col-md-2 border-right">
                @include('partials.search.fields.ipanorama')
            </div>
        </div>
    </div>
    <div class="g-button-submit">
        <button class="btn btn-primary btn-search" type="submit">{{__("Search")}}</button>
    </div>
</form>
