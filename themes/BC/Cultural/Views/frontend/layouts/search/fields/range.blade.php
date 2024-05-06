<div class="form-group">
    <div class="form-content" style="padding: 20px 0 10px 10px">
        <label class="mb-2 font-weight-bold">Proximity <span id="cultural_proximity_text">0</span> km</label>
        <div class="input-search">
            <input type="range" id="cultural_search_radius" name="search_radius" min="0" max="500"
                class="w-100 cursor-pointer" value="0" />
        </div>
    </div>
</div>

@push('js')
    <script>
        const culturalSearchRadiusInput = document.getElementById('cultural_search_radius');
        const culturalProximityText = document.getElementById('cultural_proximity_text');
        culturalSearchRadiusInput.value = "0";

        culturalSearchRadiusInput.addEventListener('input', function() {
            culturalProximityText.textContent = this.value;
        });
    </script>
@endpush