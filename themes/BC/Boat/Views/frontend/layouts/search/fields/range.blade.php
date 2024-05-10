<div class="form-group">
    <div class="form-content" style="padding: 20px 0 10px 10px">
        <label class="mb-2 font-weight-bold">Proximity <span id="boat_proximity_text">0</span> km</label>
        <div class="input-search">
            <input type="range" id="boat_search_radius" name="search_radius" min="0" max="500"
                class="w-100 cursor-pointer" value="0" />
        </div>
    </div>
</div>

@push('js')
    <script>
        const artSearchRadiusInput = document.getElementById('boat_search_radius');
        const artProximityText = document.getElementById('boat_proximity_text');
        artSearchRadiusInput.value = "0";

        artSearchRadiusInput.addEventListener('input', function() {
            artProximityText.textContent = this.value;
        });
    </script>
@endpush