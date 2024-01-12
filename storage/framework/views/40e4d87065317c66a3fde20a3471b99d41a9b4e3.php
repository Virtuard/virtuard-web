<?php $__env->startSection('content'); ?>
    <h2 class="title-bar">
        <?php echo e(__("Manage Rooms")); ?>

        <div class="title-action">
            <a href="<?php echo e(route('hotel.vendor.edit',['id'=>$hotel->id])); ?>" class="btn btn-info"><i class="fa fa-hand-o-right"></i> <?php echo e(__("Back to hotel")); ?></a>
            <a href="<?php echo e(route('hotel.vendor.room.availability.index',['hotel_id'=>$hotel->id])); ?>" class="btn btn-warning"><i class="fa fa-calendar"></i> <?php echo e(__("Availability Rooms")); ?></a>
            <a href="<?php echo e(route("hotel.vendor.room.create",['hotel_id'=>$hotel->id])); ?>" class="btn btn-success"><i class="fa fa-plus" aria-hidden="true"></i> <?php echo e(__("Add Room")); ?></a>
        </div>
    </h2>
    <?php echo $__env->make('admin.message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php if($rows->total() > 0): ?>
        <div class="bravo-list-item">
            <div class="bravo-pagination">
                <span class="count-string"><?php echo e(__("Showing :from - :to of :total Rooms",["from"=>$rows->firstItem(),"to"=>$rows->lastItem(),"total"=>$rows->total()])); ?></span>
                <?php echo e($rows->appends(request()->query())->links()); ?>

            </div>
            <div class="list-item">
                <div class="row">
                    <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-md-12">
                            <?php echo $__env->make('Hotel::frontend.vendorHotel.room.loop-list', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <div class="bravo-pagination">
                <span class="count-string"><?php echo e(__("Showing :from - :to of :total Rooms",["from"=>$rows->firstItem(),"to"=>$rows->lastItem(),"total"=>$rows->total()])); ?></span>
                <?php echo e($rows->appends(request()->query())->links()); ?>

            </div>
        </div>
    <?php else: ?>
        <?php echo e(__("No Room")); ?>

    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.user', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/buac2919/public_html/virtuard.buatpc.com/themes/BC/Hotel/Views/frontend/vendorHotel/room/index.blade.php ENDPATH**/ ?>