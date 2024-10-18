<div class="row">
    <div class="col-md-12">
        <ul>
            <li class="my-5">
                <h6>Add New Image</h6>
                <img src="{{ asset('uploads/images/panorama-tutorial/add-image.png') }}" alt="">
                <ol class="m-3">
                    <li>Click button "Add New Image"</li>
                </ol>
            </li>
            <li class="my-5">
                <h6>Submit Image</h6>
                <img src="{{ asset('uploads/images/panorama-tutorial/submit-image.png') }}" alt="">
                <ol class="m-3">
                    <li>Set title image</li>
                    <li>Choose image</li>
                    <li>Click "Submit"</li>
                </ol>
            </li>
            <li class="mb-5">
                <h6>Open Scenes</h6>
                <img src="{{ asset('uploads/images/panorama-tutorial/section-scenes.png') }}" alt="">
                <ol class="m-3">
                    <li>Click "Tab Scenes"</li>
                    <li>Click add button "+" to open scenes form</li>
                </ol>
            </li>
            <li class="mb-5">
                <h6>Set Scenes</h6>
                <img src="{{ asset('uploads/images/panorama-tutorial/set-title-scene.png') }}" alt="">
                <ol class="m-3">
                    <li>Set title scene</li>
                    <li>Select image scene</li>
                </ol>
            </li>
            <li class="mb-5">
                <h6>Select Image Scene</h6>
                <img src="{{ asset('uploads/images/panorama-tutorial/select-image-scene.png') }}" alt="">
                <ol class="m-3">
                    <li>Select image scene</li>
                    <li>Click button "Select"</li>
                </ol>
            </li>
            <li class="mb-5">
                <h6>Set Yaw and Pitch Scene</h6>
                <img src="{{ asset('uploads/images/panorama-tutorial/set-yaw-scene.png') }}" alt="">
                <ol class="m-3">
                    <li>Click image and drag to set your image position</li>
                    <li>Click arrow button to copy coordinat "Yaw" and "Pitch"</li>
                </ol>
            </li>
            <li class="mb-5">
                <h6>Open Hotspots</h6>
                <img src="{{ asset('uploads/images/panorama-tutorial/section-hotspots.png') }}" alt="">
                <ol class="m-3">
                    <li>Select scene</li>
                    <li>Click tab "Hotspots"</li>
                </ol>
            </li>
            <li class="mb-5">
                <h6>Set Hotspots</h6>
                <img src="{{ asset('uploads/images/panorama-tutorial/set-hotspot.png') }}" alt="">
                <ol class="m-3">
                    <li>After your click tab "Hotspots", then</li>
                    <li>Click add button "+" to open hotspots form</li>
                    <li>Set "Yaw" and "Pitch" to get the position of hotspot</li>
                    <li>Set title of hotspot</li>
                    <li>Select the target of scene (when user click hotspot)</li>
                    <li>Add thumbnail of scene</li>
                </ol>
            </li>
            <li class="mb-5">
                <h6>Save Project</h6>
                <img src="{{ asset('uploads/images/panorama-tutorial/pra-save.png') }}" alt="">
                <ol class="m-3">
                    <li>After your setting the all scenes and hotspots, click "Save"</li>
                    <li>If a confirmation popup appears, click "Submit".</li>
                </ol>
            </li>
        </ul>
    </div>
</div>

@push('css')
    <style>
        img {
            width: 100%;
        }

        @media(max-width: 768px) {
            img {
                width: 100%;
            }
        }
    </style>
@endpush
