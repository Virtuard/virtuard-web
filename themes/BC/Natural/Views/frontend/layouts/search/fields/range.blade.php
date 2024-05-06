<div class="form-group">
    <div class="form-content" style="padding: 20px 0 10px 10px">
        <label class="mb-2 font-weight-bold">Proximity <span id="natural_proximity_text">0</span> km</label>
        <div class="input-search">
            <input type="range" id="natural_search_radius" name="search_radius" min="0" max="500"
                class="w-100 cursor-pointer" value="0" />
        </div>
    </div>
</div>

@push('js')
    <script>
        const naturalSearchRadiusInput = document.getElementById('natural_search_radius');
        const naturalProximityText = document.getElementById('natural_proximity_text');
        naturalSearchRadiusInput.value = "0";

        naturalSearchRadiusInput.addEventListener('input', function() {
            naturalProximityText.textContent = this.value;
        });
    </script>
@endpush