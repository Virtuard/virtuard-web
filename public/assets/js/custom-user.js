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
