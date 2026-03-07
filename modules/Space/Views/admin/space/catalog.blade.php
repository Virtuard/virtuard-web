<div class="form-group-item">
    <label class="control-label">{{__('Catalog')}}</label>
    <div class="g-items-header">
        <div class="row">
            <div class="col-md-3 text-left">{{__("Name")}}</div>
            <div class="col-md-2 text-left">{{__("Type")}}</div>
            <div class="col-md-5">{{__('File')}}</div>
            <div class="col-md-2"></div>
        </div>
    </div>
    <div class="g-items">
        @if(!empty($row->catalogs))

            @php if(!is_array($row->catalogs)) $row->catalogs = json_decode($row->catalogs); @endphp

            @foreach($row->catalogs as $key=>$catalog)
                <div class="item" data-number="{{$key}}">
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" name="catalogs[{{$key}}][name]" class="form-control" value="{{$catalog['name'] ?? ""}}" placeholder="{{__('Catalog Name')}}">
                        </div>
                        <div class="col-md-2">
                            <select name="catalogs[{{$key}}][type]" class="form-control catalog-type">
                                <option value="file" {{($catalog['type'] ?? '') == 'file' ? 'selected' : ''}}>{{__('File')}}</option>
                                <option value="link" {{($catalog['type'] ?? '') == 'link' ? 'selected' : ''}}>{{__('Link')}}</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <div class="catalog-file-section">
                                <div class="catalog-file-row" style="{{($catalog['type'] ?? '') == 'link' ? 'display:none;' : ''}}">
                                    <input type="file" name="catalogs[{{$key}}][file]" class="form-control" accept=".pdf" />
                                    @if(isset($catalog['url']) && $catalog['url'] && ($catalog['type'] ?? '') == 'file')
                                        <small class="text-muted">Current file: {{basename($catalog['url'])}}</small>
                                    @endif
                                </div>
                                <div class="catalog-url-row" style="{{($catalog['type'] ?? '') == 'file' ? 'display:none;' : ''}}">
                                    <input type="text" name="catalogs[{{$key}}][url]" class="form-control" value="{{$catalog['url'] ?? ""}}" placeholder="https://example.com/catalog.pdf">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <span class="btn btn-danger btn-sm btn-remove-catalog"><i class="fa fa-trash"></i></span>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
    <div class="text-right">
            <span class="btn btn-info btn-sm btn-add-catalog"><i class="icon ion-ios-add-circle-outline"></i> {{__('Add catalog')}}</span>
    </div>
    <div class="g-more hide">
        <div class="item" data-number="__number__">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" __name__="catalogs[__number__][name]" class="form-control" placeholder="{{__('Name')}}">
                </div>
                <div class="col-md-2">
                    <select __name__="catalogs[__number__][type]" class="form-control catalog-type">
                        <option value="file">{{__('File')}}</option>
                        <option value="link">{{__('Link')}}</option>
                    </select>
                </div>
                <div class="col-md-5">
                    <div class="catalog-file-section">
                        <div class="catalog-file-row">
                            <input type="file" __name__="catalogs[__number__][file]" class="form-control" accept=".pdf" />
                        </div>
                        <div class="catalog-url-row" style="display:none;">
                            <input type="text" __name__="catalogs[__number__][url]" class="form-control" placeholder="https://example.com/catalog.pdf">
                        </div>
                    </div>
                </div>
                <div class="col-md-1">
                    <span class="btn btn-danger btn-sm btn-remove-catalog"><i class="fa fa-trash"></i></span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.item {
    border: 1px solid #e9ecef;
    border-radius: 6px;
    padding: 15px;
    margin-bottom: 15px;
    background: #f8f9fa;
}

.item:hover {
    border-color: #007bff;
    background: #fff;
}

.g-items-header {
    background: #e9ecef;
    padding: 10px 15px;
    border-radius: 6px;
    margin-bottom: 15px;
    font-weight: 600;
}

.btn-add-item {
    margin-top: 10px;
}

.item .form-control {
    margin-bottom: 5px;
}

