/**
 * virtual_tour.js
 * Logic for the public 360° Virtual Tour page.
 * Server data is injected via window.vtRoomData from the Blade view.
 */

(function () {
    'use strict';

    const roomData = window.vtRoomData || {};
    let vtViewer   = null;

    // ── Arrow SVG icon (Dark chevron arrow) ──────────────────
    const ARROW_SVG = `<div class="vt-hotspot-floor">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22"
            fill="none" stroke="#4b5563" stroke-width="3.5"
            stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"
            style="display:block;">
            <polyline points="18 15 12 9 6 15"></polyline>
        </svg>
    </div>`;

    // ── Build hotspot list for a room ─────────────────────
    function buildHotspots(roomId) {
        const room = roomData[String(roomId)];
        if (!room) return [];

        return (room.hotspots || []).map(function (hs) {
            const targetId  = String(hs.target_room_id || '');
            const hasTarget = !!(targetId && roomData[targetId]);

            return {
                pitch:    parseFloat(hs.pitch),
                yaw:      parseFloat(hs.yaw),
                type:     'custom',
                cssClass: 'vt-hotspot' + (hasTarget ? ' vt-hotspot-nav' : ''),

                createTooltipFunc: function (container, args) {
                    container.innerHTML = ARROW_SVG;
                    var label = document.createElement('div');
                    label.className   = 'vt-hotspot-label';
                    label.textContent = args.text + (args.hasTarget ? ' →' : '');
                    container.appendChild(label);
                },
                createTooltipArgs: {
                    text:      hs.text_tooltip,
                    hasTarget: hasTarget,
                },

                clickHandlerFunc: hasTarget ? function () {
                    var target = roomData[targetId];
                    var pano   = document.getElementById('vt-panorama');
                    pano.style.transition = 'opacity 0.35s';
                    pano.style.opacity    = '0';
                    setTimeout(function () {
                        openTour(target.id, target.name, target.imageUrl);
                        pano.style.opacity = '1';
                    }, 350);
                } : undefined,
            };
        });
    }

    // ── Open panorama viewer ──────────────────────────────
    window.openTour = function (roomId, roomName, imageUrl) {
        if (!imageUrl) {
            alert('Panorama untuk ruangan ini belum tersedia.');
            return;
        }

        document.getElementById('vtModalTitle').textContent = roomName;
        document.getElementById('vtModal').classList.add('active');
        document.body.style.overflow = 'hidden';

        if (vtViewer) { vtViewer.destroy(); vtViewer = null; }

        vtViewer = pannellum.viewer('vt-panorama', {
            type:        'equirectangular',
            panorama:    imageUrl,
            autoLoad:    true,
            autoRotate:  -2,
            showZoomCtrl: true,
            mouseZoom:   true,
            compass:     false,
            hotSpots:    buildHotspots(roomId),
        });
    };

    // ── Close viewer ──────────────────────────────────────
    window.closeTour = function () {
        document.getElementById('vtModal').classList.remove('active');
        document.body.style.overflow = '';
        if (vtViewer) { vtViewer.destroy(); vtViewer = null; }
    };

    // ── Wire modal events ─────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        var modal = document.getElementById('vtModal');
        if (modal) {
            // Close on backdrop click
            modal.addEventListener('click', function (e) {
                if (e.target === modal) window.closeTour();
            });
        }
        // Close on Escape
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') window.closeTour();
        });
    });

}());
