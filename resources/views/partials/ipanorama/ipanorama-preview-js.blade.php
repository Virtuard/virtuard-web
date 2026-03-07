<script>
    $(document).ready(function() {
        previewPanorama();
    });

    function previewPanorama() {
        let panoramaCode = $('#data-panorama').data('code');
        let userId = $('#data-panorama').data('user_id');
        panoramaCode = JSON.stringify(panoramaCode);
        panoramaCode = panoramaCode.replaceAll(`upload/`, `/uploads/ipanoramaBuilder/upload/${userId}/`);
        panoramaCode = panoramaCode.replaceAll(`/uploads/ipanoramaBuilder/upload/${userId}/${userId}/`, `/uploads/ipanoramaBuilder/upload/${userId}/`);
        panoramaCode = JSON.parse(panoramaCode)
        $(`#mypanorama`).ipanorama(panoramaCode);
    }
</script>