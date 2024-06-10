@if (is_default_lang())
    <div class="panel">
        <div class="panel-title"><strong>{{ __('Extra Info') }}</strong></div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>{{ __('No. Room') }}</label>
                        <input type="number" value="{{ $row->room }}" placeholder="{{ __('Example: 3') }}" name="room" class="form-control">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>{{ __('No. Bed') }}</label>
                        <input type="number" value="{{ $row->bed }}" placeholder="{{ __('Example: 3') }}" name="bed" class="form-control">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>{{ __('No. Bathroom') }}</label>
                        <input type="number" value="{{ $row->bathroom }}" placeholder="{{ __('Example: 5') }}" name="bathroom" class="form-control">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>{{ __('No. Flooring') }}</label>
                        <input type="number" value="{{ $row->flooring }}" placeholder="{{ __('Example: 5') }}" name="flooring" class="form-control">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>{{ __('Square meters of land') }}</label>
                        <input type="number" value="{{ $row->square_land }}" placeholder="{{ __('Example: 100') }}" name="square_land" class="form-control">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>{{ __('Square meters built') }}</label>
                        <input type="number" value="{{ $row->square }}" placeholder="{{ __('Example: 100') }}" name="square" class="form-control">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>{{ __('Real Estate Agency') }}</label>
                        <input type="text" value="{{ $row->real_estate_agency }}" placeholder="{{ __('Real Estate Agency') }}" name="real_estate_agency" class="form-control">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>{{ __('Land Registry Category') }}</label>
                        <input type="text" value="{{ $row->land_registry_category }}" placeholder="{{ __('Land Registry Category') }}" name="land_registry_category" class="form-control">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>{{ __('Phone') }}</label>
                        <input type="text" value="{{ $row->phone }}" placeholder="{{ __('Phone') }}" name="phone" class="form-control">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>{{ __('Website') }}</label>
                        <input type="text" value="{{ $row->website }}" placeholder="{{ __('Website') }}" name="website" class="form-control">
                    </div>
                </div>
            </div>
            @if (is_default_lang())
                <div class="row d-none">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label class="control-label">{{ __('Minimum advance reservations') }}</label>
                            <input type="number" name="min_day_before_booking" class="form-control" value="{{ $row->min_day_before_booking }}" placeholder="{{ __('Ex: 3') }}">{{ __('Leave blank if you dont need to use the min day option') }}</i>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label class="control-label">{{ __('Minimum day stay requirements') }}</label>
                            <input type="number" name="min_day_stays" class="form-control" value="{{ $row->min_day_stays }}" placeholder="{{ __('Ex: 2') }}">{{ __('Leave blank if you dont need to set minimum day stay option') }}</i>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endif
