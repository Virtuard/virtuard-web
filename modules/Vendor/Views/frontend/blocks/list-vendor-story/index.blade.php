<link href="{{ asset('libs/lightbox2/dist/css/lightbox.css') }}" rel="stylesheet" />
<style>
    .item {
        position: relative;
    }

    .image-overlay {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        overflow: hidden;
        padding: 3px;
        background: linear-gradient(45deg, rgb(255, 230, 0), rgb(255, 0, 128) 80%);
    }

    .image-overlay img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
        border: 2px solid #fff;

    }

    .custom-text-container {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .black-background {
        background: rgba(0, 0, 0, 0.8);
        padding: 20px;
        border-radius: 5px;
        color: #fff;
    }

    .centered-text {
        text-align: center;
    }

    .user-story {
        margin: 0 10px;
        cursor: pointer;
    }

    .profile-pic {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        border: 2px solid #dd3651;
    }

    .story-popup {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.8);
        justify-content: center;
        align-items: center;
        z-index: 1;
    }

    .story-container {
        position: relative;
        width: 80%;
        height: 80%;
        max-width: 600px;
        max-height: 600px;
        overflow: hidden;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
    }

    .story-bar-container {
        position: absolute;
        top: 10px;
        left: 10px;
        right: 10px;
        height: 5px;
        background-color: rgba(255, 255, 255, 0.5);
        border-radius: 2.5px;
        overflow: hidden;
    }

    .story-bar {
        width: 0;
        height: 100%;
        background-color: #fff;
    }

    .story-content {
        width: 100%;
        height: 100%;
        overflow: hidden;
    }

    .story-item {
        display: none;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .story-item.active {
        display: block;
    }
    .prev-btn,
    .next-btn {
        position: absolute;
        top: 50%;
        padding: 0px 10px;
        background-color: #ccc;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 24px;
    }

    .prev-btn {
        left: 10px;
    }

    .next-btn {
        right: 10px;
    }

    .prev-btn:hover,
    .next-btn:hover {
        background-color: #ddd;
    }

    .prev-btn::after {
        content: '\00AB';
    }

    .next-btn::after {
        content: '\00BB';
    }

    .story-control-container {
        display: flex;
        justify-content: space-between;
        position: absolute;
        top: 20px;
        left: 10px;
        right: 10px;
    }
    .story-control {
        cursor: pointer;
    }
    .story-control .dropdown-menu {
        left: unset !important;
        right: 0 !important;
        transform: translate3d(0px, 20px, 0px) !important;
        min-width: unset !important;
        padding: unset !important;
        margin: unset;
    }
    .story-control .dropdown-menu .dropdown-item {
    }
    .story-control .dropdown-toggle::after {
        content: unset;
    }
</style>
<div class="bravo-list-vendor story-vendor" style="margin-bottom:20px;">
    <!-- Button trigger modal -->

    <!-- Modal Add -->
    <div class="modal fade" id="addStory" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Story</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <label for="linkText">Story Title</label>
                            <input type="text" class="form-control" name="linkText" id="linkText"
                                aria-describedby="linkTextHelp" maxlength="50">
                            <small id="captionHelp" class="form-text text-muted">Ie: "See Article"</small>
                        </div>
                        <div class="form-group d-none">
                            <label for="link">Story Link</label>
                            <input type="text" class="form-control" name="link" id="link"
                                aria-describedby="linkHelp">
                        </div>
                        <div class="form-group">
                            <label for="media">Story Media</label>
                            <input type="file" class="form-control" name="media" id="media"
                                aria-describedby="mediaHelp" accept="image/*,.mp4">
                            <small id="mediaHelp" class="form-text text-muted">Recommended size: 1080x1920px and max 5MB.</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="addStoryInput()">Publish</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Show Story -->
    {{-- <div class="modal fade" id="showMediaStory" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-body p-0">
            <img id="img_story" style="width: 100%;" src="-" alt="">
          </div>
        </div>
      </div>
    </div> --}}
    <div class="modal fade" id="showMediaStory" tabindex="-1" role="dialog" aria-labelledby="showMediaStoryLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-story" role="document">
            <div class="modal-content p-0" style="position: relative;">
                <img id="modalImage" src="" alt="" style="width: 100%; padding: 0;">

                <button id="previousButton" class="btn modal-nav-button prev"><i class="fa fa-arrow-left"
                        aria-hidden="true" style="color: white;"></i>
                </button>

                <button id="nextButton" class="btn modal-nav-button next"><i class="fa fa-arrow-right"
                        aria-hidden="true" style="color:white;"></i>
                </button>

                <p id="modalText" class="text-center"></p>
            </div>
        </div>
    </div>

    <div class="container" style="margin-top:20px;">
        @if (!empty($desc))
            <div class="title">
                {{ $title }}
                @if (!empty($desc))
                    <div class="sub-title">
                        {{ $desc }}
                    </div>
                @endif
            </div>
        @endif
        <div class="list-item story-vendor">
            <div class="d-flex" style="overflow-x: auto;">
                    <div class="item relative" style="background-color: transparent;">
                        <div data-toggle="modal" data-target="{{ Auth::check() ? '#addStory' : '#login' }}" class="cursor-pointer"
                            style="
                        position: absolute;
                        z-index: 1;">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24">
                                <path d="M0 0h24v24H0z" fill="none"></path>
                                <path fill="#64B5F6"
                                    d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm5 11h-4v4h-2v-4H7v-2h4V7h2v4h4v2z">
                                </path>
                            </svg>
                        </div>
                        <div class="image" id="my_self" onclick="showStory()">
                            <img src="/images/avatar.png" class="w-100">
                        </div>
                    </div>

                @php
                    $today = \Carbon\Carbon::now(); 
                    $stories = \App\Models\Story::query()
                        ->whereDate('created_at', $today->toDateString())
                        ->orderBy('created_at', 'desc') 
                        ->get();

                    $groupedStoriesByUser = $stories->groupBy('user_id');

                    $storyUsers = $groupedStoriesByUser->sortByDesc(function ($story, $userId) {
                        return $story->first()->id;
                    });
                @endphp

                @foreach ($storyUsers as $ks => $story)
                    <div class="user-story" data-userId="{{ $story[0]->user_id }}">
                        @php
                            $type = get_file_type($story[0]->media);
                            $thumb = asset('uploads/'.$story[0]->media);
                            if ($type != 'image') {
                                $thumb = $story[0]->user->getAvatarUrl();
                            }
                        @endphp
                        <img src="{{ $thumb }}" alt="" class="profile-pic">
                    </div>
                @endforeach

                <div class="story-popup" id="story-popup">
                    <div class="story-container">
                        <div class="story-bar-container">
                            <div class="story-bar" id="story-bar"></div>
                        </div>
                        <div class="story-control-container">
                            <div class="story-profile">
                            </div>
                            <div id="story-control" class="story-control d-none">
                                <div class="btn-group" role="group">
                                    <div id="btnGroupControl" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <svg
                                        aria-label="Menu"
                                        color="#ffffff"
                                        fill="#ffffff"
                                        height="24"
                                        role="img"
                                        viewBox="0 0 24 24"
                                        width="24"
                                        >
                                        <path
                                            d="M12 9.75A2.25 2.25 0 1014.25 12 2.25 2.25 0 0012 9.75zm-6 0A2.25 2.25 0 108.25 12 2.25 2.25 0 006 9.75zm12 0A2.25 2.25 0 1020.25 12 2.25 2.25 0 0018 9.75z"
                                            fill-rule="evenodd"
                                        ></path>
                                    </svg>
                                    </div>
                                    <div id="story-action-menu" class="dropdown-menu" aria-labelledby="btnGroupControl">
                                    <a class="dropdown-item btn-delete-story" href="javascript:void(0)" data-id="">Delete</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="story-content" id="story-content">
                            @foreach ($storyUsers as $ks => $stories)
                            @foreach ($stories as $story)
                            @php
                                $storyType = get_file_type($story->media);
                                $isOwner = false;
                                if (auth()->check()) {
                                    if ((auth()->user()->id == $story->user_id) || auth()->user()->isAdmin()) {
                                        $isOwner = true;
                                    }
                                }
                            @endphp
                                @if($storyType == 'image')
                                    <img src="{{ asset('uploads/'.$story->media) }}" class="story-item" data-id="{{ $story->id }}" data-userId="{{ $story->user_id }}" data-isowner="{{ $isOwner }}" data-type="image" />
                                @elseif($storyType == 'video')
                                    <video src="{{ asset('uploads/'.$story->media) }}" class="story-item" data-id="{{ $story->id }}" data-userId="{{ $story->user_id }}" data-iswoner="{{ $isOwner }}" data-type="video"></video>
                                @endif
                            @endforeach
                            @endforeach
                        </div>
                        <span class="prev-btn" id="prev-btn"></span>
                        <span class="next-btn" id="next-btn"></span>
                    </div>
                </div>

            </div>
        </div>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const userStories = document.querySelectorAll('.user-story');
        const storyPopup = document.getElementById('story-popup');
        const stories = document.querySelectorAll('.story-item');
        const storyBar = document.getElementById('story-bar');
        const nextBtn = document.getElementById('next-btn');
        const prevBtn = document.getElementById('prev-btn');
        const btnGroupControl = document.getElementById('btnGroupControl');
        let currentIndex = 0;
        let currentStoryId;
        let interval;
        let isPaused = false;
        let pauseWidth = 0;
        let pauseDuration = 0;
        let pauseStartTime = 0;

        function showStories(index) {
            const currentUser = stories[index].getAttribute('data-userId');
            const isOwner = stories[index].getAttribute('data-isowner');
            if (isOwner) {
                $('#story-control').removeClass('d-none');
            }
            stories.forEach((story, i) => {
                currentStoryId = stories[index].getAttribute('data-id');
                story.classList.remove('active');
                if (i === index) {
                    story.classList.add('active');
                    if (story.tagName === 'VIDEO') {
                        story.play();
                        story.onended = nextStory;
                    } else {
                        clearTimeout(interval);
                        interval = setTimeout(nextStory, 3000);
                    }
                } else if (story.tagName === 'VIDEO') {
                    story.pause();
                    story.currentTime = 0;
                }
            });

            storyBar.style.transition = 'none';
            storyBar.style.width = `${pauseWidth}%`;

            setTimeout(() => {
                const duration = stories[index].tagName === 'VIDEO' ? stories[index].duration * 1000 : 3000;
                storyBar.style.transition = `width ${duration}ms linear`;
                storyBar.style.width = '100%';
            }, 10);
        }

        function nextStory() {
            currentIndex++;
            if (currentIndex >= stories.length || stories[currentIndex].getAttribute('data-userId') !== stories[currentIndex - 1].getAttribute('data-userId')) {
                storyPopup.style.display = 'none';
                currentIndex = 0;
                clearTimeout(interval);
            } else {
                showStories(currentIndex);
            }
        }

        function prevStory() {
            currentIndex--;
            if (currentIndex < 0 || stories[currentIndex].getAttribute('data-userId') !== stories[currentIndex + 1].getAttribute('data-userId')) {
                storyPopup.style.display = 'none';
                currentIndex = 0;
                clearTimeout(interval);
            } else {
                showStories(currentIndex);
            }
        }

        function pauseStories() {
            isPaused = true;
            clearTimeout(interval);

             // Mendapatkan lebar komputasi dari story bar
            const computedStyle = window.getComputedStyle(storyBar);
            const storyBarWidth = parseFloat(computedStyle.width);
            const parentWidth = parseFloat(storyBar.parentElement.clientWidth);
            pauseWidth = (storyBarWidth / parentWidth) * 100;

            // Menghentikan animasi story bar
            storyBar.style.transition = 'none';

            // Menghitung waktu yang tersisa
            const storyDuration = stories[currentIndex].tagName === 'VIDEO' ? stories[currentIndex].duration * 1000 : 3000;
            const progress = (parseFloat(storyBar.style.width) / 100) * storyDuration;
            pauseDuration = storyDuration - progress;

            stories.forEach(story => {
                if (story.tagName === 'VIDEO') {
                    story.pause();
                }
            });

            // Simpan waktu mulai pause untuk menghitung sisa waktu dengan tepat
            pauseStartTime = Date.now();

            storyBar.style.width = `${pauseWidth}%`;
        }

        function resumeStories() {
            isPaused = false;
            const resumeTime = Date.now() - pauseStartTime;
            const storyDuration = stories[currentIndex].tagName === 'VIDEO' ? stories[currentIndex].duration * 1000 : 3000;
            const remainingTime = Math.max(pauseDuration - resumeTime, 0);

            storyBar.style.transition = `width ${storyDuration}ms linear`;
            storyBar.style.width = '100%';
            interval = setTimeout(nextStory, storyDuration);

        }

        userStories.forEach(userStory => {
            userStory.addEventListener('click', () => {
                const userId = userStory.getAttribute('data-userId');
                currentIndex = Array.from(stories).findIndex(stories => stories.getAttribute('data-userId') === userId);
                storyPopup.style.display = 'flex';
                showStories(currentIndex);
            });
        });

        nextBtn.addEventListener('click', nextStory);
        prevBtn.addEventListener('click', prevStory);
        btnGroupControl.addEventListener('click', pauseStories);

        // storyPopup.addEventListener('click', () => {
        //     storyPopup.style.display = 'none';
        //     clearTimeout(interval);
        // });

        $('.btn-delete-story').on('click', function(e){
            e.preventDefault();
            e.stopImmediatePropagation();

            $.ajax({
                url:`/story/${currentStoryId}`,
                method: 'DELETE',
                success:function (json) {
                    if (json.status) {
                        location.reload();
                    }
                },
                error:function (e) {
                    //
                }
            });
        });
    });
