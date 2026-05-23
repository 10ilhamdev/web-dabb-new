/* ============================================
   CMS Virtual 3D Room - Wall Editor JS
   ============================================ */

var mediaItems = [];
var doorsData = {}; // Stores door config for all walls
var currentWall = 'front';
var activeItem = null;
var activeMediaId = null;

// Drag & Resize state
var isDragging = false;
var isResizing = false;
var startX, startY;
var originalLeft, originalTop, originalWidth, originalHeight;

function initWallEditor() {
    // Parse data from the page
    const dataEl = document.getElementById('roomMediaData');
    if (dataEl) {
        try { 
            const parsed = JSON.parse(dataEl.textContent); 
            if (parsed.media) {
                mediaItems = parsed.media;
                doorsData = parsed.doors || {};
            } else {
                // Backward compatibility
                mediaItems = parsed;
            }
        } catch(e) { 
            mediaItems = []; 
            doorsData = {};
        }
    }
    renderWallItems();
    filterMediaList(); // Filter the sidebar list on init

    // Deselect if clicking on empty wall area
    const wallEditor = document.getElementById('wallEditor');
    if (wallEditor) {
        wallEditor.addEventListener('mousedown', (e) => {
            if (e.target === wallEditor || e.target.id === 'wallEditor') {
                deselectItem();
            }
        });
    }
}

// Support both normal load and dynamic script injection
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initWallEditor);
} else {
    // DOM is already ready (script was loaded dynamically)
    initWallEditor();
}

// Read translations from Blade-injected v3dConfig (edit page) or fallback defaults
var V3D = typeof window.v3dConfig === 'object' ? window.v3dConfig : {};
var wallLabels = (V3D.labels && V3D.labels.wall) || {
    front: { big: 'FRONT WALL',  small: 'Front Wall',  preview: 'FRONT'    },
    left:  { big: 'LEFT WALL',   small: 'Left Wall',   preview: 'LEFT'     },
    right: { big: 'RIGHT WALL',  small: 'Right Wall',  preview: 'RIGHT'    },
    back:  { big: 'BACK WALL',    small: 'Back Wall',   preview: 'BACK'     },
};
var messages = V3D.labels && V3D.labels.messages || {
    mediaEmpty:    'No media on this wall yet',
    selectFile:    'Select a file to upload!',
    uploadSuccess: 'Media uploaded successfully!',
    uploadFailed:  'Upload failed.',
    saveSuccess:   'Position saved!',
    saveFailed:    'Failed to save position.',
    deleteConfirm: 'Delete this media from the wall?',
    deleteSuccess: 'Media deleted.',
    deleteFailed:  'Failed to delete media.',
    doorLabel:     'Door Label',
};
var badgeSuffix = V3D.labels && V3D.labels.badgeSuffix || 'item';

// --- Wall View Switching ---

function switchWallView(wall) {
    currentWall = wall;

    // Update buttons
    document.querySelectorAll('.wall-tab-btn').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.wall === wall);
    });

    // Update title label using translated strings
    const titleEl = document.getElementById('wallTitleLabel');
    if (titleEl && wallLabels[wall]) {
        titleEl.innerText = wallLabels[wall].big;
    }

    // Show/hide door on the wall it belongs to
    const doorEl = document.getElementById('doorRender');
    if (doorEl) {
        const doorWall = doorEl.dataset.doorWall || 'back';
        doorEl.style.display = (wall === doorWall && doorEl.dataset.active === '1') ? 'flex' : 'none';
    }

    // Sync upload section wall value and label
    const uploadWallEl = document.getElementById('uploadWall');
    if (uploadWallEl) uploadWallEl.value = wall;
    const uploadWallLabel = document.getElementById('uploadWallLabel');
    if (uploadWallLabel && wallLabels[wall]) {
        uploadWallLabel.textContent = wallLabels[wall].small;
    }

    deselectItem();
    renderWallItems();
    filterMediaList(); // Filter the sidebar media list by active wall

    // Dispatch event to sync with door settings panel (Alpine.js)
    window.dispatchEvent(new CustomEvent('wall-changed', { detail: { wall: wall } }));
    
    // Update the door preview on the wall
    updateWallEditorDoors();
}

