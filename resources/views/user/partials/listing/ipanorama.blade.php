@if (is_display_panorama_listing($row))
    <input type="hidden" id="data-panorama" data-code="{{ $row->ipanorama->code }}"
        data-user_id="{{ $row->ipanorama->user_id }}">

    <div id="mypanorama" class="mypanorama-preview"></div>
    <div class="pull-right mb-3">
        <a href="{{ route("$row->type.detail", ['slug' => $row->slug, 'preview_panorama' => '1']) }}"
            class="btn btn-warning btn-sm">
            Click to View Low Quality (suggested for iphone)
        </a>
    </div>
@endif
