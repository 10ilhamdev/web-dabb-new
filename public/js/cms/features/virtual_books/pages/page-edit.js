// These are set by the Blade template via window.pageEditorConfig
let existingImages = window.pageEditorConfig.existingImages || [];
let existingImagePositions = window.pageEditorConfig.existingImagePositions || [];
let newImages = [];
let newImagePositions = [];
let textPosition = window.pageEditorConfig.textPosition || { x: 0, y: 0, width: 45, height: 30 };
let imageFitMode = 'contained';

// Mark images to remove
let imagesToRemove = [];

function setImageFitMode(mode) {
    imageFitMode = mode;
    const hidden = document.getElementById('imageFitModeInput');
    if (hidden) hidden.value = mode;

    document.querySelectorAll('.fit-mode-btn').forEach(btn => {
        if (btn.dataset.mode === mode) {
            btn.classList.add('active');
        } else {
            btn.classList.remove('active');
        }
    });

    updatePreview();
}

function handleImageUpload(input) {
    const files = input.files;
    if (files && files.length > 0) {
        Array.from(files).forEach((file) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                newImages.push(e.target.result);
                newImagePositions.push({ x: 0, y: 0 });
                updateThumbnails();
                updatePreview();
            };
            reader.readAsDataURL(file);
        });
    }
}

function updateThumbnails() {
    const container = document.getElementById('imageThumbnails');
    container.innerHTML = '';

    newImages.forEach((src, index) => {
        const div = document.createElement('div');
        div.className = 'image-thumbnail';
        div.innerHTML = `
            <img src="${src}" alt="Image ${index + 1}">
            <button type="button" class="remove-btn" onclick="removeNewImage(${index})">&times;</button>
            <div class="position-label">Baru ${index + 1}</div>
        `;
        container.appendChild(div);
    });
}

function removeExistingImage(index) {
    imagesToRemove.push(index);
    existingImages[index] = null;

    const thumbs = document.querySelectorAll('#existingThumbnails .image-thumbnail');
    if (thumbs[index]) {
        thumbs[index].style.opacity = '0.3';
        thumbs[index].style.pointerEvents = 'none';
    }

    updatePreview();
}

function removeNewImage(index) {
    newImages.splice(index, 1);
    newImagePositions.splice(index, 1);
    updateThumbnails();
    updatePreview();
}

function getAllImages() {
    const images = [];
    existingImages.forEach((img, index) => {
        if (img !== null && !imagesToRemove.includes(index)) {
            images.push({ src: img, isExisting: true, position: existingImagePositions[index] || { x: 0, y: 0 } });
        }
    });
    newImages.forEach((src, index) => {
        images.push({ src: src, isExisting: false, position: newImagePositions[index] || { x: 0, y: 0 } });
    });
    return images;
}

function updatePreview() {
    const title = document.getElementById('pageTitleInput').value || 'Judul Halaman';
    const content = document.getElementById('pageContentInput').value;
    const imageHeight = document.getElementById('imageHeightSlider').value;
    const allImages = getAllImages();
    const hasImages = allImages && allImages.length > 0;
    const pageFooterOrder = document.getElementById('bookPreview').dataset.pageOrder || '';

    const preview = document.getElementById('bookPreview');
    preview.className = 'book-preview content-page' + (imageFitMode === 'fullbleed' ? ' fullbleed-mode' : '');
    preview.innerHTML = '';

    let innerContent = '<div class="content-page-inner">';

    if (title) {
        innerContent += `<div class="content-page-header">${title}</div>`;
    }

    innerContent += '<div class="draggable-container" id="draggableContainer">';

    if (hasImages) {
        const existingCount = existingImages.filter(x => x !== null).length;
        allImages.forEach((img, index) => {
            const pos = img.position || { x: 0, y: 0 };
            const size = Math.max(20, parseInt(imageHeight));
            const heightPct = imageFitMode === 'fullbleed' ? size : size * 0.75;
            const imgSrc = img.isExisting ? `/storage/${img.src}` : img.src;
            const isNew = index >= existingCount;
            innerContent += `
                <div class="draggable-element draggable-image"
                     data-type="image"
                     data-is-new="${isNew ? 'true' : 'false'}"
                     data-index="${index}"
                     style="background-image: url('${imgSrc}'); width: ${size}%; height: ${heightPct}%; left: ${pos.x}%; top: ${pos.y}%;"
                     onmousedown="startDrag(event, this)">
                </div>`;
        });
    }

    if (content) {
        const textPos = textPosition || { x: 0, y: 0, width: 45, height: 30 };
        const textWidth = textPos.width || 45;
        const textHeight = textPos.height || 30;
        innerContent += `
            <div class="draggable-element draggable-text"
                 data-type="text"
                 style="width: ${textWidth}%; height: ${textHeight}%; left: ${textPos.x}%; top: ${textPos.y}%;"
                 onmousedown="startDrag(event, this)">
                ${content.replace(/\n/g, '<br>')}
                <div class="resize-handle" onmousedown="startResize(event, this.parentElement)"></div>
            </div>`;
    }

    innerContent += '</div>';
    innerContent += `<div class="content-page-footer">${pageFooterOrder}</div>`;
    innerContent += '</div>';
    preview.innerHTML = innerContent;
}

