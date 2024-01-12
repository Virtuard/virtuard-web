<div class="card w-100 shadow-hover-3 mb-4">
	<a href="#" class="d-block mb-0 mx-1 mt-1 p-3" tabindex="0">
		<img class="card-img-top" src="<?php echo e($row->airline->image_url); ?>" alt="<?php echo e($row->airline->name); ?>">
	</a>
	<div class="card-body px-3 pt-0 pb-3 my-0 mx-1">
		<div class="row">
			<div class="col-7">
				<a href="#" class="card-title text-dark font-size-17 font-weight-bold" tabindex="0"><?php echo e($row->airportFrom->name); ?></a>
			</div>
			<div class="col-5">
				<div class="text-right">
					<h6 class="font-weight-bold font-size-17 text-gray-3 mb-0"><?php echo e(format_money(@$row->min_price)); ?></h6>
					<span class="font-weight-normal font-size-12 d-block text-color-1"><?php echo e(__('avg/person')); ?></span>
				</div>
			</div>
		</div>
	</div>
	<div class="mb-3">
		<div class="border-bottom pb-3 mb-3">
			<div class="px-3">
				<div class="d-flex mx-1">
					<i class="icofont-airplane font-size-30 text-primary mr-3"></i>
					<div class="d-flex flex-column">
						<span class="font-weight-normal text-gray-5"><?php echo e(__('Take off')); ?></span>
						<span class="font-size-14 text-gray-1"><?php echo e($row->departure_time->format("D M d H:i A")); ?></span>
					</div>
				</div>
			</div>
		</div>
		<div class="border-bottom pb-3 mb-3">
			<div class="px-3">
				<div class="d-flex mx-1">
					<i class="d-block rotate-90 icofont-airplane-alt font-size-30 text-primary mr-3"></i>
					<div class="d-flex flex-column">
						<span class="font-weight-normal text-gray-5"><?php echo e(__('Landing')); ?></span>
						<span class="font-size-14 text-gray-1"><?php echo e($row->arrival_time->format("D M d H:i A")); ?></span>
					</div>
				</div>
			</div>
		</div>
		<div class="d-flex justify-content-center pl-3 pr-3">
			<?php if($row->can_book): ?>
			<a @click="openModalBook('<?php echo e($row->id); ?>')" href=""  onclick="event.preventDefault()" class="btn btn-primary text-white btn-choose w-100"><?php echo e(__("Choose")); ?></a>
			<?php else: ?>
				<a  href="#"  class="btn btn-warning btn-disabled"><?php echo e(__("Full Book")); ?></a>
			<?php endif; ?>
		</div>
	</div>
</div>
<?php /**PATH /home/buac2919/public_html/virtuard.buatpc.com/themes/BC/Flight/Views/frontend/layouts/search/loop-grid.blade.php ENDPATH**/ ?>