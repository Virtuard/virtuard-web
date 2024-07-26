<form action="{{ route("art.search") }}" class="form bravo_form" method="get">
    <div class="g-field-search">
        {{-- <div class="row d-flex align-items-center">
            @php $art_search_fields = setting_item_array('art_search_fields');
            $art_search_fields = array_values(\Illuminate\Support\Arr::sort($art_search_fields, function ($value) {
                return $value['position'] ?? 0;
            }));
            @endphp
            @if(!empty($art_search_fields))
                @foreach($art_search_fields as $field)
                    @php
                        $field['title'] = $field['title_'.app()->getLocale()] ?? $field['title'] ?? "";
                    @endphp
                    <div class="col-md-{{ $field['size'] ?? "4" }} border-right">
                        @switch($field['field'])
                            @case ('service_name')
                                @include('Art::frontend.layouts.search.fields.service_name')
                            @break
                            @case ('location')
                                @include('Art::frontend.layouts.search-map.fields.location')
                            @break
                            @case ('date')
                                @include('Art::frontend.layouts.search.fields.category')
                            @break
                            @case ('guests')
                                @include('Art::frontend.layouts.search.fields.range')
                            @break
                        @endswitch
                    </div>
                @endforeach
            @endif
        </div> --}}

        <div class="row d-flex align-items-center">
            <div class="col-md-2 border-right">
                @include('Art::frontend.layouts.search.fields.service_name')
            </div>
            <div class="col-md-3 border-right">
                @include('partials.search.fields.location')
            </div>
            <div class="col-md-2 border-right">
                @include('Art::frontend.layouts.search.fields.range')
            </div>
            <div class="col-md-3 border-right">
                @include('Art::frontend.layouts.search.fields.category')
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
