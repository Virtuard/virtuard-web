<form action="{{ route("business.search") }}" class="form bravo_form" method="get">
    <div class="g-field-search">
        <div class="row d-flex align-items-center">
            @php $business_search_fields = setting_item_array('business_search_fields');
            $business_search_fields = array_values(\Illuminate\Support\Arr::sort($business_search_fields, function ($value) {
                return $value['position'] ?? 0;
            }));
            @endphp
            @if(!empty($business_search_fields))
                @foreach($business_search_fields as $field)
                    @php $field['title'] = $field['title_'.app()->getLocale()] ?? $field['title'] ?? "" @endphp
                    <div class="col-md-{{ $field['size'] ?? "4" }} border-right">
                        @switch($field['field'])
                            @case ('service_name')
                                @include('Business::frontend.layouts.search.fields.service_name')
                            @break
                            @case ('location')
                                @include('Business::frontend.layouts.search.fields.location')
                            @break
                            @case ('date')
                                @include('Business::frontend.layouts.search.fields.date')
                            @break
                            @case ('guests')
                                @include('Business::frontend.layouts.search.fields.range')
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
