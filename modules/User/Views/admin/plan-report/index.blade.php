@extends('admin.layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between mb20">
            <h1 class="title-bar">{{__("Plan Report")}}</h1>
            <div class="title-actions">
                @if(empty($recovery))
                    <a href="{{route('user.admin.plan_report.create')}}" class="btn btn-primary">{{__("Add new plan")}}</a>
                @endif
            </div>
        </div>
        @include('admin.message')
        <div class="row">
            <div class="col-md-12">
                <div class="filter-div d-flex justify-content-between ">
                    <div class="col-left">
{{--                        @if(!empty($rows))--}}
{{--                            <form method="post" action="{{ route('user.admin.plan_report.bulkEdit')  }}"--}}
{{--                                  class="filter-form filter-form-left d-flex justify-content-start">--}}
{{--                                {{csrf_field()}}--}}
{{--                                <select name="action" class="form-control">--}}
{{--                                    <option value="">{{__(" Bulk Actions ")}}</option>--}}
{{--                                    <option value="publish">{{__(" Publish ")}}</option>--}}
{{--                                    <option value="draft">{{__(" Move to Draft ")}}</option>--}}
{{--                                </select>--}}
{{--                                <button data-confirm="{{__("Do you want to delete?")}}" class="btn-info btn btn-icon dungdt-apply-form-btn" type="button">{{__('Apply')}}</button>--}}
{{--                            </form>--}}
{{--                        @endif--}}
                    </div>
                    <div class="col-left">
                        <form method="get" action="" class="filter-form filter-form-right d-flex justify-content-end" role="search">
                            @if(is_admin())
                                <?php
                                $company = \App\User::find(Request()->input('create_user'));
                                \App\Helpers\AdminForm::select2('create_user', [
                                    'configs' => [
                                        'ajax'        => [
                                            'url' => route('user.admin.getForSelect2'),
                                            'dataType' => 'json'
                                        ],
                                        'allowClear'  => true,
                                        'placeholder' => __('-- Select User --')
                                    ]
                                ], !empty($company->id) ? [
                                    $company->id,
                                    $company->getDisplayName()
                                ] : false)
                                ?>
                            @endif
                                <select name="plan_id" class="form-control">
                                       <option value="">{{__(" All Plan ")}}</option>
                                    @foreach($plans as $plan)
                                       <option @if(Request()->plan_id == $plan->id) selected @endif value="{{ $plan->id }}">{{ $plan->title }}</option>
                                    @endforeach
                                </select>
                            <button class="btn-info btn btn-icon btn_search" id="search-submit" type="submit">{{__('Search')}}</button>
                        </form>
                    </div>
                </div>
                <div class="panel">
                    <div class="panel-body">
                        <form class="bravo-form-item">
                            <table class="table table-hover">
                                <thead>
                                <tr>
{{--                                    <th width="60px"><input type="checkbox" class="check-all"></th>--}}
                                    <th>{{__("#")}}</th>
                                    <th>{{__("User")}}</th>
                                    <th>{{__("Email")}}</th>
                                    <th>{{__("Plan Name")}}</th>
                                    <th>{{__("Expiry")}}</th>
                                    <th>{{__("Used/Total Ipanorama")}}</th>
                                    <th>{{__("Price")}}</th>
                                    <th>{{__("Status")}}</th>
                                    <th width="100px">{{__("Action")}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if($rows->total() > 0)
                                    @foreach($rows as $key => $row)
                                        <tr>
{{--                                            <td><input type="checkbox" name="ids[]" value="{{$row->id}}" class="check-item">--}}
                                            <td>{{$key + 1}}</td>
                                            <td>{{ $row->user ? $row->user->getDisplayName() : '' }}</td>
                                            <td>{{ $row->user->email ?? '' }}</td>
                                            <td class="trans-id">{{$row->plan->title ?? ''}}</td>
                                            <td class="total-jobs">
                                                @if($row->end_date)
                                                    {{display_datetime($row->end_date)}}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="used">@if(!$row->max_ipanorama) {{__("Unlimited")}} @else {{$row->used_ipanorama}}/{{$row->max_ipanorama}} @endif</td>
                                            <td class="remaining">{{format_money($row->price)}}</td>
                                            <td >
                                                @if($row->status==0)
                                                    <div class="text-warning mb-3">{{__('Pending')}}</div>
                                                @elseif($row->is_valid)
                                                    <span class="text-success">{{__('Active')}}</span>
                                                @else
                                                    <div class="text-danger mb-3">{{__('Expired')}}</div>
                                                    {{-- <div>
                                                        <a href="{{route('plan')}}" class="btn btn-warning">{{__('Renew')}}</a>
                                                    </div> --}}
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('user.admin.plan_report.edit', $row->id) }}" class="btn btn-primary">Edit</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr class="text-center">
                                        <td colspan="6">{{__("No data")}}</td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                            {{$rows->appends(request()->query())->links()}}
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
