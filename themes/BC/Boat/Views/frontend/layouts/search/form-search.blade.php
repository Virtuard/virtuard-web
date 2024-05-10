<form action="{{ route("boat.search") }}" class="form bravo_form" method="get">
    <div class="g-field-search">
        {{-- <div class="row d-flex align-items-center">
            @php
                $boat_search_fields = setting_item_array('boat_search_fields');
                $boat_search_fields = array_values(\Illuminate\Support\Arr::sort($boat_search_fields, function ($value) {
                    return $value['position'] ?? 0;
                }));
            @endphp
            @if(!empty($boat_search_fields))
                @foreach($boat_search_fields as $field)
                    @php
                        $field['title'] = $field['title_'.app()->getLocale()] ?? $field['title'] ?? "";
                    @endphp
                    <div class="col-md-{{ $field['size'] ?? "4" }} border-right">
                        @switch($field['field'])
                            @case ('service_name')
                                @include('Boat::frontend.layouts.search.fields.service_name')
                            @break
                            @case ('location')
                                @include('Boat::frontend.layouts.search-map.fields.location')
                            @break
                            @case ('date')
                                @include('Boat::frontend.layouts.search.fields.category')
                            @break
                            @case ('guests')
                                @include('Boat::frontend.layouts.search.fields.range')
                            @break
                        @endswitch
                    </div>
                @endforeach
            @endif
        </div> --}}

        <div class="row d-flex align-items-center">
            <div class="col-md-4 border-right">
                @include('Boat::frontend.layouts.search.fields.service_name')
            </div>
            <div class="col-md-4 border-right">
                @include('Boat::frontend.layouts.search.fields.location')
            </div>
            <div class="col-md-4 border-right">
                @include('Boat::frontend.layouts.search.fields.category')
            </div>
        </div>
    </div>
    <div class="g-button-submit">
        <button class="btn btn-primary btn-search" type="submit">{{__("Search")}}</button>
    </div>
</form>
