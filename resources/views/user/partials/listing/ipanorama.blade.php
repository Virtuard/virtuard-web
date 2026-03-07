@if (is_display_panorama_listing($row))
    <input type="hidden" id="data-panorama" data-code="{{ $row->ipanorama->code }}"
        data-user_id="{{ $row->ipanorama->user_id }}">

    <div id="mypanorama" class="mypanorama-preview"></div>
    <div class="row">
        <div class="col-md-12">
            <div class="pull-right mb-3">
                <a href="javascript:void(0)"
                    id="share-vtour-btn"
                    onclick="actionCopyToClipBoard('{{ route('panorama.share', ['id' => $row->ipanorama->uuid]) }}')"
                    class="btn btn-primary btn-sm">
                    <i class="fa fa-share" data-toggle="tooltip" data-placement="top"
                        title="Share Vtour"></i> 
                    Share Vtour
                </a>
                <a href="{{ route("$row->type.detail", ['slug' => $row->slug, 'preview_panorama' => '1']) }}"
                    class="btn btn-warning btn-sm">
                    <i class="fa fa-apple" data-toggle="tooltip" data-placement="top"
                        title="If the screen is blank, click here to view low quality"></i> 
                    Iphone view
                </a>
            </div>
        </div>
    </div>
@endif