</script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function openMediaModal(mediaUrl, altText) {
            var modal = $('#showMediaStory');
            modal.find('#modalImage').attr('src', mediaUrl);
            modal.find('#modalText').text(altText);
            modal.modal('show');
        }
    </script>

    <script type="text/javascript">
        function addStoryInput() {
            const storyLinkText = document.getElementById('linkText').value;
            const storyLink = document.getElementById('link').value;
            const storyMedia = document.getElementById('media');

            var formData = new FormData();
            formData.append('linkText', storyLinkText);
            formData.append('link', storyLink);
            formData.append('media', storyMedia.files[0]);

            var requestOptions = {
                method: 'POST',
                body: formData,
            };

            fetch('/story', requestOptions)
                .then(function(response) {
                    if (!response.ok) {
                        throw new Error('Gagal melakukan add');
                    }
                    return response.json();
                })
                .then(function(data) {
                    if (data.status == 'success') {
                    // Tampilkan SweetAlert ketika berhasil
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Add story successful!',
                    });

                    document.getElementById('my_self').style.border = '4px solid #ff9e9e;'

                    // Reset semua form
                    document.getElementById('linkText').value = '';
                    document.getElementById('link').value = '';
                    document.getElementById('media').value = '';

                    // Reload the page
                    location.reload();
                    }else {
                        Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Somtehing wrong. Add story error!',
                    });
                    }
                })

                .catch(function(error) {
                    console.error('Kesalahan:', error);
                });
        }

        function getStory() {
            var requestOptions = {
                method: 'GET',
            };

            fetch('/story/list', requestOptions)
                .then(function(response) {
                    if (!response.ok) {
                        throw new Error('Gagal melakukan add');
                    }
                    return response.json();
                })
                .then(function(data) {
                    return true
                })
                .catch(function(error) {
                    console.error('Kesalahan:', error);
                });
        }

        function showStory() {
            var requestOptions = {
                method: 'GET',
            };

            fetch('/user/get/story/api', requestOptions)
                .then(function(response) {
                    if (!response.ok) {
                        throw new Error('Gagal melakukan add');
                    }
                    return response.json();
                })
                .then(function(data) {
                    document.getElementById('img_story').src = `/uploads/${data.data[0].media}`
                    $('#showMediaStory').modal('show');
                })
                .catch(function(error) {
                    console.error('Kesalahan:', error);
                });

        }
    </script>
</div>

@push('js')
    <script>
        lightbox.option({
            'resizeDuration': 200,
            'wrapAround': false,
            'disableScrolling': true,
        })
    </script>
@endpush
