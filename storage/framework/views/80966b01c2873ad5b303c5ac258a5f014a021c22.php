<div class="panel">
    <div class="panel-title"><strong><?php echo e(__('Property Content')); ?></strong></div>
    <div class="panel-body">
        <div class="form-group">
            <label><?php echo e(__('Title')); ?></label>
            <input type="text" value="<?php echo clean($translation->title); ?>" placeholder="<?php echo e(__('Name of the property')); ?>" name="title" class="form-control">
        </div>
        <div class="form-group">
            <label class="control-label"><?php echo e(__('Content')); ?></label>
            <div class="">
                <textarea name="content" class="d-none has-ckeditor" cols="30" rows="10"><?php echo e($translation->content); ?></textarea>
            </div>
        </div>
        <?php if(is_default_lang()): ?>
            <div class="form-group">
                <label class="control-label"><?php echo e(__('Youtube Video')); ?></label>
                <input type="text" name="video" class="form-control" value="<?php echo e($row->video); ?>" placeholder="<?php echo e(__('Youtube link video')); ?>">
            </div>
        <?php endif; ?>
        <div class="form-group-item">
            <label class="control-label"><?php echo e(__('FAQs')); ?></label>
            <div class="g-items-header">
                <div class="row">
                    <div class="col-md-5"><?php echo e(__('Title')); ?></div>
                    <div class="col-md-5"><?php echo e(__('Content')); ?></div>
                    <div class="col-md-1"></div>
                </div>
            </div>
            <div class="g-items">
                <?php if(!empty($translation->faqs)): ?>
                    <?php
                        if (!is_array($translation->faqs)) {
                            $translation->faqs = json_decode($translation->faqs);
                        }
                    ?>
                    <?php $__currentLoopData = $translation->faqs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $faq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="item" data-number="<?php echo e($key); ?>">
                            <div class="row">
                                <div class="col-md-5">
                                    <input type="text" name="faqs[<?php echo e($key); ?>][title]" class="form-control" value="<?php echo e($faq['title']); ?>" placeholder="<?php echo e(__('Eg: When and where does the tour end?')); ?>">
                                </div>
                                <div class="col-md-6">
                                    <textarea name="faqs[<?php echo e($key); ?>][content]" class="form-control" placeholder="..."><?php echo e($faq['content']); ?></textarea>
                                </div>
                                <div class="col-md-1">
                                    <span class="btn btn-danger btn-sm btn-remove-item"><i class="fa fa-trash"></i></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            </div>
            <div class="text-right">
                <span class="btn btn-info btn-sm btn-add-item"><i class="icon ion-ios-add-circle-outline"></i> <?php echo e(__('Add item')); ?></span>
            </div>
            <div class="g-more hide">
                <div class="item" data-number="__number__">
                    <div class="row">
                        <div class="col-md-5">
                            <input type="text" __name__="faqs[__number__][title]" class="form-control" placeholder="<?php echo e(__('Eg: Can I bring my pet?')); ?>">
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
        <?php if(is_default_lang()): ?>
            <div class="form-group">
                <label class="control-label"><?php echo e(__('Banner Image')); ?></label>
                <div class="form-group-image">
                    <?php echo \Modules\Media\Helpers\FileHelper::fieldUpload('banner_image_id', $row->banner_image_id); ?>

                </div>
            </div>
            <div class="form-group">
                <label class="control-label"><?php echo e(__('Gallery')); ?></label>
                <?php echo \Modules\Media\Helpers\FileHelper::fieldGalleryUpload('gallery', $row->gallery); ?>

            </div>
        <?php endif; ?>
    </div>
</div>


<?php if($isVirtuard360): ?>

    <div class="panel">
        <div class="panel-title"><strong><?php echo e(__('Virtuard 360 Content')); ?></strong></div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="card p-4">
                        <label>
                            Image 1
                        </label>
                        <select class="form-control" name="div-ipanorama">
                            <option>Select</option>
                            <?php $__currentLoopData = $dataIpanorama; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dataIpanorama): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?= $dataIpanorama->id ?>"><?php echo e($dataIpanorama->title); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if($categories ?? []): ?>
    <div class="panel">
        <div class="panel-title"><strong><?php echo e(__('Property Category')); ?></strong></div>
        <div class="panel-body">
            <fieldset class="form-group">
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="categoryProd" id="<?php echo e($category->id); ?>" value="<?php echo e($category->id); ?>" required>
                        <label class="form-check-label" for="<?php echo e($category->id); ?>"><?php echo e($category->title); ?></label>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </fieldset>
        </div>
    </div>
<?php endif; ?>


<?php if(is_default_lang()): ?>
    <div class="panel">
        <div class="panel-title"><strong><?php echo e(__('Extra Info')); ?></strong></div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label><?php echo e(__('No. Bed')); ?></label>
                        <input type="number" value="<?php echo e($row->bed); ?>" placeholder="<?php echo e(__('Example: 3')); ?>" name="bed" class="form-control">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label><?php echo e(__('No. Bathroom')); ?></label>
                        <input type="number" value="<?php echo e($row->bathroom); ?>" placeholder="<?php echo e(__('Example: 5')); ?>" name="bathroom" class="form-control">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label><?php echo e(__('Square')); ?></label>
                        <input type="number" value="<?php echo e($row->square); ?>" placeholder="<?php echo e(__('Example: 100')); ?>" name="square" class="form-control">
                    </div>
                </div>
            </div>
            <?php if(is_default_lang()): ?>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label class="control-label"><?php echo e(__('Minimum advance reservations')); ?></label>
                            <input type="number" name="min_day_before_booking" class="form-control" value="<?php echo e($row->min_day_before_booking); ?>" placeholder="<?php echo e(__('Ex: 3')); ?>">
                            <i><?php echo e(__('Leave blank if you dont need to use the min day option')); ?></i>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label class="control-label"><?php echo e(__('Minimum day stay requirements')); ?></label>
                            <input type="number" name="min_day_stays" class="form-control" value="<?php echo e($row->min_day_stays); ?>" placeholder="<?php echo e(__('Ex: 2')); ?>">
                            <i><?php echo e(__('Leave blank if you dont need to set minimum day stay option')); ?></i>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
<?php /**PATH /home/buac2919/public_html/virtuard.buatpc.com/modules/Space/Views/admin/space/content.blade.php ENDPATH**/ ?>