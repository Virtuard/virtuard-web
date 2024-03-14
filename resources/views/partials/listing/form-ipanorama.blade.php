@if (auth()->user()->checkUserPlan() || auth()->user()->isAdmin())
    <div class="panel">
        <div class="panel-title"><strong>{{ __('Virtuard 360 Content') }}</strong></div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="card p-4">
                        <label> Image </label>
                        <select class="form-control" name="div-ipanorama">
                            <option>Select</option>
                            @foreach ($dataIpanorama as $panorama)
                                <option value="{{ $panorama->id }}" {{ $row->ipanorama_id == $panorama->id ? 'selected' : '' }}>
                                    {{ $panorama->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
