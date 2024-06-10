@if (is_default_lang())
    <div class="panel">
        <div class="panel-title"><strong>{{ __('Extra Info') }}</strong></div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>{{ __('No. Room') }}</label>
                        <input type="number" value="{{ $row->room }}" placeholder="{{ __('Example: 3') }}" name="room" class="form-control">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>{{ __('Hotel chain') }}</label>
                        <input type="text" value="{{ $row->hotel_chain }}" placeholder="{{ __('Hotel chain') }}" name="hotel_chain" class="form-control">
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
