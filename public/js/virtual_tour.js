/**
 * virtual_tour.js
 * Logic for the public 360° Virtual Tour page.
 * Server data is injected via window.vtRoomData from the Blade view.
 */

(function () {
    'use strict';

    const roomData = window.vtRoomData || {};
    let vtViewer   = null;
    let currentRoomId = null;

    // ── Hotspot SVGs ──────────────────
    const ARROW_SVG = `<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22"
        fill="none" stroke="#4b5563" stroke-width="3.5"
        stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"
        style="display:block;">
        <polyline points="18 15 12 9 6 15"></polyline>
    </svg>`;

    const DOOR_SVG = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256"
        fill="white" style="width:22px;height:22px;display:block;pointer-events:none;">
        <path d="M224,48V208a16,16,0,0,1-16,16H48a16,16,0,0,1-16-16V48A16,16,0,0,1,48,32H208A16,16,0,0,1,224,48ZM192,48H64V208H192V48Zm-32,80a12,12,0,1,1,12-12A12,12,0,0,1,160,128Z"></path>
    </svg>`;

    // ── Build hotspot list for a room ─────────────────────
    function buildHotspots(roomId) {
        const room = roomData[String(roomId)];
        if (!room) return [];

        return (room.hotspots || []).map(function (hs) {
            const targetId  = String(hs.target_room_id || '');
            const hasTarget = !!(targetId && roomData[targetId]);
            const type      = hs.type || 'floor';

            return {
                pitch:    parseFloat(hs.pitch),
                yaw:      parseFloat(hs.yaw),
                type:     'custom',
                cssClass: 'vt-hotspot' + (hasTarget ? ' vt-hotspot-nav' : '') + (type === 'floor' ? ' hs-type-floor' : ' hs-type-door'),

                createTooltipFunc: function (container, args) {
                    const type = args.type || 'floor';
                    container.innerHTML = type === 'floor' 
                        ? `<div class="vt-hotspot-floor">${ARROW_SVG}</div>`
                        : `<div class="vt-hotspot-door">${DOOR_SVG}</div>`;
                    
                    var label = document.createElement('div');
                    label.className   = 'vt-hotspot-label';
                    label.textContent = args.text + (args.hasTarget ? ' →' : '');
                    container.appendChild(label);
                },
                createTooltipArgs: {
                    text:      hs.text_tooltip,
                    hasTarget: hasTarget,
                    type:      type,
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

    // ── Build Panorama List ──────────────────────────────
    function buildPanoramaList() {
        const grid = document.getElementById('vtListGrid');
        if (!grid) return;
        grid.innerHTML = '';

        Object.values(roomData).forEach(room => {
            const item = document.createElement('div');
            item.className = 'vt-list-item' + (room.id === currentRoomId ? ' active' : '');
            
            const thumb = room.thumbnailUrl || 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="100" height="60" viewBox="0 0 100 60"%3E%3Crect width="100" height="60" fill="%23eee"/%3E%3Ctext x="50" y="35" font-family="sans-serif" font-size="10" text-anchor="middle" fill="%23aaa"%3EPreview%3C/text%3E%3C/svg%3E';

            item.innerHTML = `
                <div class="vt-list-thumb-wrap">
                    <img src="${thumb}" alt="${room.name}" loading="lazy">
                </div>
                <div class="vt-list-item-name">${room.name}</div>
            `;

            item.onclick = function() {
                if (room.id === currentRoomId) {
                    closeList();
                    return;
                }
                var pano = document.getElementById('vt-panorama');
                pano.style.transition = 'opacity 0.3s';
                pano.style.opacity = '0';
                closeList();
                setTimeout(function() {
                    openTour(room.id, room.name, room.imageUrl);
                    pano.style.opacity = '1';
                }, 300);
            };

            grid.appendChild(item);
        });
    }

    function openList() {
        buildPanoramaList();
        document.getElementById('vtListPopup').classList.add('active');
    }

    function closeList() {
        document.getElementById('vtListPopup').classList.remove('active');
    }

    // ── Open panorama viewer ──────────────────────────────
    window.openTour = function (roomId, roomName, imageUrl) {
        if (!imageUrl) {
            alert('Panorama untuk ruangan ini belum tersedia.');
            return;
        }

        currentRoomId = roomId;
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

        // List Popup Events
        var listBtn = document.getElementById('vtListBtn');
        var listClose = document.getElementById('vtListClose');
        var listPopup = document.getElementById('vtListPopup');

        if (listBtn) listBtn.addEventListener('click', openList);
        if (listClose) listClose.addEventListener('click', closeList);
        if (listPopup) {
            listPopup.addEventListener('click', function(e) {
                if (e.target === listPopup) closeList();
            });
        }

        // Close on Escape
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                if (listPopup && listPopup.classList.contains('active')) {
                    closeList();
                } else {
                    window.closeTour();
                }
            }
        });
    });

}());
