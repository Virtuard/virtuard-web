<div class="form-group">
    <div class="form-content" style="padding: 20px 0 10px 10px">
        <label class="mb-2 font-weight-bold">Proximity <span id="car_proximity_text">0</span> mil</label>
        <div class="input-search">
            <input type="range" id="car_search_radius" name="search_radius" min="0" max="500"
                class="w-100 cursor-pointer" value="0" />
        </div>
    </div>
</div>

@push('js')
    <script>
        const carSearchRadiusInput = document.getElementById('car_search_radius');
        const carProximityText = document.getElementById('car_proximity_text');
        carSearchRadiusInput.value = "0";

        carSearchRadiusInput.addEventListener('input', function() {
            carProximityText.textContent = this.value;
        });
    </script>
@endpush