<div class="form-group-item">
    <label class="control-label">{{__('Product')}}</label>
    <div class="g-items-header">
        <div class="row">
            <div class="col-md-3 text-left">{{__("Image")}}</div>
            <div class="col-md-5 text-left">{{__("Title")}}</div>
            <div class="col-md-3">{{__('Price')}} ({{ currency_symbol() }})</div>
            <div class="col-md-1"></div>
        </div>
    </div>
    <div class="g-items">
        @if(!empty($translation->items))

            @php if(!is_array($translation->items)) $translation->items = json_decode($translation->items); @endphp


            @foreach($translation->items as $key=>$item)
                <div class="item" data-number="{{$key}}">
                    <div class="row">
                        <div class="col-md-3">
                            {!! \Modules\Media\Helpers\FileHelper::fieldUpload('items['.$key.'][image_id]',$item['image_id'] ?? '') !!}
                        </div>
                        <div class="col-md-5">
                            <input type="text" name="items[{{$key}}][title]" class="form-control" value="{{$item['title'] ?? ""}}">
                        </div>
                        <div class="col-md-3">
                            <input type="number" name="items[{{$key}}][price]" class="form-control" value="{{$item['price'] ?? ""}}">
                        </div>
                        <div class="col-md-1">
                                <span class="btn btn-danger btn-sm btn-remove-item"><i class="fa fa-trash"></i></span>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
    <div class="text-right">
            <span class="btn btn-info btn-sm btn-add-item"><i class="icon ion-ios-add-circle-outline"></i> {{__('Add item')}}</span>
    </div>
    <div class="g-more hide">
        <div class="item" data-number="__number__">
            <div class="row">
                <div class="col-md-3">
                    {!! \Modules\Media\Helpers\FileHelper::fieldUpload('items[__number__][image_id]','','__name__') !!}
                </div>
                <div class="col-md-5">
                    <input type="text" __name__="items[__number__][title]" class="form-control">
                </div>
                <div class="col-md-3">
                    <input type="number" __name__="items[__number__][price]" class="form-control">
                </div>
                <div class="col-md-1">
                    <span class="btn btn-danger btn-sm btn-remove-item"><i class="fa fa-trash"></i></span>
                </div>
            </div>
        </div>
    </div>
</div>