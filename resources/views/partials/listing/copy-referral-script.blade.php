<script>
    $(document).ready(function() {
        copyReferralLink();
    });

    function copyReferralLink() {
        const isLoggedIn = {{ Auth::check() ? 'true' : 'false' }};
        const userId = {{ auth()->user()->id ?? 'null' }};
        const isSubscribed = 1;

        document.getElementById('copyReferralButton').addEventListener('click', function() {
            if (isLoggedIn && isSubscribed) {
                let referralUrl = `{{ route('business.detail', $row->slug) }}?reference=${userId}`;

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
