@extends('admin.layouts.app')

@section('content')
    <form
        action="{{ route('user.admin.plan_report.store', ['id' => $row->id ? $row->id : '-1', 'lang' => request()->query('lang')]) }}"
        method="post">
        @csrf
        <div class="container-fluid">
            <div class="d-flex justify-content-between mb20">
                <div class="">
                    <h1 class="title-bar">{{ $row->id ? __('Edit: ') . $row->title : __('Add new plan') }}</h1>
                </div>
            </div>
            @include('admin.message')
            @if ($row->id)
                @include('Language::admin.navigation')
            @endif
            <div class="lang-content-box">
                <div class="row">
                    <div class="col-md-9">
                        <div class="panel">
                            <div class="panel-title"><strong>{{ __('Content') }}</strong></div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <label>User</label>
                                    <?php
                                    $user = $row->user;
                                    \App\Helpers\AdminForm::select2(
                                        'author_id',
                                        [
                                            'configs' => [
                                                'ajax' => [
                                                    'url' => route('user.admin.getForSelect2'),
                                                    'dataType' => 'json',
                                                ],
                                                'allowClear' => true,
                                                'placeholder' => __('-- Select User --'),
                                            ],
                                        ],
                                        !empty($user->id) ? [$user->id, $user->getDisplayName() . ' (#' . $user->id . ')'] : false,
                                    );
                                    ?>
                                </div>
                                <div class="form-group">
                                    <label>Plan</label>
                                    <select name="plan_id" class="form-control">
                                        <option value="">--Select Plan--</option>
                                        @foreach ($plans as $plan)
                                            <option value="{{ $plan->id }}" {{ $plan->id == $row->plan_id ? 'selected' : '' }}>{{ $plan->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @if ($row->id)
                                    <div class="form-group">
                                        <label>{{ __('End Date') }}</label>
                                        <input type="text" value="{{ old('end_date', $row->end_date) }}"
                                            placeholder="{{ __('YYYY/MM/DD') }}" name="end_date"
                                            class="form-control has-datepicker">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="panel">
                            <div class="panel-title"><strong>{{ __('Publish') }}</strong></div>
                            <div class="panel-body">
                                @if (is_default_lang())
                                    <div>
                                        <label>
                                            <input @if ($row->status == '1') checked @endif type="radio"
                                                name="status" value="1"> {{ __('Publish') }}
                                        </label>
                                    </div>
                                    <div>
                                        <label>
                                            <input @if ($row->status != '1') checked @endif type="radio"
                                                name="status" value="0"> {{ __('Pending') }}
                                        </label>
                                    </div>
                                @endif
                                <div class="text-right">
                                    <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i>
                                        {{ __('Save Changes') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
@push('js')
    <link rel="stylesheet" href="{{ asset('libs/daterange/daterangepicker.css') }}">
    <script src="{{ asset('libs/daterange/moment.min.js') }}"></script>
    <script src="{{ asset('libs/daterange/daterangepicker.min.js') }}"></script>
    <script>
        $('.has-datepicker').daterangepicker({
            singleDatePicker: true,
            showCalendar: false,
            autoUpdateInput: false, //disable default date
            sameDate: true,
            autoApply: true,
            disabledPast: true,
            enableLoading: true,
            showEventTooltip: true,
            classNotAvailable: ['disabled', 'off'],
            disableHightLight: true,
            locale: {
                format: 'YYYY/MM/DD'
            }
        }).on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY/MM/DD'));
        });
    </script>
@endpush
