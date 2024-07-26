<div class="form-group">
    <div class="form-content" style="padding: 20px 0 10px 10px">
        <label>{{ __('Filter 360') }}</label>
        <div class="smart-search smart-search-category">
            <select name="is_ipanorama" class="form-control" style="width: 100%;">
                <option value="">{{ __('All') }}</option>
                <option value="1" @if(request('is_ipanorama') == '1') selected @endif>{{ __('360 Only') }}</option>
            </select>
        </div>
    </div>
</div>