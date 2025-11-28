<div class="g-attributes space-type attr-1">
    <h3>{{ __('Detail') }}</h3>
    <div class="list-attributes">
        @if (!empty($row->square))
            <div class="item">
                <i class="icofont-check-circled icon-default"></i>
                {{ $row->square }} {{ __('Squares') }}
            </div>
        @endif
        @if (!empty($row->flooring))
            <div class="item">
                <i class="icofont-check-circled icon-default"></i>
                {{ $row->flooring }} {{ __('Floorings') }}
            </div>
        @endif
        @if (!empty($row->room))
            <div class="item">
                <i class="icofont-check-circled icon-default"></i>
                {{ $row->room }} {{ __('Rooms') }}
            </div>
        @endif
        @if (!empty($row->bedroom))
            <div class="item">
                <i class="icofont-check-circled icon-default"></i>
                {{ $row->bedroom }} {{ __('Bedrooms') }}
            </div>
        @endif
        @if (!empty($row->bathroom))
            <div class="item">
                <i class="icofont-check-circled icon-default"></i>
                {{ $row->bathroom }} {{ __('Bathrooms') }}
            </div>
        @endif
        @if (!empty($row->bed))
            <div class="item">
                <i class="icofont-check-circled icon-default"></i>
                {{ $row->bed }} {{ __('Beds') }}
            </div>
        @endif
        @if (!empty($row->single_bed))
            <div class="item">
                <i class="icofont-check-circled icon-default"></i>
                {{ $row->single_bed }} {{ __('Single Beds') }}
            </div>
        @endif
        @if (!empty($row->double_bed))
            <div class="item">
                <i class="icofont-check-circled icon-default"></i>
                {{ $row->double_bed }} {{ __('Double Beds') }}
            </div>
        @endif
        @if (!empty($row->chain))
            <div class="item">
                <i class="icofont-check-circled icon-default"></i>
                {{ $row->chain }} {{ __('Chain') }}
            </div>
        @endif
        @if (!empty($row->agency))
            <div class="item">
                <i class="icofont-check-circled icon-default"></i>
                {{ $row->agency }} {{ __('Agency') }}
            </div>
        @endif
        @if (!empty($row->franchising))
            <div class="item">
                <i class="icofont-check-circled icon-default"></i>
                {{ $row->franchising }} {{ __('Franchising') }}
            </div>
        @endif
        @if (!empty($row->engineering))
            <div class="item">
                <i class="icofont-check-circled icon-default"></i>
                {{ $row->engineering }} {{ __('Engineering') }}
            </div>
        @endif
    </div>
</div>
@if (!empty($row->software))
<div class="g-attributes space-type attr-1">
    <h3>{{ __('Software') }}</h3>
    <div class="list-attributes">
        @foreach ($row->software as $software)
        <div class="item">
            <i class="icofont-check-circled icon-default"></i>
            {{ $software }}
        </div>
        @endforeach
    </div>
</div>
@endif