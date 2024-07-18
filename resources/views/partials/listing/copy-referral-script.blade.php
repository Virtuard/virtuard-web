<script>
    $(document).ready(function() {
        copyReferralLink();
    });

    function copyReferralLink() {
        const url = "{{ $row->getDetailUrl() }}";
        const isLoggedIn = "{{ auth()->check() ? 'true' : 'false' }}";
        let reference = "{{ auth()->user()->user_name ?? 'null' }}";
        if (reference == 'null') {
            reference = "{{ auth()->user()->id ?? 'null' }}";
        }
        
        document.getElementById('copyReferralButton').addEventListener('click', function() {
            if (isLoggedIn == 'true') {
                let referralUrl = `${url}?reference=${reference}`;

                //Copy the referral URL to the clipboard
                var tempInput = document.createElement('input');
                tempInput.value = referralUrl;
                document.body.appendChild(tempInput);
                tempInput.select();
                document.execCommand('copy');
                document.body.removeChild(tempInput);

                Toast.fire({
                    icon: 'success',
                    title: 'Copied product referral'
                });

            } else {
                $('#login').modal('show');
            }
        });
    }
</script>
