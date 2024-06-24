<div class="panel">
    <div class="panel-title"><strong>{{ __('Virtuard 360 Content') }}</strong></div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12">
                <div class="card p-4">
                    <div class="text-right">
                        <a href=""></a>
                        <a class="btn btn-info btn-sm btn-add-item" href="{{ route('user.virtuard-360.index') }}"><i class="icon ion-ios-add-circle-outline"></i> Add 360 Image</a>
                    </div>
                    <label>360 Image </label>
                    @if (auth()->user()->checkUserPlanStatus() || auth()->user()->isAdmin())
                        @php
                            $dataIpanorama = \App\Models\RefIpanorama::query()
                                ->where([
                                    ['user_id', $row->author_id],
                                    ['status', 'publish'],
                                ])
                                ->get();
                        @endphp
                        <select class="form-control" name="ipanorama_id">
                            <option>-- Select Item --</option>
                            @foreach ($dataIpanorama as $panorama)
                                <option value="{{ $panorama->id }}"
                                    {{ $row->ipanorama_id == $panorama->id ? 'selected' : '' }}>
                                    {{ $panorama->title }}
                                </option>
                            @endforeach
                        </select>
                    @else
                        <div class="alert alert-danger" role="alert">
                            Feature locked. please <a href="{{ route('user.plan') }}">Click here</a> to subscribe our
                            plan.

                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
