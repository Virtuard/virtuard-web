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
                            <label for="linkText">Story Link Text</label>
                            <input type="text" class="form-control" name="linkText" id="linkText"
                                aria-describedby="linkTextHelp" maxlength="50">
                            <small id="captionHelp" class="form-text text-muted">Ie: "See Article"</small>
                        </div>
                        <div class="form-group">
                            <label for="link">Story Link</label>
                            <input type="text" class="form-control" name="link" id="link"
                                aria-describedby="linkHelp">
                        </div>
                        <div class="form-group">
                            <label for="media">Story Media</label>
                            <input type="file" class="form-control" name="media" id="media"
                                aria-describedby="mediaHelp">
                            <small id="mediaHelp" class="form-text text-muted">Recommended sizes: 1080x1920 px.</small>
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
        @if ($title)
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
            <div class="d-flex">
                @if (!Auth::check())
                    <div class="item relative">
                        <div class="image">
                            <img src="/images/avatar.png" class="w-100">
                        </div>
                    </div>
                @else
                    <div class="item relative">
                        <div data-toggle="modal" data-target="#addStory" class="cursor-pointer"
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
                @endif

                @foreach ($storyData as $story)
                    @php
                        $storyLink = $story->link ?? '';
                        $storyLinkText = $story->link_text ?? '';
                    @endphp
                    <div class="item">
                        <div class="image">
                            @if ($story)
                                <a href="{{ asset('uploads/' . $story->media) }}" data-lightbox="image-1"
                                    data-title="{{ $story->link_text ?? '' }}" data-story="{{ $story->link ?? '' }}">
                                    <div class="image-overlay">
                                        <img src="{{ asset('uploads/' . $story->media) }}"
                                            alt="{{ $story->link_text }}" class="w-100" style="object-fit: cover;">
                                    </div>
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </div>

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

            fetch('/user/add/story/api', requestOptions)
                .then(function(response) {
                    if (!response.ok) {
                        throw new Error('Gagal melakukan add');
                    }
                    return response.json();
                })
                .then(function(data) {
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
                })

                .catch(function(error) {
                    console.error('Kesalahan:', error);
                });
        }

        function getStory() {
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
