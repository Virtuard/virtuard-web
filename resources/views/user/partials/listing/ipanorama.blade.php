@if ($row->ipanorama && $row->ipanorama->status == 'publish' and $row->author->checkUserPlanStatus())
    <input type="hidden" id="data-panorama" data-code="{{ $row->ipanorama->code }}" data-user_id="{{ $row->ipanorama->user_id }}">
    <div id="mypanorama"></div>
@endif
<style>
    #mypanorama {
        position: relative;
        width: 100%;
        height: 325px;
        background-color: #ddd;
        border: 5px solid #fff;
    }
</style>