// Drag functionality
let draggedElement = null;
let dragOffset = { x: 0, y: 0 };
let dragContainer = null;

function startDrag(e, element) {
    e.preventDefault();
    draggedElement = element;
    dragContainer = document.getElementById('draggableContainer');

    const rect = element.getBoundingClientRect();
    dragOffset.x = e.clientX - rect.left;
    dragOffset.y = e.clientY - rect.top;

    element.classList.add('dragging');

    document.addEventListener('mousemove', onDrag);
    document.addEventListener('mouseup', stopDrag);
}

function onDrag(e) {
    if (!draggedElement || !dragContainer) return;

    const containerRect = dragContainer.getBoundingClientRect();
    let newX = ((e.clientX - containerRect.left - dragOffset.x) / containerRect.width) * 100;
    let newY = ((e.clientY - containerRect.top - dragOffset.y) / containerRect.height) * 100;

    newX = Math.max(0, Math.min(100 - parseFloat(draggedElement.style.width) || 50, newX));
    newY = Math.max(0, Math.min(100 - parseFloat(draggedElement.style.height) || 30, newY));

    draggedElement.style.left = newX + '%';
    draggedElement.style.top = newY + '%';

    const type = draggedElement.dataset.type;
    const index = parseInt(draggedElement.dataset.index);
    const isNew = draggedElement.dataset.isNew === 'true';

    if (type === 'image') {
        if (isNew) {
            const newIndex = index - existingImages.filter(x => x !== null).length;
            newImagePositions[newIndex] = { x: newX, y: newY };
        } else {
            existingImagePositions[index] = { x: newX, y: newY };
        }
    } else if (type === 'text') {
        textPosition = { ...textPosition, x: newX, y: newY };
    }
}

function stopDrag() {
    if (draggedElement) {
        draggedElement.classList.remove('dragging');
        draggedElement = null;
    }
    document.removeEventListener('mousemove', onDrag);
    document.removeEventListener('mouseup', stopDrag);
    updatePositionInputs();
}

// Resize functionality for text
let resizingElement = null;
let resizeStartPos = { x: 0, y: 0 };
let resizeStartSize = { width: 0, height: 0 };

function startResize(e, element) {
    e.preventDefault();
    e.stopPropagation();
    resizingElement = element;
    dragContainer = document.getElementById('draggableContainer');

    resizeStartPos.x = e.clientX;
    resizeStartPos.y = e.clientY;
    resizeStartSize.width = parseFloat(element.style.width) || 45;
    resizeStartSize.height = parseFloat(element.style.height) || 30;

    element.classList.add('dragging');

    document.addEventListener('mousemove', onResize);
    document.addEventListener('mouseup', stopResize);
}

