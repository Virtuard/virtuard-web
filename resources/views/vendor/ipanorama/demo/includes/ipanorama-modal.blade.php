 <!-- panoramaModal -->
 <div class="modal fade" id="panoramaModal" tabindex="-1" role="dialog" aria-labelledby="panoramaModalLabel"
     aria-hidden="true">
     <div class="modal-dialog modal-lg" role="document">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title" id="panoramaModalLabel">Preview</h5>
                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true">&times;</span>
                 </button>
             </div>
             <div class="modal-body">
                {{-- @isset($post) --}}
                {{-- and $post->ipanorama->author->checkUserPlanStatus() --}}
                @if ($post->ipanorama && $post->ipanorama->status == 'publish')
                 <div id="mypanorama" class="load-panorama"
                     style=" position: relative; width: 100%; height: 450px; z-index: 1;">
                 </div>
                 @else
                 <p class="text-center">{{ __("If you don't preview the 360 tour. The uploader does not have a subscription plan or the subscription has expired.") }}</p>
                 @endif
                {{-- @endisset --}}
             </div>
             <div class="modal-footer">
                 <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
             </div>
         </div>
     </div>
 </div>

 @push('js')
    <script>
        $(document).ready(function() {
            previewPanorama();
        });

        function previewPanorama() {
            $('.preview-panorama').click(function() {
                let panoramaCode = $(this).data('code');
                let userId = $(this).data('user_id');
                panoramaCode = JSON.stringify(panoramaCode);
                panoramaCode = panoramaCode.replaceAll(`upload/`, `/uploads/ipanoramaBuilder/upload/${userId}/`);
                panoramaCode = JSON.parse(panoramaCode)
                $(`#mypanorama`).ipanorama(panoramaCode);
                $('#panoramaModal').modal('toggle');
            })
        }
    </script>
 @endpush