/**
 * Updates the door preview in the wall editor based on the current wall's config.
 * Called when switching walls or when door settings change in the sidebar.
 */
function updateWallEditorDoors() {
    const doorEl = document.getElementById('doorRender');
    if (!doorEl) return;

    // Use current settings from Alpine if available, else from doorsData
    // Actually, it's easier to just read from the active wall's config
    const wallConfig = doorsData[currentWall] || { link_type: 'none', label: '' };
    
    const isActive = wallConfig.link_type !== 'none';
    doorEl.style.display = isActive ? 'flex' : 'none';
    
    if (isActive) {
        const labelEl = doorEl.querySelector('.text-xs');
        if (labelEl) {
            labelEl.textContent = wallConfig.label || (messages.doorLabel || 'Door Label');
        }
    }
}

// --- Filter Media List by Wall ---

function filterMediaList() {
    var listItems = document.querySelectorAll('#mediaList .media-list-item');
    var visibleCount = 0;

    listItems.forEach(function(item) {
        var itemWall = item.getAttribute('data-wall');
        if (itemWall === currentWall) {
            item.style.display = '';
            visibleCount++;
        } else {
            item.style.display = 'none';
        }
    });

    // Update count badge
    var badge = document.getElementById('mediaCountBadge');
    if (badge) {
        badge.textContent = visibleCount + ' ' + (badgeSuffix || 'item');
    }

    // Show/hide empty message
    var noMsg = document.getElementById('noMediaMsg');
    if (noMsg) {
        noMsg.style.display = visibleCount === 0 ? '' : 'none';
    } else if (visibleCount === 0) {
        // Create empty message if not present
        var list = document.getElementById('mediaList');
        if (list && !list.querySelector('#noMediaMsg')) {
            var emptyDiv = document.createElement('div');
            emptyDiv.id = 'noMediaMsg';
            emptyDiv.className = 'text-center py-4 text-sm text-gray-400 border-2 border-dashed border-gray-100 rounded-lg';
            emptyDiv.textContent = messages.mediaEmpty || 'No media on this wall yet';
            list.appendChild(emptyDiv);
        }
    }
}


// --- Render Media Items ---

function renderWallItems() {
    const wallEditor = document.getElementById('wallEditor');
    if (!wallEditor) return;

    // Remove existing movable elements
    document.querySelectorAll('.media-item').forEach(e => e.remove());

    const items = mediaItems.filter(m => m.wall === currentWall);

    items.forEach(item => {
        const el = document.createElement('div');
        el.className = 'media-item';
        el.id = 'media-' + item.id;
        el.dataset.id = item.id;

        el.style.left   = item.position_x + '%';
        el.style.top    = item.position_y + '%';
        el.style.width  = item.width + '%';
        el.style.height = item.height + '%';

        // Content - build with proper DOM manipulation to avoid innerHTML += issue
        if (item.type === 'image') {
            const img = document.createElement('img');
            img.src = '/storage/' + item.file_path;
            img.alt = 'media';
            el.appendChild(img);
        } else {
            const video = document.createElement('video');
            video.src = '/storage/' + item.file_path;
            video.muted = true;
            video.loop = true;
            el.appendChild(video);
        }

        const label = document.createElement('div');
        label.className = 'media-item-label';
        label.textContent = item.type.toUpperCase() + ': #' + item.id;
        el.appendChild(label);

        const handle = document.createElement('div');
        handle.className = 'resize-handle';
        el.appendChild(handle);

        el.addEventListener('mousedown', (e) => handleMouseDown(e, item.id));
        el.addEventListener('touchstart', (e) => handleTouchStart(e, item.id), { passive: false });

        wallEditor.appendChild(el);
    });

    // Also update 3D preview faces with media thumbnails
    update3dPreviewMedia();
}

