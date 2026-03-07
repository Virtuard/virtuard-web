<div class="bravo-form-search-tour {{$style}} @if(!empty($style) and $style == "carousel") bravo-form-search-slider @endif" @if(empty($style)) style="background-image: linear-gradient(0deg,rgba(0, 0, 0, 0.2),rgba(0, 0, 0, 0.2)),url('{{$bg_image_url}}') !important" @endif>
    @if(in_array($style,['carousel','']))
        @include("Natural::frontend.blocks.form-search-natural.style-normal")
    @endif
    @if($style == "carousel_v2")
        @include("Natural::frontend.blocks.form-search-natural.style-slider-ver2")
    @endif
</div>