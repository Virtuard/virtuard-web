<link href="<?php echo e(asset('libs/ipanorama/src/ipanorama.css')); ?>" rel="stylesheet">
<link href="<?php echo e(asset('libs/ipanorama/src/ipanorama.theme.default.css')); ?>" rel="stylesheet">
<link href="<?php echo e(asset('libs/ipanorama/src/ipanorama.theme.modern.css')); ?>" rel="stylesheet">
<link href="<?php echo e(asset('libs/ipanorama/src/ipanorama.theme.dark.css')); ?>" rel="stylesheet">
<link href="<?php echo e(asset('libs/ipanorama/src/effect.css')); ?>" rel="stylesheet">
<link href="<?php echo e(asset('libs/ipanorama/src/style.css')); ?>" rel="stylesheet">
<?php $__env->startSection('content'); ?>
    <style>
        textarea {
            resize: none;
            overflow: hidden;
            min-height: 30px;
            max-height: auto;
        }

        .grid-container {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
            width: 80%;
            margin: 0 auto;
        }

        .grid-item {
            background-color: #f5f5f5;
            color: #fff;
            text-align: center;
        }

        .grid-item img {
            width: 100%;
            height: 20vw;
            object-fit: cover;
        }
    </style>
    <div style="padding-top: 115px;padding-bottom: 40px; background: #f5f5f5;">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-9" style="background: #f5f5f5; padding: 0 20px;">
                    <form action="<?php echo e(route('user.post.status')); ?>" class="mb-4" method="POST"
                        enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <div class="w-100" style="background: #FFF; border-radius: 8px; padding: 23px 35px;">
                            <ul class="d-flex flex-wrap" style="list-style: none; ">
                                <li
                                    style="background: #FFF;
                                    padding: 3px 18px;
                                    border-radius: 8px;
                                    margin-right: 16px;">
                                    <i class="fa fa-comments"></i> Status
                                </li>
                                <?php if(auth()->guard()->check()): ?>
                                    <li style="background: #f5f5f5; padding: 3px 18px; border-radius: 8px; margin-right: 16px; cursor: pointer;"
                                        onclick="document.getElementById('fileInput').click();">
                                        <input type="file" id="fileInput" style="display: none;" name="media_user[]"
                                            accept=".jpeg, .jpg, .png, .webp, .pdf, .doc, .docx, .xls, .xlsx, .mp4, .mkv"
                                            multiple>
                                        <i class="fa fa-picture-o"></i> Media
                                    </li>
                                    <li style="background: #f5f5f5; padding: 3px 18px; border-radius: 8px; margin-right: 16px; cursor: pointer;"
                                        onclick="document.getElementById('ipanoramaModal').click();">
                                        <i class="fa fa-picture-o"></i> 360 Media
                                        <button id="ipanoramaModal" type="button" class="d-none" data-toggle="modal"
                                            data-target="#modalPanorama"></button>
                                    </li>
                                <?php else: ?>
                                    <li
                                        style="background: #f5f5f5; padding: 3px 18px; border-radius: 8px; margin-right: 16px; cursor: pointer;">
                                        <input type="file" id="fileInput" style="display: none;" name="media"
                                            accept=".jpeg, .jpg, .png, .webp, .pdf, .doc, .docx, .xls, .xlsx, .mp4, .mkv">
                                        <i class="fa fa-picture-o"></i> Media
                                    </li>
                                    <li
                                        style="background: #f5f5f5; padding: 3px 18px; border-radius: 8px; margin-right: 16px; cursor: pointer;">
                                        <i class="fa fa-picture-o"></i> 360 Media
                                    </li>
                                <?php endif; ?>
                            </ul>

                            <hr>

                            <div class="d-flex align-items-center">
                                <img class="mr-4"
                                    src="https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_960_720.png"
                                    style="width: 60px; height: 60px; object-fit: cover; border-radius: 100px;"
                                    alt="">
                                <?php if(auth()->guard()->check()): ?>
                                    <textarea style="width: 100%; border: 0; outline: none;" name="message" placeholder="What's new?"
                                        oninput="auto_grow(this)"></textarea>
                                <?php else: ?>
                                    <textarea style="width: 100%; border: 0; outline: none;" name="message"
                                        placeholder="Please register or login to write status" oninput="auto_grow(this)" disabled></textarea>
                                <?php endif; ?>
                            </div>

                            <div id="search-tag" class="w-100 mt-4 position-relative d-none"
                                style="padding: 15px;background-color: #f4f4f4;border-top: 1px solid #eee; border-bottom: 1px solid #eee;">
                                <select class="form-control position-relative p-2 select-search" name="state"
                                    style="padding-left:2.5em;">
                                    <option value="search">Search your friends!</option>
                                    <option value="WY">Wyoming</option>
                                </select>
                                
                            </div>

                            <hr>

                            <div class="w-100 d-flex justify-content-between">
                                <div class="d-flex align-items-center">
                                    <select class="h-100" name="type_post"
                                        style="
                                        padding: 0 13px;
                                        background: #f5f5f5;
                                        border: 0;
                                        border-radius: 100px;
                                        font-weight: 600;
                                        outline: none;
                                    ">
                                        <option value="Public">Public</option>
                                        <option value="Only Me">Only Me</option>
                                        <option value="My Friend">My Friend</option>
                                        <option value="Members">Members</option>
                                    </select>
                                    <a class="cursor-pointer">
                                        <i class="fa fa-lg fa-smile-o ml-3"></i>
                                    </a>
                                    <div class="cursor-pointer" id="toogle-tag" onclick="showSelect()">
                                        <i class="fa fa-lg fa-tags ml-3"></i>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span>Posts in :</span>
                                    <select class="h-100 ml-3" name="type_post"
                                        style="
                                        padding: 0 13px;
                                        background: #f5f5f5;
                                        border: 0;
                                        border-radius: 100px;
                                        font-weight: 600;
                                        outline: none;
                                    ">
                                        <option value="My Profile">My Profile</option>
                                    </select>
                                    <?php if(auth()->guard()->check()): ?>
                                        <button type="submit" class="btn btn-primary ml-3"
                                            style="border-radius: 100px; outline: none;">
                                            POST
                                        </button>
                                    <?php else: ?>
                                        <button type="submit" class="btn btn-primary ml-3"
                                            style="border-radius: 100px; outline: none;" disabled>
                                            POST
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div id="modalPanorama" class="modal fade">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Select 360</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <label for="panoramaSelect" class="form-label">Select 360</label>
                                        <select id="panoramaSelect" name="ipanorama_id" class="form-control">
                                            <option value="">Select your 360</option>
                                            <?php $__currentLoopData = $dataIpanorama; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $panorama): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php if($panorama->code): ?>
                                                    <option value="<?php echo e($panorama->id); ?>"><?php echo e($panorama->title); ?></option>
                                                <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary" data-dismiss="modal">Save</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div id="content_status_post" style="display: block;">
                        <?php $__currentLoopData = $dataPostMe; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $postMe): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="w-100 mt-3" style="background: #FFF; border-radius: 8px; padding: 23px 35px;" id="Post-<?php echo e($postMe->id); ?>">
                                <div style="display: flex; align-items: center;">
                                    <img class="mr-4"
                                        src="https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_960_720.png"
                                        style="width: 60px; height: 60px; object-fit: cover; border-radius: 100px;"
                                        alt="">
                                    <div>
                                        <p class="m-0" style="font-weight: 600;">
                                            <?php echo e($postMe->name); ?>

                                        </p>
                                        <p class="m-0" style="font-size: 0.7rem; font-weight: 500; color: #9b9b9b;">
                                            <?php echo e($postMe->created_at->diffForHumans()); ?>

                                        </p>
                                    </div>
                                </div>
                                <hr>
                                <?php if($postMe->ipanorama_id): ?>
                                    <div id="panorama"></div>
                                <?php endif; ?>
                                <?php $count = 0; ?>
                                <?php $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($post->post_id == $postMe->id): ?>
                                        <?php
                                            $fileType = pathinfo($post->media, PATHINFO_EXTENSION);
                                        ?>

                                        <?php if(in_array(strtolower($fileType), ['jpeg', 'jpg', 'png', 'webp'])): ?>
                                            <a href="<?php echo e(asset('uploads/' . $post->media)); ?>" data-lightbox="image-1">
                                                <img src="<?php echo e(url('uploads/' . $post->media)); ?>" alt=""
                                                    style="width: 320px; height: 220px; position: relative;"
                                                    class="<?php echo e($count >= 4 ? 'd-none' : ''); ?>">
                                            </a>
                                        <?php elseif(in_array(strtolower($fileType), ['pdf', 'doc', 'docx', 'xls', 'xlsx'])): ?>
                                            <a href="<?php echo e(url('uploads/' . $post->media)); ?>" download
                                                style="text-decoration: none; color: #333;"
                                                class="<?php echo e($count >= 4 ? ' d-none' : ''); ?>">
                                                <div style="display: flex; align-items: center;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                        width="20" height="20" fill="none"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round" style="margin-right: 5px;">
                                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                        <polyline points="7 10 12 15 17 10"></polyline>
                                                        <line x1="12" y1="15" x2="12"
                                                            y2="3">
                                                        </line>
                                                    </svg>
                                                    Download File
                                                </div>
                                            </a>
                                        <?php elseif(in_array(strtolower($fileType), ['mp4', 'mkv'])): ?>
                                            <a href="<?php echo e(url('uploads/' . $post->media)); ?>" data-lightbox="videos"
                                                data-title="Video Title">
                                                <video width="320" height="240" controls
                                                    class="<?php echo e($count >= 4 ? 'd-none' : ''); ?>">
                                                    <source src="<?php echo e(url('uploads/' . $post->media)); ?>">
                                                </video>
                                            </a>
                                        <?php endif; ?>
                                        <?php $count++; ?>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                <p class="mt-3"
                                    style="
                                            font-size: 0.9rem;
                                            font-weight: 500;
                                            color: #9b9b9b;
                                        ">
                                    <?php echo e($postMe->message); ?>

                                </p>
                                <hr>
                                <?php
                                    $comments = $comments->where('post_id', $postMe->id);
                                ?>
                                
                                <?php if($comments->count() > 0): ?>
                                    <div class="w-100 mt-3"
                                        style="background: #FFF; border-radius: 8px; padding: 23px 35px;">
                                        <?php $__currentLoopData = $comments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div style="display: flex; align-items: center;">
                                                <img class="mr-4"
                                                    src="https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_960_720.png"
                                                    style="width: 60px; height: 60px; object-fit: cover; border-radius: 100px;"
                                                    alt="">
                                                <div>
                                                    <p class="m-0" style="font-weight: 600;">
                                                        <?php echo e($comment->user->name); ?>

                                                    </p>
                                                    <p class="m-0"
                                                        style="font-size: 0.7rem; font-weight: 500; color: #9b9b9b;">
                                                        <?php echo e($comment->created_at->diffForHumans()); ?>

                                                    </p>
                                                </div>
                                            </div>
                                            <hr>

                                            <p class="mt-3"
                                                style="
                                            font-size: 0.9rem;
                                            font-weight: 500;
                                            color: #9b9b9b;
                                        ">
                                                <?php echo e($comment->comment); ?>

                                            </p>
                                            <hr />
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="d-flex mt-4">
                                    <div class="d-flex">
                                        <img src="https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_960_720.png"
                                            style="width: 20px; height: 20px; object-fit: cover; border-radius: 100px;"
                                            alt="">
                                        <img src="https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_960_720.png"
                                            style="width: 20px; height: 20px; object-fit: cover; border-radius: 100px;margin-left: -5px;">
                                    </div>

                                    <p class="m-0 ml-2" style="color: #9b9b9b;">and
                                        <?php echo e($likes->where('post_id', $postMe->id)->count()); ?> Like This</p>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-around align-items-center">
                                    <?php
                                        $liked = $likes->where('post_id', $postMe->id)->where('user_id', Auth::id());
                                    ?>

                                    <?php if(auth()->guard()->check()): ?>
                                        <?php if($liked->count() > 0): ?>
                                            <a href="<?php echo e(route('user.post.like', ['id' => $postMe->id])); ?>"
                                                class="cursor-pointer" style="color: pink;">
                                                <i class="fa fa-heart"></i> Liked
                                            </a>
                                        <?php else: ?>
                                            <a href="<?php echo e(route('user.post.like', ['id' => $postMe->id])); ?>"
                                                class="cursor-pointer" style="color: #9b9b9b;">
                                                <i class="fa fa-heart-o"></i> Like
                                            </a>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?php if($liked->count() > 0): ?>
                                            <a class="cursor-pointer" style="color: pink;" onclick="alert('You need to login to like this post');">
                                                <i class="fa fa-heart"></i> Liked
                                            </a>
                                        <?php else: ?>
                                            <a class="cursor-pointer" style="color: #9b9b9b;" onclick="alert('You need to login to like this post');">
                                                <i class="fa fa-heart-o"></i> Like
                                            </a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <a class="cursor-pointer" style="color: #9b9b9b;"
                                        onclick="toggleCommentInput(<?php echo e($postMe->id); ?>)"><i
                                            class="fa fa-commenting-o"></i>
                                        Comment</a>
                                    <div class="btn-group">
                                        <button type="button" class="btn" data-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false" style="color: #9b9b9b;">
                                            <i class="fa fa-share-square-o"></i> Share
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item"
                                                href="https://www.facebook.com/sharer/sharer.php?u=<?php echo e(urlencode(url('/user/follow-boards#Post-' . $postMe->id))); ?>"
                                                target="_blank">
                                                <i class="fa fa-facebook"></i> Share on Facebook
                                            </a>
                                        </div>
                                    </div>

                                </div>
                                <?php if(auth()->guard()->check()): ?>
                                    <div id="commentInput_<?php echo e($postMe->id); ?>" style="display:none;" class="mt-2">
                                        <form action="<?php echo e(route('user.post.comment', ['id' => $postMe->id])); ?>" method="POST">
                                            <?php echo csrf_field(); ?>
                                            <div class="form-group" style="display: flex;">
                                                <textarea class="form-control" id="comment" name="comment" placeholder="Write your comment here" rows="3"
                                                    style="flex: 1;"></textarea>
                                                <button type="submit" class="btn btn-primary">Comment</button>
                                            </div>
                                        </form>
                                    </div>
                                <?php else: ?>
                                    <div id="commentInput_<?php echo e($postMe->id); ?>" style="display:none;" class="mt-2">
                                        <form id="commentForm">
                                            <div class="form-group" style="display: flex;">
                                                <textarea class="form-control" id="comment" name="comment" rows="3" style="flex: 1;"
                                                    placeholder="You need to log in to comment" disabled></textarea>
                                                <button type="button" class="btn btn-primary" onclick="submitComment()"
                                                    disabled>Comment</button>
                                            </div>
                                        </form>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <div id="content_feed_post" class="w-100 mt-3 grid-container"
                        style="background: #FFF; border-radius: 8px; display: none; padding: 23px 35px;">
                        <?php $__currentLoopData = $dataFeedMe; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feedMe): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="grid-item">
                                <img src="/uploads/<?= $feedMe->media ?>" alt="">
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
                <div class="col-md-3" style="background: #f5f5f5; padding: 0 20px;">
                    <div class="w-100" style="background: #FFF; border-radius: 8px; padding: 23px 35px;">
                        <div>
                            <p class="m-0">
                                <i class="fa fa-globe mr-2"></i>
                                All Members
                                <span class="badge badge-primary"><?php echo e(count($dataUser)); ?></span>
                            </p>
                        </div>
                    </div>
                    <div class="w-100 mt-3" style="background: #FFF; border-radius: 8px; padding: 23px 35px;">
                        <div>
                            <p class="m-0">
                                <i class="fa fa-signal mr-2"></i>
                                Following
                                <span class="badge badge-primary">0</span>
                            </p>
                        </div>
                    </div>
                    <hr class="mx-4">
                    <div class="w-100" onclick="feedShow()" id="feed_post"
                        style="cursor: pointer; background: #FFF; border-radius: 8px; padding: 23px 35px;">
                        <div>
                            <p class="m-0">
                                <i class="fa fa-picture-o mr-2"></i>
                                My Feed
                            </p>
                        </div>
                    </div>
                    <div class="w-100" onclick="statusShow()" id="status_post"
                        style="cursor: pointer; background: #FFF; border-radius: 8px; padding: 23px 35px; display: none;">
                        <div>
                            <p class="m-0">
                                <i class="fa fa-comments mr-2"></i>
                                My Post
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('css'); ?>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        #search-tag span.select2.select2-container {
            width: 100% !important;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('js'); ?>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="<?php echo e(asset('libs/ipanorama/src/lib/jquery.min.js')); ?>"></script>
    <script src="<?php echo e(asset('libs/ipanorama/src/jquery.ipanorama.js')); ?>"></script>
    <script src="<?php echo e(asset('libs/ipanorama/src/lib/three.min.js')); ?>"></script>
    <script>
        function feedShow() {
            document.getElementById('content_status_post').style.display = "none"
            document.getElementById('feed_post').style.display = "none"
            document.getElementById('status_post').style.display = "block"
            document.getElementById('content_feed_post').style.display = "grid"
        }

        function statusShow() {
            document.getElementById('content_status_post').style.display = "block"
            document.getElementById('content_feed_post').style.display = "none"
            document.getElementById('feed_post').style.display = "block"
            document.getElementById('status_post').style.display = "none"
        }

        function auto_grow(element) {
            element.style.height = "5px";
            element.style.height = (element.scrollHeight) + "px";
        }

        function showSelect() {
            const searchTag = document.getElementById("search-tag");
            if (searchTag.classList.contains("d-block")) {
                searchTag.classList.remove("d-block");
                searchTag.classList.add("d-none");
            } else {
                searchTag.classList.remove("d-none");
                searchTag.classList.add("d-block");
            }
        }

        $(document).ready(function() {
            $('.select-search').select2();
        });
    </script>
    <script>
        lightbox.option({
            'resizeDuration': 200,
            'wrapAround': false,
            'disableScrolling': true,
        })
    </script>
    <script>
        var panoramaData = <?php echo json_encode($postMe->panorama->code ?? ''); ?>;

        var panorama = $("#panorama").ipanorama(panoramaData);
    </script>
    <script>
        function toggleCommentInput(postId) {
            var commentInput = document.getElementById('commentInput_' + postId);
            commentInput.style.display = (commentInput.style.display === 'none' || commentInput.style.display === '') ?
                'block' : 'none';
        }

        function submitComment(postId) {
            // Add your logic to submit the comment (e.g., AJAX request)
            var commentText = document.getElementById('commentText_' + postId).value;
            console.log('Submitted Comment for Post ' + postId + ':', commentText);

            // Optionally, hide the comment input after submission
            document.getElementById('commentInput_' + postId).style.display = 'none';
        }
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\virtuard\resources\views/boards/index.blade.php ENDPATH**/ ?>