@php($location_search_style = setting_item('space_location_search_style'))

<div class="form-group">
    <i class="field-icon fa icofont-map"></i>
    <div class="form-content">
        <label> Category </label>
        @if($location_search_style=='autocompletePlace')
            <div class="g-map-place" >
                <input type="text" name="map_place" placeholder="{{__("Select category")}}"  value="{{request()->input('map_place')}}" class="form-control border-0">
                <div class="map d-none" id="map-{{\Illuminate\Support\Str::random(10)}}"></div>
                <input type="hidden" name="map_lat" value="{{request()->input('map_lat')}}">
                <input type="hidden" name="map_lgn" value="{{request()->input('map_lgn')}}">
            </div>

        @else
        <?php
        $location_name = "";
        $list_json = [];
        $list_json[] = [
            'id' => 1,
            'title' => 'Sales',
        ];
        $list_json[] = [
            'id' => 2,
            'title' => 'Rent',
        ];
        $list_json[] = [
            'id' => 3,
            'title' => 'Showroom',
        ];
        ?>
        <div class="smart-search">
            <input type="text" class="smart-search-location parent_text form-control" {{ ( empty(setting_item("space_location_search_style")) or setting_item("space_location_search_style") == "normal" ) ? "readonly" : ""  }} placeholder="{{__("Where are you going?")}}" value="{{ $location_name }}" data-onLoad="{{__("Loading...")}}"
                   data-default="{{ json_encode($list_json) }}">
            <input type="hidden" class="child_id" name="category_id" value="{{Request::query('category_id')}}">
        </div>
            @endif
    </div>
</div>
