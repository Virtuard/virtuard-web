<form action="{{ route("natural.search") }}" class="form bravo_form" method="get">
    <div class="g-field-search">
        {{-- <div class="row d-flex align-items-center">
            @php $natural_search_fields = setting_item_array('natural_search_fields');
            $natural_search_fields = array_values(\Illuminate\Support\Arr::sort($natural_search_fields, function ($value) {
                return $value['position'] ?? 0;
            }));
            @endphp
            @if(!empty($natural_search_fields))
                @foreach($natural_search_fields as $field)
                    @php
                        $field['title'] = $field['title_'.app()->getLocale()] ?? $field['title'] ?? "";
                    @endphp
                    <div class="col-md-{{ $field['size'] ?? "4" }} border-right">
                        @switch($field['field'])
                            @case ('service_name')
                                @include('Natural::frontend.layouts.search.fields.service_name')
                            @break
                            @case ('location')
                                @include('Natural::frontend.layouts.search.fields.location')
                            @break
                            @case ('date')
                                @include('Natural::frontend.layouts.search.fields.date')
                            @break
                            @case ('guests')
                                @include('Natural::frontend.layouts.search.fields.range')
                            @break
                        @endswitch
                    </div>
                @endforeach
            @endif
        </div> --}}
        <div class="row d-flex align-items-center">
            <div class="col-md-3 border-right">
                @include('Natural::frontend.layouts.search.fields.service_name')
            </div>
            <div class="col-md-3 border-right">
                @include('Natural::frontend.layouts.search.fields.location')
            </div>
            <div class="col-md-3 border-right">
                @include('Natural::frontend.layouts.search.fields.category')
            </div>
            <div class="col-md-3 border-right">
                @include('Natural::frontend.layouts.search.fields.range')
            </div>
        </div>
    </div>
    <div class="g-button-submit">
        <button class="btn btn-primary btn-search" type="submit">{{__("Search")}}</button>
    </div>
</form>
