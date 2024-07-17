<script>
    $(document).ready(function() {
        copyReferralLink();
    });

    function copyReferralLink() {
        const url = "{{ $row->getDetailUrl() }}";
        const isLoggedIn = "{{ auth()->check() ? 'true' : 'false' }}";
        const userName = "{{ auth()->user()->user_name ?? 'null' }}";
        const isSubscribed = "true";
        
        document.getElementById('copyReferralButton').addEventListener('click', function() {
            console.log(isLoggedIn)
            if (isLoggedIn && isSubscribed) {
                let referralUrl = `${url}?reference=${userName}`;

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
                // Show an error message if the user is not logged in or not subscribed
            }
        });
    }
</script>
