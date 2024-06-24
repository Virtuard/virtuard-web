@extends('admin.layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between mb20">
            <h1 class="title-bar">{{!empty($recovery) ? __('Recovery') : __("All Virtuard 360")}}</h1>
            <div class="title-actions">
                @if(empty($recovery))
                <a href="{{route('admin.virtuard360.create')}}" class="btn btn-primary">{{__("Add new 360")}}</a>
                @endif
            </div>
        </div>
        @include('admin.message')
        <div class="filter-div d-flex justify-content-between ">
            <div class="col-left">
            </div>
            <div class="col-left dropdown">
                <form method="get" action="{{ route('admin.virtuard360.index')}}" class="filter-form filter-form-right d-flex justify-content-end flex-column flex-sm-row" role="search">
                    @if(!empty($rows))
                        <input type="text" name="s" value="{{ Request()->s }}" placeholder="{{__('Search by name')}}" class="form-control">
                    @endif
                    <button class="btn-info btn btn-icon btn_search" type="submit">{{__('Search')}}</button>
                </form>
            </div>
        </div>
        <div class="text-right">
            <p><i>{{__('Found :total items',['total'=>$rows->total()])}}</i></p>
        </div>
        <div class="panel">
            <div class="panel-body">
                <form action="" class="bravo-form-item">
                    <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th width="60px">#</th>
                            <th> {{ __('Title')}}</th>
                            <th width="130px"> {{ __('Author')}}</th>
                            <th width="100px"> {{ __('Status')}}</th>
                            <th width="100px"> {{ __('Date')}}</th>
                            <th width="100px"></th>
                        </tr>
                        </thead>
                        <tbody>
                        @if($rows->total() > 0)
                            @foreach($rows as $key => $row)
                                <tr class="{{$row->status}}">
                                    <td>{{ $key + 1}}</td>
                                    <td class="title">
                                        {{$row->title}}
                                    </td>
                                    <td>
                                        @if(!empty($row->author))
                                            {{$row->author->getDisplayName()}}
                                        @else
                                            {{__("[Author Deleted]")}}
                                        @endif
                                    </td>
                                    <td><span class="badge badge-{{ $row->status }}">{{ $row->status }}</span></td>
                                    <td>{{ display_date($row->updated_at)}}</td>
                                    <td>
                                        @if(empty($recovery))
                                            <a href="{{route('admin.virtuard360.edit',['id'=>$row->id,'user_id'=>$row->create_user])}}" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> {{__('Edit')}}</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7">{{__("No data found")}}</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                    </div>
                </form>
                {{$rows->appends(request()->query())->links()}}
            </div>
        </div>
    </div>
@endsection
