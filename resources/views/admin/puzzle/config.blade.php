@extends('admin.layouts.app')
@section('content')
    <h2 class="title-bar no-border-bottom">
        {{ $page_title }}
    </h2>

    @include('admin.message')

    <div class="booking-history-manager mt-3">
        <form action="{{ route('admin.puzzle.config.store') }}" method="POST">
            @csrf
            <div class="panel">
                <div class="panel-title"><strong>{{ __('Android Configuration') }}</strong></div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('Android Package Name') }} <span class="text-danger">*</span></label>
                                <input type="text" 
                                       name="android_package" 
                                       value="{{ old('android_package', $row->android_package ?? '') }}"
                                       class="form-control" 
                                       placeholder="com.antoniorutilio.puzzle"
                                       required>
                                <small class="form-text text-muted">{{ __('Example: com.antoniorutilio.puzzle') }}</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('Android Deep Link Scheme') }} <span class="text-danger">*</span></label>
                                <input type="text" 
                                       name="android_deep_link_scheme" 
                                       value="{{ old('android_deep_link_scheme', $row->android_deep_link_scheme ?? 'https') }}"
                                       class="form-control" 
                                       placeholder="https"
                                       required>
                                <small class="form-text text-muted">{{ __('Usually: https') }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>{{ __('Android Play Store Link') }} <span class="text-danger">*</span></label>
                        <input type="url" 
                               name="android_store_link" 
                               value="{{ old('android_store_link', $row->android_store_link ?? '') }}"
                               class="form-control" 
                               placeholder="https://play.google.com/store/apps/details?id=com.antoniorutilio.puzzle"
                               required>
                        <small class="form-text text-muted">{{ __('Full URL to your app on Google Play Store') }}</small>
                    </div>
                </div>
            </div>

            <div class="panel mt-3">
                <div class="panel-title"><strong>{{ __('iOS Configuration') }}</strong></div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('iOS App ID') }}</label>
                                <input type="text" 
                                       name="ios_app_id" 
                                       value="{{ old('ios_app_id', $row->ios_app_id ?? '') }}"
                                       class="form-control" 
                                       placeholder="123456789">
                                <small class="form-text text-muted">{{ __('Your iOS App ID from App Store Connect') }}</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('iOS Deep Link Scheme') }}</label>
                                <input type="text" 
                                       name="ios_deep_link_scheme" 
                                       value="{{ old('ios_deep_link_scheme', $row->ios_deep_link_scheme ?? 'virtuardpuzzle') }}"
                                       class="form-control" 
                                       placeholder="virtuardpuzzle">
                                <small class="form-text text-muted">{{ __('Custom URL scheme for iOS deep linking') }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>{{ __('iOS App Store Link') }}</label>
                        <input type="url" 
                               name="ios_store_link" 
                               value="{{ old('ios_store_link', $row->ios_store_link ?? '') }}"
                               class="form-control" 
                               placeholder="https://apps.apple.com/app/id123456789">
                        <small class="form-text text-muted">{{ __('Full URL to your app on App Store') }}</small>
                    </div>
                </div>
            </div>

            <div class="panel mt-3">
                <div class="panel-title"><strong>{{ __('Web Game Configuration') }}</strong></div>
                <div class="panel-body">
                    <div class="form-group">
                        <label>{{ __('Web Game URL') }}</label>
                        <input type="url" 
                               name="web_game_url" 
                               value="{{ old('web_game_url', $row->web_game_url ?? '') }}"
                               class="form-control" 
                               placeholder="https://game.example.com">
                        <small class="form-text text-muted">{{ __('URL to web version of the game (optional)') }}</small>
                    </div>
                </div>
            </div>

            <div class="panel mt-3">
                <div class="panel-title"><strong>{{ __('Status') }}</strong></div>
                <div class="panel-body">
                    <div class="form-group">
                        <label>
                            <input type="checkbox" 
                                   name="is_active" 
                                   value="1"
                                   {{ old('is_active', $row->is_active ?? true) ? 'checked' : '' }}>
                            {{ __('Active') }}
                        </label>
                        <small class="form-text text-muted d-block">{{ __('Enable or disable puzzle AR smart links') }}</small>
                    </div>
                </div>
            </div>

            <div class="form-group mt-3">
                <button type="submit" class="btn btn-primary">{{ __('Save Configuration') }}</button>
            </div>
        </form>
    </div>
@endsection
