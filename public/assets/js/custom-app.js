document.addEventListener("DOMContentLoaded", function (event) {
    //
});

/* ------------------------------Copy Clipboard-------------------------------- */
function copyToClipboard(str) {
    let text = document.getElementById(str).innerText;
    
    actionCopyToClipBoard(text)
    
    $("#share-copy-btn")
        .tooltip("hide")
        .attr("data-original-title", "Copied")
        .tooltip("show");
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
}
/* ------------------------------Copy Clipboard-------------------------------- */
