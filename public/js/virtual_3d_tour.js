// --- Info Popup Modal Logic (Global Scope) ---
const closePopup = () => {
    const overlay = document.getElementById('vss-popup-overlay');
    const card = document.getElementById('vsshow-popup-card') || document.getElementById('vss-popup-card');
    if (overlay) overlay.classList.remove('active');
    if (card) card.classList.remove('active');
    document.body.style.overflow = '';
};

window.openV3DInfo = function(description, mediaUrl, type) {
    
    const textContainer = document.getElementById('vss-popup-text');
    const imgPreview = document.getElementById('vss-popup-img');
    const overlay = document.getElementById('vss-popup-overlay');
    const card = document.getElementById('vss-popup-card') || document.getElementById('vsshow-popup-card');

    if (!textContainer || !overlay || !card) {
        return;
    }

    let html = '';
    let data = null;

    if (description && typeof description === 'string') {
        const trimmed = description.trim();
        if (trimmed.startsWith('{') || trimmed.startsWith('[')) {
            try {
                data = JSON.parse(trimmed);
            } catch(e) {
                html = description;
            }
        } else {
            html = description;
        }
    } else if (description && typeof description === 'object') {
        data = description;
    } else {
        html = description || '';
    }

    if (data) {
        if (data.type === 'multi' && Array.isArray(data.items)) {
            html = '<div class="vsshow-qa-list">';
            data.items.forEach(item => {
                html += `
                    <div class="vsshow-qa-item">
                        <div class="vsshow-qa-q">${item.question || ''}</div>
                        <div class="vsshow-qa-a rte-content-body">${item.answer || ''}</div>
                    </div>
                `;
            });
            html += '</div>';
        } else if (Array.isArray(data)) {
             html = '<div class="vsshow-qa-list">';
             data.forEach(item => {
                 html += `
                     <div class="vsshow-qa-item">
                         <div class="vsshow-qa-q">${item.question || ''}</div>
                         <div class="vsshow-qa-a rte-content-body">${item.answer || ''}</div>
                     </div>
                 `;
             });
             html += '</div>';
        } else {
            html = typeof description === 'string' ? description : JSON.stringify(description);
        }
    }

    textContainer.innerHTML = html;
    
    if (imgPreview) {
        if (type === 'image' && mediaUrl) {
            imgPreview.src = mediaUrl;
            imgPreview.style.display = 'block';
        } else {
            imgPreview.style.display = 'none';
        }
    }

    overlay.classList.add('active');
    card.classList.add('active');
    document.body.style.overflow = 'hidden';
};

let currentRoom = null;
let currentView = 'front';
let currentRotationX = 0;
let currentRotationY = 0;
let isDragging = false;
let previousMousePosition = { x: 0, y: 0 };
let dragStartPos = { x: 0, y: 0 };

let currentZoom = 600;

