<div class="btn-group dropup">
    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      Change Status
    </button>
    <div class="dropdown-menu">
      <a class="dropdown-item" href="{{ route("$row->type.vendor.bulk_edit", [$row->id,'action' => "make-hide"]) }}">Hide</a>
      <a class="dropdown-item" href="{{ route("$row->type.vendor.bulk_edit", [$row->id,'action' => "make-publish"]) }}">Publish</a>
      <a class="dropdown-item" href="{{ route("$row->type.vendor.bulk_edit", [$row->id,'action' => "make-private"]) }}">Private</a>
    </div>
  </div>