.item textarea {
    resize: vertical;
    min-height: 60px;
}

.catalog-file-section {
    position: relative;
}

.catalog-file-row, .catalog-url-row {
    transition: all 0.3s ease;
}

.catalog-file-row.hidden, .catalog-url-row.hidden {
    display: none !important;
    visibility: hidden !important;
}

.catalog-file-row.visible, .catalog-url-row.visible {
    display: block !important;
    visibility: visible !important;
}

/* Force hide/show for catalog rows */
.catalog-file-row[style*="display: none"] {
    display: none !important;
}

.catalog-url-row[style*="display: none"] {
    display: none !important;
}

.catalog-file-row .form-group {
    margin-bottom: 0;
}

.pdf-preview {
    text-align: center;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 6px;
    border: 2px dashed #dee2e6;
}

.catalog-upload .attach-demo {
    min-height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.pdf-upload-icon {
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
    border: 2px dashed #dee2e6;
    margin-bottom: 15px;
}

.pdf-upload-icon:hover {
    border-color: #007bff;
    background: #e3f2fd;
}

.pdf-preview {
    text-align: center;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 6px;
    border: 1px solid #dee2e6;
}

.pdf-preview i {
    margin-bottom: 10px;
}

.catalog-upload .upload-box {
    padding: 20px;
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    background: #fafafa;
    transition: all 0.3s ease;
}

.catalog-upload .upload-box:hover {
    border-color: #007bff;
    background: #f0f8ff;
}
</style>

@push('js')
<script>
$(document).ready(function() {
    var $catalogSection = $('.form-group-item:has(.catalog-type)');
    
    $catalogSection.on('change', '.catalog-type', function() {
        var $item = $(this).closest('.item');
        var type = $(this).val();
        
        var $fileRow = $item.find('.catalog-file-row');
        var $urlRow = $item.find('.catalog-url-row');
        
        if (type === 'file') {
            $fileRow.show().removeClass('hidden').addClass('visible');
            $urlRow.hide().removeClass('visible').addClass('hidden');
            $urlRow.find('input[type="url"]').val('');
        } else if (type === 'link') {
            $fileRow.hide().removeClass('visible').addClass('hidden');
            $urlRow.show().removeClass('hidden').addClass('visible');
            $fileRow.find('input[type="file"]').val('');
        }
    });
    
    // Initialize existing items on page load
    $catalogSection.find('.catalog-type').each(function() {
        var $item = $(this).closest('.item');
        var type = $(this).val();
        
        var $fileRow = $item.find('.catalog-file-row');
        var $urlRow = $item.find('.catalog-url-row');
        
        if (type === 'file') {
            $fileRow.show().removeClass('hidden').addClass('visible');
            $urlRow.hide().removeClass('visible').addClass('hidden');
        } else if (type === 'link') {
            $fileRow.hide().removeClass('visible').addClass('hidden');
            $urlRow.show().removeClass('hidden').addClass('visible');
        }
    });
    
    $catalogSection.on('click', '.btn-add-catalog', function() {
        var $template = $catalogSection.find('.g-more').html();
        var $items = $catalogSection.find('.g-items');
        var index = $items.find('.item').length;
        
        var $newItem = $(($template.replace(/__number__/g, index).replace(/__name__/g, 'name')));
        $items.append($newItem);
        
        $newItem.find('.catalog-type').trigger('change');
    });
    
    $('form').on('submit', function() {
        var catalogs = [];
        $catalogSection.find('.item').each(function(index) {
            var $item = $(this);
            var catalog = {
                name: $item.find('input[name*="[name]"]').val(),
                type: $item.find('select[name*="[type]"]').val(),
                file: $item.find('input[name*="[file]"]')[0]?.files[0] || null,
                url: $item.find('input[name*="[url]"]').val()
            };
            catalogs.push(catalog);
        });
    });
    
    $catalogSection.on('click', '.btn-remove-catalog', function() {
        $(this).closest('.item').remove();
    });
});
</script>
@endpush

