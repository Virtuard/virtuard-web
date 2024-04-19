@php
    $link = $row['link'];
@endphp
<div class="destination-item @if(!$row['image']) no-image  @endif">
    @if(!empty($link)) <a href="{{$link}}">  @endif
        <div class="image" @if($row['image']) style="background: url({{asset($row['image'])}})" @endif >
            <div class="effect"></div>
            <div class="content">
                <h4 class="title">{{$row['name']}}</h4>
            </div>
        </div>
    @if(!empty($link)) </a> @endif
</div>
