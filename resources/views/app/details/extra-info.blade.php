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
        @if (!empty($row->bed))
            <div class="item">
                <i class="icofont-check-circled icon-default"></i>
                {{ $row->bed }} {{ __('Bedrooms') }}
            </div>
        @endif
        @if (!empty($row->bathroom))
            <div class="item">
                <i class="icofont-check-circled icon-default"></i>
                {{ $row->bathroom }} {{ __('Bathrooms') }}
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
        @if (!empty($row->software))
            <div class="item">
                <i class="icofont-check-circled icon-default"></i>
                {{ $row->software }} {{ __('Software') }}
            </div>
        @endif
    </div>
</div>
