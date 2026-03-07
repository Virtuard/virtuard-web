@if (isset($categories))
    <div class="panel">
        <div class="panel-title"><strong>{{ __('Category') }}</strong></div>
        <div class="panel-body">
            <fieldset class="form-group">
                <div class="form-group">
                    <label class="control-label">{{ __('Category') }}</label>
                    <div class="">
                        <select name="category_id" class="form-control">
                            <option value="">{{ __('-- Select Category--') }}</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ $row->icategory_id == $category->id ? 'selected' : '' }}>
                                    {{ $category->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
@endif
