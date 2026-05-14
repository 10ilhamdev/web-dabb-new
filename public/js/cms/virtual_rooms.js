document.addEventListener('DOMContentLoaded', function() {

    // Elements
    const hContainer  = document.getElementById('hotspot-container');
    const noMsg       = document.getElementById('no-hotspot-msg');
    const addBtn      = document.getElementById('add-hotspot-btn');
    const imgInput    = document.getElementById('image_360_input');
    const placeholder = document.getElementById('panorama-placeholder');
    const existUrlInput = document.getElementById('existing_panorama_url');

    // Globals
    let viewer              = null;
    let hotspotCount        = 0;
    let activeHotspotIndex  = null;
    let renderedHotspotIds  = [];

    // ─── Init existing ─────────────────────────────────────────
    if (window.existingHotspots && window.existingHotspots.length > 0) {
        window.existingHotspots.forEach(hs => {
            addHotspotRow(hs.id, hs.yaw, hs.pitch, hs.text_tooltip, hs.target_room_id);
        });
    }
    if (existUrlInput && existUrlInput.value) {
        initViewer(existUrlInput.value);
    }

    if (addBtn) {
        addBtn.addEventListener('click', () => addHotspotRow(null, 0, 0, '', ''));
    }
    if (imgInput) {
        imgInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) initViewer(URL.createObjectURL(file));
        });
    }

    // ─── Add hotspot row ───────────────────────────────────────
    function addHotspotRow(id, yaw, pitch, tooltip, targetId) {
        noMsg.style.display = 'none';
        const index = hotspotCount++;
        const row = document.createElement('div');
        row.className = 'border border-gray-200 rounded-lg p-4 bg-gray-50 relative group transition-all cursor-pointer';
        row.dataset.index = index;

        const L = window.hotspotLabels || {};
        let roomOpts = '<option value="">' + (L.select_placeholder || '-- Pilih --') + '</option>';
        if (window.allRoomsData) {
            window.allRoomsData.forEach(r => {
                roomOpts += `<option value="${r.id}" ${parseInt(targetId) === r.id ? 'selected' : ''}>${r.name}</option>`;
            });
        }

        row.innerHTML = `
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <span class="flex items-center justify-center w-5 h-5 rounded-full bg-gray-800 text-white text-xs font-bold leading-none">${index + 1}</span>
                    <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider">${L.hotspot_index?.replace(':number', index + 1) || 'Hotspot ' + (index + 1)}</h4>
                </div>
                <button type="button" class="text-gray-400 hover:text-red-500 transition-colors btn-remove">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                </button>
            </div>
            ${id ? `<input type="hidden" name="hotspots[${index}][id]" value="${id}">` : ''}
            <div class="grid grid-cols-2 gap-3 mb-3">
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 uppercase tracking-wide mb-1">Yaw (Horizontal)</label>
                    <input type="number" step="any" name="hotspots[${index}][yaw]" value="${yaw}" class="input-yaw w-full px-2.5 py-1.5 border border-gray-300 rounded text-xs focus:ring-1 focus:ring-blue-500" required>
                    <p class="text-[9px] text-gray-400 mt-1">-180° (kiri) ~ 180° (kanan)</p>
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 uppercase tracking-wide mb-1">Pitch (Vertikal)</label>
                    <input type="number" step="any" name="hotspots[${index}][pitch]" value="${pitch}" class="input-pitch w-full px-2.5 py-1.5 border border-gray-300 rounded text-xs focus:ring-1 focus:ring-blue-500" required>
                    <p class="text-[9px] text-gray-400 mt-1">-90° (bawah) ~ 90° (atas)</p>
                </div>
            </div>
            <div class="space-y-3">
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 uppercase tracking-wide mb-1">${L.tooltip_label || 'Teks Tooltip'} <span class="text-red-500">*</span></label>
                    <input type="text" name="hotspots[${index}][text_tooltip]" value="${tooltip}" class="input-text w-full px-2.5 py-1.5 border border-gray-300 rounded text-xs focus:ring-1 focus:ring-blue-500" required>
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 uppercase tracking-wide mb-1">${L.target_label || 'Target Ruangan'} <span class="text-red-500">*</span></label>
                    <select name="hotspots[${index}][target_room_id]" class="w-full px-2.5 py-1.5 border border-gray-300 rounded text-xs focus:ring-1 focus:ring-blue-500" required>
                        ${roomOpts}
                    </select>
                </div>
            </div>
        `;

        hContainer.appendChild(row);
        selectRow(row, index);

        row.addEventListener('click', () => selectRow(row, index));
        row.querySelectorAll('.input-yaw, .input-pitch, .input-text').forEach(inp => {
            inp.addEventListener('input', updateViewerHotspots);
        });
        row.querySelector('.btn-remove').addEventListener('click', function(e) {
            e.stopPropagation();
            if (confirm(L.delete_confirm || 'Hapus hotspot ini?')) {
                row.remove();
                if (activeHotspotIndex === index) activeHotspotIndex = null;
                if (hContainer.children.length === 0) noMsg.style.display = 'block';
                updateViewerHotspots();
            }
        });

        updateViewerHotspots();
    }

    function selectRow(row, index) {
        document.querySelectorAll('#hotspot-container > div').forEach(el => {
            el.classList.remove('ring-2', 'ring-blue-500', 'bg-blue-50');
            el.classList.add('bg-gray-50');
        });
        row.classList.remove('bg-gray-50');
        row.classList.add('ring-2', 'ring-blue-500', 'bg-blue-50');
        activeHotspotIndex = index;
    }

    // ─── Viewer init ───────────────────────────────────────────
    function initViewer(imageUrl) {
        if (placeholder) {
            placeholder.style.opacity = '0';
            setTimeout(() => { placeholder.style.display = 'none'; }, 300);
        }
        if (viewer) { viewer.destroy(); viewer = null; renderedHotspotIds = []; }

        viewer = pannellum.viewer('panorama', {
            type: 'equirectangular',
            panorama: imageUrl,
            autoLoad: true,
            showZoomCtrl: false,
            mouseZoom: true,
            compass: false,
        });

        viewer.on('load', function() {
            updateViewerHotspots();
            attachClickListener();
        });
    }

    // ─── Click on empty panorama area ─────────────────────────
    function attachClickListener() {
        const panoEl = document.getElementById('panorama');
        if (!panoEl) return;
        let mdTime = 0, mdX = 0, mdY = 0;

        panoEl.addEventListener('mousedown', function(e) {
            if (e.target.closest('.custom-hotspot')) return;
            mdTime = Date.now(); mdX = e.clientX; mdY = e.clientY;
        });
        panoEl.addEventListener('mouseup', function(e) {
            if (e.target.closest('.custom-hotspot')) return;
            if (Date.now() - mdTime < 300 && Math.abs(e.clientX - mdX) < 8 && Math.abs(e.clientY - mdY) < 8) {
                if (activeHotspotIndex === null) { showTip('Pilih form hotspot di kiri dulu, lalu klik panorama'); return; }
                try {
                    const c = viewer.mouseEventToCoords(e);
                    if (c) applyCoords(activeHotspotIndex, c[1], c[0]);
                } catch(err) {}
            }
        });
    }

    // ─── Smooth hotspot drag ───────────────────────────────────
    // Strategy: during drag we move the DOM element directly so it
    // follows the cursor instantly. On mouseup we compute final
    // sphere coords and re-render Pannellum.
    function makeDraggable(hotSpotDiv, hsIndex) {
        hotSpotDiv.style.cursor = 'grab';

        hotSpotDiv.addEventListener('mousedown', function(e) {
            e.stopPropagation();

            // Activate the corresponding form row
            const row = document.querySelector(`#hotspot-container > div[data-index="${hsIndex}"]`);
            if (row) selectRow(row, hsIndex);

            isDragging = true;
            hotSpotDiv.classList.add('dragging');
            hotSpotDiv.style.cursor = 'grabbing';
            hotSpotDiv.style.willChange = 'transform';

            // We'll move the element by overriding its translateX/Y on top of
            // whatever Pannellum already applied via its own positioning.
            let startX = e.clientX, startY = e.clientY;
            let offsetX = 0, offsetY = 0;

            function onMove(ev) {
                offsetX = ev.clientX - startX;
                offsetY = ev.clientY - startY;
                // Shift the element visually; preserve Pannellum's own transform
                hotSpotDiv.style.marginLeft = offsetX + 'px';
                hotSpotDiv.style.marginTop  = offsetY + 'px';

                // Live-update form inputs while dragging
                try {
                    const c = viewer.mouseEventToCoords(ev);
                    if (c) {
                        const r = document.querySelector(`#hotspot-container > div[data-index="${hsIndex}"]`);
                        if (r) {
                            r.querySelector('.input-yaw').value   = c[1].toFixed(2);
                            r.querySelector('.input-pitch').value = c[0].toFixed(2);
                        }
                    }
                } catch(_) {}
            }

            function onUp(ev) {
                isDragging = false;
                hotSpotDiv.classList.remove('dragging');
                document.removeEventListener('mousemove', onMove);
                document.removeEventListener('mouseup',   onUp);

                // Reset our temporary offset
                hotSpotDiv.style.marginLeft = '';
                hotSpotDiv.style.marginTop  = '';
                hotSpotDiv.style.willChange = '';

                // Commit final coordinates and re-render
                try {
                    const c = viewer.mouseEventToCoords(ev);
                    if (c) applyCoords(hsIndex, c[1], c[0]);
                    else updateViewerHotspots();
                } catch(_) { updateViewerHotspots(); }
            }

            document.addEventListener('mousemove', onMove);
            document.addEventListener('mouseup',   onUp);
        });
    }

    let isDragging = false;

    // ─── Helpers ───────────────────────────────────────────────
    function applyCoords(index, yaw, pitch) {
        const row = document.querySelector(`#hotspot-container > div[data-index="${index}"]`);
        if (row) {
            row.querySelector('.input-yaw').value   = parseFloat(yaw).toFixed(2);
            row.querySelector('.input-pitch').value = parseFloat(pitch).toFixed(2);
            updateViewerHotspots();
        }
    }

    function showTip(msg) {
        const tip = document.createElement('div');
        tip.style.cssText = 'position:fixed;top:20px;right:20px;background:#1f2937;color:white;padding:10px 16px;border-radius:8px;font-size:13px;z-index:9999;transition:opacity .3s;';
        tip.textContent = msg;
        document.body.appendChild(tip);
        setTimeout(() => { tip.style.opacity = '0'; setTimeout(() => tip.remove(), 300); }, 2500);
    }

    // ─── Arrow icon SVG (Dark chevron arrow) ─────────
    const ARROW_SVG = `<div class="vt-hotspot-floor">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
            fill="none" stroke="#4b5563" stroke-width="3.5"
            stroke-linecap="round" stroke-linejoin="round"
            style="width:22px;height:22px;display:block;pointer-events:none;">
            <polyline points="18 15 12 9 6 15"></polyline>
        </svg>
    </div>`;

    // ─── Custom hotspot tooltip builder ───────────────────────
    window.hotspotTooltipFunction = function(hotSpotDiv, args) {
        hotSpotDiv.classList.add('custom-tooltip');

        // Arrow icon inside the circle
        hotSpotDiv.innerHTML = ARROW_SVG;

        // Tooltip text label
        const span = document.createElement('span');
        span.innerHTML = args.text || 'Hotspot';
        hotSpotDiv.appendChild(span);

        // Numeric badge
        const badge = document.createElement('div');
        badge.className = 'hotspot-badge';
        badge.innerText = args.index;
        hotSpotDiv.appendChild(badge);

        // Enable smooth drag
        makeDraggable(hotSpotDiv, args.hotspotIndex);
    };

    // ─── Render hotspots on viewer ─────────────────────────────
    function updateViewerHotspots() {
        if (!viewer) return;

        renderedHotspotIds.forEach(id => { try { viewer.removeHotSpot(id); } catch(_) {} });
        renderedHotspotIds = [];

        document.querySelectorAll('#hotspot-container > div').forEach((row, rawIndex) => {
            const yaw       = parseFloat(row.querySelector('.input-yaw').value)   || 0;
            const pitch     = parseFloat(row.querySelector('.input-pitch').value)  || 0;
            const text      = row.querySelector('.input-text').value || 'Hotspot ' + (rawIndex + 1);
            const hsIndex   = parseInt(row.dataset.index);
            const hotspotId = 'hs_' + rawIndex + '_' + Date.now();

            viewer.addHotSpot({
                id: hotspotId,
                pitch, yaw,
                cssClass: 'custom-hotspot',
                createTooltipFunc: window.hotspotTooltipFunction,
                createTooltipArgs: { text, index: rawIndex + 1, hotspotIndex: hsIndex }
            });

            renderedHotspotIds.push(hotspotId);
        });
    }

});
