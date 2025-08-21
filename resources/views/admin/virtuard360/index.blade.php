@extends('admin.layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between mb20">
            <h1 class="title-bar">{{ !empty($recovery) ? __('Recovery') : __('All Virtuard 360') }}</h1>
            <div class="title-actions">
                @if (empty($recovery))
                    <a href="{{ route('admin.virtuard360.create') }}" class="btn btn-primary">{{ __('Add new 360') }}</a>
                @endif
            </div>
        </div>
        @include('admin.message')
        <div class="filter-div d-flex justify-content-between ">
            <div class="col-left">
            </div>
            <div class="col-left dropdown">
                <form method="get" action="{{ route('admin.virtuard360.index') }}"
                    class="filter-form filter-form-right d-flex justify-content-end flex-column flex-sm-row" role="search">
                    @if (!empty($rows))
                        <input type="text" name="s" value="{{ Request()->s }}"
                            placeholder="{{ __('Search by name') }}" class="form-control">
                    @endif
                    <button class="btn-info btn btn-icon btn_search" type="submit">{{ __('Search') }}</button>
                </form>
            </div>
        </div>
        <div class="text-right">
            <p><i>{{ __('Found :total items', ['total' => $rows->total()]) }}</i></p>
        </div>
        <div class="panel">
            <div class="panel-body">
                <form action="" class="bravo-form-item">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('Title') }}</th>
                                    <th>{{ __('Author') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($rows->total() > 0)
                                    @foreach ($rows as $key => $row)
                                        <tr class="{{ $row->status }}">
                                            <td>{{ $key + 1 }}</td>
                                            <td class="title">
                                                {{ $row->title }}
                                            </td>
                                            <td>
                                                @if (!empty($row->author))
                                                    {{ $row->author->getDisplayName() }}
                                                @else
                                                    {{ __('[Author Deleted]') }}
                                                @endif
                                            </td>
                                            <td>
                                                @if (!empty($row->author))
                                                    {{ $row->author->email }}
                                                @else
                                                    {{ __('[Author Deleted]') }}
                                                @endif
                                            </td>
                                            <td><span class="badge badge-{{ $row->status }}">{{ $row->status }}</span>
                                            </td>
                                            <td>
                                                @if (empty($recovery))
                                                    <a href="{{ route('admin.virtuard360.show', $row->id) }}"
                                                        class="btn btn-info btn-sm"><i class="fa fa-eye"></i> Preview</a>
                                                    <a href="{{ route('admin.virtuard360.edit', ['id' => $row->id, 'user_id' => $row->user_id]) }}"
                                                        class="btn btn-primary btn-sm"><i class="fa fa-edit"></i>
                                                        {{ __('Edit') }}</a>
                                                    @if ($row->status == 'publish')
                                                        <a href="{{ route('admin.virtuard360.setstatus', ['id' => $row->id, 'status' => 'draft']) }}"
                                                            class="btn btn-warning btn-sm">Make Draft</a>
                                                    @else
                                                        <a href="{{ route('admin.virtuard360.setstatus', ['id' => $row->id, 'status' => 'publish']) }}"
                                                            class="btn btn-success btn-sm">Make Publish</a>
                                                    @endif
                                                    <a href="javascript:void(0)" class="btn btn-secondary btn-sm btn-copy"
                                                        data-id="{{ $row->uuid }}" data-toggle="tooltip" data-placement="top" title="Copy Link"><i class="fa fa-copy"></i>
                                                        {{ __('Copy Link') }}</a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="7">{{ __('No data found') }}</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </form>
                {{ $rows->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script>
        function copyToClipBoard(text, buttonElement) {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard
                    .writeText(text)
                    .then(function() {
                        $(buttonElement).attr('data-original-title', 'Copied Success!').tooltip('show');
                        
                        setTimeout(function() {
                            $(buttonElement).tooltip('hide');
                            $(buttonElement).attr('data-original-title', 'Copy Link');
                        }, 2000);
                    })
                    .catch(function(err) {
                        console.error("Error copy text: ", err);
                    });
            } else {
                let tempInput = $("<input>");
                tempInput.attr("type", "text");
                $("body").append(tempInput);
                tempInput.val(text).select();
                document.execCommand("copy");
                tempInput.remove();
                
                $(buttonElement).attr('data-original-title', 'Copied Success!').tooltip('show');
                
                setTimeout(function() {
                    $(buttonElement).tooltip('hide');
                    $(buttonElement).attr('data-original-title', 'Copy Link');
                }, 2000);
            }
        }

        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip();
            
            $('.btn-copy').click(function() {
                var id = $(this).data('id');
                var url = "{{ route('panorama.share', ['id' => ':id']) }}";
                url = url.replace(':id', id);
                
                copyToClipBoard(url, this);
            });
        });
    </script>
@endpush
