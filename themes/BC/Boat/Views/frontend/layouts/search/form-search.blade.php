<form action="{{ route("boat.search") }}" class="form bravo_form" method="get">
    <div class="g-field-search">
        <div class="row d-flex align-items-center">
            @php
                $boat_search_fields = setting_item_array('boat_search_fields');

                $boat_search_fields = array_values(\Illuminate\Support\Arr::sort($boat_search_fields, function ($value) {
                    return $value['position'] ?? 0;
                }));

                $boat_search_fields[] = [
                    'title' => 'Nearby',
                    'field' => 'Nearby',
                    'size' => '4',
                    'position' => '3',
                ];
            @endphp
            @if(!empty($boat_search_fields))
                @foreach($boat_search_fields as $field)
                    @php
                        $field['title'] = $field['title_'.app()->getLocale()] ?? $field['title'] ?? "";
                        $field['size'] = '4';
                    @endphp
                    <div class="col-md-{{ $field['size'] ?? "6" }} border-right">
                        @switch($field['field'])
                            @case ('service_name')
                                @include('Boat::frontend.layouts.search.fields.service_name')
                            @break
                            @case ('location')
                                @include('Boat::frontend.layouts.search.fields.location')
                            @break
                            @case ('date')
                                @include('Boat::frontend.layouts.search.fields.date')
                            @break
                            @case ('Nearby')
                                @include('Boat::frontend.layouts.search.fields.range')
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
