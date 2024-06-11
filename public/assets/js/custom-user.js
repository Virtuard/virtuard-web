document.addEventListener("DOMContentLoaded", function(event) { 
    // initActiveMenuListing();
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

/* ------------------------------Copy Clipboard-------------------------------- */
function copyToClipboard(str) {
    let text = document.getElementById(str).innerText;
    
    actionCopyToClipBoard(text)
}

function outCopyFunc() {
    $("#share-copy-btn")
        .tooltip("hide")
        .attr("data-original-title", "Copy to clipboard");
}

function actionCopyToClipBoard(text) {
    if (navigator.clipboard && window.isSecureContext) {
        // use Clipboard API if exist
        navigator.clipboard
            .writeText(text)
            .then(function () {
                // console.error("Text Copied: ", text);
            })
            .catch(function (err) {
                // console.error("Error copy text: ", err);
            });
    } else {
        let tempInput = $("<input>");
        tempInput.attr("type", "text");
        $("body").append(tempInput);
        tempInput.val(text).select();
        document.execCommand("copy");
        tempInput.remove();
    }

    $("#share-copy-btn")
        .tooltip("hide")
        .attr("data-original-title", "Copied")
        .tooltip("show");
}
/* ------------------------------Copy Clipboard-------------------------------- */