// --- Update 3D Preview Faces with Media ---
function update3dPreviewMedia() {
    const walls = ['front', 'left', 'right', 'back'];
    const wallIdMap = { front: 'pv-wall-front', left: 'pv-wall-left', right: 'pv-wall-right', back: 'pv-wall-back' };

    walls.forEach(wall => {
        const faceEl = document.getElementById(wallIdMap[wall]);
        if (!faceEl) return;

        // Remove existing preview media but keep the door on back wall
        faceEl.querySelectorAll('.pv-media-thumb').forEach(e => e.remove());

        const wallMedia = mediaItems.filter(m => m.wall === wall);

        if (wallMedia.length > 0) {
            // Show first few media as small thumbnails on the 3D face
            wallMedia.forEach((item, idx) => {
                const thumb = document.createElement('div');
                thumb.className = 'pv-media-thumb';
                thumb.style.cssText = 'position:absolute; border:1px solid rgba(255,255,255,0.3); overflow:hidden; border-radius:2px;';
                
                // Calculate position on the 3D face relative to face size
                const faceW = faceEl.offsetWidth || 260;
                const faceH = faceEl.offsetHeight || 200;
                const thumbW = (item.width / 100) * faceW;
                const thumbH = (item.height / 100) * faceH;
                const thumbX = (item.position_x / 100) * faceW - thumbW / 2;
                const thumbY = (item.position_y / 100) * faceH - thumbH / 2;

                thumb.style.left = thumbX + 'px';
                thumb.style.top = thumbY + 'px';
                thumb.style.width = thumbW + 'px';
                thumb.style.height = thumbH + 'px';

                if (item.type === 'image') {
                    const img = document.createElement('img');
                    img.src = '/storage/' + item.file_path;
                    img.style.cssText = 'width:100%;height:100%;object-fit:cover;';
                    thumb.appendChild(img);
                } else {
                    thumb.style.cssText += 'display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.3);';
                    thumb.innerHTML = '<svg style="width:12px;height:12px;fill:white;" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>';
                }

                faceEl.appendChild(thumb);
            });

            // Clear the text label if there are media items
            const textNodes = Array.from(faceEl.childNodes).filter(n => n.nodeType === 3);
            textNodes.forEach(n => { if (n.textContent.trim() === wallLabels[wall].preview) n.textContent = ''; });
        } else {
            // Restore the wall label text if no media
            const hasLabel = faceEl.textContent.trim().includes(wallLabels[wall].preview);
            if (!hasLabel) {
                // Check if we need to restore the text node
                const existingText = Array.from(faceEl.childNodes).filter(n => n.nodeType === 3).join('');
                if (!existingText.trim()) {
                    // Only add text if the face doesn't have the door element
                    if (wall !== 'back') {
                        faceEl.insertBefore(document.createTextNode(wallLabels[wall].preview), faceEl.firstChild);
                    }
                }
            }
        }
    });
}


// --- Drag & Drop + Resize Logic ---

function handleMouseDown(e, id) {
    e.stopPropagation();
    selectItem(id);

    const el = document.getElementById('media-' + id);
    if (!el) return;

    if (e.target.classList.contains('resize-handle')) {
        isResizing = true;
    } else {
        isDragging = true;
    }

    startX = e.clientX;
    startY = e.clientY;

    originalLeft   = parseFloat(el.style.left) || 0;
    originalTop    = parseFloat(el.style.top) || 0;
    originalWidth  = parseFloat(el.style.width) || 0;
    originalHeight = parseFloat(el.style.height) || 0;

    document.addEventListener('mousemove', handleMouseMove);
    document.addEventListener('mouseup', handleMouseUp);
}

function handleMouseMove(e) {
    if (!activeItem) return;

    const wallEditor = document.getElementById('wallEditor');
    const rect = wallEditor.getBoundingClientRect();

    const deltaX = e.clientX - startX;
    const deltaY = e.clientY - startY;

    const pctX = (deltaX / rect.width) * 100;
    const pctY = (deltaY / rect.height) * 100;

    const el = document.getElementById('media-' + activeMediaId);
    if (!el) return;

    if (isDragging) {
        let newLeft = Math.max(0, Math.min(100, originalLeft + pctX));
        let newTop  = Math.max(0, Math.min(100, originalTop + pctY));

        el.style.left = newLeft + '%';
        el.style.top  = newTop + '%';

        syncProperties(newLeft, newTop, parseFloat(el.style.width), parseFloat(el.style.height));

    } else if (isResizing) {
        let newWidth  = Math.max(5, Math.min(100, originalWidth + pctX));
        let newHeight = Math.max(5, Math.min(100, originalHeight + pctY));

        el.style.width  = newWidth + '%';
        el.style.height = newHeight + '%';

        syncProperties(parseFloat(el.style.left), parseFloat(el.style.top), newWidth, newHeight);
    }
}

