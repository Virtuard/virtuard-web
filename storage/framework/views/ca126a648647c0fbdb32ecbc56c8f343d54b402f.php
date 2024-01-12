<?php $__env->startSection('content'); ?>
    <h2 class="title-bar no-border-bottom">
        Add Virtuard 360
    </h2>

    <?php if(!request()->has('id')): ?>
        <div class="alert alert-primary" role="alert">
            <b>You must first create a title for your Virtuard 360!</b>
        </div>

        <form action="<?php echo e(route('add-new-service')); ?>" method="POST">
        <?php echo csrf_field(); ?>

            <div class="card p-4">
                <div class="row">
                    <div class="col-md-10">
                        <div class="form-group">
                            <input placeholder="Your title..." type="text" name="title" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-2 p-0">
                        <button class="btn btn-primary w-100">
                            Submit
                        </button>
                    </div>
                </div>
            </div>
            
        </form>
    <?php endif; ?>

    <?php if(request()->has('id')): ?>
        <div class="col-md-12 p-0 mb-4">
            <button class="btn btn-primary w-100" data-toggle="modal" data-target="#modalAddImage">
                + Add New Image
            </button>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="modalAddImage" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Insert New Image</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="<?php echo e(route('add-new-image-service')); ?>" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="exampleFormControlFile1">Title Image</label>
                                <input type="text" name="title" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="exampleFormControlFile1">Proof of payment</label>
                                <input type="file" name="image" class="form-control-file" id="exampleFormControlFile1">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <iframe src="/uploads/ipanoramaBuilder/?idItem=<?php echo e(request()->id); ?>" width="100%" height="100%"></iframe>
    <?php endif; ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.user', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\virtuard\resources\views/vendor/virtuard360/add.blade.php ENDPATH**/ ?>