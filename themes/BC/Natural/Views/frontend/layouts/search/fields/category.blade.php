@php($location_search_style = setting_item('natural_location_search_style'))

<div class="form-group">
    <i class="field-icon fa icofont-list"></i>
    <div class="form-content">
        <label> Category </label>
        <?php
        $category_name = "";
        $list_json = [];
        $categories = \Modules\Natural\Models\NaturalCategory::where('status', 'publish')->get();
        foreach ($categories as $category) {
            $list_json[] = [
                'id' => $category->id,
                'title' => $category->name,
            ];
        }
        ?>
        <div class="smart-search">
            <input type="text" class="smart-search-location parent_text form-control" {{ ( empty(setting_item("natural_location_search_style")) or setting_item("natural_location_search_style") == "normal" ) ? "readonly" : ""  }} placeholder="{{__("Select Category")}}" value="{{ $category_name }}" data-onLoad="{{__("Loading...")}}"
                   data-default="{{ json_encode($list_json) }}">
            <input type="hidden" class="child_id" name="category_id" value="{{Request::query('category_id')}}">
        </div>
    </div>
</div>
