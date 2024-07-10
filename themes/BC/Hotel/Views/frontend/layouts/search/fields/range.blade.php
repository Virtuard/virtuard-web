<div class="form-group">
    <div class="form-content" style="padding: 20px 0 10px 10px">
        <label class="mb-2 font-weight-bold">Proximity <span id="hotel_proximity_text">0</span> mil</label>
        <div class="input-search">
            <input type="range" id="hotel_search_radius" name="search_radius" min="0" max="500"
                class="w-100 cursor-pointer" value="0" />
        </div>
    </div>
</div>

@push('js')
    <script>
        const hotelSearchRadiusInput = document.getElementById('hotel_search_radius');
        const hotelProximityText = document.getElementById('hotel_proximity_text');
        hotelSearchRadiusInput.value = "0";

        hotelSearchRadiusInput.addEventListener('input', function() {
            hotelProximityText.textContent = this.value;
        });
    </script>
@endpush