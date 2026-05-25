/* ============================================================
   virtual_3d_rooms_edit.js
   JS for edit.blade.php — functions that don't need Blade data
   Routes/csrf are injected inline via window.v3dCsrf / window.v3dRoutes
   ============================================================ */

/* ── Save button: just submit (auto-thumbnail is server-side) ── */
document.addEventListener('DOMContentLoaded', function () {

    /* Initialise preview colours from color inputs */
    updatePreviewColors();

    const saveBtn = document.getElementById('saveRoomBtn');
    if (saveBtn) {
        saveBtn.addEventListener('click', function (e) {
            e.preventDefault();
            document.getElementById('virtual3d-room-form').submit();
        });
    }

    /* Sync door wall selector with wall editor preview */
    const doorWallSelect = document.getElementById('doorWallSelect');
    if (doorWallSelect) {
        doorWallSelect.addEventListener('change', function () {
            const doorEl = document.getElementById('doorRender');
            if (doorEl) {
                doorEl.dataset.doorWall = this.value;
                // Re-render the current wall view to show/hide door
                switchWallView(currentWall);
            }
        });
    }
});

/* ── Colour preview ─────────────────────────────────────────── */
function updatePreviewColors() {
    const wc = document.getElementById('wallColorInput').value;
    const fc = document.getElementById('floorColorInput').value;
    const cc = document.getElementById('ceilingColorInput').value;

    document.getElementById('wallColorText').value  = wc;
    document.getElementById('floorColorText').value = fc;
    document.getElementById('ceilingColorText').value = cc;

    document.getElementById('pv-wall-front').style.backgroundColor = wc;
    document.getElementById('pv-wall-back').style.backgroundColor  = wc;
    document.getElementById('pv-wall-left').style.backgroundColor  = wc;
    document.getElementById('pv-wall-right').style.backgroundColor = wc;
    document.getElementById('pv-floor').style.backgroundColor      = fc;
    document.getElementById('pv-ceiling').style.backgroundColor    = cc;

    const wallEditor = document.getElementById('wallEditor');
    if (wallEditor) wallEditor.style.backgroundColor = wc;
}

/* ── 3D preview rotation ────────────────────────────────────── */
function rotatePreview(view, btn) {
    const scene = document.getElementById('preview3dScene');
    const rotations = {
        'default': 'translate(-50%, -50%) rotateX(-10deg) rotateY(-25deg)',
        'front':   'translate(-50%, -50%) rotateX(0deg) rotateY(0deg)',
        'back':    'translate(-50%, -50%) rotateX(0deg) rotateY(180deg)',
        'left':    'translate(-50%, -50%) rotateX(0deg) rotateY(90deg)',
        'right':   'translate(-50%, -50%) rotateX(0deg) rotateY(-90deg)',
        'top':     'translate(-50%, -50%) rotateX(-90deg) rotateY(0deg)',
    };
    scene.style.transform = rotations[view] || rotations['default'];
    document.querySelectorAll('.preview-rot-btn').forEach(b => b.classList.remove('active'));
    if (btn) btn.classList.add('active');
}

/* ── Thumbnail removal ──────────────────────────────────────── */
function removeThumbnail() {
    document.getElementById('removeThumbnailInput').value = '1';
    const wrap = document.getElementById('thumbnailPreviewWrap');
    if (wrap) wrap.style.display = 'none';
    const fi = document.getElementById('thumbnailFileInput');
    if (fi) fi.value = '';
}

/* ── Media list helpers ─────────────────────────────────────── */
async function deleteMediaItem(id, btnEl) {
    const confirmMsg = (window.v3dConfig?.messages?.deleteConfirm) || 'Yakin hapus media ini dari dinding?';
    const deleteBtnText = (window.v3dConfig?.deleteBtn) || 'Hapus';
    Swal.fire({
        title: (window.v3dConfig?.deleteBtn) || 'Hapus Media',
        html: confirmMsg,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: deleteBtnText,
        cancelButtonText: 'Batal',
        reverseButtons: true,
        borderRadius: '12px',
        customClass: {
            confirmButton: 'px-5 py-2.5 text-sm font-semibold text-white bg-red-500 hover:bg-red-600 rounded-lg transition-colors',
            cancelButton: 'px-5 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors mr-3'
        },
        buttonsStyling: false
    }).then(async (result) => {
        if (result.isConfirmed) {
            const url = window.v3dRoutes.deleteMedia.replace('__MEDIA_ID__', id);
            try {
                const response = await fetch(url, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': window.v3dCsrf, 'Accept': 'application/json' }
                });
                const data = await response.json();
                if (data.success) {
                    mediaItems = mediaItems.filter(m => m.id !== id);
                    if (activeMediaId === id) deselectItem();
                    renderWallItems();
                    const listItem = btnEl.closest('.media-list-item');
                    if (listItem) listItem.remove();
                    filterMediaList(); // Re-filter to update count and empty message
                    
                    Swal.fire({
                        title: 'Berhasil',
                        text: (window.v3dConfig?.messages?.deleteSuccess) || 'Media berhasil dihapus.',
                        icon: 'success',
                        borderRadius: '12px',
                        confirmButtonColor: '#3b82f6'
                    });
                } else {
                    Swal.fire({
                        title: 'Gagal',
                        text: (window.v3dConfig?.messages?.deleteFailed) || 'Gagal menghapus media.',
                        icon: 'error',
                        borderRadius: '12px',
                        confirmButtonColor: '#3b82f6'
                    });
                }
            } catch (error) {
                console.error(error);
                Swal.fire({
                    title: 'Gagal',
                    text: (window.v3dConfig?.messages?.deleteFailed) || 'Gagal menghapus media.',
                    icon: 'error',
                    borderRadius: '12px',
                    confirmButtonColor: '#3b82f6'
                });
            }
        }
    });
}

function addMediaToList(media) {
    const noMsg = document.getElementById('noMediaMsg');
    if (noMsg) noMsg.remove();
    const list = document.getElementById('mediaList');
    const html = `
    <div class="flex items-center gap-3 p-2 bg-gray-50 rounded-lg border border-gray-100 media-list-item" data-id="${media.id}" data-wall="${media.wall}">
        <div class="w-12 h-10 flex-shrink-0 rounded overflow-hidden bg-gray-200">
            ${media.type === 'image'
                ? `<img src="/storage/${media.file_path}" class="w-full h-full object-cover">`
                : `<div class="w-full h-full flex items-center justify-center text-gray-400"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></div>`}
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-xs font-medium text-gray-800 truncate">${media.type.charAt(0).toUpperCase() + media.type.slice(1)} #${media.id}</p>
            <p class="text-xs text-gray-500">${(window.v3dConfig?.labels?.wall?.[media.wall]?.preview) || media.wall.charAt(0).toUpperCase() + media.wall.slice(1)}</p>
        </div>
        <button type="button" onclick="deleteMediaItem(${media.id}, this)" class="p-1.5 text-red-500 hover:bg-red-50 rounded transition-colors" title="${(window.v3dConfig?.labels?.messages?.deleteBtn) || 'Delete'}">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        </button>
    </div>`;
    list.insertAdjacentHTML('beforeend', html);
    filterMediaList(); // Re-filter to show/hide based on current wall
}

/* ── Upload media (injected via window.uploadNewMedia in blade) ─ */
// The actual uploadNewMedia function is defined inline in the blade
// because it needs window.v3dRoutes (Blade-rendered URLs).
