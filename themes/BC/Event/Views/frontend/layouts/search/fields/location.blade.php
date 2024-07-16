<div class="filter-item">
    <div class="form-group">
        <div class="form-content" style="padding: 20px 0 10px 10px">
            <label>Location</label>
            <div class="g-map-place">
                <input type="text" name="map_place" placeholder="{{__("Location...")}}" class="form-control border-0"
                style="height: 25px; padding: 0.375rem 0;"
                >
                <input type="hidden" name="map_lat" value="{{request()->input('map_lat')}}">
                <input type="hidden" name="map_lgn" value="{{request()->input('map_lgn')}}">
            </div>
        </div>
    </div>
</div>