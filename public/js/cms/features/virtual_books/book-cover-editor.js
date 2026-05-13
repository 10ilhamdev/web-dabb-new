/**
 * Unified Book Cover Editor
 *
 * Expects window.bookEditorConfig to be set before this script loads:
 * {
 *   coverX, coverY, coverScale,
 *   titleX, titleY,
 *   additionalTexts: [{ id, text, position: {x, y} }],
 *   backCoverX, backCoverY, backCoverScale,
 *   backTitleX, backTitleY,
 *   backAdditionalTexts: [{ id, text, position: {x, y} }],
 * }
 */
document.addEventListener('DOMContentLoaded', function() {
    var cfg = window.bookEditorConfig || {};

    // ==================== Front Cover ====================
    var coverInput = document.getElementById('coverImageInput');
    var coverPreview = document.getElementById('coverPreview');
    var coverPlaceholder = document.getElementById('coverPlaceholder');
    var coverContainer = document.getElementById('coverContainer');
    var coverPositionInput = document.getElementById('coverPosition');
    var coverScaleInput = document.getElementById('coverScale');
    var resizeBorder = document.getElementById('resizeBorder');

    var titleInput = document.getElementById('bookTitle');
    var previewTitle = document.getElementById('previewTitle');
    var titleContainer = document.getElementById('titleContainer');
    var titlePositionInput = document.getElementById('titlePosition');

    var additionalTextsContainer = document.getElementById('additionalTextsContainer');
    var additionalTextsPreview = document.getElementById('additionalTextsPreview');
    var addTextBtn = document.getElementById('addTextBtn');
    var coverTextsInput = document.getElementById('coverTexts');
    var resetPositionBtn = document.getElementById('resetPosition');

    var zoomInBtn = document.getElementById('zoomInBtn');
    var zoomOutBtn = document.getElementById('zoomOutBtn');
    var zoomSlider = document.getElementById('zoomSlider');
    var zoomLevel = document.getElementById('zoomLevel');

    // State
    var textCounter = (cfg.additionalTexts || []).length;
    var additionalTexts = (cfg.additionalTexts || []).slice();

    var isCoverDragging = false;
    var coverStartX, coverStartY;
    var coverX = cfg.coverX || 0;
    var coverY = cfg.coverY || 0;

    var isTitleDragging = false;
    var titleStartX, titleStartY;
    var titleX = cfg.titleX || 0;
    var titleY = cfg.titleY || 0;

    var isTextDragging = false;
    var textDragStartX, textDragStartY;
    var currentTextId = null;

    var currentScale = cfg.coverScale || 1;
    var isResizing = false;

    // --- Cover image upload ---
    if (coverInput) {
        coverInput.addEventListener('change', function(e) {
            var file = e.target.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    coverX = 0;
                    coverY = 0;
                    currentScale = 1;
                    coverPreview.src = e.target.result;
                    coverPreview.style.display = 'block';
                    if (coverPlaceholder) coverPlaceholder.style.display = 'none';
                    if (resizeBorder) {
                        resizeBorder.style.display = 'block';
                        resizeBorder.style.opacity = '1';
                    }
                    updateCoverPosition(0, 0);
                    updateCoverScale(1);
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // --- Title sync ---
    if (titleInput) {
        titleInput.addEventListener('input', function(e) {
            previewTitle.textContent = e.target.value || 'Judul Buku';
        });
    }

    // --- Cover drag ---
    if (coverContainer) {
        coverContainer.addEventListener('mousedown', function(e) {
            if (!coverPreview.src || coverPreview.style.display === 'none') return;
            isCoverDragging = true;
            coverStartX = e.clientX - coverX;
            coverStartY = e.clientY - coverY;
            coverContainer.style.cursor = 'grabbing';
        });

        coverContainer.addEventListener('touchstart', function(e) {
            if (!coverPreview.src || coverPreview.style.display === 'none') return;
            isCoverDragging = true;
            var touch = e.touches[0];
            coverStartX = touch.clientX - coverX;
            coverStartY = touch.clientY - coverY;
        });

        coverContainer.addEventListener('wheel', function(e) {
            e.preventDefault();
            var delta = e.deltaY > 0 ? -0.1 : 0.1;
            updateCoverScale(currentScale + delta);
        }, { passive: false });
    }

    // --- Title drag ---
    if (titleContainer) {
        titleContainer.addEventListener('mousedown', function(e) {
            e.preventDefault();
            isTitleDragging = true;
            titleStartX = e.clientX - titleX;
            titleStartY = e.clientY - titleY;
            titleContainer.style.cursor = 'grabbing';
        });

        titleContainer.addEventListener('touchstart', function(e) {
            isTitleDragging = true;
            var touch = e.touches[0];
            titleStartX = touch.clientX - titleX;
            titleStartY = touch.clientY - titleY;
        });
    }

    // --- Text preview drag (delegation) ---
    if (additionalTextsPreview) {
        additionalTextsPreview.addEventListener('mousedown', function(e) {
            var el = e.target.closest('[data-text-id]');
            if (el) {
                e.stopPropagation();
                isTextDragging = true;
                currentTextId = parseInt(el.dataset.textId);
                var textObj = additionalTexts.find(function(t) { return t.id === currentTextId; });
                textDragStartX = e.clientX - (textObj ? textObj.position.x : 0);
                textDragStartY = e.clientY - (textObj ? textObj.position.y : 0);
                el.style.cursor = 'grabbing';
            }
        });

        additionalTextsPreview.addEventListener('touchstart', function(e) {
            var el = e.target.closest('[data-text-id]');
            if (el) {
                e.stopPropagation();
                isTextDragging = true;
                currentTextId = parseInt(el.dataset.textId);
                var textObj = additionalTexts.find(function(t) { return t.id === currentTextId; });
                var touch = e.touches[0];
                textDragStartX = touch.clientX - (textObj ? textObj.position.x : 0);
                textDragStartY = touch.clientY - (textObj ? textObj.position.y : 0);
            }
        });
    }

    // --- Global mouse/touch move ---
    document.addEventListener('mousemove', function(e) {
        if (isCoverDragging) {
            e.preventDefault();
            coverX = e.clientX - coverStartX;
            coverY = e.clientY - coverStartY;
            updateCoverPosition(coverX, coverY);
        }
        if (isTitleDragging) {
            e.preventDefault();
            titleX = e.clientX - titleStartX;
            titleY = e.clientY - titleStartY;
            updateTitlePosition(titleX, titleY);
        }
        if (isTextDragging && currentTextId !== null) {
            e.preventDefault();
            var textObj = additionalTexts.find(function(t) { return t.id === currentTextId; });
            if (textObj) {
                updateTextPosition(currentTextId, e.clientX - textDragStartX, e.clientY - textDragStartY);
            }
        }
    });

    document.addEventListener('mouseup', function() {
        if (isCoverDragging) {
            isCoverDragging = false;
            if (coverContainer) coverContainer.style.cursor = 'move';
        }
        if (isTitleDragging) {
            isTitleDragging = false;
            if (titleContainer) titleContainer.style.cursor = 'move';
        }
        if (isTextDragging) {
            isTextDragging = false;
            var el = document.getElementById('textPreview_' + currentTextId);
            if (el) el.style.cursor = 'move';
            currentTextId = null;
        }
    });

    document.addEventListener('touchmove', function(e) {
        if (isCoverDragging) {
            var touch = e.touches[0];
            coverX = touch.clientX - coverStartX;
            coverY = touch.clientY - coverStartY;
            updateCoverPosition(coverX, coverY);
        }
        if (isTitleDragging) {
            var touch = e.touches[0];
            titleX = touch.clientX - titleStartX;
            titleY = touch.clientY - titleStartY;
            updateTitlePosition(titleX, titleY);
        }
        if (isTextDragging && currentTextId !== null) {
            var touch = e.touches[0];
            var textObj = additionalTexts.find(function(t) { return t.id === currentTextId; });
            if (textObj) {
                updateTextPosition(currentTextId, touch.clientX - textDragStartX, touch.clientY - textDragStartY);
            }
        }
    });

    document.addEventListener('touchend', function() {
        isCoverDragging = false;
        isTitleDragging = false;
        isTextDragging = false;
        isResizing = false;
        currentTextId = null;
    });

    // --- Pinch-to-zoom ---
    var initialPinchDistance = null;
    if (coverContainer) {
        coverContainer.addEventListener('touchstart', function(e) {
            if (e.touches.length === 2) {
                var dx = e.touches[0].clientX - e.touches[1].clientX;
                var dy = e.touches[0].clientY - e.touches[1].clientY;
                initialPinchDistance = Math.sqrt(dx * dx + dy * dy);
            }
        });
    }

    document.addEventListener('touchmove', function(e) {
        if (e.touches.length === 2 && initialPinchDistance !== null) {
            var dx = e.touches[0].clientX - e.touches[1].clientX;
            var dy = e.touches[0].clientY - e.touches[1].clientY;
            var dist = Math.sqrt(dx * dx + dy * dy);
            var delta = (dist - initialPinchDistance) / 200;
            updateCoverScale(currentScale + delta);
            initialPinchDistance = dist;
        }
    });

    document.addEventListener('touchend', function() {
        initialPinchDistance = null;
        isResizing = false;
    });

    // --- Zoom controls ---
    if (zoomInBtn) {
        zoomInBtn.addEventListener('click', function() { updateCoverScale(currentScale + 0.1); });
    }
    if (zoomOutBtn) {
        zoomOutBtn.addEventListener('click', function() { updateCoverScale(currentScale - 0.1); });
    }
    if (zoomSlider) {
        zoomSlider.addEventListener('input', function(e) { updateCoverScale(e.target.value / 100); });
    }

    // --- Update helpers ---
    function updateCoverPosition(x, y) {
        if (coverPreview) {
            coverPreview.style.transform = 'translate(' + x + 'px, ' + y + 'px) scale(' + currentScale + ')';
        }
        if (coverPositionInput) {
            coverPositionInput.value = JSON.stringify({x: x, y: y});
        }
    }

    function updateTitlePosition(x, y) {
        if (titleContainer) {
            titleContainer.style.transform = 'translate(' + x + 'px, ' + y + 'px)';
        }
        if (titlePositionInput) {
            titlePositionInput.value = JSON.stringify({x: x, y: y});
        }
    }

    function updateCoverScale(scale) {
        currentScale = Math.max(0.3, Math.min(2.5, scale));
        if (coverPreview) {
            coverPreview.style.transform = 'translate(' + coverX + 'px, ' + coverY + 'px) scale(' + currentScale + ')';
        }
        if (coverScaleInput) coverScaleInput.value = currentScale;
        if (zoomSlider) zoomSlider.value = currentScale * 100;
        if (zoomLevel) zoomLevel.textContent = Math.round(currentScale * 100) + '%';
    }

    function updateTextPosition(textId, x, y) {
        var textObj = additionalTexts.find(function(t) { return t.id === textId; });
        if (textObj) {
            textObj.position = {x: x, y: y};
            var preview = document.getElementById('textPreview_' + textId);
            if (preview) {
                preview.style.transform = 'translate(' + x + 'px, ' + y + 'px)';
            }
            updateCoverTexts();
        }
    }

    function updateCoverTexts() {
        if (coverTextsInput) {
            var texts = additionalTexts.map(function(t) {
                return { text: t.text, position: t.position };
            });
            coverTextsInput.value = JSON.stringify(texts);
        }
    }

    // --- Add text ---
    if (addTextBtn) {
        addTextBtn.addEventListener('click', function() {
            var textId = textCounter++;
            additionalTexts.push({ id: textId, text: '', position: {x: 0, y: 0} });

            var els = addTextFieldAndPreview(textId, '', additionalTextsContainer, additionalTextsPreview, 'cover_text_', 'data-text-id', 'textPreview_', 'remove-text-btn');

            var input = els.textField.querySelector('input');
            input.addEventListener('input', function(e) {
                var tid = parseInt(this.getAttribute('data-text-id'));
                var obj = additionalTexts.find(function(t) { return t.id === tid; });
                if (obj) {
                    obj.text = e.target.value;
                    var p = document.getElementById('textPreview_' + tid);
                    if (p) p.textContent = e.target.value || ('Teks ' + (tid + 1));
                    updateCoverTexts();
                }
            });

            var removeBtn = els.textField.querySelector('.remove-text-btn');
            removeBtn.addEventListener('click', function() {
                removeText(parseInt(this.dataset.id));
            });

            updateCoverTexts();
        });
    }

    function addTextFieldAndPreview(textId, text, container, previewContainer, namePrefix, dataAttr, previewPrefix, removeBtnClass) {
        var textField = document.createElement('div');
        textField.className = 'flex items-center gap-2';
        textField.dataset.id = textId;
        textField.innerHTML =
            '<input type="text" name="' + namePrefix + textId + '" placeholder="Teks tambahan ' + (textId + 1) + '"' +
            ' class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm"' +
            ' ' + dataAttr + '="' + textId + '" value="' + escapeHtml(text) + '">' +
            '<button type="button" class="' + removeBtnClass + ' p-1.5 text-red-500 hover:bg-red-50 rounded-md transition-colors" data-id="' + textId + '">' +
            '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />' +
            '</svg></button>';
        container.appendChild(textField);

        var textPreview = document.createElement('span');
        textPreview.id = previewPrefix + textId;
        textPreview.className = 'block text-white/80 text-[10px] drop-shadow-md line-clamp-1 mt-1 cursor-move';
        textPreview.textContent = text || ('Teks ' + (textId + 1));
        textPreview.dataset[dataAttr === 'data-text-id' ? 'textId' : 'backTextId'] = textId;
        previewContainer.appendChild(textPreview);

        return { textField: textField, textPreview: textPreview };
    }

    function escapeHtml(str) {
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(str || ''));
        return div.innerHTML;
    }

    // --- Remove text ---
    function removeText(textId) {
        additionalTexts = additionalTexts.filter(function(t) { return t.id !== textId; });
        var field = additionalTextsContainer.querySelector('[data-id="' + textId + '"]');
        if (field) field.remove();
        var preview = document.getElementById('textPreview_' + textId);
        if (preview) preview.remove();
        updateCoverTexts();
    }

    // --- Bind existing text inputs (edit page) ---
    function bindExistingTextInputs(container, textsArray, previewPrefix, dataAttrName, updateFn) {
        if (!container) return;
        container.querySelectorAll('input[' + dataAttrName + ']').forEach(function(input) {
            input.addEventListener('input', function(e) {
                var tid = parseInt(this.getAttribute(dataAttrName));
                var obj = textsArray.find(function(t) { return t.id === tid; });
                if (obj) {
                    obj.text = e.target.value;
                    var p = document.getElementById(previewPrefix + tid);
                    if (p) p.textContent = e.target.value || ('Teks ' + (tid + 1));
                    updateFn();
                }
            });
        });
        container.querySelectorAll('.remove-text-btn, .remove-back-text-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var tid = parseInt(this.dataset.id);
                var isBack = this.classList.contains('remove-back-text-btn');
                if (isBack) {
                    removeBackText(tid);
                } else {
                    removeText(tid);
                }
            });
        });
    }

    bindExistingTextInputs(additionalTextsContainer, additionalTexts, 'textPreview_', 'data-text-id', updateCoverTexts);

    // --- Reset position ---
    if (resetPositionBtn) {
        resetPositionBtn.addEventListener('click', function() {
            coverX = 0; coverY = 0;
            titleX = 0; titleY = 0;
            updateCoverPosition(0, 0);
            updateCoverScale(1);
            updateTitlePosition(0, 0);
            additionalTexts.forEach(function(t) {
                t.position = {x: 0, y: 0};
                var p = document.getElementById('textPreview_' + t.id);
                if (p) p.style.transform = 'translate(0, 0)';
            });
            updateCoverTexts();
        });
    }

    // ==================== Thumbnail ====================
    var generateThumbnailBtn = document.getElementById('generateThumbnailBtn');
    var thumbnailPreviewContainer = document.getElementById('thumbnailPreviewContainer');
    var thumbnailPreview = document.getElementById('thumbnailPreview');
    var generatedThumbnailInput = document.getElementById('generatedThumbnail');
    var removeThumbnailBtn = document.getElementById('removeThumbnail');
    var thumbnailInput = document.getElementById('thumbnailInput');
    var existingThumbnail = document.getElementById('existingThumbnail');

    if (generateThumbnailBtn) {
        generateThumbnailBtn.addEventListener('click', async function() {
            var bookPreview = document.getElementById('bookPreview');
            if (!bookPreview) {
                alert('Preview buku tidak ditemukan');
                return;
            }
            if (!coverPreview || !coverPreview.src || coverPreview.style.display === 'none') {
                alert('Silakan upload cover buku terlebih dahulu');
                return;
            }
            try {
                generateThumbnailBtn.disabled = true;
                generateThumbnailBtn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg> Generating...';

                bookPreview.classList.add('is-capturing');

                var canvas = await html2canvas(bookPreview, {
                    backgroundColor: null,
                    scale: 2,
                    useCORS: true,
                    allowTaint: true,
                    logging: false,
                    onclone: function(clonedDoc) {
                        const preview = clonedDoc.getElementById('bookPreview');
                        if (preview) {
                            const guides = preview.querySelectorAll('#resizeBorder, #backResizeBorder, .resize-handle');
                            guides.forEach(g => g.style.setProperty('display', 'none', 'important'));
                            
                            const draggables = preview.querySelectorAll('.draggable-element');
                            draggables.forEach(d => {
                                d.style.setProperty('outline', 'none', 'important');
                                d.style.setProperty('border', 'none', 'important');
                                d.style.setProperty('box-shadow', 'none', 'important');
                            });
                        }
                    }
                });

                bookPreview.classList.remove('is-capturing');

                var dataUrl = canvas.toDataURL('image/png');
                if (thumbnailPreview) thumbnailPreview.src = dataUrl;
                if (generatedThumbnailInput) generatedThumbnailInput.value = dataUrl;
                if (thumbnailPreviewContainer) thumbnailPreviewContainer.classList.remove('hidden');
                if (existingThumbnail) existingThumbnail.classList.add('hidden');
                if (thumbnailInput) thumbnailInput.value = '';
            } catch (error) {
                console.error('Error generating thumbnail:', error);
                alert('Gagal membuat thumbnail: ' + error.message);
            } finally {
                generateThumbnailBtn.disabled = false;
                generateThumbnailBtn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg> Generate dari Preview';
            }
        });
    }

    if (removeThumbnailBtn) {
        removeThumbnailBtn.addEventListener('click', function() {
            if (generatedThumbnailInput) generatedThumbnailInput.value = '';
            if (thumbnailPreviewContainer) thumbnailPreviewContainer.classList.add('hidden');
            if (thumbnailPreview) thumbnailPreview.src = '';
            if (existingThumbnail) existingThumbnail.classList.remove('hidden');
        });
    }

    // ==================== Back Cover ====================
    var backCoverInput = document.getElementById('backCoverImageInput');
    var backCoverPreview = document.getElementById('backCoverPreview');
    var backCoverPlaceholder = document.getElementById('backCoverPlaceholder');
    var backCoverContainer = document.getElementById('backCoverContainer');
    var backCoverPositionInput = document.getElementById('backCoverPosition');
    var backCoverScaleInput = document.getElementById('backCoverScale');
    var backResizeBorder = document.getElementById('backResizeBorder');
    var previewBackTitle = document.getElementById('previewBackTitle');
    var backTitleContainer = document.getElementById('backTitleContainer');
    var backTitlePositionInput = document.getElementById('backTitlePosition');

    var backZoomInBtn = document.getElementById('backZoomInBtn');
    var backZoomOutBtn = document.getElementById('backZoomOutBtn');
    var backZoomSlider = document.getElementById('backZoomSlider');
    var backZoomLevel = document.getElementById('backZoomLevel');

    var backCoverX = cfg.backCoverX || 0;
    var backCoverY = cfg.backCoverY || 0;
    var isBackCoverDragging = false;
    var backCoverStartX, backCoverStartY;
    var backCurrentScale = cfg.backCoverScale || 1;

    var isBackTitleDragging = false;
    var backTitleStartX, backTitleStartY;
    var backTitleX = cfg.backTitleX || 0;
    var backTitleY = cfg.backTitleY || 0;

    var backAdditionalTextsContainer = document.getElementById('backAdditionalTextsContainer');
    var backAdditionalTextsPreview = document.getElementById('backAdditionalTextsPreview');
    var addBackTextBtn = document.getElementById('addBackTextBtn');
    var backCoverTextsInput = document.getElementById('backCoverTexts');

    var backTextCounter = (cfg.backAdditionalTexts || []).length;
    var backAdditionalTexts = (cfg.backAdditionalTexts || []).slice();

    // --- Back cover image upload ---
    if (backCoverInput) {
        backCoverInput.addEventListener('change', function(e) {
            var file = e.target.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    backCoverX = 0; backCoverY = 0;
                    backCurrentScale = 1;
                    backCoverPreview.src = e.target.result;
                    backCoverPreview.style.display = 'block';
                    if (backCoverPlaceholder) backCoverPlaceholder.style.display = 'none';
                    if (backResizeBorder) {
                        backResizeBorder.style.display = 'block';
                        backResizeBorder.style.opacity = '1';
                    }
                    updateBackCoverPosition(0, 0);
                    updateBackCoverScale(1);
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // --- Back title sync ---
    var backBookTitleInput = document.getElementById('backBookTitle');
    if (backBookTitleInput) {
        backBookTitleInput.addEventListener('input', function(e) {
            if (previewBackTitle) previewBackTitle.textContent = e.target.value || 'Judul Buku';
        });
    }

    // --- Back cover drag ---
    if (backCoverContainer) {
        backCoverContainer.addEventListener('mousedown', function(e) {
            if (!backCoverPreview.src || backCoverPreview.style.display === 'none') return;
            isBackCoverDragging = true;
            backCoverStartX = e.clientX - backCoverX;
            backCoverStartY = e.clientY - backCoverY;
            backCoverContainer.style.cursor = 'grabbing';
        });

        backCoverContainer.addEventListener('wheel', function(e) {
            e.preventDefault();
            var delta = e.deltaY > 0 ? -0.1 : 0.1;
            updateBackCoverScale(backCurrentScale + delta);
        }, { passive: false });
    }

    if (backTitleContainer) {
        backTitleContainer.addEventListener('mousedown', function(e) {
            e.preventDefault();
            isBackTitleDragging = true;
            backTitleStartX = e.clientX - backTitleX;
            backTitleStartY = e.clientY - backTitleY;
            backTitleContainer.style.cursor = 'grabbing';
        });
    }

    document.addEventListener('mousemove', function(e) {
        if (isBackCoverDragging) {
            e.preventDefault();
            backCoverX = e.clientX - backCoverStartX;
            backCoverY = e.clientY - backCoverStartY;
            updateBackCoverPosition(backCoverX, backCoverY);
        }
        if (isBackTitleDragging) {
            e.preventDefault();
            backTitleX = e.clientX - backTitleStartX;
            backTitleY = e.clientY - backTitleStartY;
            updateBackTitlePosition(backTitleX, backTitleY);
        }
    });

    document.addEventListener('mouseup', function() {
        if (isBackCoverDragging) {
            isBackCoverDragging = false;
            if (backCoverContainer) backCoverContainer.style.cursor = 'move';
        }
        if (isBackTitleDragging) {
            isBackTitleDragging = false;
            if (backTitleContainer) backTitleContainer.style.cursor = 'move';
        }
    });

    // --- Back cover zoom ---
    if (backZoomInBtn) {
        backZoomInBtn.addEventListener('click', function() { updateBackCoverScale(backCurrentScale + 0.1); });
    }
    if (backZoomOutBtn) {
        backZoomOutBtn.addEventListener('click', function() { updateBackCoverScale(backCurrentScale - 0.1); });
    }
    if (backZoomSlider) {
        backZoomSlider.addEventListener('input', function(e) { updateBackCoverScale(e.target.value / 100); });
    }

    // --- Back cover update helpers ---
    function updateBackCoverPosition(x, y) {
        if (backCoverPreview) {
            backCoverPreview.style.transform = 'translate(' + x + 'px, ' + y + 'px) scale(' + backCurrentScale + ')';
        }
        if (backCoverPositionInput) {
            backCoverPositionInput.value = JSON.stringify({x: x, y: y});
        }
    }

    function updateBackTitlePosition(x, y) {
        if (backTitleContainer) {
            backTitleContainer.style.transform = 'translate(' + x + 'px, ' + y + 'px)';
        }
        if (backTitlePositionInput) {
            backTitlePositionInput.value = JSON.stringify({x: x, y: y});
        }
    }

    function updateBackCoverScale(scale) {
        backCurrentScale = Math.max(0.3, Math.min(2.5, scale));
        if (backCoverPreview) {
            backCoverPreview.style.transform = 'translate(' + backCoverX + 'px, ' + backCoverY + 'px) scale(' + backCurrentScale + ')';
        }
        if (backCoverScaleInput) backCoverScaleInput.value = backCurrentScale;
        if (backZoomSlider) backZoomSlider.value = backCurrentScale * 100;
        if (backZoomLevel) backZoomLevel.textContent = Math.round(backCurrentScale * 100) + '%';
    }

    function updateBackCoverTexts() {
        if (backCoverTextsInput) {
            var texts = backAdditionalTexts.map(function(t) {
                return { text: t.text, position: t.position };
            });
            backCoverTextsInput.value = JSON.stringify(texts);
        }
    }

    function removeBackText(textId) {
        backAdditionalTexts = backAdditionalTexts.filter(function(t) { return t.id !== textId; });
        var field = backAdditionalTextsContainer ? backAdditionalTextsContainer.querySelector('[data-id="' + textId + '"]') : null;
        if (field) field.remove();
        var preview = document.getElementById('backTextPreview_' + textId);
        if (preview) preview.remove();
        updateBackCoverTexts();
    }

    // --- Add back text ---
    if (addBackTextBtn) {
        addBackTextBtn.addEventListener('click', function() {
            var textId = backTextCounter++;
            backAdditionalTexts.push({ id: textId, text: '', position: {x: 0, y: 0} });

            if (backAdditionalTextsContainer && backAdditionalTextsPreview) {
                var els = addTextFieldAndPreview(textId, '', backAdditionalTextsContainer, backAdditionalTextsPreview, 'back_cover_text_', 'data-back-text-id', 'backTextPreview_', 'remove-back-text-btn');

                var input = els.textField.querySelector('input');
                input.addEventListener('input', function(e) {
                    var tid = parseInt(this.getAttribute('data-back-text-id'));
                    var obj = backAdditionalTexts.find(function(t) { return t.id === tid; });
                    if (obj) {
                        obj.text = e.target.value;
                        var p = document.getElementById('backTextPreview_' + tid);
                        if (p) p.textContent = e.target.value || ('Teks ' + (tid + 1));
                        updateBackCoverTexts();
                    }
                });

                var removeBtn = els.textField.querySelector('.remove-back-text-btn');
                removeBtn.addEventListener('click', function() {
                    removeBackText(parseInt(this.dataset.id));
                });
            }

            updateBackCoverTexts();
        });
    }

    // --- Bind existing back text inputs (edit page) ---
    bindExistingBackTextInputs();

    function bindExistingBackTextInputs() {
        if (!backAdditionalTextsContainer) return;
        backAdditionalTextsContainer.querySelectorAll('input[data-back-text-id]').forEach(function(input) {
            input.addEventListener('input', function(e) {
                var tid = parseInt(this.getAttribute('data-back-text-id'));
                var obj = backAdditionalTexts.find(function(t) { return t.id === tid; });
                if (obj) {
                    obj.text = e.target.value;
                    var p = document.getElementById('backTextPreview_' + tid);
                    if (p) p.textContent = e.target.value || ('Teks ' + (tid + 1));
                    updateBackCoverTexts();
                }
            });
        });
        backAdditionalTextsContainer.querySelectorAll('.remove-back-text-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                removeBackText(parseInt(this.dataset.id));
            });
        });
    }

    // --- Reset back cover position ---
    var resetBackPositionBtn = document.getElementById('resetBackPosition');
    if (resetBackPositionBtn) {
        resetBackPositionBtn.addEventListener('click', function() {
            backCoverX = 0; backCoverY = 0;
            backTitleX = 0; backTitleY = 0;
            updateBackCoverPosition(0, 0);
            updateBackCoverScale(1);
            updateBackTitlePosition(0, 0);
        });
    }

    // --- Sync hidden fields on form submit ---
    var bookForm = document.getElementById('bookForm');
    if (bookForm) {
        bookForm.addEventListener('submit', function() {
            updateCoverTexts();
            updateBackCoverTexts();
        });
    }
});


// File edit
