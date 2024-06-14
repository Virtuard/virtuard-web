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
                        <label>{{ __('Square meters') }}</label>
                        <input type="number" value="{{ $row->square }}" placeholder="{{ __('Example: 100') }}" name="square" class="form-control">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>{{ __('Engineering or Architecture') }}</label>
                        <input type="number" value="{{ $row->engineering }}" placeholder="{{ __('Engineering or Architecture Study') }}" name="engineering" class="form-control">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>{{ __('Software used') }}</label>
                        <input type="text" value="{{ $row->software }}" placeholder="{{ __('Software used') }}" name="software" class="form-control">
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
        </div>
    </div>
@endif
