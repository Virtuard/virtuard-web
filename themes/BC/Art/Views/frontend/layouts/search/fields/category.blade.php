@php($location_search_style = setting_item('art_location_search_style'))

<div class="form-group">
    <i class="field-icon fa icofont-list"></i>
    <div class="form-content">
        <label> Category </label>
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
            <input type="text" class="smart-search-location parent_text form-control" {{ ( empty(setting_item("art_location_search_style")) or setting_item("art_location_search_style") == "normal" ) ? "readonly" : ""  }} placeholder="{{__("Select Category")}}" value="{{ $location_name }}" data-onLoad="{{__("Loading...")}}"
                   data-default="{{ json_encode($list_json) }}">
            <input type="hidden" class="child_id" name="category_id" value="{{Request::query('category_id')}}">
        </div>
    </div>
</div>
