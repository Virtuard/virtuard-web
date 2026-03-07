<div class="form-group">
    <div class="form-content" style="padding: 20px 0 10px 10px">
        <label class="mb-2 font-weight-bold">Proximity <span id="event_proximity_text">0</span> mil</label>
        <div class="input-search">
            <input type="range" id="event_search_radius" name="search_radius" min="0" max="500"
                class="w-100 cursor-pointer" value="0" />
        </div>
    </div>
</div>

@push('js')
    <script>
        const eventSearchRadiusInput = document.getElementById('event_search_radius');
        const eventProximityText = document.getElementById('event_proximity_text');
        eventSearchRadiusInput.value = "0";

        eventSearchRadiusInput.addEventListener('input', function() {
            eventProximityText.textContent = this.value;
        });
    </script>
@endpush