function handleMouseUp() {
    isDragging = false;
    isResizing = false;
    document.removeEventListener('mousemove', handleMouseMove);
    document.removeEventListener('mouseup', handleMouseUp);

    // Update memory
    if (activeItem) {
        activeItem.position_x = parseFloat(document.getElementById('propX').value);
        activeItem.position_y = parseFloat(document.getElementById('propY').value);
        activeItem.width      = parseFloat(document.getElementById('propW').value);
        activeItem.height     = parseFloat(document.getElementById('propH').value);
    }
}

function handleTouchStart(e, id) {
    if (e.touches.length > 1) return;
    const touch = e.touches[0];
    e.stopPropagation();
    selectItem(id);

    const el = document.getElementById('media-' + id);
    if (!el) return;

    if (e.target.classList.contains('resize-handle')) {
        isResizing = true;
    } else {
        isDragging = true;
    }

    startX = touch.clientX;
    startY = touch.clientY;

    originalLeft   = parseFloat(el.style.left) || 0;
    originalTop    = parseFloat(el.style.top) || 0;
    originalWidth  = parseFloat(el.style.width) || 0;
    originalHeight = parseFloat(el.style.height) || 0;

    document.addEventListener('touchmove', handleTouchMove, { passive: false });
    document.addEventListener('touchend', handleTouchEnd, { passive: false });
}

function handleTouchMove(e) {
    if (!activeItem) return;
    if (e.touches.length > 1) return;
    e.preventDefault();

    const touch = e.touches[0];
    const wallEditor = document.getElementById('wallEditor');
    const rect = wallEditor.getBoundingClientRect();

    const deltaX = touch.clientX - startX;
    const deltaY = touch.clientY - startY;

    const pctX = (deltaX / rect.width) * 100;
    const pctY = (deltaY / rect.height) * 100;

    const el = document.getElementById('media-' + activeMediaId);
    if (!el) return;

    if (isDragging) {
        let newLeft = Math.max(0, Math.min(100, originalLeft + pctX));
        let newTop  = Math.max(0, Math.min(100, originalTop + pctY));

        el.style.left = newLeft + '%';
        el.style.top  = newTop + '%';

        syncProperties(newLeft, newTop, parseFloat(el.style.width), parseFloat(el.style.height));

    } else if (isResizing) {
        let newWidth  = Math.max(5, Math.min(100, originalWidth + pctX));
        let newHeight = Math.max(5, Math.min(100, originalHeight + pctY));

        el.style.width  = newWidth + '%';
        el.style.height = newHeight + '%';

        syncProperties(parseFloat(el.style.left), parseFloat(el.style.top), newWidth, newHeight);
    }
}

function handleTouchEnd(e) {
    isDragging = false;
    isResizing = false;
    document.removeEventListener('touchmove', handleTouchMove);
    document.removeEventListener('touchend', handleTouchEnd);

    // Update memory
    if (activeItem) {
        activeItem.position_x = parseFloat(document.getElementById('propX').value);
        activeItem.position_y = parseFloat(document.getElementById('propY').value);
        activeItem.width      = parseFloat(document.getElementById('propW').value);
        activeItem.height     = parseFloat(document.getElementById('propH').value);
    }
}


// --- Selection Logic ---

function selectItem(id) {
    deselectItem();
    activeMediaId = id;
    activeItem = mediaItems.find(m => m.id == id);

    if (activeItem) {
        const el = document.getElementById('media-' + id);
        if (el) el.classList.add('active');

        document.getElementById('propertiesPanel').style.display = 'block';
        document.getElementById('propX').value = activeItem.position_x;
        document.getElementById('propY').value = activeItem.position_y;
        document.getElementById('propW').value = activeItem.width;
        document.getElementById('propH').value = activeItem.height;
        if (document.getElementById('propDescription')) {
            document.getElementById('propDescription').value = activeItem.description || '';
        }
    }
}

