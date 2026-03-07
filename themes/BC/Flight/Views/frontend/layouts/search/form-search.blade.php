<form action="{{ route("flight.search") }}" class="form bravo_form" method="get">
    <div class="g-field-search">
        <div class="row d-flex align-items-center">
            @php
                $flight_search_fields = setting_item_array('flight_search_fields');
                $flight_search_fields = array_values(\Illuminate\Support\Arr::sort($flight_search_fields, function ($value) {
                    return $value['position'] ?? 0;
                }));

                $flight_search_fields[] = [
                    'title' => 'Location',
                    'field' => 'location',
                    'size' => '4',
                    'position' => '5',
                ];
                $flight_search_fields[] = [
                    'title' => 'Nearby',
                    'field' => 'Nearby',
                    'size' => '4',
                    'position' => '6',
                ];
                unset($flight_search_fields[0], $flight_search_fields[1], $flight_search_fields[3]);
            @endphp
            @if(!empty($flight_search_fields))
                @foreach($flight_search_fields as $field)
                    @php
                        $field['title'] = $field['title_'.app()->getLocale()] ?? $field['title'] ?? "";
                        $field['size'] = "4";
                    @endphp
                    <div class="col-md-{{ $field['size'] ?? "4" }} border-right">
                        @switch($field['field'])
                            @case ('service_name')
                                @include('Flight::frontend.layouts.search.fields.service_name')
                            @break
                            @case ('location')
                                @include('Flight::frontend.layouts.search.fields.location')
                            @break
                            @case ('date')
                                @include('Flight::frontend.layouts.search.fields.date')
                            @break
                            @case ('Nearby')
                            @include('Flight::frontend.layouts.search.fields.range')
                        @break
                        @endswitch
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    <div class="g-button-submit">
        <button class="btn btn-primary btn-search" type="submit">{{__("Search")}}</button>
    </div>
</form>
