<?php $__env->startSection('content'); ?>
    <h2 class="title-bar no-border-bottom">
        Virtuard 360
    </h2>
    <?php echo $__env->make('admin.message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <div class="border rounded text-center p-4">

        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">No</th>
                    <th scope="col">Name</th>
                    <th scope="col">Status</th>
                    <th scope="col">Start Date</th>
                    <th scope="col">Expired Date</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $no = 1;
                ?>
                <tr>
                    <th scope="row"><?php echo e($no); ?></th>
                    <td>
                        <?php echo e($data->name); ?>

                    </td>

                    <td>
                        <?php echo e($data->status); ?>

                    </td>

                    <td>
                        <?php echo e($data->start_date); ?>

                    </td>

                    <td>
                        <?php echo e($data->expired_date); ?>

                    </td>
                    <td>
                        <div class="dropdown">
                          <button class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                            Validate
                          </button>
                          <div class="dropdown-menu">
                            <form class="dropdown-item" action="<?php echo e(route('validate-service')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                                <input type="hidden" name="param" value="SUCCESS">
                                <input type="hidden" name="id" value="<?php echo e($data->id); ?>">
                                <button class="btn btn-success" type="submit">Approve</button>
                            </form>
                            <form class="dropdown-item" action="<?php echo e(route('validate-service')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                                <input type="hidden" name="param" value="REJECTED">
                                <input type="hidden" name="id" value="<?php echo e($data->id); ?>">
                                <button class="btn btn-danger" type="submit">Rejected</button>
                            </form>
                            <form class="dropdown-item" action="<?php echo e(route('validate-service')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                                <input type="hidden" name="param" value="STOP">
                                <input type="hidden" name="id" value="<?php echo e($data->id); ?>">
                                <button class="btn btn-warning" type="submit">Stop</button>
                            </form>
                          </div>
                        </div>
                    </td>
                </tr>
                <?php
                    $no++;
                ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>

    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\virtuard\resources\views/admin/virtuard360/index.blade.php ENDPATH**/ ?>