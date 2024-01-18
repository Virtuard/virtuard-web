<form action="{{ route("cultural.search") }}" class="form bravo_form" method="get">
    <div class="g-field-search">
        <div class="row d-flex align-items-center">
            @php $cultural_search_fields = setting_item_array('cultural_search_fields');
            $cultural_search_fields = array_values(\Illuminate\Support\Arr::sort($cultural_search_fields, function ($value) {
                return $value['position'] ?? 0;
            }));

            $cultural_search_fields[] = [
                "title" => "Nearby",
                "field" => "Nearby",
                "size" => "4",
                "position" => "3",
            ];
            @endphp
            @if(!empty($cultural_search_fields))
                @foreach($cultural_search_fields as $field)
                    @php
                        $field['title'] = $field['title_'.app()->getLocale()] ?? $field['title'] ?? "";
                        $field['size'] = "4";
                    @endphp
                    <div class="col-md-{{ $field['size'] ?? "6" }} border-right">
                        @switch($field['field'])
                            @case ('service_name')
                                @include('Event::frontend.layouts.search.fields.service_name')
                            @break
                            @case ('location')
                                @include('Event::frontend.layouts.search.fields.location')
                            @break
                            @case ('date')
                                @include('Event::frontend.layouts.search.fields.date')
                            @break
                            @case ('Nearby')
                                @include('Event::frontend.layouts.search.fields.range')
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
