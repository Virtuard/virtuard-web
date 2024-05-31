document.addEventListener("DOMContentLoaded", function(event) { 
    //
});

/* ------------------------------Copy Clipboard-------------------------------- */
function copyToClipboard(element) {
	let $temp = $("<input>");
	$("body").append($temp);

    var textToCopy = $(element).text();

	$temp.val(textToCopy).select();
	document.execCommand("copy");

	$('#share-copy-btn')
    .tooltip('hide')
	.attr('data-original-title', 'Copied')
	.tooltip('show');

	$temp.remove();
}

function outCopyFunc() {
	$('#share-copy-btn').tooltip('hide')
	.attr('data-original-title', 'Copy to clipboard');
}
/* ------------------------------Copy Clipboard-------------------------------- */