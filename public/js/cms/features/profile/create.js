/**
 * Profile Page Create - JavaScript
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

        const cols = Math.min(Math.max(_gambarStore.length, 1), 4);
        const grid = document.createElement("div");
        grid.className = "grid gap-3 mb-3";
        grid.style.gridTemplateColumns = "repeat(" + cols + ", 1fr)";

        _gambarStore.forEach((img) => {
            const wrapper = document.createElement("div");
            wrapper.className = "relative group";
            wrapper.dataset.imgId = img.id;

            const dragBox = document.createElement("div");
            dragBox.className =
                "relative overflow-hidden rounded-lg bg-gray-200 cursor-crosshair";
            dragBox.style.aspectRatio = "4/3";
            dragBox.style.minHeight = "200px";

            // Add loading indicator
            const loader = document.createElement("div");
            loader.className =
                "absolute inset-0 flex items-center justify-center bg-gray-100";
            loader.innerHTML =
                '<div class="w-6 h-6 border-2 border-blue-400 border-t-transparent rounded-full animate-spin"></div>';
            dragBox.appendChild(loader);

            const imgEl = document.createElement("img");
            imgEl.className =
                "absolute w-full h-full object-cover transition-transform duration-300";
            imgEl.style.objectPosition = img.x + "% " + img.y + "%";
            imgEl.style.display = "none"; // Hide until loaded

            // Track successful load
            let imageLoaded = false;

            imgEl.onload = function () {
                console.log(
                    "[IMG LOAD] Successfully loaded:",
                    img.preview.substring(0, 80),
                );
                imageLoaded = true;
                imgEl.style.display = "block";
                loader.style.display = "none";
            };

            imgEl.onerror = function () {
                console.error("[IMG ERROR] Failed to load image");
                loader.innerHTML =
                    '<div class="text-red-500 text-xs text-center">Failed to load<br>image</div>';
                imgEl.style.display = "none";
            };

            // Set src last to trigger loading
            imgEl.src = img.preview;
            dragBox.appendChild(imgEl);

            const focal = document.createElement("div");
            focal.className =
                "absolute w-5 h-5 border-2 border-white rounded-full shadow-lg pointer-events-none flex items-center justify-center";
            focal.style.cssText =
                "background-color:rgba(59,130,246,0.6);transform:translate(-50%,-50%);left:" +
                img.x +
                "%;top:" +
                img.y +
                "%;z-index:10;";
            const dot = document.createElement("div");
            dot.className = "w-1 h-1 bg-white rounded-full";
            focal.appendChild(dot);
            dragBox.appendChild(focal);

            dragBox.addEventListener("mousedown", function (e) {
                if (e.target.closest("button")) return;
                if (e.button !== 0) return;
                e.preventDefault();
                e.stopPropagation();
                const update = (ev) => {
                    const rect = dragBox.getBoundingClientRect();
                    if (rect.width === 0) return;
                    const px = parseFloat(
                        Math.max(
                            0,
                            Math.min(
                                100,
                                ((ev.clientX - rect.left) / rect.width) * 100,
                            ),
                        ).toFixed(2),
                    );
                    const py = parseFloat(
                        Math.max(
                            0,
                            Math.min(
                                100,
                                ((ev.clientY - rect.top) / rect.height) * 100,
                            ),
                        ).toFixed(2),
                    );
                    imgEl.style.objectPosition = px + "% " + py + "%";
                    focal.style.left = px + "%";
                    focal.style.top = py + "%";
                    img.x = px;
                    img.y = py;
                    if (typeof renderPagePreview === "function")
                        renderPagePreview();
                };
                const stop = () => {
                    window.removeEventListener("mousemove", update);
                    window.removeEventListener("mouseup", stop);
                };
                window.addEventListener("mousemove", update);
                window.addEventListener("mouseup", stop);
                update(e);
            });

            wrapper.appendChild(dragBox);

            const delBtn = document.createElement("button");
            delBtn.type = "button";
            delBtn.className =
                "absolute bg-red-500 text-white rounded-full flex items-center justify-center shadow-md hover:bg-red-600 transition-colors cursor-pointer z-50";
            delBtn.style.cssText =
                "width:22px;height:22px;top:-6px;right:-6px;line-height:1";
            delBtn.innerHTML =
                '<svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>';
            delBtn.addEventListener("click", (e) => {
                e.stopPropagation();
                const idx = _gambarStore.findIndex((i) => i.id === img.id);
                if (idx !== -1) {
                    _gambarStore.splice(idx, 1);
                    delete _files[img.id];
                }
                renderGambarPreviews();
                if (typeof renderPagePreview === "function")
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

        _chartInstances.forEach((c) => c.destroy());
        _chartInstances = [];

        if (!data || Object.keys(data).length === 0) {
            container.innerHTML =
                '<p class="text-xs text-gray-400 text-center py-8">Tidak ada data untuk ditampilkan. Pilih field data dan tipe grafik, lalu klik "Generate Grafik"</p>';
            return;
        }

        let html = '<div class="chart-preview-container">';

        Object.keys(data).forEach((key) => {
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

            html += `
                <div class="chart-card">
                    <p class="chart-card-title">${chart.title || key}${chartTypeLabel}</p>
                    <div style="height:${isPie ? "250px" : "200px"};position:relative">
                        <canvas id="${chartId}"></canvas>
                    </div>
                </div>`;
        });

        html += "</div>";
        container.innerHTML = html;

        // Render each chart
        Object.keys(data).forEach((key) => {
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
        // Must have at least one type
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
        fields.forEach((field) => {
            const label = _availableFields[field] || field;
            const types = _chartConfig[field] || [];
            const isTanggalLahir = field === "tanggal_lahir";

            html += `
                <div class="chart-config-item" data-field="${field}">
                    <div class="chart-config-header">
                        <span class="chart-config-label">${label}</span>
                        <button type="button" onclick="removeFieldFromConfig('${field}')" class="chart-config-remove">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="chart-config-types">
                        <button type="button"
                            onclick="toggleChartTypeForField('${field}', 'pie')"
                            class="chart-type-btn ${isChartTypeSelectedForField(field, "pie") ? "active" : ""}"
                            ${isTanggalLahir ? 'title="Usia dihitung dari tanggal lahir"' : ""}>
                            Pie Chart
                        </button>
                        <button type="button"
                            onclick="toggleChartTypeForField('${field}', 'bar')"
                            class="chart-type-btn ${isChartTypeSelectedForField(field, "bar") ? "active" : ""}">
                            Bar Chart
                        </button>
                    </div>
                </div>`;
        });

        container.innerHTML = html;
    }

    function saveImagePositionsBeforeSubmit() {
        const form = document.getElementById("pageForm");
        if (!form) return;

        // Remove previous dynamically generated inputs
        document
            .querySelectorAll(".dynamic-img-pos")
            .forEach((el) => el.remove());

        // Create individual array inputs for each image
        _gambarStore.forEach((img, idx) => {
            const posX = img.x !== undefined ? img.x : 50;
            const posY = img.y !== undefined ? img.y : 50;
            const posStr = posX + "% " + posY + "%";

            const inputs = [
                { name: "image_positions[]", value: posStr },
                { name: "image_widths[]", value: img.width || 200 },
                { name: "image_heights[]", value: img.height || 150 },
                { name: "image_offset_x[]", value: img.offsetX !== undefined && img.offsetX !== null ? img.offsetX : 0 },
                { name: "image_offset_y[]", value: img.offsetY !== undefined && img.offsetY !== null ? img.offsetY : 0 },
            ];

            inputs.forEach((inputData) => {
                const hiddenInput = document.createElement("input");
                hiddenInput.type = "hidden";
                hiddenInput.name = inputData.name;
                hiddenInput.value = inputData.value;
                hiddenInput.className = "dynamic-img-pos";
                form.appendChild(hiddenInput);
            });
        });

        // Disable old stringified JSON input if it exists
        const oldInput = document.getElementById("image_positions_input");
        if (oldInput) {
            oldInput.disabled = true;
        }
    }

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
                '<div style="display:grid;grid-template-columns:1fr auto;gap:32px;align-items:start;width:100%;">';

            previewHTML +=
                '<div class="preview-text-col" style="min-width:0;overflow:hidden;">' +
                leftCol +
                "</div>";
            previewHTML +=
                '<div style="display:flex;flex-direction:column;align-items:flex-end;gap:32px;min-width:220px;">';

            _gambarStore.forEach((img) => {
                const w = img.width || 200;
                const h = img.height || 150;
                const oX = Number(img.offsetX) || 0;
                const oY = Number(img.offsetY) || 0;
                const styleStr = `position: relative; border-radius: 0.75rem; overflow: visible !important; width: ${w}px; height: ${h}px; margin: 0; z-index: 10; transform: translate(${oX}px, ${oY}px);`;
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

            previewHTML += "</div>";
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

            // Add focal point picker overlay (hidden by default, show on dblclick)
            const focalOverlay = document.createElement("div");
            focalOverlay.dataset.focal = "overlay";
            focalOverlay.style.cssText = `
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: transparent;
                cursor: crosshair;
                display: none;
                z-index: 15;
                border: 2px dashed rgba(59,130,246,0.5);
                border-radius: 0.75rem;
            `;

            focalOverlay.addEventListener("click", (e) => {
                e.stopPropagation();
                const rect = focalOverlay.getBoundingClientRect();
                const x = Math.round(
                    ((e.clientX - rect.left) / rect.width) * 100,
                );
                const y = Math.round(
                    ((e.clientY - rect.top) / rect.height) * 100,
                );
                img.x = Math.max(0, Math.min(100, x));
                img.y = Math.max(0, Math.min(100, y));
                renderPagePreview();
            });

            // Double click to toggle focal point picker
            item.addEventListener("dblclick", (e) => {
                e.stopPropagation();
                focalOverlay.style.display =
                    focalOverlay.style.display === "none" ? "block" : "none";
            });

            item.appendChild(focalOverlay);

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
                item.style.margin = "0";
                item.style.transform = "translate(" + oX + "px, " + oY + "px)";
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

                        draggedItem.style.margin = "0";
                        draggedItem.style.transform =
                            "translate(" + newX + "px, " + newY + "px)";
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

    // Profile Page Form Alpine Component
    function profilePageForm() {
        return {
            pageType: "tugas_fungsi",
            title: "",
            linkText: "",
            linkUrl: "",
            subtitle: "",
            isGeneratingChart: false,
            selectedField: "",
            availableFields: {},

            async init() {
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
            },

            onTypeChange() {
                // This is called when the page type changes
            },

            async handleGambarChange(event) {
                const files = Array.from(event.target.files);
                const compressionPromises = files.map(async (file) => {
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
                // Build config from _chartConfig
                const config = {};
                Object.keys(_chartConfig).forEach((field) => {
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

    // Form submit handler
    window.initProfileCreateForm = function () {
        console.log("[RTE] initProfileCreateForm called");
        // RTE init - wait for CDN and DOM to be ready
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
            var container = document.getElementById("div_editor1");
            console.log("[RTE] container found:", !!container);
            if (!container) {
                rteRetries++;
                if (rteRetries < 100) {
                    setTimeout(initRTE, 100);
                }
                return;
            }

            console.log("[RTE] Creating RichTextEditor...");
            var rteUploadUrl =
                window.rteUploadUrl || "/cms/settings/rte-upload";
            try {
                // Define base_url - local path
                var rteBaseUrl = "/richtexteditor";

                editor1 = new RichTextEditor("#div_editor1", {
                    base_url: rteBaseUrl,
                    editorBodyCssClass: "rte-content-body",
                    file_upload_handler: function (file, callback) {
                        var formData = new FormData();
                        formData.append("file", file);
                        formData.append("_token", window.csrfToken || "");
                        fetch(rteUploadUrl, { method: "POST", body: formData })
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
                console.log(
                    "[RTE] RichTextEditor created successfully with base_url:",
                    rteBaseUrl,
                );
            } catch (e) {
                console.error("[RTE] Error creating RichTextEditor:", e);
            }

            // Listen for editor content changes to update preview
            const editorContainer = document.querySelector("#div_editor1");
            if (editorContainer) {
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
        }
        // Wait for scripts to fully load, then init
        setTimeout(initRTE, 500);

        // Initialize chart config list
        renderChartConfigList();

        // Setup preview triggers
        setupPreviewTriggers();
        setupZoomControls();


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
                    .filter((i) => !i.isExisting)
                    .map((i) => _files[i.id])
                    .filter(Boolean);

                if (gambarFiles.length) {
                    const dt = new DataTransfer();
                    gambarFiles.forEach((f) => dt.items.add(f));
                    const fileInput = document.createElement("input");
                    fileInput.type = "file";
                    fileInput.name = "images[]";
                    fileInput.multiple = true;
                    fileInput.files = dt.files;
                    fileInput.className = "hidden";
                    form.appendChild(fileInput);
                }

                // Save image positions, widths, heights, offsets
                saveImagePositionsBeforeSubmit();

                // Only disable the original gambar_files input, NOT the dynamically created images[]
                form.querySelectorAll(
                    'input[type="file"][name="gambar_files"]',
                ).forEach((i) => (i.disabled = true));

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
