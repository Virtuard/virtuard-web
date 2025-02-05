import { Viewer } from '@photo-sphere-viewer/core';
import { MarkersPlugin } from '@photo-sphere-viewer/markers-plugin';
import { AutorotatePlugin } from '@photo-sphere-viewer/autorotate-plugin';
import { VirtualTourPlugin } from '@photo-sphere-viewer/virtual-tour-plugin';
import { GalleryPlugin } from '@photo-sphere-viewer/gallery-plugin';

const baseUrl = '/assets/images/';
const baseUrl2 = 'https://photo-sphere-viewer-data.netlify.app/assets/';

const container = document.createElement('section'); 
const caption = 'Deep Blue Villa New <br> <b>&copy; virtuard.com</b>';

// const markerLighthouse = {
//     id: 'marker-1',
//     image: baseUrl2 + 'pictos/pin-red.png',
//     tooltip: 'Cape Florida Light, Key Biscayne',
//     size: { width: 32, height: 32 },
//     anchor: 'bottom center',
//     gps: [-80.155973, 25.666601, 29 + 3],
// };

const nodes = [
    {
        id: '1',
        panorama: baseUrl2 + 'tour/key-biscayne-1.jpg',
        thumbnail: baseUrl2 + 'tour/key-biscayne-1-thumb.jpg',
        // name: 'One',
        caption: `[1] ${caption}`,
        links: [
            { 
                nodeId: '2',
                position: { yaw: 10.0, pitch: 10.0 },
            }
        ],
        // markers: [markerLighthouse],
        gps: [-80.156479, 25.666725, 3],
        sphereCorrection: { pan: '33deg' },
    },
    {
        id: '2',
        panorama: baseUrl2 + 'tour/key-biscayne-2.jpg',
        thumbnail: baseUrl2 + 'tour/key-biscayne-2-thumb.jpg',
        // name: 'Two',
        caption: `[2] ${caption}`,
        links: [{ nodeId: '3' }, { nodeId: '1' }],
        // markers: [markerLighthouse],
        gps: [-80.156168, 25.666623, 3],
        sphereCorrection: { pan: '42deg' },
    },
    {
        id: '3',
        panorama: baseUrl2 + 'tour/key-biscayne-3.jpg',
        thumbnail: baseUrl2 + 'tour/key-biscayne-3-thumb.jpg',
        // name: 'Three',
        caption: `[3] ${caption}`,
        links: [{ nodeId: '4' }, { nodeId: '2' }, { nodeId: '5' }],
        gps: [-80.155932, 25.666498, 5],
        sphereCorrection: { pan: '50deg' },
    },
    {
        id: '4',
        panorama: baseUrl2 + 'tour/key-biscayne-4.jpg',
        thumbnail: baseUrl2 + 'tour/key-biscayne-4-thumb.jpg',
        // name: 'Four',
        caption: `[4] ${caption}`,
        links: [{ nodeId: '3' }, { nodeId: '5' }],
        gps: [-80.156089, 25.666357, 3],
        sphereCorrection: { pan: '-78deg' },
    },
    {
        id: '5',
        panorama: baseUrl2 + 'tour/key-biscayne-5.jpg',
        thumbnail: baseUrl2 + 'tour/key-biscayne-5-thumb.jpg',
        // name: 'Five',
        caption: `[5] ${caption}`,
        links: [{ nodeId: '6' }, { nodeId: '3' }, { nodeId: '4' }],
        gps: [-80.156292, 25.666446, 2],
        sphereCorrection: { pan: '170deg' },
    },
    {
        id: '6',
        panorama: baseUrl2 + 'tour/key-biscayne-6.jpg',
        thumbnail: baseUrl2 + 'tour/key-biscayne-6-thumb.jpg',
        // name: 'Six',
        caption: `[6] ${caption}`,
        links: [{ nodeId: '5' }, { nodeId: '7' }],
        gps: [-80.156465, 25.666496, 2],
        sphereCorrection: { pan: '65deg' },
    },
    {
        id: '7',
        panorama: baseUrl2 + 'tour/key-biscayne-7.jpg',
        thumbnail: baseUrl2 + 'tour/key-biscayne-7-thumb.jpg',
        // name: 'Seven',
        caption: `[7] ${caption}`,
        links: [{ nodeId: '6' }],
        gps: [-80.15707, 25.6665, 3],
        sphereCorrection: { pan: '110deg', pitch: -3 },
    },
];

const viewer = new Viewer({
    container: 'viewer',
    // panorama: baseUrl + 'tour-example-360.jpg',
    caption: 'Copyright &copy; 2025 virtuard.com. All Right Reserved',
    // loadingImg: 'https://photo-sphere-viewer-data.netlify.app/assets/loader.gif',
    // touchmoveTwoFingers: true,
    defaultYaw: 0,
    defaultPitch: 0,
    defaultZoomLvl: 20,
    fisheye: true,
    navbar: [
        'fullscreen',
    ],
    plugins: [
        [MarkersPlugin, {
            markers: [
                {
                    id: 'Register For Free',
                    elementLayer: container,
                    position: { yaw: 0, pitch: 0.2},
                    rotation: { yaw: 0 },
                },
            ],
        }],
        [AutorotatePlugin, {
            autorotatePitch: '3deg',
        }],
        [GalleryPlugin, {
            thumbnailSize: { width: 100, height: 100 },
        }],
        [VirtualTourPlugin, {
            positionMode: 'gps',
            renderMode: '3d',
            nodes: nodes,
            startNodeId: '1',   
        }],
    ],
});

// Function to handle fullscreen change
function handleFullscreenChange() {
    const mainElement = document.getElementById('main');
    if (document.fullscreenElement) {
        mainElement.style.zIndex = '0';
    } else {
        mainElement.style.zIndex = '99';
    }
}

// Add event listeners for fullscreen change
document.addEventListener('fullscreenchange', handleFullscreenChange);
document.addEventListener('webkitfullscreenchange', handleFullscreenChange);
document.addEventListener('mozfullscreenchange', handleFullscreenChange);
document.addEventListener('MSFullscreenChange', handleFullscreenChange);