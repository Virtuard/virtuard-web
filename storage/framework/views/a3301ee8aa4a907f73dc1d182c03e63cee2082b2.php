<?php $__env->startSection('content'); ?>
        <div class="container" style="padding-top: 115px;padding-bottom: 40px; background: #f5f5f5;">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-12" style="background: #f5f5f5; padding: 0 20px;">
                        <div class="w-100 mt-3 d-flex" style="background: #FFF; border-radius: 8px; padding: 23px 35px;">
                            <div>
                                <p class="m-0">
                                    <i class="fa fa-globe mr-2"></i>
                                    All Members 
                                    <span class="badge badge-primary"><?php echo e(count($dataUser)); ?></span>
                                </p>
                            </div>
                            <div class="ml-3">
                                <p class="m-0">
                                    <i class="fa fa-signal mr-2"></i>
                                    Following 
                                    <span class="badge badge-primary">0</span>
                                </p>
                            </div>
                        </div>  
                    </div>  
                    <?php $__currentLoopData = $dataUser; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dataUser): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-4" style="background: #f5f5f5; padding: 0 20px;">
                        <div class="w-100 mt-3" style="background: #FFF; border-radius: 8px; padding: 23px 35px;">
                            <div class="d-flex align-items-center">
                                <img class="mr-4" src="https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_960_720.png" style="width: 60px; height: 60px; object-fit: cover; border-radius: 100px;" alt="">
                            
                                <div>
                                    <p class="m-0">
                                        <b><?php echo e($dataUser->name); ?></b>
                                    </p>
                                    <p class="m-0">
                                        <?php echo e($dataUser->business_name); ?>

                                    </p>
                                </div>
                            </div>

                            <hr>

                            <p style="color: #a3a3a3;">
                                <?php echo e($dataUser->bio); ?>

                            </p>

                            <hr>
                            
                            <?php
                            if($dataUser->isFollow == 0) { ?>
                                <form action="<?php echo e(route('user.add.follow.member')); ?>" class="mb-4" method="POST">
                                <?php echo csrf_field(); ?>
                                    <input type="hidden" name="param" value="Follow">
                                    <input type="hidden" name="id_follow" value="<?=$dataUser->id?>">
                                    <button class="btn btn-primary w-100 mb-2">
                                        Follow
                                    </button>
                                </form>
                            <?php }else{ ?>
                                <form action="<?php echo e(route('user.add.follow.member')); ?>" class="mb-4" method="POST">
                                <?php echo csrf_field(); ?>
                                    <input type="hidden" name="param" value="Unfollow">
                                    <input type="hidden" name="id_follow" value="<?=$dataUser->id?>">
                                    <button class="btn btn-secondary w-100 mb-2">
                                        Unfollow
                                    </button>
                                </form>
                            <?php } ?>

                            <a href="/profile/<?=$dataUser->id?>">
                                <button class="btn btn-primary w-100">
                                    View Site
                                </button>
                            </a>
                        </div>
                    </div>  
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\virtuard\resources\views/members/index.blade.php ENDPATH**/ ?>