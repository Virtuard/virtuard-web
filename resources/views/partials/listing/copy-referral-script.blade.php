<script>
    var isLoggedIn = "{{ auth()->check() ? 'true' : 'false' }}";

    $(document).ready(function() {
        openModalReferral();
        copyReferralLink();
    });

    function openModalReferral() {
        document.getElementById('sellButtonReferral').addEventListener('click', function(e) {
            if (isLoggedIn == 'true') {
                $('#modalCopyReferral').modal('show');
            } else {
                $('#login').modal('show');
            }
        });
    }

    function copyReferralLink() {
        document.getElementById('copyReferralButton').addEventListener('click', function(e) {
            let refUrl = this.getAttribute('data-ref');
            if (isLoggedIn == 'true') {
                actionCopyToClipBoard(refUrl)
            } else {
                $('#login').modal('show');
            }
        });
    }
</script>
