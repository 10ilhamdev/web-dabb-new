/* global $, jQuery */

/* ═══════════════════════════════════════════════════════════════════════════
   Export — Word / PDF / CSV / Excel / Copy / Print (roles page)
══════════════════════════════════════════════════════════════════════════════ */
function escapeHtml(str) {
    return String(str)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#39;");
}

function confirmDeleteRole(form, roleName) {
    if (window.Swal) {
        Swal.fire({
            title: isIndonesian() ? 'Apakah Anda yakin?' : 'Are you sure?',
            text: isIndonesian()
                ? 'Peran "' + roleName + '" akan dihapus beserta seluruh kolom tabel dan profil fisik penggunanya!'
                : 'Role "' + roleName + '" will be deleted along with all its table columns and user profiles!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#6B7280',
            confirmButtonText: isIndonesian() ? 'Ya, Hapus!' : 'Yes, Delete!',
            cancelButtonText: isIndonesian() ? 'Batal' : 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    } else {
        if (confirm(isIndonesian()
            ? 'Apakah Anda yakin ingin menghapus peran "' + roleName + '"?'
            : 'Are you sure you want to delete role "' + roleName + '"?')) {
            form.submit();
        }
    }
}

function getLocale() {
    return $("html").attr("lang") || "id";
}
function isIndonesian() {
    return getLocale() === "id";
}

function i18nTitle() {
    return isIndonesian() ? "DAFTAR PERAN SISTEM" : "SYSTEM ROLE LIST";
}
function i18nInstName() {
    return isIndonesian() ? "Depot Arsip Berkelanjutan Bandung" : "Continuing Archive Depot Bandung";
}
function i18nAddress() {
    return isIndonesian()
        ? "Jl. Ciwastra, Mekarjaya, Kec. Rancasari, Kota Bandung 40292, Jawa Barat. Indonesia"
        : "Jl. Ciwastra, Mekarjaya, Rancasari, Bandung 40292, West Java, Indonesia";
}
function i18nDate() {
    if (isIndonesian()) {
        return new Date().toLocaleDateString("id-ID", { day: "2-digit", month: "long", year: "numeric" });
    }
    return new Date().toLocaleDateString("en-US", { year: "numeric", month: "long", day: "2-digit" });
}

function downloadBlob(blob, filename) {
    var url = URL.createObjectURL(blob);
    var a = document.createElement("a");
    a.href = url;
    a.download = filename;
    a.style.display = "none";
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    setTimeout(function () { URL.revokeObjectURL(url); }, 2000);
}

function buildTableHTML(dt) {
    var isId = isIndonesian();
    var colNames = [
        isId ? "No" : "No",
        isId ? "Nama" : "Name",
        isId ? "Label" : "Label",
        isId ? "Tabel" : "Table",
        isId ? "Kolom" : "Columns",
        isId ? "Tipe" : "Type",
        isId ? "Pengguna" : "Users",
    ];
    var rows = dt.rows({ search: "applied" });
    var html = '<table style="width:100%;border-collapse:collapse;font-size:9.5pt;" border="1" cellpadding="6" cellspacing="0">';
    html += "<thead><tr style=\"background:#174E93;color:white;\">";
    colNames.forEach(function (name) {
        html += '<th style="text-align:center;padding:6px 10px;font-size:9pt;">' + name + "</th>";
    });
    html += "</tr></thead><tbody>";
    rows.every(function () {
        var $cells = $(this.node()).find("td");
        var cells = [];
        $cells.each(function () { cells.push($(this).clone().find(".arrow-icon").remove().end().text().trim()); });
        if (cells.length < 8) return;
        var bg = this[0] % 2 === 0 ? "#ffffff" : "#f3f6f9";
        html += '<tr style="background:' + bg + ';">';
        for (var c = 1; c < 8; c++) {
            var isCenter = (c === 1 || c === 5 || c === 6 || c === 4);
            html += '<td style="' + (isCenter ? 'text-align:center;' : '') + 'padding:5px 10px;color:#374151;font-size:9pt;">' + escapeHtml(cells[c]) + "</td>";
        }
        html += "</tr>";
    });
    html += "</tbody></table>";
    return html;
}

function buildHeaderHTML(logoSrc) {
    logoSrc = logoSrc || (document.querySelector('img[src*="logo_anri"]') || {}).src || "/image/logo_anri.png";
    if (logoSrc.indexOf("http") !== 0 && logoSrc.indexOf("file://") !== 0) {
        logoSrc = window.location.origin + (logoSrc.indexOf("/") === 0 ? "" : "/") + logoSrc;
    }
    return (
        '<div style="margin-bottom:14px;">' +
        '<table style="width:100%;border-collapse:collapse;margin-bottom:8px;">' +
        "<tr>" +
        '<td style="width:62px;vertical-align:middle;">' +
        '<img src="' + logoSrc + '" alt="ANRI" width="52" height="52" style="width:52px;height:52px;display:block;" />' +
        "</td>" +
        '<td style="vertical-align:middle;padding-left:12px;">' +
        '<div style="font-weight:bold;font-size:12.5pt;color:#174E93;line-height:1.3;">' + i18nInstName() + "<br/>" +
        '<span style="font-size:9.5pt;color:#374151;font-weight:normal;">DABB &mdash; CMS Management</span></div>' +
        '<div style="font-size:8pt;color:#6b7280;margin-top:3px;">' + i18nAddress() + '</div>' +
        '<div style="font-size:8pt;color:#6b7280;">' + i18nDate() + '</div>' +
        "</td></tr></table></div>"
    );
}

function exportToWord(dt) {
    loadLogoBase64(function (logoBase64) {
        var hasLogo = logoBase64 && logoBase64.indexOf("base64,") !== -1;
        var logoRef = hasLogo ? "file:///logo_anri.png" : "";

        var html = [
            '<html xmlns:o="urn:schemas-microsoft-com:office:office"',
            'xmlns:w="urn:schemas-microsoft-com:office:word"',
            'xmlns="http://www.w3.org/TR/REC-html40">',
            '<head><meta charset="utf-8"><title>DABB - Peran</title>',
            "<style>body{font-family:Arial,sans-serif;margin:25px 20px;font-size:10pt;color:#111827;}table{width:100%;border-collapse:collapse;}th,td{padding:5px 8px;border:1px solid #d1d5db;}@page{margin:20mm;}</style>",
            "</head><body>", buildHeaderHTML(logoRef), buildTableHTML(dt), "</body></html>",
        ].join("");

        var mhtml = [
            'Mime-Version: 1.0',
            'Content-Type: multipart/related; boundary="NEXT.ITEM-BOUNDARY"',
            '',
            '--NEXT.ITEM-BOUNDARY',
            'Content-Type: text/html; charset="utf-8"',
            'Content-Location: file:///main.html',
            '',
            html,
            ''
        ];

        if (hasLogo) {
            var base64Data = logoBase64.split("base64,")[1];
            mhtml.push(
                '--NEXT.ITEM-BOUNDARY',
                'Content-Type: image/png',
                'Content-Transfer-Encoding: base64',
                'Content-Location: file:///logo_anri.png',
                '',
                base64Data,
                ''
            );
        }

        mhtml.push('--NEXT.ITEM-BOUNDARY--');

        var fileContent = mhtml.join('\r\n');
        var fname = isIndonesian() ? "Daftar-Peran-Sistem-Bandung-Sustainable-Archives-Depot.doc" : "System-Role-List-Bandung-Sustainable-Archives-Depot.doc";
        downloadBlob(new Blob(["﻿" + fileContent], { type: "application/msword" }), fname);
    });
}

function exportToCSV(dt) {
    var isId = isIndonesian();
    var headers = ["No", "Nama", "Label", "Tabel", "Kolom", "Tipe", "Pengguna"];
    var lines = [headers.join(",")];
    dt.rows({ search: "applied" }).every(function () {
        var $cells = $(this.node()).find("td");
        var cells = [];
        $cells.each(function () { cells.push($(this).clone().find(".arrow-icon").remove().end().text().trim()); });
        if (cells.length >= 8) lines.push('"' + cells.slice(1).join('","').replace(/"/g, '""') + '"');
    });
    var content = ['"' + i18nInstName() + '"', '"' + i18nAddress() + '"', ""].concat(lines).join("\n");
    var fname = isIndonesian() ? "Daftar-Peran-Sistem-Bandung-Sustainable-Archives-Depot.csv" : "System-Role-List-Bandung-Sustainable-Archives-Depot.csv";
    downloadBlob(new Blob(["﻿" + content], { type: "text/csv;charset=utf-8" }), fname);
}

function exportToExcel(dt) {
    var isId = isIndonesian();
    var colNames = ["No", "Nama", "Label", "Tabel", "Kolom", "Tipe", "Pengguna"];
    var tableRows = "";
    var hdrBg = "174E93";
    tableRows += "<tr style=\"background:#" + hdrBg + ";color:white;font-weight:bold;text-align:center;\">";
    colNames.forEach(function (name) { tableRows += "<td style=\"padding:6px 10px;border:1px solid #dee2e6;text-align:center;\">" + escapeHtml(name) + "</td>"; });
    tableRows += "</tr>";
    dt.rows({ search: "applied" }).every(function (idx) {
        var $cells = $(this.node()).find("td");
        var cells = [];
        $cells.each(function () { cells.push($(this).clone().find(".arrow-icon").remove().end().text().trim()); });
        if (cells.length < 8) return;
        var bg = idx % 2 === 0 ? "#FFFFFF" : "#D6E4F0";
        var textColor = idx % 2 === 0 ? "#374151" : "#1a3a5c";
        tableRows += "<tr style=\"background:" + bg + ";\">";
        var isCenterArr = [true, false, false, false, true, true, true];
        for (var c = 1; c < cells.length; c++) {
            var centerStyle = isCenterArr[c - 1] ? "text-align:center;" : "";
            tableRows += "<td style=\"padding:5px 10px;border:1px solid #dee2e6;" + centerStyle + "color:" + textColor + ";\">" + escapeHtml(cells[c]) + "</td>";
        }
        tableRows += "</tr>";
    });
    var htmlContent =
        '<html xmlns:ns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel">' +
        '<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/><title>DABB - Peran</title>' +
        '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Peran</x:Name><x:WorksheetOptions><x:Print><x:ValidPrinterInfo/></x:Print></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->' +
        '<style>body{font-family:Calibri,sans-serif;margin:0;padding:0;}table{border-collapse:collapse;width:100%;}td{padding:4px 6px;border:1px solid #dee2e6;vertical-align:middle;}</style></head><body><table>' +
        '<tr><td colspan="7" style="text-align:center;padding:12px;border:none;font-size:22pt;font-weight:bold;color:#174E93;">ANRI - Depot Arsip Berkelanjutan Bandung</td></tr>' +
        '<tr><td colspan="7" style="text-align:center;padding:4px 6px;border:none;font-size:14pt;font-weight:bold;color:#174E93;">' + escapeHtml(i18nInstName()) + '</td></tr>' +
        '<tr><td colspan="7" style="text-align:center;padding:2px 6px;border:none;font-size:10pt;color:#374151;">DABB — CMS Management</td></tr>' +
        '<tr><td colspan="7" style="text-align:center;padding:2px 6px;border:none;font-size:9pt;color:#6b7280;">' + escapeHtml(i18nAddress()) + '</td></tr>' +
        '<tr><td colspan="7" style="text-align:center;padding:2px 6px;border:none;font-size:9pt;color:#9ca3af;">' + escapeHtml(i18nDate()) + '</td></tr>' +
        '<tr><td colspan="7" style="text-align:center;padding:6px;border:none;"><span style="font-size:11pt;font-weight:bold;color:#174E93;">' + escapeHtml(i18nTitle()) + '</span></td></tr>' +
        '</table><table>' + tableRows + '</table></body></html>';
    var fname = isIndonesian() ? "Daftar-Peran-Sistem-Bandung-Sustainable-Archives-Depot.xls" : "System-Role-List-Bandung-Sustainable-Archives-Depot.xls";
    downloadBlob(new Blob(["﻿" + htmlContent], { type: "application/vnd.ms-excel" }), fname);
}

function exportToCopy(dt) {
    var isId = isIndonesian();
    var headers = ["No", "Nama", "Label", "Tabel", "Kolom", "Tipe", "Pengguna"];
    var lines = [headers.join("\t")];
    dt.rows({ search: "applied" }).every(function () {
        var $cells = $(this.node()).find("td");
        var cells = [];
        $cells.each(function () { cells.push($(this).clone().find(".arrow-icon").remove().end().text().trim()); });
        if (cells.length >= 8) lines.push(cells.slice(1, 8).join("\t"));
    });
    if (navigator.clipboard) navigator.clipboard.writeText(lines.join("\n")).catch(function () { });
}

function exportToPrint(dt) {
    var w = window.open("", "_blank");
    if (!w) return;
    w.document.write([
        '<!DOCTYPE html><html><head><meta charset="utf-8"><title>DABB - Peran</title>',
        "<style>body{font-family:Arial,sans-serif;margin:20px 18px;font-size:10pt;color:#111827;}table{width:100%;border-collapse:collapse;margin-top:8px;}th,td{padding:5px 8px;border:1px solid #d1d5db;}th{background:#174E93;color:white;font-size:8.5pt;text-align:center;padding:6px 8px;}tr:nth-child(even){background:#f3f6f9;}@media print{@page{size:A4 landscape;margin:15mm;}}</style>",
        "</head><body>", buildHeaderHTML(), buildTableHTML(dt), "</body></html>",
    ].join(""));
    w.document.close();
    w.print();
}

function readTableData(dt) {
    var headers = [], rows = [];
    dt.columns([1, 2, 3, 4, 5, 6, 7]).header().each(function (th) {
        headers.push((th.innerText || th.textContent || "").trim());
    });
    dt.rows({ search: "applied" }).every(function () {
        var $node = $(dt.row(this).node()), $cells = $node.find("td"), row = [];
        for (var c = 1; c < 8; c++) {
            row.push($cells.eq(c).clone().find(".arrow-icon").remove().end().text().trim());
        }
        rows.push(row);
    });
    return { headers: headers, rows: rows };
}

var _logoLoadCache = null;
function loadLogoBase64(callback) {
    if (_logoLoadCache) { callback(_logoLoadCache); return; }
    var imgEl = document.querySelector('img[src*="logo_anri"]');
    if (imgEl && imgEl.complete && imgEl.naturalWidth) {
        try {
            var c = document.createElement("canvas");
            c.width = imgEl.naturalWidth; c.height = imgEl.naturalHeight;
            c.getContext("2d").drawImage(imgEl, 0, 0);
            var d = c.toDataURL("image/png");
            _logoLoadCache = d;
            callback(d);
            return;
        } catch (e) {}
    }
    var img = new Image();
    img.onload = function () {
        try {
            var c = document.createElement("canvas");
            c.width = img.naturalWidth || 120; c.height = img.naturalHeight || 120;
            c.getContext("2d").drawImage(img, 0, 0);
            var d = c.toDataURL("image/png");
            _logoLoadCache = d;
            callback(d);
        } catch (e) { _logoLoadCache = ""; callback(""); }
    };
    img.onerror = function () { _logoLoadCache = ""; callback(""); };
    img.src = imgEl ? imgEl.src : "/image/logo_anri.png";
}

function exportToPDF(dt) {
    var data = readTableData(dt);
    var body = [
        data.headers.map(function (h, i) {
            return { text: h, fontSize: 10, bold: true, color: "white", fillColor: "#174E93", alignment: i === 0 || i === 4 || i === 5 || i === 6 ? "center" : "left" };
        })
    ];

    data.rows.forEach(function (row, idx) {
        var bg = idx % 2 === 0 ? "#ffffff" : "#f3f6f9";
        body.push(row.map(function (cell, ci) {
            return { text: cell, fontSize: 9, alignment: ci === 0 || ci === 4 || ci === 5 || ci === 6 ? "center" : "left", fillColor: bg, color: "#374151" };
        }));
    });

    var fname = isIndonesian()
        ? "Daftar-Peran-Sistem-Bandung-Sustainable-Archives-Depot.pdf"
        : "System-Role-List-Bandung-Sustainable-Archives-Depot.pdf";

    loadLogoBase64(function (logoDataUrl) {
        var headerBlock = {
            stack: [
                logoDataUrl
                    ? { image: logoDataUrl, width: 52, alignment: "center", margin: [0, 0, 0, 4] }
                    : { text: "", width: 52 },
                { text: i18nInstName(), fontSize: 13, bold: true, color: "#174E93", alignment: "center", margin: [0, 2, 0, 2] },
                { text: i18nAddress(), fontSize: 8, color: "#6b7280", alignment: "center", margin: [0, 0, 0, 2] },
                { text: i18nDate(), fontSize: 8, color: "#9ca3af", alignment: "center", margin: [0, 0, 0, 0] },
            ],
            margin: [0, 0, 0, 8],
            alignment: "center",
        };

        var docDef = {
            pageSize: "A4",
            pageOrientation: "landscape",
            pageMargins: [18, 18, 18, 18],
            defaultStyle: { font: "Roboto" },
            content: [
                headerBlock,
                {
                    canvas: [{ type: "line", x1: 0, y1: 0, x2: 805, y2: 0, lineWidth: 2, lineColor: "#174E93" }],
                    margin: [0, 0, 0, 6],
                },
                {
                    text: i18nTitle(),
                    fontSize: 11,
                    bold: true,
                    alignment: "center",
                    margin: [0, 0, 0, 8],
                },
                {
                    columns: [
                        { width: 40, text: "" },
                        {
                            width: "auto",
                            alignment: "center",
                            table: {
                                headerRows: 1,
                                widths: [30, 100, 150, 150, 80, 80, 80],
                                body: body,
                            },
                        },
                        { width: 40, text: "" },
                    ],
                },
            ],
            footer: function (page, count) {
                return {
                    columns: [
                        { text: "DABB CMS — " + i18nDate(), fontSize: 7, color: "#9ca3af", margin: [18, 0, 0, 0] },
                        { text: page + " / " + count, fontSize: 7, color: "#9ca3af", alignment: "right", margin: [0, 0, 18, 0] },
                    ],
                    margin: [0, 4, 0, 0],
                };
            },
        };

        try {
            pdfMake.createPdf(docDef).download(fname);
        } catch (e) {
            alert('PDF Error: ' + e.message);
        }
    });
}

/* ═══════════════════════════════════════════════════════════════════════════
   DataTables Init
══════════════════════════════════════════════════════════════════════════════ */
$(document).ready(function () {
    if (!$("#tableRoles").length) {
        console.warn("[roles] #tableRoles not found");
        return;
    }

    var i18n = window.rolesI18n || {};

    /* Custom filter — disabled for now, re-enable after table works */
    // $.fn.dataTable.ext.search.push(function (settings, data) { return true; });

    var table = $("#tableRoles").DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: window.location.href,
            type: "GET",
            headers: { "X-Requested-With": "XMLHttpRequest" },
            dataSrc: function (json) {
                return json.data || [];
            },
            error: function (xhr, status, thrown) {
                console.error("[roles] AJAX error:", status, thrown, xhr.responseText);
            }
        },
        columnDefs: [
            { orderable: false, targets: [0, 7] },
            { className: "details-control", targets: 0 },
        ],
        columns: [
            {
                data: null,
                defaultContent: '<span class="arrow-icon"><svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>',
            },
            {
                data: null,
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                }
            },
            {
                data: 'name',
                render: function (data) {
                    return '<code class="text-sm font-mono bg-gray-100 px-2 py-1 rounded">' + data + '</code>';
                }
            },
            { data: 'label' },
            {
                data: 'table_name',
                render: function (data) {
                    if (!data) return '<span class="text-gray-400">—</span>';
                    return '<code class="font-mono bg-gray-50 px-1.5 py-0.5 rounded text-xs">' + data + '</code>';
                }
            },
            {
                data: 'columns_count',
                render: function (data) {
                    return '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-600 border border-blue-100">' + (data || 0) + ' ' + (i18n.columnsCount || 'columns') + '</span>';
                }
            },
            {
                data: 'is_system',
                render: function (data) {
                    if (data) {
                        return '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-50 text-red-600 border border-red-100">' + (i18n.typeSystem || 'System') + '</span>';
                    }
                    return '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 text-green-600 border border-green-100">' + (i18n.typeCustom || 'Custom') + '</span>';
                }
            },
            { data: 'users_count', defaultContent: '0' },
            {
                data: null,
                orderable: false,
                render: function (data) {
                    var editUrl = i18n.urlBaseEdit.replace(':id', data.id);
                    var deleteUrl = i18n.urlBaseDelete.replace(':id', data.id);
                    var html = '<div class="flex items-center justify-center gap-2">' +
                        '<a href="' + editUrl + '" class="inline-flex items-center justify-center w-8 h-8 bg-yellow-400 hover:bg-yellow-500 text-white rounded-md transition-colors" title="Edit">' +
                        '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg></a>';
                    if (!data.is_system) {
                        html += '<form action="' + deleteUrl + '" method="POST" class="inline delete-role-form" data-role-label="' + escapeHtml(data.label) + '">' +
                            '<input type="hidden" name="_token" value="' + i18n.csrfToken + '">' +
                            '<input type="hidden" name="_method" value="DELETE">' +
                            '<button type="submit" class="inline-flex items-center justify-center w-8 h-8 bg-red-500 hover:bg-red-600 text-white rounded-md transition-colors" title="Hapus">' +
                            '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button></form>';
                    }
                    html += '</div>';
                    return html;
                }
            }
        ],
        order: [[1, "asc"]],
        language: {
            search: "",
            searchPlaceholder: i18n.dtSearchPlaceholder || "",
            lengthMenu: "_MENU_",
            info: i18n.dtInfo || "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: i18n.dtInfoEmpty || "No entries",
            infoFiltered: i18n.dtInfoFiltered || "(filtered)",
            zeroRecords: i18n.dtZeroRecords || "No matching records found",
            paginate: { first: "&laquo;", previous: "&lsaquo;", next: "&rsaquo;", last: "&raquo;" },
            loadingRecords: "Memuat...",
        },
        dom: '<"dt-top-row"<"dataTables_length"l><"dt-top-right"fB>>t<"dt-bottom-row"<"dataTables_info"i><"dataTables_paginate"p>>',
        buttons: [
            {
                extend: "collection",
                text: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg> ' + (i18n.btnExport || "Export"),
                className: "btn-export-dropdown",
                buttons: [
                    { text: i18n.btnCopy || "Copy", action: function (e, dt) { exportToCopy(dt); } },
                    { text: i18n.btnCsv || "CSV", action: function (e, dt) { exportToCSV(dt); } },
                    { text: i18n.btnExcel || "Excel", action: function (e, dt) { exportToExcel(dt); } },
                    { text: i18n.btnWord || "Word", action: function (e, dt) { exportToWord(dt); } },
                    { text: i18n.btnPdf || "PDF", action: function (e, dt) { exportToPDF(dt); } },
                    { text: i18n.btnPrint || "Print", action: function (e, dt) { exportToPrint(dt); } },
                ],
            },
        ],
        initComplete: function () {
            var $tr = $("#tableRoles_wrapper .dt-top-right");
            if ($tr.length && i18n.urlCreate) {
                $tr.append(
                    '<a class="btn-add-role" href="' + i18n.urlCreate + '">' +
                    '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>' +
                    '<span>' + (i18n.btnAddRole || "Tambah") + '</span></a>'
                );
            }
        },
    });

    /* Handle role delete confirmation via event delegation */
    $("#tableRoles").on("submit", ".delete-role-form", function (e) {
        e.preventDefault();
        confirmDeleteRole(this, $(this).data("role-label"));
    });

    /* Expandable row details */
    $("#tableRoles tbody").on("click", "td.details-control", function () {
        var tr = $(this).closest("tr");
        var row = table.row(tr);
        if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass("shown");
        } else {
            var data = row.data();
            if (data && (data.columns_data || data.columns)) {
                var cols = data.columns_data || data.columns;
                if (typeof formatRolesColumns === "function") {
                    row.child(formatRolesColumns(cols)).show();
                } else {
                    row.child('<div class="role-columns-detail"><p class="text-sm text-gray-400 italic">Columns: ' + JSON.stringify(cols).substring(0, 100) + '</p></div>').show();
                }
                tr.addClass("shown");
            }
        }
    });

    /* Filter: type */
    $("#filter-type").on("change", function () {
        var val = $(this).val();
        if (!val) {
            table.column(6).search("").draw();
        } else if (val === "system") {
            table.column(6).search("System").draw();
        } else if (val === "custom") {
            table.column(6).search("Custom").draw();
        }
    });

    /* Filter: columns count */
    $("#filter-columns").on("change", function () {
        var val = $(this).val();
        if (!val) {
            table.column(5).search("").draw();
        } else if (val === "0") {
            table.column(5).search("0 ").draw();
        } else {
            table.column(5).search("").draw();
            $.fn.dataTable.ext.search.push(function (settings, data) {
                if (settings.nTable.id !== "tableRoles") return true;
                var text = data[5] || "";
                var count = parseInt(text) || 0;
                return count > 0;
            });
            table.draw();
            $.fn.dataTable.ext.search.pop();
        }
    });
});