document.addEventListener('DOMContentLoaded', () => {
    // Setup view switch buttons
    document.querySelectorAll('.vt3d-view-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const v = e.target.dataset.view;
            setView(v);
        });
    });

    // --- Drag to rotate logic ---
    const wrapper = document.getElementById('vt3d-scene-wrapper');
    const scene = document.getElementById('vt3d-scene');

    if (wrapper && scene) {
        wrapper.style.cursor = 'grab';

        // Mouse Events
        wrapper.addEventListener('mousedown', (e) => {
            isDragging = true;
            dragStartPos = { x: e.clientX, y: e.clientY };
            previousMousePosition = { x: e.clientX, y: e.clientY };
            wrapper.style.cursor = 'grabbing';
            scene.style.transition = 'none'; // remove transition for smooth drag
        });

        window.addEventListener('mousemove', (e) => {
            if (!isDragging) return;

            const deltaMove = {
                x: e.clientX - previousMousePosition.x,
                y: e.clientY - previousMousePosition.y
            };

            currentRotationY += deltaMove.x * 0.25;
            currentRotationX -= deltaMove.y * 0.25;

            // Limit up/down angle to avoid flipping
            currentRotationX = Math.max(-25, Math.min(25, currentRotationX));

            updateSceneTransform();

            previousMousePosition = { x: e.clientX, y: e.clientY };
        });

        window.addEventListener('mouseup', (e) => {
            if (isDragging) {
                isDragging = false;
                wrapper.style.cursor = 'grab';
                scene.style.transition = 'transform 0.4s ease-out';

                const dist = Math.hypot(e.clientX - dragStartPos.x, e.clientY - dragStartPos.y);
                if (dist < 5) {
                    // 1. Check for Info Buttons (Manual Hit-Test)
                    const infoBtns = document.querySelectorAll('.vsshow-info-btn');
                    let clickedInfo = false;
                    infoBtns.forEach(btn => {
                        const rect = btn.getBoundingClientRect();
                        if (e.clientX >= rect.left && e.clientX <= rect.right &&
                            e.clientY >= rect.top && e.clientY <= rect.bottom) {
                            clickedInfo = true;
                            btn.click();
                        }
                    });
                    if (clickedInfo) return;

                    // 2. Check for Doors
                    const activeDoors = document.querySelectorAll('.vt3d-door-slot[style*="display: block"], .vt3d-door-slot[style*="display:block"]');
                    activeDoors.forEach(activeDoor => {
                        const rect = activeDoor.getBoundingClientRect();
                        if (e.clientX >= rect.left && e.clientX <= rect.right &&
                            e.clientY >= rect.top && e.clientY <= rect.bottom) {
                            
                            const doorWall = activeDoor.dataset.wall;
                            let rotY = ((currentRotationY % 360) + 360) % 360;
                            let isFacing = false;
                            if (doorWall === 'back')  isFacing = (rotY > 90 && rotY < 270);
                            if (doorWall === 'front') isFacing = (rotY < 90 || rotY > 270);
                            if (doorWall === 'left')  isFacing = (rotY > 0 && rotY < 180);
                            if (doorWall === 'right') isFacing = (rotY > 180 && rotY < 360);
                            
                            if (isFacing) {
                                window.handleDoorClick(e, activeDoor);
                            }
                        }
                    });
                }
            }
        });

        // Zoom with Mouse Wheel
        wrapper.addEventListener('wheel', (e) => {
            e.preventDefault();
            const zoomSpeed = 0.5;
            currentZoom -= e.deltaY * zoomSpeed; // Scroll up (neg delta) bounds to Zoom In (higher Z)
            // Limit zoom distance (lower = zoom out, higher = zoom in)
            currentZoom = Math.max(200, Math.min(1200, currentZoom));
            
            updateSceneTransform();
        }, { passive: false });

        // Touch Events for mobile
        let lastTouchDist = 0;
        wrapper.addEventListener('touchstart', (e) => {
            if (e.touches.length === 2) {
                // Pinch zoom start
                lastTouchDist = Math.hypot(
                    e.touches[0].clientX - e.touches[1].clientX,
                    e.touches[0].clientY - e.touches[1].clientY
                );
                isDragging = false;
                return;
            }
            isDragging = true;
            dragStartPos = { x: e.touches[0].clientX, y: e.touches[0].clientY };
            previousMousePosition = { x: e.touches[0].clientX, y: e.touches[0].clientY };
            scene.style.transition = 'none';
        }, { passive: true });

        wrapper.addEventListener('touchmove', (e) => {
            if (e.touches.length === 2) {
                // Pinch zoom
                const dist = Math.hypot(
                    e.touches[0].clientX - e.touches[1].clientX,
                    e.touches[0].clientY - e.touches[1].clientY
                );
                if (lastTouchDist > 0) {
                    const delta = (lastTouchDist - dist) * 2;
                    currentZoom = Math.max(200, Math.min(1200, currentZoom + delta));
                    updateSceneTransform();
                }
                lastTouchDist = dist;
                e.preventDefault();
                return;
            }
            if (!isDragging) return;
            // Prevent scrolling while panning 3D viewer
            e.preventDefault();

            const deltaMove = {
                x: e.touches[0].clientX - previousMousePosition.x,
                y: e.touches[0].clientY - previousMousePosition.y
            };

            currentRotationY += deltaMove.x * 0.4;
            currentRotationX -= deltaMove.y * 0.4;

            currentRotationX = Math.max(-25, Math.min(25, currentRotationX));

            updateSceneTransform();

            previousMousePosition = { x: e.touches[0].clientX, y: e.touches[0].clientY };
        }, { passive: false });

        wrapper.addEventListener('touchend', (e) => {
            if (e.touches.length < 2) lastTouchDist = 0;
            if (isDragging) {
                isDragging = false;
                scene.style.transition = 'transform 0.4s ease-out';

                if (e.changedTouches && e.changedTouches.length > 0) {
                    const touch = e.changedTouches[0];
                    const dist = Math.hypot(touch.clientX - dragStartPos.x, touch.clientY - dragStartPos.y);
                    if (dist < 10) {
                        // 1. Check for Info Buttons
                        const infoBtns = document.querySelectorAll('.vsshow-info-btn');
                        let clickedInfo = false;
                        infoBtns.forEach(btn => {
                            const rect = btn.getBoundingClientRect();
                            if (touch.clientX >= rect.left && touch.clientX <= rect.right &&
                                touch.clientY >= rect.top && touch.clientY <= rect.bottom) {
                                clickedInfo = true;
                                btn.click();
                            }
                        });
                        if (clickedInfo) return;

                        // 2. Check for Doors
                        const activeDoors = document.querySelectorAll('.vt3d-door-slot[style*="display: block"], .vt3d-door-slot[style*="display:block"]');
                        activeDoors.forEach(activeDoor => {
                            const rect = activeDoor.getBoundingClientRect();
                            if (touch.clientX >= rect.left && touch.clientX <= rect.right &&
                                touch.clientY >= rect.top && touch.clientY <= rect.bottom) {

                                const doorWall = activeDoor.dataset.wall;
                                let rotY = ((currentRotationY % 360) + 360) % 360;
                                let isFacing = false;
                                if (doorWall === 'back')  isFacing = (rotY > 90 && rotY < 270);
                                if (doorWall === 'front') isFacing = (rotY < 90 || rotY > 270);
                                if (doorWall === 'left')  isFacing = (rotY > 0 && rotY < 180);
                                if (doorWall === 'right') isFacing = (rotY > 180 && rotY < 360);

                                if (isFacing) {
                                    window.handleDoorClick(e, activeDoor);
                                }
                            }
                        });
                    }
                }
            }
        });
    }

    const closeBtn = document.getElementById('vss-popup-close');
    const popupOverlay = document.getElementById('vss-popup-overlay');
    if (closeBtn) closeBtn.onclick = closePopup;
    if (popupOverlay) popupOverlay.onclick = closePopup;
});

