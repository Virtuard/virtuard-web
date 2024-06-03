<div class="bravo-list-tour {{$style_list}}">
    @if(in_array($style_list,['normal','carousel','box_shadow']))
        @include("Cultural::frontend.blocks.list-cultural.style-normal")
    @endif
    @if($style_list == "carousel_simple")
        @include("Cultural::frontend.blocks.list-cultural.style-carousel-simple")
    @endif
</div>