function deselectItem() {
    if (activeMediaId) {
        const el = document.getElementById('media-' + activeMediaId);
        if (el) el.classList.remove('active');
    }
    activeMediaId = null;
    activeItem = null;
    const pp = document.getElementById('propertiesPanel');
    if (pp) pp.style.display = 'none';
}


// --- Properties Panel Sync ---

function syncProperties(x, y, w, h) {
    document.getElementById('propX').value = x.toFixed(2);
    document.getElementById('propY').value = y.toFixed(2);
    document.getElementById('propW').value = w.toFixed(2);
    document.getElementById('propH').value = h.toFixed(2);
}

function updatePropertiesFromInput() {
    if (!activeMediaId || !activeItem) return;

    const el = document.getElementById('media-' + activeMediaId);
    if (!el) return;

    activeItem.position_x = parseFloat(document.getElementById('propX').value);
    activeItem.position_y = parseFloat(document.getElementById('propY').value);
    activeItem.width      = parseFloat(document.getElementById('propW').value);
    activeItem.height     = parseFloat(document.getElementById('propH').value);
    if (document.getElementById('propDescription')) {
        activeItem.description = document.getElementById('propDescription').value;
    }

    el.style.left   = activeItem.position_x + '%';
    el.style.top    = activeItem.position_y + '%';
    el.style.width  = activeItem.width + '%';
    el.style.height = activeItem.height + '%';
}


// --- API Interactions ---

async function uploadNewMedia() {
    const form = document.getElementById('uploadMediaForm');
    const fileInput = form.querySelector('input[name="file"]');
    if (!fileInput || !fileInput.files.length) {
        alert(messages.selectFile || 'Select a file to upload!');
        return;
    }

    const formData = new FormData(form);

    try {
        const response = await fetch(window.v3dRoutes.upload, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': window.v3dCsrf },
            body: formData
        });

        const data = await response.json();
        if (data.success) {
            mediaItems.push(data.media);
            renderWallItems();
            selectItem(data.media.id);
            form.reset();
            document.getElementById('uploadWall').value = currentWall;
            showToast(messages.uploadSuccess || 'Media uploaded successfully!');
        } else {
            alert(messages.uploadFailed || 'Upload failed.');
        }
    } catch (error) {
        console.error(error);
        alert(messages.uploadFailed || 'Upload failed.');
    }
}

async function saveActiveMedia() {
    if (!activeMediaId || !activeItem) return;

    const url = window.v3dRoutes.updateMedia.replace('__MEDIA_ID__', activeItem.id);

    try {
        const response = await fetch(url, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': window.v3dCsrf
            },
            body: JSON.stringify({
                position_x: activeItem.position_x,
                position_y: activeItem.position_y,
                width: activeItem.width,
                height: activeItem.height,
                description: activeItem.description
            })
        });

        const data = await response.json();
        if (data.success) {
            showToast(messages.saveSuccess || 'Position & size saved!');
        }
    } catch (error) {
        console.error(error);
        showToast(messages.saveFailed || 'Failed to save position.');
    }
}

async function deleteActiveMedia() {
    if (!activeMediaId || !activeItem) return;
    if (!confirm(messages.deleteConfirm || 'Delete this media from the wall?')) return;

    const url = window.v3dRoutes.deleteMedia.replace('__MEDIA_ID__', activeItem.id);

    try {
        const response = await fetch(url, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': window.v3dCsrf, 'Accept': 'application/json' }
        });

        const data = await response.json();
        if (data.success) {
            mediaItems = mediaItems.filter(m => m.id !== activeMediaId);
            deselectItem();
            renderWallItems();
            showToast(messages.deleteSuccess || 'Media deleted.');
        }
    } catch (error) {
        console.error(error);
        showToast(messages.deleteFailed || 'Failed to delete media.');
    }
}


// --- Toast Helper ---

function showToast(message) {
    const toast = document.createElement('div');
    toast.className = 'upload-toast';
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 2500);
}
