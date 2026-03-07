@php
    $link = $row['link'];
@endphp
<div class="destination-item @if(!$row['image']) no-image  @endif list-item-{{$row['name']}}">
    @if(!empty($link)) <a href="{{$link}}">  @endif
        <div class="image" @if($row['image']) style="background: url({{asset($row['image'])}})" @endif >
            <div class="effect"></div>
            <div class="content">
                <div class="title">{{$row['name']}}</div>
            </div>
        </div>
    @if(!empty($link)) </a> @endif
</div>
