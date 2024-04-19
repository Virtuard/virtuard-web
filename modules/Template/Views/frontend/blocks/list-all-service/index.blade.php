<div class="container">
    <div class="bravo-list-locations @if(!empty($layout)) {{ $layout }} @endif">
        <div class="title">
            {{$title}}
        </div>
        @if(!empty($desc))
            <div class="sub-title">
                {{$desc}}
            </div>
        @endif
        @if(!empty($rows))
            <div class="list-item">
                <div class="row">
                    @foreach($rows as $key=>$row)
                        <?php
                        $size_col = 4;
                        ?>
                        <div class="col-lg-{{$size_col}} col-md-6">
                            @include('Template::frontend.blocks.list-all-service.loop')
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>