<div class="form-group">
    <div class="form-content" style="padding: 20px 0 10px 10px">
        <label class="mb-2 font-weight-bold">Proximity <span id="business_proximity_text">0</span> mil</label>
        <div class="input-search">
            <input type="range" id="business_search_radius" name="search_radius" min="0" max="500"
                class="w-100 cursor-pointer" value="0" />
        </div>
    </div>
</div>

@push('js')
    <script>
        const businessSearchRadiusInput = document.getElementById('business_search_radius');
        const businessProximityText = document.getElementById('business_proximity_text');
        businessSearchRadiusInput.value = "0";

        businessSearchRadiusInput.addEventListener('input', function() {
            businessProximityText.textContent = this.value;
        });
    </script>
@endpush