<form action="{{ route("space.search") }}" class="form bravo_form" method="get">
    <div class="g-field-search">
        <div class="row d-flex align-items-center">
            <div class="col-md-2 border-right">
                @include('Space::frontend.layouts.search.fields.service_name')
            </div>
            <div class="col-md-3 border-right">
                @include('partials.search.fields.location')
            </div>
            <div class="col-md-2 border-right">
                @include('Space::frontend.layouts.search.fields.range')
            </div>
            <div class="col-md-3 border-right">
                @include('Space::frontend.layouts.search.fields.category')
            </div>
            <div class="col-md-2 border-right">
                @include('partials.search.fields.ipanorama')
            </div>
        </div>
    </div>
    <div class="g-button-submit">
        <button class="btn btn-primary btn-search" type="submit">{{__("Search")}}</button>
    </div>
</form>
