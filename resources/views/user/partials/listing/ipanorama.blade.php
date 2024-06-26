<input type="hidden" id="panId" value="{{ $row->ipanorama->code ?? '' }}">
@if ($row->ipanorama && $row->ipanorama->status == 'publish')
    @if($row->author->checkUserPlanStatus())
    <div id="panorama"></div>
    @endif
@endif