function onResize(e) {
    if (!resizingElement || !dragContainer) return;

    const containerRect = dragContainer.getBoundingClientRect();
    const deltaX = e.clientX - resizeStartPos.x;
    const deltaY = e.clientY - resizeStartPos.y;

    const newWidth = Math.max(20, Math.min(100, resizeStartSize.width + (deltaX / containerRect.width) * 100));
    const newHeight = Math.max(15, Math.min(100, resizeStartSize.height + (deltaY / containerRect.height) * 100));

    resizingElement.style.width = newWidth + '%';
    resizingElement.style.height = newHeight + '%';
}

function stopResize() {
    if (resizingElement) {
        textPosition.width = parseFloat(resizingElement.style.width) || 45;
        textPosition.height = parseFloat(resizingElement.style.height) || 30;

        resizingElement.classList.remove('dragging');
        resizingElement = null;
    }
    document.removeEventListener('mousemove', onResize);
    document.removeEventListener('mouseup', stopResize);
    updatePositionInputs();
}

function updatePositionInputs() {
    const container = document.getElementById('positionInputs');
    let html = '';

    let allImages = getAllImages();
    allImages.forEach((img, index) => {
        html += `<input type="hidden" name="image_positions[${index}][x]" value="${img.position.x}">`;
        html += `<input type="hidden" name="image_positions[${index}][y]" value="${img.position.y}">`;
    });

    html += `<input type="hidden" name="text_position[x]" value="${textPosition.x}">`;
    html += `<input type="hidden" name="text_position[y]" value="${textPosition.y}">`;
    html += `<input type="hidden" name="text_position[width]" value="${textPosition.width || 45}">`;
    html += `<input type="hidden" name="text_position[height]" value="${textPosition.height || 30}">`;

    imagesToRemove.forEach(index => {
        html += `<input type="hidden" name="remove_images[]" value="${index}">`;
    });

    container.innerHTML = html;
}

// Thumbnail generation & initial render
document.addEventListener('DOMContentLoaded', function() {
    const fitInput = document.getElementById('imageFitModeInput');
    if (fitInput) {
        setImageFitMode(fitInput.value || 'contained');
    } else {
        updatePreview();
    }

    const generateBtn = document.getElementById('generateThumbnailBtn');
    const generatedInput = document.getElementById('generatedThumbnail');
    const previewContainer = document.getElementById('thumbnailPreviewContainer');
    const previewImg = document.getElementById('thumbnailPreview');
    const removeBtn = document.getElementById('removeThumbnail');

    generateBtn.addEventListener('click', async function() {
        const bookPreview = document.getElementById('bookPreview');
        if (!bookPreview) return;

        try {
            generateBtn.disabled = true;
            generateBtn.textContent = 'Generating...';

            bookPreview.classList.add('is-capturing');

            const canvas = await html2canvas(bookPreview, {
                backgroundColor: null,
                scale: 2,
                useCORS: true,
                allowTaint: true,
                logging: false,
                onclone: function(clonedDoc) {
                    const clonedPreview = clonedDoc.getElementById('bookPreview');
                    if (clonedPreview) {
                        const guides = clonedPreview.querySelectorAll('.resize-handle');
                        guides.forEach(g => g.style.setProperty('display', 'none', 'important'));

                        clonedPreview.querySelectorAll('.draggable-element').forEach(el => {
                            el.style.setProperty('outline', 'none', 'important');
                            el.style.setProperty('box-shadow', 'none', 'important');
                            el.style.setProperty('border', 'none', 'important');
                        });
                    }
                }
            });

            bookPreview.classList.remove('is-capturing');

            const dataUrl = canvas.toDataURL('image/png');
            generatedInput.value = dataUrl;
            previewImg.src = dataUrl;
            previewContainer.classList.remove('hidden');

            document.getElementById('thumbnailInput').value = '';
        } catch (err) {
            alert('Gagal generate thumbnail: ' + err.message);
        } finally {
            generateBtn.disabled = false;
            generateBtn.innerHTML = `
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Generate dari Preview
            `;
        }
    });

    removeBtn.addEventListener('click', function() {
        generatedInput.value = '';
        previewImg.src = '';
        previewContainer.classList.add('hidden');
    });
});
