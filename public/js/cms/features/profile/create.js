/**
 * Profile Page Create - JavaScript
 * Synchronized with edit.js architecture for full parity
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

            imgEl.onload = function () {
                imgEl.style.display = "block";
                loader.style.display = "none";
            };

            imgEl.onerror = function () {
                loader.innerHTML =
                    '<div class="text-gray-400 text-xs text-center">Gagal memuat gambar</div>';
            };

            imgEl.src = img.preview;
            dragBox.appendChild(imgEl);
            wrapper.appendChild(dragBox);

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
                    _gambarStore.splice(idx, 1);
                    delete _files[img.id];
                }
                renderGambarPreviews();
                renderPagePreview();
            });
            wrapper.appendChild(delBtn);
            grid.appendChild(wrapper);
        });

        container.appendChild(grid);
    }

    const chartColors = ['#36c5f0', '#0a0b1e', '#85d7ff', '#2eb67d', '#174e93', '#10b981', '#06b6d4', '#6366f1'];

    // Plugin to draw total in center of doughnut
    const centerTextPlugin = {
        id: 'centerText',
        beforeDraw: function(chart) {
            if (chart.config.type !== 'doughnut') return;
            const width = chart.width,
                height = chart.height,
                ctx = chart.ctx;
            ctx.restore();
            const fontSize = (height / 160).toFixed(2);
            ctx.font = "bold " + fontSize + "em sans-serif";
            ctx.textBaseline = "middle";
            const text = chart.data.datasets[0].data.reduce((a, b) => a + b, 0).toString();
            const textX = Math.round((width - ctx.measureText(text).width) / 2);
            const textY = height / 2;
            ctx.fillStyle = '#1e293b';
            ctx.fillText(text, textX, textY);
            ctx.save();
        }
    };
    Chart.register(centerTextPlugin);

    function renderChartPreview(data) {
        const container = document.getElementById("chart_preview");
        if (!container) return;

        _chartInstances.forEach(c => c.destroy());
        _chartInstances = [];

        if (!data || Object.keys(data).length === 0) {
            container.innerHTML = '<p class="text-xs text-gray-400 text-center py-8">Tidak ada data untuk ditampilkan. Pilih field data dan tipe grafik, lalu klik "Generate Grafik"</p>';
            return;
        }

        let html = '<div class="chart-preview-container">';
        Object.keys(data).forEach(key => {
            const chart = data[key];
            
            // Safety: only render if in config
            if (!chart.field || !chart.type) {
                const parts = key.split("-");
                if (parts.length >= 2) {
                    chart.field = chart.field || parts[0];
                    chart.type = chart.type || parts[1];
                }
            }
            if (!_chartConfig[chart.field] || !_chartConfig[chart.field].includes(chart.type)) return;

            if (!chart.labels || !chart.data) return;
            const chartId = "chart-" + key;
            const isDoughnut = chart.type === "pie" || chart.type === "doughnut";
            html += `<div class="chart-card">
                <button type="button" class="chart-card-remove" onclick="removeChartFromPreview('${chart.field}', '${chart.type}')" title="Hapus grafik ini">
                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
                <p class="chart-card-title">${chart.title || key}${isDoughnut ? ' (Pie/Doughnut)' : ' (Bar)'}</p>
                <div style="height:300px;position:relative"><canvas id="${chartId}"></canvas></div>
            </div>`;
        });
        html += "</div>";
        container.innerHTML = html;

        Object.keys(data).forEach(key => {
            const chart = data[key];
            if (!chart.labels || !chart.data) return;
            const canvasEl = document.getElementById("chart-" + key);
            if (!canvasEl) return;

            if (chart.type === "pie" || chart.type === "doughnut") {
                _chartInstances.push(new Chart(canvasEl.getContext("2d"), {
                    type: "doughnut",
                    data: {
                        labels: chart.labels,
                        datasets: [{
                            data: chart.data,
                            backgroundColor: chartColors,
                            borderWidth: 2,
                            borderColor: "#fff",
                            hoverOffset: 8
                        }]
                    },
                    options: { 
                        responsive: true, 
                        maintainAspectRatio: false, 
                        cutout: '70%',
                        plugins: { 
                            legend: { 
                                position: "bottom", 
                                labels: { usePointStyle: true, font: { size: 10 }, padding: 12 } 
                            },
                            tooltip: {
                                backgroundColor: 'rgba(15, 23, 42, 0.9)',
                                padding: 10,
                                cornerRadius: 6
                            }
                        } 
                    }
                }));
            } else {
                _chartInstances.push(new Chart(canvasEl.getContext("2d"), {
                    type: "bar",
                    data: {
                        labels: chart.labels,
                        datasets: [{ 
                            label: "Jumlah", 
                            data: chart.data, 
                            backgroundColor: '#36c5f0', 
                            borderRadius: 6,
                            borderSkipped: false,
                            barThickness: 30
                        }]
                    },
                    options: { 
                        responsive: true, 
                        maintainAspectRatio: false, 
                        plugins: { 
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(15, 23, 42, 0.9)',
                                padding: 10,
                                cornerRadius: 6
                            }
                        }, 
                        scales: { 
                            y: { 
                                beginAtZero: true, 
                                ticks: { stepSize: 1, color: '#94a3b8', font: { size: 9 } },
                                grid: { color: '#f1f5f9', drawBorder: false }
                            }, 
                            x: { 
                                ticks: { color: '#94a3b8', font: { size: 9 } },
                                grid: { display: false, drawBorder: false }
                            } 
                        } 
                    }
                }));
            }
        });
    }
    function removeChartFromPreview(field, type) {
        console.log("[CHART DELETE] Target:", field, type);
        if (!_chartConfig[field]) {
            console.warn("[CHART DELETE] Field not in _chartConfig:", field);
        } else {
            const idx = _chartConfig[field].indexOf(type);
            if (idx > -1) {
                _chartConfig[field].splice(idx, 1);
                if (_chartConfig[field].length === 0) delete _chartConfig[field];
            }
        }
        renderChartConfigList();
        const input = document.getElementById("chart_data_input");
        const currentDataRaw = input ? input.value : null;
        if (currentDataRaw) {
            try {
                const data = JSON.parse(currentDataRaw);
                const targetKey = field + "-" + type;
                console.log("[CHART DELETE] Current keys:", Object.keys(data));
                
                let deleted = false;
                if (data[targetKey]) {
                    delete data[targetKey];
                    deleted = true;
                } else {
                    const foundKey = Object.keys(data).find(k => k.trim() === targetKey.trim());
                    if (foundKey) {
                        delete data[foundKey];
                        deleted = true;
                    }
                }
                
                if (deleted) {
                    input.value = JSON.stringify(data);
                    console.log("[CHART DELETE] Deleted successfully. New size:", Object.keys(data).length);
                }
                renderChartPreview(data);
            } catch (e) { console.error("[CHART DELETE] Error:", e); }
        }
    }

    function addFieldToConfig(field, types) {
        if (!field || _chartConfig[field]) return;
        _chartConfig[field] = types;
        renderChartConfigList();
    }

    function removeFieldFromConfig(field) {
        delete _chartConfig[field];
        renderChartConfigList();
    }

    function toggleChartTypeForField(field, type) {
        if (!_chartConfig[field]) return;
        const idx = _chartConfig[field].indexOf(type);
        if (idx > -1) _chartConfig[field].splice(idx, 1);
        else _chartConfig[field].push(type);
        if (_chartConfig[field].length === 0) _chartConfig[field].push(type);
        renderChartConfigList();
    }

    function isChartTypeSelectedForField(field, type) {
        return _chartConfig[field] && _chartConfig[field].includes(type);
    }

    function renderChartConfigList() {
        const container = document.getElementById("chart-config-list");
        if (!container) return;
        const fields = Object.keys(_chartConfig);
        if (fields.length === 0) {
            container.innerHTML = '<p class="text-xs text-gray-400 py-2">Pilih field data di atas untuk menambahkan grafik</p>';
            return;
        }
        let html = "";
        fields.forEach(field => {
            const label = _availableFields[field] || field;
            html += `<div class="chart-config-item" data-field="${field}"><div class="chart-config-header"><span class="chart-config-label">${label}</span><button type="button" onclick="removeFieldFromConfig('${field}')" class="chart-config-remove"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div><div class="chart-config-types"><button type="button" onclick="toggleChartTypeForField('${field}', 'pie')" class="chart-type-btn ${isChartTypeSelectedForField(field, 'pie') ? 'active' : ''}">Pie Chart</button><button type="button" onclick="toggleChartTypeForField('${field}', 'bar')" class="chart-type-btn ${isChartTypeSelectedForField(field, 'bar') ? 'active' : ''}">Bar Chart</button></div></div>`;
        });
        container.innerHTML = html;
    }

    function showPositionMenu(imgId, button, dragBox) {
        const presets = [{ label: "Kiri", x: 20, y: 50 }, { label: "Tengah", x: 50, y: 50 }, { label: "Kanan", x: 80, y: 50 }, { label: "Atas", x: 50, y: 30 }, { label: "Bawah", x: 50, y: 70 }];
        const existingMenu = document.getElementById("position-menu-" + imgId);
        if (existingMenu) existingMenu.remove();
        const menu = document.createElement("div");
        menu.id = "position-menu-" + imgId;
        menu.className = "absolute z-50 bg-white border border-gray-200 rounded-lg shadow-lg p-2";
        menu.style.cssText = "bottom: 100%; left: 0; white-space: nowrap; min-width: 120px; margin-bottom: 4px;";
        presets.forEach(preset => {
            const btn = document.createElement("button");
            btn.type = "button";
            btn.className = "block w-full text-left px-3 py-2 text-xs hover:bg-blue-100 rounded transition-colors";
            btn.textContent = `${preset.label} (${preset.x}%, ${preset.y}%)`;
            btn.addEventListener("click", (e) => {
                e.preventDefault();
                e.stopPropagation();
                const img = _gambarStore.find(i => i.id === imgId);
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
        button.parentElement.insertBefore(menu, button.nextSibling);
        const closeMenu = (e) => { if (!menu.contains(e.target) && e.target !== button) { menu.remove(); document.removeEventListener("click", closeMenu); } };
        setTimeout(() => document.addEventListener("click", closeMenu), 0);
    }

    function renderPagePreview() {
        const container = document.getElementById("preview-container");
        if (!container) return;

        let descriptionHTML = "";
        if (editor1 && typeof editor1.getHTMLCode === "function") {
            try { descriptionHTML = editor1.getHTMLCode() || ""; } catch (e) { }
        }

        const titleVal = document.querySelector('[name="title"]')?.value || "";
        const linkTextVal = document.querySelector('[name="link_text"]')?.value || "";
        const linkUrlVal = document.querySelector('[name="link_url"]')?.value || "";

        let previewHTML = "<div style=\"width: 100%; font-family: 'Montserrat', Arial, Helvetica, sans-serif; color: #475569; line-height: 1.75; font-size: 1rem; padding: 2rem 0;\">";
        const hasDesc = descriptionHTML && descriptionHTML.trim() !== "" && descriptionHTML !== "<p><br></p>";
        const hasImages = _gambarStore.length > 0;
        const hasTitle = titleVal && titleVal.trim() !== "";
        const hasLink = linkTextVal && linkUrlVal;

        let leftCol = '<div style="width: 100%; word-break: break-word; overflow-wrap: break-word; min-width: 0;">';
        if (hasDesc) leftCol += `<div class="profile-section-desc" style="margin-bottom: 1.5rem;">${descriptionHTML}</div>`;
        if (hasTitle) leftCol += `<h2 class="profile-section-title">${titleVal}</h2>`;
        if (hasLink) leftCol += `<a href="${linkUrlVal}" class="page-link-btn" target="_blank">${linkTextVal} <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg></a>`;
        leftCol += "</div>";

        if (hasImages) {
            previewHTML += '<div style="display:grid;grid-template-columns:1fr auto;gap:32px;align-items:start;width:100%;">';
            previewHTML += `<div class="preview-text-col" style="min-width:0;overflow:hidden;">${leftCol}</div>`;
            previewHTML += '<div style="display:flex;flex-direction:column;align-items:flex-end;gap:32px;min-width:220px;">';
            _gambarStore.forEach(img => {
                const w = img.width || 200, h = img.height || 150, oX = Number(img.offsetX) || 0, oY = Number(img.offsetY) || 0;
                previewHTML += `<div class="preview-img-item" data-img-id="${img.id}" style="position: relative; border-radius: 0.75rem; overflow: visible !important; width: ${w}px; height: ${h}px; margin: ${oY}px ${-oX}px 0 0; z-index: 10;">
                    <div style="position: absolute; top: 2px; left: 2px; background: rgba(0,0,0,0.65); color: white; padding: 2px 4px; font-size: 10px; border-radius: 3px; z-index: 20; cursor: grab;">☰</div>
                    <img src="${img.preview}" style="width: 100%; height: 100%; object-fit: cover; object-position: ${img.x || 50}% ${img.y || 50}%; display: block; border-radius: 0.75rem;">
                </div>`;
            });
            previewHTML += "</div></div>";
        } else if (hasDesc || hasTitle || hasLink) {
            previewHTML += leftCol;
        } else {
            previewHTML += '<div style="color: #999; text-align: center; padding: 2rem; font-style: italic; border: 2px dashed #e5e7eb; border-radius: 8px;"><p style="margin: 0; font-size: 13px;">Tambahkan konten dan/atau gambar untuk melihat preview</p></div>';
        }
        previewHTML += "</div>";
        container.innerHTML = previewHTML;

        attachPreviewDragHandlers();
        attachPreviewResizeHandlers();
    }

    function attachPreviewResizeHandlers() {
        document.querySelectorAll(".preview-img-item").forEach(item => {
            const img = _gambarStore.find(i => i.id === item.dataset.imgId);
            if (!img) return;
            const handles = [{ pos: "tl", cursor: "nwse-resize", top: "-5px", left: "-5px" }, { pos: "tr", cursor: "nesw-resize", top: "-5px", right: "-5px" }, { pos: "bl", cursor: "nesw-resize", bottom: "-5px", left: "-5px" }, { pos: "br", cursor: "nwse-resize", bottom: "-5px", right: "-5px" }];
            handles.forEach(h => {
                const el = document.createElement("div");
                el.style.cssText = `position: absolute; width: 10px; height: 10px; background: #3B82F6; border: 2px solid white; border-radius: 1px; cursor: ${h.cursor}; z-index: 40; box-shadow: 0 0 4px rgba(59,130,246,0.8); pointer-events: auto; ${h.top ? "top: " + h.top + ";" : ""} ${h.bottom ? "bottom: " + h.bottom + ";" : ""} ${h.left ? "left: " + h.left + ";" : ""} ${h.right ? "right: " + h.right + ";" : ""}`;
                el.addEventListener("mousedown", (e) => {
                    e.preventDefault(); e.stopPropagation();
                    const startX = e.clientX, startY = e.clientY, startWidth = item.offsetWidth, startHeight = item.offsetHeight, startOX = Number(img.offsetX) || 0, startOY = Number(img.offsetY) || 0;
                    const onMove = (mv) => {
                        const dx = mv.clientX - startX, dy = mv.clientY - startY;
                        let nw = startWidth, nh = startHeight, sx = 0, sy = 0;
                        if (h.pos.includes("r")) nw = Math.max(80, startWidth + dx); else if (h.pos.includes("l")) { nw = Math.max(80, startWidth - dx); sx = -(nw - startWidth); }
                        if (h.pos.includes("b")) nh = Math.max(60, startHeight + dy); else if (h.pos.includes("t")) { nh = Math.max(60, startHeight - dy); sy = -(nh - startHeight); }
                        const nOX = startOX + sx, nOY = startOY + sy;
                        item.style.width = nw + "px"; item.style.height = nh + "px"; item.style.margin = nOY + "px " + (-nOX) + "px 0 0";
                        img.width = Math.round(nw); img.height = Math.round(nh); img.offsetX = Math.round(nOX); img.offsetY = Math.round(nOY);
                    };
                    const onUp = () => { document.removeEventListener("mousemove", onMove); document.removeEventListener("mouseup", onUp); };
                    document.addEventListener("mousemove", onMove); document.addEventListener("mouseup", onUp);
                });
                item.appendChild(el);
            });
        });
    }

    function attachPreviewDragHandlers() {
        document.querySelectorAll(".preview-img-item").forEach(item => {
            const dragHandle = item.querySelector('div[style*="position: absolute"][style*="top: 2px"]');
            if (dragHandle) {
                dragHandle.addEventListener("mousedown", (e) => {
                    if (e.button !== 0) return; e.preventDefault(); e.stopPropagation();
                    const img = _gambarStore.find(i => i.id === item.dataset.imgId);
                    if (!img) return;
                    const startOX = img.offsetX || 0, startOY = img.offsetY || 0, startMX = e.clientX, startMY = e.clientY;
                    item.style.cursor = "grabbing"; item.style.opacity = "0.7"; item.style.zIndex = "1000";
                    const onMove = (mv) => {
                        const dx = mv.clientX - startMX, dy = mv.clientY - startMY;
                        const nx = Math.round(startOX + dx), ny = Math.round(startOY + dy);
                        img.offsetX = nx; img.offsetY = ny;
                        item.style.margin = ny + "px " + (-nx) + "px 0 0";
                    };
                    const onUp = () => { item.style.cursor = "grab"; item.style.opacity = "1"; item.style.zIndex = "auto"; document.removeEventListener("mousemove", onMove); document.removeEventListener("mouseup", onUp); };
                    document.addEventListener("mousemove", onMove); document.addEventListener("mouseup", onUp);
                });
            }
        });
    }

    function profilePageForm() {
        return {
            pageType: "tugas_fungsi", title: "", linkText: "", linkUrl: "", subtitle: "", isGeneratingChart: false, 
            selectedField: "", availableFields: {}, availableRoles: window.availableRoles || {}, selectedRoles: [],
            async init() {
                try {
                    const response = await fetch(window.dataFieldsUrl);
                    const data = await response.json();
                    this.availableFields = data; _availableFields = data;
                } catch (e) { console.error("Failed to load data fields:", e); }
                setTimeout(renderPagePreview, 500);

                // Restore existing chart data if available (e.g. after validation error)
                const chartDataInput = document.getElementById("chart_data_input");
                if (chartDataInput && chartDataInput.value) {
                    try {
                        const chartData = JSON.parse(chartDataInput.value);
                        Object.keys(chartData).forEach((key) => {
                            const chart = chartData[key];
                            // Handle both key formats for field/type inference
                            if (!chart.field || !chart.type) {
                                const parts = key.includes('_') ? key.split('_') : key.split('-');
                                if (parts.length >= 2) {
                                    chart.field = chart.field || parts[0];
                                    chart.type = chart.type || parts[1];
                                }
                            }
                            if (chart.field) {
                                const type = chart.type === "pie" ? "pie" : "bar";
                                if (!_chartConfig[chart.field]) _chartConfig[chart.field] = [];
                                if (!_chartConfig[chart.field].includes(type)) _chartConfig[chart.field].push(type);
                                if (chart.roles && Array.isArray(chart.roles)) {
                                    this.selectedRoles = Array.from(new Set([...this.selectedRoles, ...chart.roles]));
                                }
                            }
                        });
                        renderChartConfigList();
                        setTimeout(() => { renderChartPreview(chartData); }, 500);
                    } catch (e) { console.error("Error parsing existing chart data:", e); }
                }
            },
            onTypeChange() { },
            async handleGambarChange(event) {
                const files = Array.from(event.target.files);
                const promises = files.map(async (file) => {
                    const compressed = await compressImage(file);
                    const id = getNextId();
                    _files[id] = compressed.file;
                    _gambarStore.push({ id, preview: compressed.preview, x: 50, y: 50, width: 200, height: 150, offsetX: 0, offsetY: 0, isExisting: false });
                });
                await Promise.all(promises);
                renderGambarPreviews(); renderPagePreview();
                event.target.value = "";
            },
            addField() { if (!this.selectedField || _chartConfig[this.selectedField]) return; addFieldToConfig(this.selectedField, ["pie", "bar"]); this.selectedField = ""; },
            async generateChart() {
                const config = {};
                Object.keys(_chartConfig).forEach(f => { if (_chartConfig[f].length > 0) config[f] = _chartConfig[f]; });
                if (Object.keys(config).length === 0) { alert("Pilih minimal satu field data dan tipe grafik"); return; }
                this.isGeneratingChart = true;
                try {
                    const roles = JSON.stringify(this.selectedRoles);
                    const url = `${window.chartGenerateUrl}?config=${encodeURIComponent(JSON.stringify(config))}&roles=${encodeURIComponent(roles)}`;
                    const response = await fetch(url); 
                    const data = await response.json();
                    // Add roles info for persistence
                    Object.keys(data).forEach(key => { data[key].roles = this.selectedRoles; });
                    document.getElementById("chart_data_input").value = JSON.stringify(data);
                    renderChartPreview(data);
                } catch (e) { alert("Gagal generate grafik: " + e.message); } finally { this.isGeneratingChart = false; }
            }
        };
    }

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

    window.initProfileCreateForm = function () {
        console.log("[RTE] initProfileCreateForm called");

        // Grab existing content BEFORE clearing (handles old() data)
        var container = document.getElementById("div_editor1");
        var initialDescriptionHtml = container ? container.innerHTML || "" : "";

        let rteRetries = 0;
        function initRTE() {
            if (typeof RichTextEditor === "undefined") {
                if (rteRetries++ < 100) setTimeout(initRTE, 100);
                return;
            }
            const container = document.getElementById("div_editor1");
            if (!container) {
                if (rteRetries++ < 100) setTimeout(initRTE, 100);
                return;
            }
            try {
                // Clear container so RTE can initialize in a clean DOM
                const editorContainer = document.getElementById("div_editor1");
                if (editorContainer) editorContainer.innerHTML = "";

                editor1 = new RichTextEditor("#div_editor1", {
                    base_url: "/cms_rte",
                    editorBodyCssClass: "rte-content-body",
                    file_upload_handler: function (file, callback, errorCallback) {
                        const fd = new FormData(); fd.append("file", file); fd.append("_token", window.csrfToken);
                        fetch(window.rteUploadUrl, { method: "POST", body: fd })
                            .then(r => r.json()).then(res => callback(res.url))
                            .catch(err => { console.error(err); alert("Upload gagal."); if (errorCallback) errorCallback(err); });
                    }
                });

                // Set initial content back
                if (initialDescriptionHtml) {
                    setRTEContent(editor1, initialDescriptionHtml);
                }

                const obs = new MutationObserver(() => renderPagePreview());
                obs.observe(document.querySelector("#div_editor1"), { subtree: true, childList: true, characterData: true });
            } catch (e) { console.error("[RTE] Error:", e); }
        }
        setTimeout(initRTE, 500);

        const titleInp = document.querySelector('[name="title"]');
        if (titleInp) titleInp.addEventListener("input", renderPagePreview);
        const ltInp = document.querySelector('[name="link_text"]');
        if (ltInp) ltInp.addEventListener("input", renderPagePreview);
        const luInp = document.querySelector('[name="link_url"]');
        if (luInp) luInp.addEventListener("input", renderPagePreview);

        document.getElementById("pageForm").addEventListener("submit", function (e) {
            if (editor1) document.getElementById("description_input").value = editor1.getHTMLCode();
            const gFiles = _gambarStore.map(i => _files[i.id]).filter(Boolean);
            if (gFiles.length) {
                const dt = new DataTransfer(); gFiles.forEach(f => dt.items.add(f));
                const fi = document.createElement("input"); fi.type = "file"; fi.name = "images[]"; fi.multiple = true; fi.files = dt.files; fi.className = "hidden";
                e.target.appendChild(fi);
            }
            _gambarStore.forEach(img => {
                const pos = `${img.x || 50}% ${img.y || 50}%`;
                [{ n: "image_positions[]", v: pos }, { n: "image_widths[]", v: img.width || 200 }, { n: "image_heights[]", v: img.height || 150 }, { n: "image_offset_x[]", v: img.offsetX || 0 }, { n: "image_offset_y[]", v: img.offsetY || 0 }]
                    .forEach(f => { const inp = document.createElement("input"); inp.type = "hidden"; inp.name = f.n; inp.value = f.v; e.target.appendChild(inp); });
            });
            
            // Only disable the original gambar_files input, NOT the dynamically created images[]
            e.target.querySelectorAll('input[type="file"][name="gambar_files"]').forEach(i => i.disabled = true);
            
            // Disable static image_positions_input to avoid double-submit with array inputs
            const staticImgPosInput = document.getElementById("image_positions_input");
            if (staticImgPosInput) staticImgPosInput.disabled = true;

            const btn = document.getElementById("submitBtn");
            if (btn) { btn.disabled = true; btn.innerHTML = "Menyimpan..."; }
        });

        // Zoom Controls
        let zoom = 1;
        const up = document.getElementById('zoomInBtn'), down = document.getElementById('zoomOutBtn'), res = document.getElementById('zoomResetBtn'), level = document.getElementById('zoomLevel'), cont = document.getElementById('preview-container');
        if (up) up.onclick = () => { if (zoom < 2) { zoom += 0.1; cont.style.transform = `scale(${zoom})`; level.textContent = Math.round(zoom * 100) + '%'; } };
        if (down) down.onclick = () => { if (zoom > 0.1) { zoom -= 0.1; cont.style.transform = `scale(${zoom})`; level.textContent = Math.round(zoom * 100) + '%'; } };
        if (res) res.onclick = () => { zoom = 1; cont.style.transform = `scale(1)`; level.textContent = '100%'; };
    };

    window.profilePageForm = profilePageForm;
    window.addFieldToConfig = addFieldToConfig;
    window.renderChartConfigList = renderChartConfigList;
    window.renderChartPreview = renderChartPreview;
    window.removeChartFromPreview = removeChartFromPreview;
    window.removeFieldFromConfig = removeFieldFromConfig;
    window.toggleChartTypeForField = toggleChartTypeForField;

    window.previewLogo = function (input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = (e) => {
                document.getElementById("logo_preview_img").src = e.target.result;
                document.getElementById("logo_preview").classList.remove("hidden");
                document.getElementById("logo-upload-area").classList.add("hidden");
            };
            reader.readAsDataURL(input.files[0]);
        }
    };
    window.removeLogo = function () {
        document.getElementById("logo_input").value = "";
        document.getElementById("logo_preview").classList.add("hidden");
        document.getElementById("logo-upload-area").classList.remove("hidden");
    };
})();
