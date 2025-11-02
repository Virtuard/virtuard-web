<div class="panel">
    <div class="panel-title"><strong>{{ __('Property Content') }}</strong></div>
    <div class="panel-body">
        <div class="form-group">
            <label>{{ __('Title') }}</label>
            <input type="text" value="{!! clean($translation->title) !!}" placeholder="{{ __('Name of the property') }}" name="title" class="form-control space-title">
        </div>
        <div class="form-group">
            <label class="control-label">{{ __('Content') }}</label>
            <div class="">
                <textarea name="content" class="d-none has-ckeditor space-content" data-fullurl="true" cols="30" rows="10">{{ $translation->content }}</textarea>
            </div>
        </div>
        @if (is_default_lang())
            <div class="form-group d-none">
                <label class="control-label">{{ __('Category') }}</label>
                <div class="">
                    <select name="category_id" class="form-control">
                        <option value="">{{ __('-- Please Select --') }}</option>
                        <?php
                        $traverse = function ($categories, $prefix = '') use (&$traverse, $row) {
                            foreach ($categories as $category) {
                                $selected = '';
                                if ($row->category_id == $category->id) {
                                    $selected = 'selected';
                                }
                                printf("<option value='%s' %s>%s</option>", $category->id, $selected, $prefix . ' ' . $category->name);
                                $traverse($category->children, $prefix . '-');
                            }
                        };
                        $traverse($space_category);
                        ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label">{{ __('Youtube Video') }}</label>
                <input type="text" name="video" class="form-control" value="{{ $row->video }}" placeholder="{{ __('Youtube link video') }}">
            </div>
        @endif
        <div class="form-group-item">
            <label class="control-label">{{ __('FAQs') }}</label>
            <div class="g-items-header">
                <div class="row">
                    <div class="col-md-5">{{ __('Title') }}</div>
                    <div class="col-md-5">{{ __('Content') }}</div>
                    <div class="col-md-1"></div>
                </div>
            </div>
            <div class="g-items">
                @if (!empty($translation->faqs))
                    @php
                        if (!is_array($translation->faqs)) {
                            $translation->faqs = json_decode($translation->faqs);
                        }
                    @endphp
                    @foreach ($translation->faqs as $key => $faq)
                        <div class="item" data-number="{{ $key }}">
                            <div class="row">
                                <div class="col-md-5">
                                    <input type="text" name="faqs[{{ $key }}][title]" class="form-control" value="{{ $faq['title'] }}" placeholder="{{ __('Eg: When and where does the tour end?') }}">
                                </div>
                                <div class="col-md-6">
                                    <textarea name="faqs[{{ $key }}][content]" class="form-control" placeholder="...">{{ $faq['content'] }}</textarea>
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
                <span class="btn btn-info btn-sm btn-add-item"><i class="icon ion-ios-add-circle-outline"></i> {{ __('Add item') }}</span>
            </div>
            <div class="g-more hide">
                <div class="item" data-number="__number__">
                    <div class="row">
                        <div class="col-md-5">
                            <input type="text" __name__="faqs[__number__][title]" class="form-control" placeholder="{{ __('Eg: Can I bring my pet?') }}">
                        </div>
                        <div class="col-md-6">
                            <textarea __name__="faqs[__number__][content]" class="form-control" placeholder=""></textarea>
                        </div>
                        <div class="col-md-1">
                            <span class="btn btn-danger btn-sm btn-remove-item"><i class="fa fa-trash"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('Space::admin.space.catalog')

        @if (is_default_lang())
            <div class="form-group">
                <label class="control-label">{{ __('Banner Image') }}</label>
                <div class="form-group-image">
                    {!! \Modules\Media\Helpers\FileHelper::fieldUpload('banner_image_id', $row->banner_image_id) !!}
                </div>
            </div>
            <div class="form-group">
                <label class="control-label">{{ __('Gallery') }}</label>
                {!! \Modules\Media\Helpers\FileHelper::fieldGalleryUpload('gallery', $row->gallery) !!}
            </div>
        @endif
    </div>
</div>

@include('partials.listing.form-ipanorama')
@include('Space::admin.space.extra-info')
