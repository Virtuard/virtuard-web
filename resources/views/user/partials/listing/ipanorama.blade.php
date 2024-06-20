<input type="hidden" id="panId" value="{{ $row->ipanorama->code ?? '' }}">
@if ($row->ipanorama && $row->author->checkUserPlanStatus())
    <div id="panorama"></div>
@endif
