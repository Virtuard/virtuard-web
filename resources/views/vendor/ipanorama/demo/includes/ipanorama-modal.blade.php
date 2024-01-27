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
                 <div id="mypanorama" class="load-panorama"
                     style=" position: relative; width: 100%; height: 450px; z-index: 1;">
                 </div>
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
                 panoramaCode = JSON.stringify(panoramaCode);
                 panoramaCode = panoramaCode.replaceAll(`upload/`, '/uploads/ipanoramaBuilder/upload/');
                 panoramaCode = JSON.parse(panoramaCode)
                 $(`#mypanorama`).ipanorama(panoramaCode);
                 $('#panoramaModal').modal('toggle');
             })
         }
     </script>
 @endpush