function openRoom3D(roomId) {
    currentRoom = window.virtualRooms3D.find(r => r.id === roomId);
    if (!currentRoom) return;

    // Reset view state
    currentZoom = 600;
    currentRotationX = 0;
    currentRotationY = 0;

    // Apply colors
    document.getElementById('wallEditor') && document.getElementById('wallEditor').style.setProperty('background-color', currentRoom.wall_color);
    document.querySelectorAll('.vt3d-wall').forEach(w => w.style.backgroundColor = currentRoom.wall_color);
    const floor = document.getElementById('vt3d-floor');
    if (floor) floor.style.backgroundColor = currentRoom.floor_color || '#8B7355';
    
    const ceiling = document.getElementById('vt3d-ceiling');
    if (ceiling) ceiling.style.backgroundColor = currentRoom.ceiling_color || '#f5f5f5';

    // Render Media
    renderRoomMedia();

    // Render Doors on their respective walls
    document.querySelectorAll('.vt3d-door-slot').forEach(slot => {
        slot.style.display = 'none';
        const wall = slot.dataset.wall;
        const config = (currentRoom.doors && currentRoom.doors[wall]) ? currentRoom.doors[wall] : null;

        if (config && config.link_type && config.link_type !== 'none') {
            slot.style.display = 'block';
            const doorLabel = slot.querySelector('.vt3d-door-label');
            if (doorLabel) {
                doorLabel.innerText = currentRoom.door_labels
                    ? (currentRoom.door_labels[wall] || config.label || '')
                    : (currentRoom.door_label || config.label || '');
            }
            
            // Setup Peek/Portal effect for door
            const doorPortal = slot.querySelector('.vt3d-door-portal');
            if (doorPortal) {
                doorPortal.style.display = 'none';
                slot.style.backgroundColor = '#000'; // default void
                
                if (config.link_type === 'room') {
                    const targetId = parseInt(config.target);
                    const targetRoom = window.virtualRooms3D.find(r => r.id === targetId);
                    if (targetRoom) {
                        if (targetRoom.thumbnail_url) {
                            doorPortal.src = targetRoom.thumbnail_url;
                            doorPortal.style.display = 'block';
                        } else {
                            slot.style.backgroundColor = targetRoom.wall_color || '#1e293b';
                        }
                    }
                } else if (config.link_type === 'url') {
                    slot.style.backgroundColor = '#e0f2fe'; // Bright light for external URL
                }
            }
        }
    });

    // Show viewer and reset view
    document.getElementById('room3d-viewer').style.display = 'block';
    
    document.getElementById('vt3d-scene').style.transition = 'none';
    setView('front');
    setTimeout(() => {
        document.getElementById('vt3d-scene').style.transition = 'transform 0.4s ease-out';
    }, 50);
    
    // Auto-play videos if any
    document.querySelectorAll('#room3d-viewer video').forEach(v => {
        v.play().catch(e => console.log('Auto-play blocked'));
    });
}

function closeRoom3D() {
    document.getElementById('room3d-viewer').style.display = 'none';
    
    // Pause videos
    document.querySelectorAll('#room3d-viewer video').forEach(v => v.pause());
}

