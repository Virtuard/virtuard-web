<div class="bravo-list-tour {{$style_list}}">
    @if(in_array($style_list,['normal','carousel','box_shadow']))
        @include("Natural::frontend.blocks.list-natural.style-normal")
    @endif
    @if($style_list == "carousel_simple")
        @include("Natural::frontend.blocks.list-natural.style-carousel-simple")
    @endif
</div>