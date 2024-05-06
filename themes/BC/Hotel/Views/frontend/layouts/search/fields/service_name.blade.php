{{-- <div class="form-group">
    <i class="field-icon fa icofont-search"></i>
    <div class="form-content">
        <label>{{ $field['title'] }}</label>
        <div class="input-search">
            <input type="text" name="service_name" class="form-control" placeholder="{{__("Search for...")}}" value="{{ request()->input("service_name") }}">
        </div>
    </div>
</div> --}}

<div class="filter-item">
    <div class="form-group">
        <div class="form-content" style="padding: 20px 0 10px 10px">
            <label>Service</label>
            <div class="g-map-place">
                <input type="text" style="height: 25px" name="map_place" placeholder="{{__("Search service...")}}" class="form-control border-0">
                <div class="map d-none" id="map-{{\Illuminate\Support\Str::random(10)}}"></div>
            </div>
        </div>
    </div>
</div>