function renderRoomMedia() {
    // Clear old media
    document.querySelectorAll('.vt3d-media-layer').forEach(layer => layer.innerHTML = '');

    if (!currentRoom.media) return;

    currentRoom.media.forEach(m => {
        const layer = document.querySelector(`.vt3d-media-layer[data-wall="${m.wall}"]`);
        if (!layer) return;

        const wrapper = document.createElement('div');
        wrapper.style.position = 'absolute';
        wrapper.style.left = m.position_x + '%';
        wrapper.style.top = m.position_y + '%';
        wrapper.style.width = m.width + '%';
        wrapper.style.height = m.height + '%';
        wrapper.style.transform = 'translate(-50%, -50%)';
        wrapper.className = 'vt3d-media-item shadow-2xl';

        if (m.type === 'image') {
            const img = document.createElement('img');
            img.src = m.file_path;
            img.style.width = '100%';
            img.style.height = '100%';
            img.style.objectFit = 'contain';
            wrapper.appendChild(img);
        } else if (m.type === 'video') {
            const vid = document.createElement('video');
            vid.src = m.file_path;
            vid.controls = true;
            vid.loop = true;
            vid.muted = false; // Allow unmute from controls
            vid.style.width = '100%';
            vid.style.height = '100%';
            vid.style.objectFit = 'contain';
            wrapper.appendChild(vid);
        }

        // Add Info Button if description exists
        const hasDescription = m.description && (
            (typeof m.description === 'string' && m.description.trim() !== '') ||
            (typeof m.description === 'object' && Object.keys(m.description).length > 0)
        );
        if (hasDescription) {
            const infoBtn = document.createElement('button');
            infoBtn.className = 'vsshow-info-btn';
            infoBtn.innerHTML = '?';
            infoBtn.title = 'More Info';
            
            // Stop events from bubbling to prevent 3D scene dragging
            const stopProp = (e) => { e.stopPropagation(); };
            infoBtn.onmousedown = stopProp;
            infoBtn.ontouchstart = stopProp;
            
            infoBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                if (typeof window.openV3DInfo === 'function') {
                    window.openV3DInfo(m.description, m.file_path, m.type);
                }
            });
            wrapper.appendChild(infoBtn);
        }

        layer.appendChild(wrapper);
    });
}

function updateSceneTransform() {
    const scene = document.getElementById('vt3d-scene');
    if(scene) {
        scene.style.transform = `translateZ(${currentZoom}px) rotateX(${currentRotationX}deg) rotateY(${currentRotationY}deg)`;
    }
}

function setView(view) {
    currentView = view;
    
    if (view === 'front') currentRotationY = 0;
    else if (view === 'left') currentRotationY = -90;
    else if (view === 'back') currentRotationY = 180;
    else if (view === 'right') currentRotationY = 90;

    currentRotationX = 0; // Reset look up/down

    updateSceneTransform();

    // Update buttons UI
    document.querySelectorAll('.vt3d-view-btn').forEach(btn => {
        if (btn.dataset.view === view) {
            btn.classList.add('active');
            btn.style.backgroundColor = '#3b82f6';
            btn.style.color = 'white';
        } else {
            btn.classList.remove('active');
            btn.style.backgroundColor = '';
            btn.style.color = '';
        }
    });
}

window.handleDoorClick = function(e, doorEl) {
    if (e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    if (!currentRoom) return;
    if (window.isNavigatingToDoor) return;

    // If doorEl is not passed (legacy/fallback), try to find the active one
    if (!doorEl) {
        doorEl = document.querySelector('.vt3d-door-slot[style*="display: block"], .vt3d-door-slot[style*="display:block"]');
    }

    // Find the config for THIS specific door based on its wall
    const wall = doorEl ? doorEl.dataset.wall : 'back';
    const config = (currentRoom.doors && currentRoom.doors[wall]) ? currentRoom.doors[wall] : null;
    
    if (!config || config.link_type === 'none') return;
    
    window.isNavigatingToDoor = true;

    // 1. Play smooth door open animation without moving the user's camera
    if (doorEl) {
        doorEl.classList.add('vt3d-door-open');
    }

    // 2. Transition to new room or URL after the door fully opens
    setTimeout(() => {
        window.isNavigatingToDoor = false; // Reset lock
        if(doorEl) doorEl.classList.remove('vt3d-door-open'); // Close door behind us

        if (config.link_type === 'url' && config.target) {
            if(config.target.startsWith('http')) {
                window.open(config.target, '_blank');
            } else {
                window.location.href = config.target;
            }
        } else if (config.link_type === 'room' && config.target) {
            const targetRoomId = parseInt(config.target);
            if(!isNaN(targetRoomId)) {
                closeRoom3D();
                setTimeout(() => { openRoom3D(targetRoomId); }, 50);
            }
        }
    }, 1200); // match CSS door open animation duration
};
