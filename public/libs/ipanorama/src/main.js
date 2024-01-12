//=================================
// Panorama
//=================================
$(document).ready(function() {
	"use strict";
	var fullURL = document.getElementById('panId').value;
	var arr =  fullURL.split(',');
	var data = document.getElementById('panId').value;

	data = data.replace('$("#mypanorama").ipanorama(', '').replace(');', '');

	// function replaceUploadPaths(data) {
    // // Fungsi rekursif untuk mengganti semua jalur 'upload/' menjadi '/uploads/public/images/ipanorama/'
    // function replacePaths(obj) {
    //     for (let key in obj) {
    //         if (typeof obj[key] === 'string') {
    //             obj[key] = obj[key].replace('upload/', '/uploads/ipanoramaBuilder/assets/images/');
    //         } else if (typeof obj[key] === 'object') {
    //             replacePaths(obj[key]);
    //         }
    //     }
    // }

    // replacePaths(data);
	// }

	// replaceUploadPaths(data);

	function replacePaths(obj) {
	    for (let key in obj) {
	        if (typeof obj[key] === 'string') {
	            obj[key] = obj[key].replace('upload/', '/uploads/ipanoramaBuilder/upload/');
	        } else if (typeof obj[key] === 'object') {
	            replacePaths(obj[key]);
	        }
	    }
	}

	var dataObj = JSON.parse(data);

	replacePaths(dataObj);

	var panorama = $("#panorama").ipanorama(
		dataObj
	);
	
	// var panorama = $("#panorama").ipanorama({
	// 	theme: "ipnrm-theme-default",
	// 	hotSpotSetup: false,
	// 	onShare: function(e) {
	// 		console.log("create a share dialog");
	// 	},
	// 	onHotSpotSetup: function(yaw, pitch, cameraYaw, cameraPitch, cameraZoom) {
	// 		console.log("yaw: " + yaw + ", pitch: " + pitch + ", cameraYaw: " + cameraYaw + ", cameraPitch: " + cameraPitch + ", cameraZoom: " + cameraZoom);
	// 	},
	// 	grab: true,
	// 	hoverGrab: false,
	// 	autoLoad: true,
	// 	autoRotate: false,
	// 	showControlsOnHover: false,
	// 	showSceneThumbsCtrl: false,
	// 	showSceneMenuCtrl: true,
	// 	showSceneNextPrevCtrl: true,
	// 	showZoomCtrl: true,
	// 	showShareCtrl: true,
	// 	showFullscreenCtrl: true,
	// 	sceneNextPrevLoop: true,
	// 	popoverHideTrigger: "manual",
	// 	popoverShowClass: "fx-rotateIn",
	// 	popoverHideClass: "fx-bounceOut",
	// 	pitchLimits: false,
	// 	yawLimits: true,
	// 	sceneThumbsVertical: true,
	// 	sceneBackgroundLoad: false,
	// 	sceneId: "main",
	// 	scenes: {
	// 		main: {
	// 			type: "sphere", // specifies the scene type ("box", "sphere", "cylinder")
	// 			//titleHtml:true,
	// 			titleSelector: "#title",
	// 			//title: "Main Scene",
	// 			yaw: 0,
	// 			pitch: 0,
	// 			compassNorthOffset: 0,
	// 			thumb: true,
	// 			thumbImage: `/uploads/${arr[0]}`,
	// 			image: `/uploads/${arr[0]}`,
	// 			hotSpots: [
	// 				{
	// 					yaw: 0, 
	// 					pitch: 0,
	// 					sceneId: "second",
	// 					popoverShow: true,
	// 					popoverPlacement: "top",
	// 					//popoverContent: "Hello Everyone .)",
	// 					popoverHtml: true,
	// 					popoverSelector: "#popover",
	// 				}, 
	// 				{
	// 					yaw: 0, 
	// 					pitch: 25,
	// 					popoverShow: true,
	// 					popoverPlacement: "top",
	// 					popoverContent: "Top",
	// 				},
	// 				{
	// 					yaw: 0, 
	// 					pitch: -15,
	// 					popoverShow: true,
	// 					popoverPlacement: "bottom",
	// 					popoverContent: "Bottom",
	// 				},
	// 				{
	// 					yaw: -45, 
	// 					pitch: 15,
	// 					popoverShow: true,
	// 					popoverPlacement: "top-right",
	// 					popoverContent: "Top right",
	// 				},
	// 				{
	// 					yaw: -45, 
	// 					pitch: 0,
	// 					popoverShow: true,
	// 					popoverPlacement: "right",
	// 					popoverContent: "Right",
	// 				},
	// 				{
	// 					yaw: -45, 
	// 					pitch: -15,
	// 					popoverShow: true,
	// 					popoverPlacement: "bottom-right",
	// 					popoverContent: "Bottom right",
	// 				},
	// 				{
	// 					yaw: 45, 
	// 					pitch: 15,
	// 					popoverShow: true,
	// 					popoverPlacement: "top-left",
	// 					popoverContent: "Top left",
	// 				},
	// 				{
	// 					yaw: 45, 
	// 					pitch: 0,
	// 					popoverShow: true,
	// 					popoverPlacement: "left",
	// 					popoverContent: "Left",
	// 				},
	// 				{
	// 					yaw: 45, 
	// 					pitch: -15,
	// 					popoverShow: true,
	// 					popoverPlacement: "bottom-left",
	// 					popoverContent: "Bottom left",
	// 				},
	// 			],
	// 		},
	// 		second: {
	// 			zoom: 40,
	// 			type: "sphere", // specifies the scene type ("box", "sphere", "cylinder")
	// 			title: "Second Scene",
	// 			thumb: true,
	// 			thumbImage: `/uploads/${arr[1]}`,
	// 			image: `/uploads/${arr[1]}`,
	// 			hotSpots: [
	// 				{
	// 					yaw: 0,
	// 					pitch: 0,
	// 					sceneId: "main",
	// 					popoverShow: true,
	// 					popoverContent: "Go Back to The Main Scene",
	// 				}
	// 			],
	// 		},
	// 		third: {
	// 			zoom: 40,
	// 			type: "sphere", // specifies the scene type ("box", "sphere", "cylinder")
	// 			title: "Third Scene",
	// 			thumb: true,
	// 			thumbImage: `/uploads/${arr[2]}`,
	// 			image: `/uploads/${arr[2]}`,
	// 		},
	// 		fourth: {
	// 			zoom: 40,
	// 			type: "sphere", // specifies the scene type ("box", "sphere", "cylinder")
	// 			title: "Fourth Scene",
	// 			thumb: true,
	// 			thumbImage: `/uploads/${arr[3]}`,
	// 			image: `/uploads/${arr[3]}`,
	// 		}
	// 	},
	// });

	$("#fullscreen").click(function() {
		panorama.ipanorama("fullscreen");
	});

	$("#grab").click(function() {
		panorama.ipanorama("grab");
	});
});
