/**
 * Profile Page Edit - JavaScript
 */
(function () {
    let _idCounter = 1;
    const _files = {};
    const _gambarStore = [];
    let _chartInstances = [];
    let editor1 = null;
    let _availableFields = {};
    let _chartConfig = {}; // { field: ['pie', 'bar'] or [] }

    function getNextId() {
        return "gbr-" + Date.now() + "-" + _idCounter++;
    }

    function compressImage(file) {
        return new Promise((resolve) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                const img = new Image();
                img.onload = () => {
                    const canvas = document.createElement("canvas");
                    let width = img.width,
                        height = img.height;
                    const MAX_SIZE = 1280;
                    if (width > height && width > MAX_SIZE) {
                        height *= MAX_SIZE / width;
                        width = MAX_SIZE;
                    } else if (height > MAX_SIZE) {
                        width *= MAX_SIZE / height;
                        height = MAX_SIZE;
                    }
                    canvas.width = width;
                    canvas.height = height;
                    const ctx = canvas.getContext("2d");
                    ctx.fillStyle = "#FFFFFF";
                    ctx.fillRect(0, 0, width, height);
                    ctx.drawImage(img, 0, 0, width, height);

                    // Get preview from canvas ASAP (before blob conversion)
                    const previewDataUrl = canvas.toDataURL("image/jpeg", 0.8);

                    const quality = file.type === "image/png" ? undefined : 0.9;
                    const ext = file.type === "image/png" ? "png" : "jpg";
                    const newName =
                        file.name.replace(/\.[^/.]+$/, "") + "." + ext;
                    canvas.toBlob(
                        (blob) => {
                            resolve({
                                file: new File([blob], newName, {
                                    type: file.type,
                                    lastModified: Date.now(),
                                }),
                                preview: previewDataUrl,
                            });
                        },
                        file.type,
                        quality,
                    );
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        });
    }

    function renderGambarPreviews() {
        const container = document.getElementById("gambar-previews");
        if (!container) return;
        container.innerHTML = "";

        if (!_gambarStore.length) return;

        // Use fixed 2 columns for smaller thumbnails
        const grid = document.createElement("div");
        grid.className = "grid gap-2 mb-3";
        grid.style.gridTemplateColumns = "repeat(2, 1fr)";

        _gambarStore.forEach((img) => {
            const wrapper = document.createElement("div");
            wrapper.className = "relative group";
            wrapper.dataset.imgId = img.id;

            const dragBox = document.createElement("div");
            dragBox.className =
                "relative overflow-hidden rounded-lg bg-gray-200 cursor-move border-2 border-gray-300 hover:border-blue-400 transition-colors";
            dragBox.style.aspectRatio = "1/1";
            dragBox.style.minHeight = "120px";
            dragBox.data = { isResizing: false, isMoving: false };

            // Add loading indicator
            const loader = document.createElement("div");
            loader.className =
                "absolute inset-0 flex items-center justify-center bg-gray-100";
            loader.innerHTML =
                '<div class="w-4 h-4 border-2 border-blue-400 border-t-transparent rounded-full animate-spin"></div>';
            dragBox.appendChild(loader);

            const imgEl = document.createElement("img");
            imgEl.className =
                "absolute w-full h-full object-cover transition-transform duration-300";
            imgEl.style.objectPosition = img.x + "% " + img.y + "%";
            imgEl.style.display = "none";
            imgEl.style.cursor = "grab";

            let imageLoaded = false;

            imgEl.onload = function () {
                console.log("[IMG] ✓ Loaded:", img.path || img.preview);
                imageLoaded = true;
                imgEl.style.display = "block";
                loader.style.display = "none";
            };

            imgEl.onerror = function () {
                console.error(
                    "[IMG] ✗ Error loading:",
                    img.path || img.preview,
                );
                // Show specific error message
                if (img.fileExists === false) {
                    loader.innerHTML =
                        '<div class="text-red-500 text-xs text-center px-2">File tidak ditemukan di storage<br><span class="text-gray-400 text-[10px]">' + (img.path || '') + '</span></div>';
                } else {
                    loader.innerHTML =
                        '<div class="text-gray-400 text-xs text-center">Gagal memuat gambar</div>';
                }
            };

            imgEl.src = img.preview;
            dragBox.appendChild(imgEl);

            wrapper.appendChild(dragBox);

            // Add positioning controls button
            const controlsBtn = document.createElement("button");
            controlsBtn.type = "button";
            controlsBtn.className =
                "absolute bg-blue-500 text-white rounded text-xs px-1.5 py-0.5 font-medium cursor-pointer z-40 opacity-0 group-hover:opacity-100 transition-opacity";
            controlsBtn.style.cssText = "bottom:4px;left:4px;line-height:1;";
            controlsBtn.innerHTML = "Posisi";

            controlsBtn.addEventListener("click", (e) => {
                e.preventDefault();
                e.stopPropagation();
                showPositionMenu(img.id, controlsBtn, dragBox);
            });
            wrapper.appendChild(controlsBtn);

            const delBtn = document.createElement("button");
            delBtn.type = "button";
            delBtn.className =
                "absolute bg-red-500 text-white rounded-full flex items-center justify-center shadow-md hover:bg-red-600 transition-colors cursor-pointer z-50 opacity-0 group-hover:opacity-100";
            delBtn.style.cssText =
                "width:20px;height:20px;top:-6px;right:-6px;line-height:1";
            delBtn.innerHTML =
                '<svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>';
            delBtn.addEventListener("click", (e) => {
                e.stopPropagation();
                const idx = _gambarStore.findIndex((i) => i.id === img.id);
                if (idx !== -1) {
                    if (!img.isExisting) {
                        _gambarStore.splice(idx, 1);
                        delete _files[img.id];
                    } else {
                        _gambarStore.splice(idx, 1);
                        const removeInput = document.getElementById(
                            "removed_images_input",
                        );
                        if (removeInput) {
                            const current = removeInput.value
                                ? JSON.parse(removeInput.value)
                                : [];
                            current.push(img.path);
                            removeInput.value = JSON.stringify(current);
                        }
                    }
                }
                renderGambarPreviews();
                renderPagePreview();
            });
            wrapper.appendChild(delBtn);
            grid.appendChild(wrapper);
        });

        container.appendChild(grid);
    }

    // Chart rendering functions
    const chartColors = [
        "#3B82F6",
        "#06B6D4",
        "#10B981",
        "#F59E0B",
        "#EF4444",
        "#8B5CF6",
        "#EC4899",
        "#14B8A6",
        "#F97316",
        "#6366F1",
    ];

    function renderChartPreview(data) {
        const container = document.getElementById("chart_preview");
        if (!container) return;

        _chartInstances.forEach(function (c) {
            c.destroy();
        });
        _chartInstances = [];

        if (!data || Object.keys(data).length === 0) {
            container.innerHTML =
                '<p class="text-xs text-gray-400 text-center py-8">Tidak ada data untuk ditampilkan. Pilih field data dan tipe grafik, lalu klik "Generate Grafik"</p>';
            return;
        }

        let html = '<div class="chart-preview-container">';

        Object.keys(data).forEach(function (key) {
            const chart = data[key];
            // Skip if labels or data is missing/invalid
            if (
                !chart.labels ||
                !chart.data ||
                !Array.isArray(chart.labels) ||
                !Array.isArray(chart.data)
            ) {
                console.warn("Invalid chart data for key:", key, chart);
                return;
            }
            const chartId = "chart-" + key;
            const isPie = chart.type === "pie";
            const chartTypeLabel = chart.type
                ? isPie
                    ? " (Pie)"
                    : " (Bar)"
                : "";

            html +=
                '<div class="chart-card">' +
                '<p class="chart-card-title">' +
                (chart.title || key) +
                chartTypeLabel +
                "</p>" +
                '<div style="height:' +
                (isPie ? "250px" : "200px") +
                ';position:relative">' +
                '<canvas id="' +
                chartId +
                '"></canvas>' +
                "</div></div>";
        });

        html += "</div>";
        container.innerHTML = html;

        // Render each chart
        Object.keys(data).forEach(function (key) {
            const chart = data[key];
            // Skip if labels or data is missing/invalid
            if (
                !chart.labels ||
                !chart.data ||
                !Array.isArray(chart.labels) ||
                !Array.isArray(chart.data)
            ) {
                return;
            }
            const chartId = "chart-" + key;
            const canvasEl = document.getElementById(chartId);
            if (!canvasEl) return;

            if (chart.type === "pie") {
                _chartInstances.push(
                    new Chart(canvasEl.getContext("2d"), {
                        type: "pie",
                        data: {
                            labels: chart.labels,
                            datasets: [
                                {
                                    data: chart.data,
                                    backgroundColor:
                                        chart.colors ||
                                        chartColors.slice(
                                            0,
                                            chart.labels.length,
                                        ),
                                    borderWidth: 2,
                                    borderColor: "#fff",
                                },
                            ],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: "bottom",
                                    labels: { font: { size: 11 }, padding: 8 },
                                },
                            },
                        },
                    }),
                );
            } else {
                _chartInstances.push(
                    new Chart(canvasEl.getContext("2d"), {
                        type: "bar",
                        data: {
                            labels: chart.labels,
                            datasets: [
                                {
                                    label: "Jumlah",
                                    data: chart.data,
                                    backgroundColor:
                                        chart.colors ||
                                        chartColors.slice(
                                            0,
                                            chart.labels.length,
                                        ),
                                    borderRadius: 4,
                                    borderSkipped: false,
                                },
                            ],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: { stepSize: 1, font: { size: 10 } },
                                },
                                x: { ticks: { font: { size: 9 } } },
                            },
                        },
                    }),
                );
            }
        });
    }

    // Add a new field to chart config
    function addFieldToConfig(field, types) {
        if (!field || _chartConfig[field]) return;
        _chartConfig[field] = types;
        renderChartConfigList();
    }

    // Remove a field from chart config
    function removeFieldFromConfig(field) {
        delete _chartConfig[field];
        renderChartConfigList();
    }

    // Update chart types for a field
    function updateFieldChartTypes(field, types) {
        if (!_chartConfig[field]) return;
        _chartConfig[field] = types;
    }

    // Toggle chart type for a field
    function toggleChartTypeForField(field, type) {
        if (!_chartConfig[field]) return;
        const idx = _chartConfig[field].indexOf(type);
        if (idx > -1) {
            _chartConfig[field].splice(idx, 1);
        } else {
            _chartConfig[field].push(type);
        }
        if (_chartConfig[field].length === 0) {
            _chartConfig[field].push(type);
        }
        // Re-render to update UI
        renderChartConfigList();
    }

    // Check if a chart type is selected for a field
    function isChartTypeSelectedForField(field, type) {
        return _chartConfig[field] && _chartConfig[field].includes(type);
    }

    // Render the chart config list
    function renderChartConfigList() {
        const container = document.getElementById("chart-config-list");
        if (!container) return;

        const fields = Object.keys(_chartConfig);
        if (fields.length === 0) {
            container.innerHTML =
                '<p class="text-xs text-gray-400 py-2">Pilih field data di atas untuk menambahkan grafik</p>';
            return;
        }

        let html = "";
        fields.forEach(function (field) {
            const label = _availableFields[field] || field;

            html +=
                '<div class="chart-config-item" data-field="' +
                field +
                '">' +
                '<div class="chart-config-header">' +
                '<span class="chart-config-label">' +
                label +
                "</span>" +
                '<button type="button" onclick="removeFieldFromConfig(\'' +
                field +
                '\')" class="chart-config-remove">' +
                '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>' +
                "</svg>" +
                "</button>" +
                "</div>" +
                '<div class="chart-config-types">' +
                '<button type="button" onclick="toggleChartTypeForField(\'' +
                field +
                "', 'pie')\" class=\"chart-type-btn " +
                (isChartTypeSelectedForField(field, "pie") ? "active" : "") +
                '">' +
                "Pie Chart" +
                "</button>" +
                '<button type="button" onclick="toggleChartTypeForField(\'' +
                field +
                "', 'bar')\" class=\"chart-type-btn " +
                (isChartTypeSelectedForField(field, "bar") ? "active" : "") +
                '">' +
                "Bar Chart" +
                "</button>" +
                "</div>" +
                "</div>";
        });

        container.innerHTML = html;
    }

    /**
     * Save image positions to hidden inputs before form submit
     * NOTE: This only disables the static input. Array inputs (image_positions[],
     * image_widths[], etc.) are built fresh in the form submit handler.
     */
    function saveImagePositionsBeforeSubmit() {
        const form = document.getElementById("pageForm");
        if (!form) return;

        // Disable old stringified JSON input — array inputs are built in submit handler
        const oldInput = document.getElementById("image_positions_input");
        if (oldInput) {
            oldInput.disabled = true;
        }
    }

    /**
     * Show position preset menu for image
     */
    function showPositionMenu(imgId, button, dragBox) {
        // Create simple preset positions
        const presets = [
            { label: "Kiri", x: 20, y: 50 },
            { label: "Tengah", x: 50, y: 50 },
            { label: "Kanan", x: 80, y: 50 },
            { label: "Atas", x: 50, y: 30 },
            { label: "Bawah", x: 50, y: 70 },
        ];

        // Remove existing menu if any
        const existingMenu = document.getElementById("position-menu-" + imgId);
        if (existingMenu) existingMenu.remove();

        // Create menu
        const menu = document.createElement("div");
        menu.id = "position-menu-" + imgId;
        menu.className =
            "absolute z-50 bg-white border border-gray-200 rounded-lg shadow-lg p-2";
        menu.style.cssText =
            "bottom: 100%; left: 0; white-space: nowrap; min-width: 120px; margin-bottom: 4px;";

        presets.forEach((preset) => {
            const btn = document.createElement("button");
            btn.type = "button";
            btn.className =
                "block w-full text-left px-3 py-2 text-xs hover:bg-blue-100 rounded transition-colors";
            btn.textContent =
                preset.label + " (" + preset.x + "%, " + preset.y + "%)";

            btn.addEventListener("click", (e) => {
                e.preventDefault();
                e.stopPropagation();

                const img = _gambarStore.find((i) => i.id === imgId);
                if (img) {
                    img.x = preset.x;
                    img.y = preset.y;
                    renderGambarPreviews();
                    renderPagePreview();
                    menu.remove();
                }
            });

            menu.appendChild(btn);
        });

        // Position menu relative to button
        button.parentElement.insertBefore(menu, button.nextSibling);

        // Close menu when clicking outside
        const closeMenu = (e) => {
            if (!menu.contains(e.target) && e.target !== button) {
                menu.remove();
                document.removeEventListener("click", closeMenu);
            }
        };

        setTimeout(() => document.addEventListener("click", closeMenu), 0);
    }

    /**
     * Render live preview halaman - 100% Match guest page layout
     * HANYA tampilkan: Description dari RTE + Gambar yang di-upload
     */
    function renderPagePreview() {
        console.log(
            "[PREVIEW RENDER] Called. Current _gambarStore:",
            JSON.stringify(_gambarStore, null, 2),
        );

        const container = document.getElementById("preview-container");
        if (!container) return;

        // Get HTML content from RTE editor
        let descriptionHTML = "";
        if (editor1 && typeof editor1.getHTMLCode === "function") {
            try {
                descriptionHTML = editor1.getHTMLCode() || "";
            } catch (e) {
                console.log("Could not get editor HTML");
            }
        }

        // Get Title/Link content
        const titleVal = document.querySelector('[name="title"]')?.value || "";
        const linkTextVal =
            document.querySelector('[name="link_text"]')?.value || "";
        const linkUrlVal =
            document.querySelector('[name="link_url"]')?.value || "";

        // Build preview HTML - EXACT match guest page structure with container wrapper
        let previewHTML =
            "<div style=\"width: 100%; font-family: 'Montserrat', Arial, Helvetica, sans-serif; color: #475569; line-height: 1.75; font-size: 1rem; padding: 2rem 0;\">";

        const hasDesc = descriptionHTML && descriptionHTML.trim() !== "";
        const hasImages = _gambarStore.length > 0;
        const hasTitle = titleVal && titleVal.trim() !== "";
        const hasLink = linkTextVal && linkUrlVal;

        // Build left column content (Same logic for both grid and single column)
        let leftCol =
            '<div style="width: 100%; word-break: break-word; overflow-wrap: break-word; min-width: 0;">';
        if (hasDesc) {
            leftCol +=
                '<div class="profile-section-desc" style="margin-bottom: 1.5rem;">' +
                descriptionHTML +
                "</div>";
        }
        if (hasTitle) {
            leftCol +=
                '<h2 class="profile-section-title">' + titleVal + "</h2>";
        }
        if (hasLink) {
            leftCol +=
                '<a href="' +
                linkUrlVal +
                '" class="page-link-btn" target="_blank">' +
                linkTextVal +
                ' <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg></a>';
        }
        leftCol += "</div>";

        if (hasImages) {
            previewHTML +=
                '<div style="width: 100%; overflow: visible; display: block;">';

            _gambarStore.forEach((img) => {
                const w = img.width || 200;
                const h = img.height || 150;
                let oX = typeof img.offsetX === "number" ? img.offsetX : null;
                let oY = typeof img.offsetY === "number" ? img.offsetY : null;
                let drTop = 0,
                    drRight = 0;
                if (oX !== null) drRight = -oX;
                if (oY !== null) drTop = oY;
                let marginStr = "";
                if (oX === null && oY === null) {
                    // First upload: float right, no offset, normal margin
                    marginStr = "margin: 0 0 32px 32px; float: right;";
                } else {
                    marginStr =
                        "margin: " +
                        drTop +
                        "px " +
                        drRight +
                        "px 32px 32px; float: right;";
                }
                const styleStr = `position: relative; border-radius: 0.75rem; overflow: visible !important; width: ${w}px; height: ${h}px; ${marginStr} z-index: 10;`;
                previewHTML +=
                    '<div class="preview-img-item" data-img-id="' +
                    img.id +
                    '" style="' +
                    styleStr +
                    '">';
                previewHTML +=
                    '<div style="position: absolute; top: 2px; left: 2px; background: rgba(0,0,0,0.65); color: white; padding: 2px 4px; font-size: 10px; border-radius: 3px; z-index: 20; cursor: grab;">☰</div>';
                previewHTML +=
                    '<img src="' +
                    img.preview +
                    '" style="width: 100%; height: 100%; object-fit: cover; object-position: ' +
                    (img.x || 50) +
                    "% " +
                    (img.y || 50) +
                    '%; display: block; border-radius: 0.75rem;">';
                previewHTML += "</div>";
            });

            // The text comes AFTER floating elements in HTML!
            previewHTML += leftCol;
            previewHTML += '<div style="clear: both;"></div>';
            previewHTML += "</div>";
        } else if (hasDesc || hasTitle || hasLink) {
            previewHTML += leftCol;
        } else {
            previewHTML +=
                '<div style="color: #999; text-align: center; padding: 2rem; font-style: italic; border: 2px dashed #e5e7eb; border-radius: 8px;">';
            previewHTML +=
                '<p style="margin: 0; font-size: 13px;">Tambahkan konten dan/atau gambar untuk melihat preview</p>';
            previewHTML += "</div>";
        }

        previewHTML += "</div>";

        container.innerHTML = previewHTML;

        // Attach handlers first, then adjust grid for saved offsets
        attachPreviewDragHandlers();
        attachPreviewResizeHandlers();

        // Adjust grid columns and apply transforms
        adjustPreviewGrid();
        applyImageTransforms();
    }

    /**
     * Attach resize handlers to preview images - Figma/Canva style corner handles
     */
    function attachPreviewResizeHandlers() {
        const items = document.querySelectorAll(".preview-img-item");
        items.forEach((item) => {
            const imgId = item.dataset.imgId;
            const img = _gambarStore.find((i) => i.id === imgId);
            if (!img) return;

            // Create 4 corner handles that are ALWAYS visible
            const handles = [
                { pos: "tl", cursor: "nwse-resize", top: "-5px", left: "-5px" },
                {
                    pos: "tr",
                    cursor: "nesw-resize",
                    top: "-5px",
                    right: "-5px",
                },
                {
                    pos: "bl",
                    cursor: "nesw-resize",
                    bottom: "-5px",
                    left: "-5px",
                },
                {
                    pos: "br",
                    cursor: "nwse-resize",
                    bottom: "-5px",
                    right: "-5px",
                },
            ];

            handles.forEach((handle) => {
                const el = document.createElement("div");
                el.dataset.handle = handle.pos;
                el.style.cssText = `
                    position: absolute;
                    width: 10px;
                    height: 10px;
                    background: #3B82F6;
                    border: 2px solid white;
                    border-radius: 1px;
                    cursor: ${handle.cursor};
                    z-index: 40;
                    box-shadow: 0 0 4px rgba(59,130,246,0.8);
                    pointer-events: auto;
                    ${handle.top ? "top: " + handle.top + ";" : ""}
                    ${handle.bottom ? "bottom: " + handle.bottom + ";" : ""}
                    ${handle.left ? "left: " + handle.left + ";" : ""}
                    ${handle.right ? "right: " + handle.right + ";" : ""}
                `;

                el.addEventListener("mousedown", (e) => {
                    e.preventDefault();
                    e.stopPropagation();

                    const startX = e.clientX;
                    const startY = e.clientY;
                    const startWidth = item.offsetWidth;
                    const startHeight = item.offsetHeight;
                    const startOffsetX = Number(img.offsetX) || 0;
                    const startOffsetY = Number(img.offsetY) || 0;

                    const handleMouseMove = (moveEvent) => {
                        const deltaX = moveEvent.clientX - startX;
                        const deltaY = moveEvent.clientY - startY;

                        let newWidth = startWidth;
                        let newHeight = startHeight;
                        let shiftX = 0;
                        let shiftY = 0;

                        if (handle.pos.includes("r")) {
                            newWidth = Math.max(80, startWidth + deltaX);
                        } else if (handle.pos.includes("l")) {
                            newWidth = Math.max(80, startWidth - deltaX);
                            // Shift image left so right edge stays anchored
                            shiftX = -(newWidth - startWidth);
                        }

                        if (handle.pos.includes("b")) {
                            newHeight = Math.max(60, startHeight + deltaY);
                        } else if (handle.pos.includes("t")) {
                            newHeight = Math.max(60, startHeight - deltaY);
                            // Shift image up so bottom edge stays anchored
                            shiftY = -(newHeight - startHeight);
                        }

                        const newOffsetX = startOffsetX + shiftX;
                        const newOffsetY = startOffsetY + shiftY;

                        // Apply size + position via margin (same convention as
                        // drag handler and applyImageTransforms) so the element
                        // stays in the exact same visual position it will have
                        // after mouseup. Using transform here caused a jump
                        // because margin offsets were still active underneath.
                        item.style.width = newWidth + "px";
                        item.style.height = newHeight + "px";
                        item.style.transform = "none";
                        item.style.margin =
                            newOffsetY + "px " + (-newOffsetX) + "px 32px 32px";

                        img.width = Math.round(newWidth);
                        img.height = Math.round(newHeight);
                        img.offsetX = Math.round(newOffsetX);
                        img.offsetY = Math.round(newOffsetY);

                        adjustPreviewGrid();
                    };

                    const handleMouseUp = () => {
                        document.removeEventListener(
                            "mousemove",
                            handleMouseMove,
                        );
                        document.removeEventListener("mouseup", handleMouseUp);
                        saveImagePositionsBeforeSubmit();
                        adjustPreviewGrid();
                        applyImageTransforms();
                    };

                    document.addEventListener("mousemove", handleMouseMove);
                    document.addEventListener("mouseup", handleMouseUp);
                });

                item.appendChild(el);
            });

            // Set initial size
            if (img.width && img.height) {
                item.style.width = img.width + "px";
                item.style.height = img.height + "px";
            }
        });
    }

    /**
     * Unified function: adjust grid columns so that the image column is wide enough
     * to contain all images (considering their width + leftward drag offset).
     * Horizontal drag = change grid columns (causes text reflow).
     * Vertical drag = transform translateY only (no text reflow needed).
     */
    function adjustPreviewGrid() {
        var gridContainer = document.querySelector(
            '#preview-container [style*="grid-template-columns"]',
        );
        if (!gridContainer) return;

        var gridWidth = gridContainer.getBoundingClientRect().width;
        var gap = 32;
        var defaultHalf = (gridWidth - gap) / 2;
        var MIN_TEXT_WIDTH = 180;

        var extraNeeded = 0;
        _gambarStore.forEach(function (img) {
            var w = Number(img.width) || 200;
            var offsetX = Number(img.offsetX) || 0;

            if (offsetX < 0) {
                var leftExtra = Math.abs(offsetX);
                if (leftExtra > extraNeeded) extraNeeded = leftExtra;
            }

            if (w > defaultHalf) {
                var widthExtra = w - defaultHalf;
                if (widthExtra > extraNeeded) extraNeeded = widthExtra;
            }
        });

        if (extraNeeded > 0) {
            var imgColWidth = defaultHalf + extraNeeded + 16;
            var maxImgCol = gridWidth - gap - MIN_TEXT_WIDTH;
            if (imgColWidth > maxImgCol) imgColWidth = maxImgCol;
            var textColWidth = gridWidth - gap - imgColWidth;

            if (textColWidth < MIN_TEXT_WIDTH) {
                textColWidth = MIN_TEXT_WIDTH;
                imgColWidth = gridWidth - gap - textColWidth;
            }

            gridContainer.style.gridTemplateColumns =
                textColWidth + "px " + imgColWidth + "px";
        } else {
            gridContainer.style.gridTemplateColumns = "1fr 1fr";
        }
    }

    /**
     * Apply transforms to all preview images from store data
     */
    function applyImageTransforms() {
        document.querySelectorAll(".preview-img-item").forEach(function (item) {
            var imgId = item.dataset.imgId;
            var imgData = _gambarStore.find(function (i) {
                return i.id === imgId;
            });
            if (imgData) {
                var oX = Number(imgData.offsetX) || 0;
                var oY = Number(imgData.offsetY) || 0;
                var drTop = oY;
                var drRight = -oX;
                item.style.margin = drTop + "px " + drRight + "px 32px 32px";
                item.style.transform = "none";
            }
        });
    }

    /**
     * Attach drag & drop handlers to preview images for repositioning
     */
    function attachPreviewDragHandlers() {
        const items = document.querySelectorAll(".preview-img-item");
        let draggedItem = null;
        let draggedIndex = null;
        let isDragging = false;
        let startMouseX = 0;
        let startMouseY = 0;
        let startItemX = 0;
        let startItemY = 0;

        items.forEach((item, idx) => {
            // Find the drag handle (☰ icon)
            const dragHandle = item.querySelector(
                'div[style*="position: absolute"][style*="top: 2px"]',
            );

            if (dragHandle) {
                dragHandle.addEventListener("mousedown", (e) => {
                    if (e.button !== 0) return; // Only left mouse button
                    e.preventDefault();
                    e.stopPropagation();

                    isDragging = true;
                    draggedItem = item;

                    // Find image by ID, not index
                    const imgId = item.dataset.imgId;
                    const foundImg = _gambarStore.find(
                        (img) => img.id === imgId,
                    );
                    draggedIndex = _gambarStore.indexOf(foundImg);

                    console.log("[DRAG START]", {
                        imgId,
                        draggedIndex,
                        foundImg,
                        gambarStoreLength: _gambarStore.length,
                    });

                    // Get current transform values
                    const img = _gambarStore[draggedIndex];
                    startItemX = img ? img.offsetX || 0 : 0;
                    startItemY = img ? img.offsetY || 0 : 0;

                    startMouseX = e.clientX;
                    startMouseY = e.clientY;

                    item.style.cursor = "grabbing";
                    item.style.opacity = "0.7";
                    item.style.zIndex = "1000";

                    const handleMouseMove = (moveEvent) => {
                        if (!isDragging || !draggedItem) return;

                        const deltaX = moveEvent.clientX - startMouseX;
                        const deltaY = moveEvent.clientY - startMouseY;

                        let newX = startItemX + deltaX;
                        const newY = startItemY + deltaY;

                        const imgLive = _gambarStore[draggedIndex];
                        if (imgLive) {
                            newX = Math.round(newX);
                            imgLive.offsetX = newX;
                            imgLive.offsetY = Math.round(newY);
                        }

                        let drTop = newY;
                        let drRight = -newX;
                        draggedItem.style.margin =
                            drTop + "px " + drRight + "px 32px 32px";
                        draggedItem.style.transform = "none";
                        adjustPreviewGrid();
                    };

                    const handleMouseUp = (upEvent) => {
                        if (!isDragging || !draggedItem) return;

                        isDragging = false;

                        const deltaX = upEvent.clientX - startMouseX;
                        const deltaY = upEvent.clientY - startMouseY;

                        let finalOffsetX = startItemX + deltaX;
                        const finalOffsetY = startItemY + deltaY;

                        const img = _gambarStore[draggedIndex];
                        if (img) {
                            img.offsetX = Math.round(finalOffsetX);
                            img.offsetY = Math.round(finalOffsetY);
                            console.log("[DRAG SAVED]", {
                                draggedIndex,
                                imgId: img.id,
                                offsetX: img.offsetX,
                                offsetY: img.offsetY,
                            });
                        } else {
                            console.error(
                                "[DRAG ERROR] Image not found at index",
                                draggedIndex,
                            );
                        }

                        draggedItem.style.cursor = "grab";
                        draggedItem.style.opacity = "1";
                        draggedItem.style.zIndex = "auto";

                        document.removeEventListener(
                            "mousemove",
                            handleMouseMove,
                        );
                        document.removeEventListener("mouseup", handleMouseUp);

                        draggedItem = null;
                        draggedIndex = null;

                        saveImagePositionsBeforeSubmit();
                        adjustPreviewGrid();
                        applyImageTransforms();
                    };

                    document.addEventListener("mousemove", handleMouseMove);
                    document.addEventListener("mouseup", handleMouseUp);
                });
            }
        });
    }

    function escapeHtml(text) {
        const map = {
            "&": "&amp;",
            "<": "&lt;",
            ">": "&gt;",
            '"': "&quot;",
            "'": "&#039;",
        };
        return (text || "").replace(/[&<>"']/g, (m) => map[m]);
    }

    /**
     * Setup preview update triggers
     */
    function setupPreviewTriggers() {
        // Update on title change
        const titleInput = document.querySelector('[name="title"]');
        if (titleInput) {
            titleInput.addEventListener("input", renderPagePreview);
            titleInput.addEventListener("change", renderPagePreview);
        }

        // Update on link changes
        const linkTextInput = document.querySelector('[name="link_text"]');
        if (linkTextInput) {
            linkTextInput.addEventListener("input", renderPagePreview);
        }
        const linkUrlInput = document.querySelector('[name="link_url"]');
        if (linkUrlInput) {
            linkUrlInput.addEventListener("input", renderPagePreview);
        }

        // Initial render
        setTimeout(renderPagePreview, 500);
    }

    /**
     * Update image position in preview
     */
    function updateImagePosition(imgId, x, y) {
        const img = _gambarStore.find((i) => i.id === imgId);
        if (img) {
            img.x = x;
            img.y = y;
            renderPagePreview();
        }
    }

    // Profile Page Form Alpine Component
    function profilePageForm() {
        return {
            pageType: window.pageType || "tugas_fungsi",
            title: window.pageTitle || "",
            linkText: window.pageLinkText || "",
            linkUrl: window.pageLinkUrl || "",
            subtitle: window.pageSubtitle || "",
            isGeneratingChart: false,
            selectedField: "",
            availableFields: {},
            sectionModal: {
                open: false,
                mode: "add",
                id: null,
                title: "",
                description: "",
                order: 0,
            },
            editSectionModal: {
                open: false,
                id: null,
                title: "",
                description: "",
                order: 0,
            },
            deleteSectionModal: { open: false, id: null, name: "" },

            async init() {
                // Initialize existing images from window.existingImages
                if (window.existingImages && window.existingImages.length) {
                    console.group("[IMG INIT] Loading existing images");
                    console.log("Total images:", window.existingImages.length);
                    window.existingImages.forEach(function (img, idx) {
                        console.log(
                            `[IMG ${idx}] Path: ${img.path}, URL: ${img.url}, Exists: ${img.fileExists}`,
                        );
                        const offsetXValue = Number(img.offsetX) || 0;
                        const offsetYValue = Number(img.offsetY) || 0;
                        _gambarStore.push({
                            id: "existing-" + idx,
                            preview: img.url,
                            path: img.path,
                            x: img.x || 50,
                            y: img.y || 50,
                            width: img.width || 200,
                            height: img.height || 150,
                            offsetX: offsetXValue,
                            offsetY: offsetYValue,
                            isExisting: true,
                            fileExists: img.fileExists !== false,
                        });
                    });
                    console.groupEnd();
                    // Render after all images pushed
                    setTimeout(function () {
                        console.log(
                            "[IMG RENDER] Rendering gambar previews...",
                        );
                        renderGambarPreviews();
                        renderPagePreview();
                    }, 100);
                }

                // Fetch available data fields
                try {
                    const response = await fetch(window.dataFieldsUrl);
                    const data = await response.json();
                    this.availableFields = data;
                    _availableFields = data;
                    window.dispatchEvent(
                        new CustomEvent("fields-loaded", { detail: data }),
                    );
                } catch (e) {
                    console.error("Failed to load data fields:", e);
                }

                // Initialize chart types from existing data
                const chartDataInput =
                    document.getElementById("chart_data_input");
                if (chartDataInput && chartDataInput.value) {
                    try {
                        const chartData = JSON.parse(chartDataInput.value);
                        // Only restore config for edit UI, don't render charts automatically
                        // Charts should only render when user clicks "Generate Grafik"
                        Object.keys(chartData).forEach(function (key) {
                            const chart = chartData[key];
                            if (chart.field) {
                                const type =
                                    chart.type === "pie" ? "pie" : "bar";
                                if (!_chartConfig[chart.field]) {
                                    _chartConfig[chart.field] = [];
                                }
                                if (!_chartConfig[chart.field].includes(type)) {
                                    _chartConfig[chart.field].push(type);
                                }
                            }
                        });
                        renderChartConfigList();
                        // Don't call renderChartPreview here - will be called when Generate Grafik is clicked
                    } catch (e) {
                        console.error("Error parsing chart data:", e);
                    }
                }

                // Setup preview triggers
                setupPreviewTriggers();
        setupZoomControls();

            },

            onTypeChange() {},

            async handleGambarChange(event) {
                const files = Array.from(event.target.files);
                const MAX_FILE_SIZE = 10 * 1024 * 1024; // 10MB
                const oversizedFiles = [];

                // Validate file sizes first before compression
                for (let file of files) {
                    if (file.size > MAX_FILE_SIZE) {
                        oversizedFiles.push(
                            file.name +
                                " (" +
                                (file.size / 1024 / 1024).toFixed(2) +
                                "MB)",
                        );
                    }
                }

                if (oversizedFiles.length > 0) {
                    alert(
                        "File berikut terlalu besar (maksimal 10MB per file):\n\n" +
                            oversizedFiles.join("\n"),
                    );
                    event.target.value = "";
                    return;
                }

                const compressionPromises = files.map(async function (file) {
                    const compressed = await compressImage(file);
                    const id = getNextId();
                    _files[id] = compressed.file;
                    _gambarStore.push({
                        id,
                        preview: compressed.preview,
                        x: 50,
                        y: 50,
                        width: 200,
                        height: 150,
                        offsetX: 0,
                        offsetY: 0,
                        isExisting: false,
                    });
                });
                // Wait for all images to be compressed before rendering
                await Promise.all(compressionPromises);
                renderGambarPreviews();
                renderPagePreview();
                event.target.value = "";
            },

            addField() {
                const field = this.selectedField;
                if (!field || _chartConfig[field]) return;
                addFieldToConfig(field, ["pie", "bar"]);
                this.selectedField = "";
            },

            async generateChart() {
                const config = {};
                Object.keys(_chartConfig).forEach(function (field) {
                    if (_chartConfig[field].length > 0) {
                        config[field] = _chartConfig[field];
                    }
                });

                if (Object.keys(config).length === 0) {
                    alert("Pilih minimal satu field data dan tipe grafik");
                    return;
                }

                this.isGeneratingChart = true;
                try {
                    const chartUrl =
                        window.chartGenerateUrl ||
                        "/cms/features/" +
                            window.featureId +
                            "/generate-profile-chart";
                    const url =
                        chartUrl +
                        "?config=" +
                        encodeURIComponent(JSON.stringify(config));
                    const response = await fetch(url);
                    const data = await response.json();
                    document.getElementById("chart_data_input").value =
                        JSON.stringify(data);
                    renderChartPreview(data);
                } catch (e) {
                    alert("Gagal generate grafik: " + e.message);
                } finally {
                    this.isGeneratingChart = false;
                }
            },

            // Section management
            openAddSection() {
                const sectionsCount = window.sectionsCount || 0;
                this.sectionModal = {
                    open: true,
                    mode: "add",
                    id: null,
                    title: "",
                    description: "",
                    order: sectionsCount + 1,
                };
            },

            openEditSection(section) {
                this.editSectionModal = {
                    open: true,
                    id: section.id,
                    title: section.title,
                    description: section.description || "",
                    order: section.order,
                };
            },

            openDeleteSection(id, name) {
                this.deleteSectionModal = { open: true, id, name };
            },

            submitDeleteSection() {
                const form = document.createElement("form");
                form.method = "POST";
                form.action = window.sectionDeleteUrl.replace(
                    "__SECTION_ID__",
                    this.deleteSectionModal.id,
                );
                form.innerHTML =
                    '<input type="hidden" name="_token" value="' +
                    window.csrfToken +
                    '"><input type="hidden" name="_method" value="DELETE">';
                document.body.appendChild(form);
                form.submit();
            },

            submitSectionForm(_event, mode) {
                // For add mode, submit the form normally
                if (mode === "add") {
                    // Let the form submit naturally
                    return true;
                }
                // For edit mode, close modal after submit
                this.editSectionModal.open = false;
                return true;
            },
        };
    }

    // Make functions globally available
    window.addFieldToConfig = addFieldToConfig;
    window.removeFieldFromConfig = removeFieldFromConfig;
    window.updateFieldChartTypes = updateFieldChartTypes;
    window.toggleChartTypeForField = toggleChartTypeForField;
    window.isChartTypeSelectedForField = isChartTypeSelectedForField;
    window.renderChartConfigList = renderChartConfigList;
    window.renderChartPreview = renderChartPreview;

    // Logo preview functions (global)
    window.previewLogo = function (input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                const preview = document.getElementById("logo_preview");
                const img = document.getElementById("logo_preview_img");
                img.src = e.target.result;
                preview.classList.remove("hidden");
                document
                    .getElementById("logo-upload-area")
                    .classList.add("hidden");
            };
            reader.readAsDataURL(input.files[0]);
        }
    };

    window.removeLogo = function () {
        document.getElementById("logo_input").value = "";
        document.getElementById("logo_preview").classList.add("hidden");
        document.getElementById("logo_preview_img").src = "";
        document.getElementById("logo-upload-area").classList.remove("hidden");
    };

    window.removeExistingLogo = function () {
        const removeInput = document.createElement("input");
        removeInput.type = "hidden";
        removeInput.name = "remove_logo";
        removeInput.value = "1";
        document.getElementById("pageForm").appendChild(removeInput);
        document.getElementById("logo_preview").classList.add("hidden");
        document.getElementById("logo_uploaded")?.classList.add("hidden");
        document.getElementById("logo-upload-area").classList.remove("hidden");
    };

    // RTE content setter — works reliably across RTE versions
    function setRTEContent(rte, html) {
        if (!rte) return;
        try {
            if (typeof rte.setHTMLCode === "function") {
                rte.setHTMLCode(html);
            } else if (typeof rte.setHTML === "function") {
                rte.setHTML(html);
            } else if (typeof rte.setValue === "function") {
                rte.setValue(html);
            }
        } catch (e) {
            console.warn("[RTE] setRTEContent failed:", e);
        }
    }

    // Form submit handler
    window.initProfileEditForm = function () {
        console.log("[RTE] initProfileEditForm called");

        // Grab existing content BEFORE clearing
        var container = document.getElementById("div_editor1");
        var initialDescriptionHtml = container ? container.innerHTML || "" : "";

        var rteRetries = 0;
        function initRTE() {
            console.log(
                "[RTE] initRTE attempt",
                rteRetries,
                "RichTextEditor available:",
                typeof RichTextEditor !== "undefined",
            );
            if (typeof RichTextEditor === "undefined") {
                rteRetries++;
                if (rteRetries < 100) {
                    setTimeout(initRTE, 100);
                }
                return;
            }
            if (!document.getElementById("div_editor1")) {
                rteRetries++;
                if (rteRetries < 100) {
                    setTimeout(initRTE, 100);
                }
                return;
            }

            // Clear container so RTE can initialize in a clean DOM
            var editorContainer = document.getElementById("div_editor1");
            editorContainer.innerHTML = "";
            console.log(
                "[RTE] Creating RichTextEditor with initial content length:",
                initialDescriptionHtml.length,
            );

            try {
                editor1 = new RichTextEditor("#div_editor1", {
                    base_url: "/richtexteditor",
                    editorBodyCssClass: "rte-content-body",
                    file_upload_handler: function (file, callback) {
                        var formData = new FormData();
                        formData.append("file", file);
                        formData.append("_token", window.csrfToken || "");
                        fetch(
                            window.rteUploadUrl || "/cms/settings/rte-upload",
                            { method: "POST", body: formData },
                        )
                            .then(function (r) {
                                return r.json();
                            })
                            .then(function (result) {
                                callback(result.url);
                            })
                            .catch(function (err) {
                                console.error(err);
                                alert("Upload gagal.");
                            });
                    },
                });

                // Set content after editor is ready — multiple attempts for iframe readiness
                setRTEContent(editor1, initialDescriptionHtml);
                setTimeout(function () {
                    setRTEContent(editor1, initialDescriptionHtml);
                }, 300);
                setTimeout(function () {
                    setRTEContent(editor1, initialDescriptionHtml);
                }, 800);
                setTimeout(function () {
                    // Final check: if editor is still empty but we have content, force-set once more
                    try {
                        var currentContent = "";
                        if (typeof editor1.getHTMLCode === "function")
                            currentContent = editor1.getHTMLCode();
                        else if (typeof editor1.getHTML === "function")
                            currentContent = editor1.getHTML();
                        if (
                            (!currentContent ||
                                currentContent.trim() === "" ||
                                currentContent.trim() === "<p><br></p>" ||
                                currentContent.trim() === "<br>") &&
                            initialDescriptionHtml &&
                            initialDescriptionHtml.trim() !== ""
                        ) {
                            setRTEContent(editor1, initialDescriptionHtml);
                        }
                    } catch (e) {}
                }, 1500);

                console.log("[RTE] RichTextEditor created successfully");

                // Listen for editor content changes to update preview
                const editorContainer = document.querySelector("#div_editor1");
                if (editorContainer) {
                    // Try to attach observer to RTE iframe or content area
                    const observer = new MutationObserver(() => {
                        renderPagePreview();
                    });
                    observer.observe(editorContainer, {
                        subtree: true,
                        childList: true,
                        characterData: true,
                        attributes: false,
                    });
                }
            } catch (e) {
                console.error("[RTE] Error creating RichTextEditor:", e);
            }
        }
        // Wait for scripts to fully load, then init
        setTimeout(initRTE, 500);

        // Initialize chart config list
        renderChartConfigList();

        // Initialize existing_images_input on page load so existing images are sent to controller
        var existingImagesInitInput = document.getElementById(
            "existing_images_container",
        );
        if (
            existingImagesInitInput &&
            window.existingImages &&
            window.existingImages.length
        ) {
            // Clear previous inputs
            existingImagesInitInput.innerHTML = "";
            // Create hidden input for each existing image
            window.existingImages.forEach(function (img, idx) {
                var input = document.createElement("input");
                input.type = "hidden";
                input.name = "existing_images[]";
                input.value = img.path;
                existingImagesInitInput.appendChild(input);
            });
        }

        // Form submit
        document
            .getElementById("pageForm")
            .addEventListener("submit", function (e) {
                if (typeof editor1 !== "undefined" && editor1) {
                    var html = editor1.getHTMLCode();
                    document.getElementById("description_input").value = html;
                }

                // Handle gambar files
                const form = e.target;
                const gambarFiles = _gambarStore
                    .filter(function (i) {
                        return !i.isExisting;
                    })
                    .map(function (i) {
                        return _files[i.id];
                    })
                    .filter(Boolean);

                if (gambarFiles.length) {
                    const dt = new DataTransfer();
                    gambarFiles.forEach(function (f) {
                        dt.items.add(f);
                    });
                    const fileInput = document.createElement("input");
                    fileInput.type = "file";
                    fileInput.name = "images[]";
                    fileInput.multiple = true;
                    fileInput.files = dt.files;
                    fileInput.className = "hidden";
                    form.appendChild(fileInput);
                }

                // Update existing_images container
                const existingImagesContainer = document.getElementById(
                    "existing_images_container",
                );
                if (existingImagesContainer) {
                    existingImagesContainer.innerHTML = "";
                    _gambarStore
                        .filter(function (i) {
                            return i.isExisting;
                        })
                        .forEach(function (img) {
                            var input = document.createElement("input");
                            input.type = "hidden";
                            input.name = "existing_images[]";
                            input.value = img.path;
                            existingImagesContainer.appendChild(input);
                        });
                }

                // Clear removed_images_input — controller uses existing_images to determine deletions
                var removedInput = document.getElementById("removed_images_input");
                if (removedInput) removedInput.value = "";

                // Image positions and dimensions as array
                _gambarStore.forEach(function (img, idx) {
                    var posX = img.x !== undefined ? img.x : 50;
                    var posY = img.y !== undefined ? img.y : 50;

                    var posInput = document.createElement("input");
                    posInput.type = "hidden";
                    posInput.name = "image_positions[]";
                    posInput.value = posX + "% " + posY + "%";
                    form.appendChild(posInput);

                    var widthInput = document.createElement("input");
                    widthInput.type = "hidden";
                    widthInput.name = "image_widths[]";
                    widthInput.value = img.width || 200;
                    form.appendChild(widthInput);

                    var heightInput = document.createElement("input");
                    heightInput.type = "hidden";
                    heightInput.name = "image_heights[]";
                    heightInput.value = img.height || 150;
                    form.appendChild(heightInput);

                    var offsetXVal = img.offsetX !== undefined && img.offsetX !== null ? img.offsetX : 0;
                    var offsetYVal = img.offsetY !== undefined && img.offsetY !== null ? img.offsetY : 0;

                    var offsetXInput = document.createElement("input");
                    offsetXInput.type = "hidden";
                    offsetXInput.name = "image_offset_x[]";
                    offsetXInput.value = offsetXVal;
                    form.appendChild(offsetXInput);

                    var offsetYInput = document.createElement("input");
                    offsetYInput.type = "hidden";
                    offsetYInput.name = "image_offset_y[]";
                    offsetYInput.value = offsetYVal;
                    form.appendChild(offsetYInput);
                });

                // Only disable the original gambar_files input, NOT the dynamically created images[]
                form.querySelectorAll(
                    'input[type="file"][name="gambar_files"]',
                ).forEach(function (i) {
                    i.disabled = true;
                });

                // Disable static image_positions_input to avoid double-submit with array inputs
                var staticImgPosInput = document.getElementById("image_positions_input");
                if (staticImgPosInput) staticImgPosInput.disabled = true;

                const btn = document.getElementById("submitBtn");
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = "Menyimpan...";
                }
            });
    };

    // Export for Alpine
    
    // Zoom functionality for preview
    let currentZoom = 1;
    const MIN_ZOOM = 0.5;
    const MAX_ZOOM = 2;
    const ZOOM_STEP = 0.1;

    function updatePreviewZoom(zoomLevel) {
        const previewContainer = document.getElementById('preview-container');
        if (previewContainer) {
            previewContainer.style.transform = `scale(` + zoomLevel + `)`;
            document.getElementById('zoomLevel').textContent = Math.round(zoomLevel * 100) + '%';
        }
    }

    function setupZoomControls() {
        const zoomInBtn = document.getElementById('zoomInBtn');
        const zoomOutBtn = document.getElementById('zoomOutBtn');
        const zoomResetBtn = document.getElementById('zoomResetBtn');

        if (zoomInBtn) {
            zoomInBtn.addEventListener('click', function(e) {
                e.preventDefault();
                if (currentZoom < MAX_ZOOM) {
                    currentZoom = Math.min(currentZoom + ZOOM_STEP, MAX_ZOOM);
                    updatePreviewZoom(currentZoom);
                }
            });
        }

        if (zoomOutBtn) {
            zoomOutBtn.addEventListener('click', function(e) {
                e.preventDefault();
                if (currentZoom > MIN_ZOOM) {
                    currentZoom = Math.max(currentZoom - ZOOM_STEP, MIN_ZOOM);
                    updatePreviewZoom(currentZoom);
                }
            });
        }

        if (zoomResetBtn) {
            zoomResetBtn.addEventListener('click', function(e) {
                e.preventDefault();
                currentZoom = 1;
                updatePreviewZoom(currentZoom);
            });
        }
    }
window.profilePageForm = profilePageForm;
})();
