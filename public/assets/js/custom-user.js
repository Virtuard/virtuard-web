$(document).on('ready', function() {
    initActiveMenuListing();
    onClickMenuListing();
});

function onClickMenuListing() {
    $('.nav-category.nav-listing').on('click', function (e) {
        let navListing = $(this);
        $(".listing").each(function () {
            if (navListing.hasClass('active_child')) {
                $(this).removeClass('d-none');
            } else {
                $(this).addClass('d-none');
            }
        });
    });
}

function initActiveMenuListing() {
    $('.nav-category.nav-listing').addClass('active_child');
    $(".listing").each(function () {
        $(this).removeClass('d-none');
    });
}