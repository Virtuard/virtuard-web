@if (is_default_lang())
    <div class="panel">
        <div class="panel-title"><strong>{{ __('Extra Info') }}</strong></div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>{{ __('Franchising company') }}</label>
                        <input type="text" value="{{ $row->franchising }}" placeholder="{{ __('Franchising company') }}" name="franchising" class="form-control">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>{{ __('Phone') }}</label>
                        <input type="text" value="{{ $row->phone }}" placeholder="{{ __('Phone') }}" name="phone" class="form-control">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>{{ __('Website') }}</label>
                        <input type="text" value="{{ $row->website }}" placeholder="{{ __('Website') }}" name="website" class="form-control">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
