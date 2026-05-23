/* global $, jQuery, ApexCharts, pdfMake */

function escapeHtml(str) {
    return String(str)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#39;");
}

function readTableData(dt) {
    var headers = [], rows = [];
    dt.columns([0,1,2,3,4,5]).header().each(function(th){
        headers.push((th.innerText || th.textContent || "").trim());
    });
    dt.rows({ search: "applied" }).every(function(){
        var $node = $(dt.row(this).node()), $cells = $node.find("td"), row = [];
        for (var c = 0; c < 6; c++) {
            var cellText = $cells.eq(c).text().trim().replace(/\s+/g, ' ');
            row.push(cellText);
        }
        rows.push(row);
    });
    return { headers: headers, rows: rows };
}

function getLocale() {
    return $("html").attr("lang") || "id";
}
function isIndonesian() {
    return getLocale() === "id";
}

function i18nTitle() {
    return window.kunjunganI18n?.title || (isIndonesian() ? "LAPORAN PENDAFTARAN KUNJUNGAN" : "VISIT REGISTRATION REPORT");
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

var _logoLoadCache = null;
function loadLogoBase64(callback) {
    if (_logoLoadCache) { callback(_logoLoadCache); return; }
    var img = new Image();
    img.onload = function(){
        try {
            var c = document.createElement("canvas");
            c.width = img.naturalWidth || 120; c.height = img.naturalHeight || 120;
            c.getContext("2d").drawImage(img, 0, 0);
            var d = c.toDataURL("image/png");
            _logoLoadCache = d;
            callback(d);
        } catch(e) { _logoLoadCache = ""; callback(""); }
    };
    img.onerror = function(){ _logoLoadCache = ""; callback(""); };
    img.src = "/image/logo_anri.png";
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
    setTimeout(function(){ URL.revokeObjectURL(url); }, 2000);
}

function buildTableHTML(dt) {
    var data = readTableData(dt);
    var colNames = data.headers;
    var colWidths = [35, 150, 120, 100, 70, 90];

    var html = '<table style="width:100%;border-collapse:collapse;font-size:9.5pt;" border="1" cellpadding="6" cellspacing="0">';
    html += "<thead><tr style=\"background:#174E93;color:white;\">";
    colNames.forEach(function(name, i){
        var w = colWidths[i] ? "width:" + colWidths[i] + "px;" : "";
        html += '<th style="text-align:center;' + w + 'padding:6px 10px;font-size:9pt;">' + escapeHtml(name) + "</th>";
    });
    html += "</tr></thead><tbody>";

    data.rows.forEach(function(row, idx){
        var bg = idx % 2 === 0 ? "#ffffff" : "#f3f6f9";
        html += '<tr style="background:' + bg + ';">';
        row.forEach(function(cell, ci){
            var align = ci === 0 || ci === 4 ? "center" : "left";
            html += '<td style="text-align:' + align + ';padding:5px 8px;font-size:9pt;color:#374151;">' + escapeHtml(cell) + "</td>";
        });
        html += "</tr>";
    });

    html += "</tbody></table>";
    return html;
}

function buildHeaderHTML() {
    var logoSrc = (document.querySelector('img[src*="logo_anri"]') || {}).src || "/image/logo_anri.png";
    return (
        '<div style="margin-bottom:14px;">' +
        '<table style="width:100%;border-collapse:collapse;margin-bottom:8px;">' +
        "<tr>" +
        '<td style="width:62px;vertical-align:middle;">' +
        '<img src="' + logoSrc + '" alt="ANRI" style="height:52px;width:auto;display:block;" />' +
        "</td>" +
        '<td style="vertical-align:middle;padding-left:12px;">' +
        '<div style="font-weight:bold;font-size:12.5pt;color:#174E93;line-height:1.3;">' +
        i18nInstName() +
        "<br/>" +
        '<span style="font-size:9.5pt;color:#374151;font-weight:normal;">DABB &mdash; CMS Management</span>' +
        "</div>" +
        '<div style="font-size:8pt;color:#6b7280;margin-top:3px;">' + i18nAddress() + "</div>" +
        '<div style="font-size:8pt;color:#6b7280;">' + i18nDate() + "</div>" +
        "</td>" +
        "</tr>" +
        "</table>" +
        "</div>"
    );
}

function exportToWord(dt) {
    var css = [
        "body{font-family:Arial,sans-serif;margin:25px 20px;font-size:10pt;color:#111827;}",
        "table{width:100%;border-collapse:collapse;}th,td{padding:5px 8px;border:1px solid #d1d5db;}",
        "@page{margin:20mm;}div{page-break-after:always;}",
    ].join("");

    var html = [
        '<html xmlns:o="urn:schemas-microsoft-com:office:office"',
        'xmlns:w="urn:schemas-microsoft-com:office:word"',
        'xmlns="http://www.w3.org/TR/REC-html40">',
        '<head><meta charset="utf-8"><title>' + i18nTitle() + '</title>',
        "<style>" + css + "</style></head><body>",
        buildHeaderHTML(),
        buildTableHTML(dt),
        "</body></html>",
    ].join("");

    var fname = isIndonesian()
        ? "Laporan-Pendaftaran-Kunjungan-DABB.doc"
        : "Visit-Registration-Report-DABB.doc";
    downloadBlob(new Blob(["\uFEFF" + html], { type: "application/msword" }), fname);
}

function exportToPDF(dt) {
    var data = readTableData(dt);
    var colWidths = [25, 140, 130, 90, 60, 80];

    var body = [
        data.headers.map(function(h, i) {
            return { text: h, fontSize: 10, bold: true, color: "white", fillColor: "#174E93", alignment: i === 0 || i === 4 ? "center" : "left" };
        })
    ];

    data.rows.forEach(function(row, idx){
        var bg = idx % 2 === 0 ? "#ffffff" : "#f3f6f9";
        body.push(row.map(function(cell, ci) {
            return { text: cell, fontSize: 9, alignment: ci === 0 || ci === 4 ? "center" : "left", fillColor: bg, color: ci === 1 ? "#111827" : "#374151", bold: ci === 1 };
        }));
    });

    var fname = isIndonesian()
        ? "Laporan-Pendaftaran-Kunjungan-DABB.pdf"
        : "Visit-Registration-Report-DABB.pdf";

    loadLogoBase64(function(logoDataUrl){
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
                        { width: 90, text: "" },
                        {
                            width: "auto",
                            alignment: "center",
                            table: {
                                headerRows: 1,
                                widths: colWidths,
                                body: body,
                            },
                        },
                        { width: 90, text: "" },
                    ],
                },
            ],
            footer: function(page, count){
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
        } catch(e) {
            alert('PDF Error: ' + e.message);
        }
    });
}

function exportToCSV(dt) {
    var data = readTableData(dt);
    var lines = [data.headers.join(",")];
    data.rows.forEach(function(row){
        lines.push(row.map(function(c){ return '"' + (c||"").replace(/"/g,'""') + '"'; }).join(","));
    });
    var content = [
        '"' + i18nInstName() + '"',
        '"' + i18nAddress() + '"',
        "",
        '"' + i18nTitle() + '"',
        ""
    ].concat(lines).join("\n");

    var fname = isIndonesian()
        ? "Laporan-Pendaftaran-Kunjungan-DABB.csv"
        : "Visit-Registration-Report-DABB.csv";
    downloadBlob(new Blob(["\uFEFF" + content], { type: "text/csv;charset=utf-8" }), fname);
}

function escXml(str) {
    return String(str == null ? "" : str)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#39;");
}

function exportToExcel(dt) {
    var isId = getLocale() === "id";
    var fname = isId
        ? "Laporan-Pendaftaran-Kunjungan-DABB.xls"
        : "Visit-Registration-Report-DABB.xls";

    loadLogoBase64(function(logoBase64) {
        var data = readTableData(dt);
        var tableRows = "";
        
        tableRows += "<tr style=\"background:#174E93;color:white;font-weight:bold;text-align:center;\">";
        data.headers.forEach(function(name) {
            tableRows += "<td style=\"padding:6px 10px;border:1px solid #dee2e6;text-align:center;\">" + escXml(name) + "</td>";
        });
        tableRows += "</tr>";

        data.rows.forEach(function(row, idx) {
            var bg = (idx % 2 === 0) ? "#FFFFFF" : "#D6E4F0";
            var textColor = (idx % 2 === 0) ? "#374151" : "#1a3a5c";
            tableRows += "<tr style=\"background:" + bg + ";\">";
            row.forEach(function(cell, ci) {
                var align = ci === 0 || ci === 4 ? "center" : "left";
                tableRows += "<td style=\"padding:5px 10px;border:1px solid #dee2e6;text-align:" + align + ";color:" + textColor + ";\">" + escXml(cell) + "</td>";
            });
            tableRows += "</tr>";
        });

        var htmlContent =
            '<html xmlns:ns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel">' +
            '<head>' +
            '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>' +
            '<title>' + i18nTitle() + '</title>' +
            '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Kunjungan</x:Name><x:WorksheetOptions><x:Print><x:ValidPrinterInfo/></x:Print></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->' +
            '<style>body{font-family:Calibri,sans-serif;margin:0;padding:0;}table{border-collapse:collapse;width:100%;}td{padding:4px 6px;border:1px solid #dee2e6;vertical-align:middle;}</style>' +
            '</head><body><table>' +
            '<tr><td colspan="6" style="text-align:center;padding:12px;border:none;font-size:22pt;font-weight:bold;color:#174E93;">ANRI - Depot Arsip Berkelanjutan Bandung</td></tr>' +
            '<tr><td colspan="6" style="text-align:center;padding:4px 6px;border:none;font-size:14pt;font-weight:bold;color:#174E93;">' + escXml(i18nInstName()) + '</td></tr>' +
            '<tr><td colspan="6" style="text-align:center;padding:2px 6px;border:none;font-size:10pt;color:#374151;">DABB \u2014 CMS Management</td></tr>' +
            '<tr><td colspan="6" style="text-align:center;padding:2px 6px;border:none;font-size:9pt;color:#6b7280;">' + escXml(i18nAddress()) + '</td></tr>' +
            '<tr><td colspan="6" style="text-align:center;padding:2px 6px;border:none;font-size:9pt;color:#9ca3af;">' + escXml(i18nDate()) + '</td></tr>' +
            '<tr><td colspan="6" style="text-align:center;padding:6px;border:none;"><span style="font-size:11pt;font-weight:bold;color:#174E93;">' + escXml(i18nTitle()) + '</span></td></tr>' +
            '</table><table>' + tableRows + '</table></body></html>';

        var blob = new Blob(["\uFEFF" + htmlContent], { type: "application/vnd.ms-excel" });
        downloadBlob(blob, fname);
    });
}

function exportToCopy(dt) {
    var data = readTableData(dt);
    var lines = [data.headers.join("\t")];
    data.rows.forEach(function(row){ lines.push(row.join("\t")); });
    if (navigator.clipboard) navigator.clipboard.writeText(lines.join("\n")).catch(function(){});
}

function exportToPrint(dt) {
    var w = window.open("", "_blank");
    if (!w) return;
    w.document.write([
        '<!DOCTYPE html><html><head><meta charset="utf-8"><title>' + i18nTitle() + '</title>',
        "<style>",
        "body{font-family:Arial,sans-serif;margin:20px 18px;font-size:10pt;color:#111827;}",
        "table{width:100%;border-collapse:collapse;margin-top:8px;}",
        "th,td{padding:5px 8px;border:1px solid #d1d5db;}",
        "th{background:#174E93;color:white;font-size:8.5pt;text-align:center;padding:6px 8px;}",
        "tr:nth-child(even){background:#f3f6f9;}",
        "@media print{@page{size:A4 landscape;margin:15mm;}}",
        "</style>",
        "</head><body>",
        buildHeaderHTML(),
        buildTableHTML(dt),
        "</body></html>",
    ].join(""));
    w.document.close();
    w.print();
}

$(function () {
    $.fn.dataTable.ext.errMode = 'none';
    if (!$("#tableKunjungan").length) return;

    var i18n = window.kunjunganI18n || {};

    var table = $("#tableKunjungan").DataTable({
        columnDefs: [{ orderable: false, targets: [6] }],
        order: [],
        language: {
            search: "",
            searchPlaceholder: window.LaravelDT?.dtSearchPlaceholder || i18n.dtSearchPlaceholder || "Cari...",
            lengthMenu: "_MENU_",
            info:        window.LaravelDT?.dtInfo        || "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty:   window.LaravelDT?.dtInfoEmpty   || "Tidak ada data",
            infoFiltered:window.LaravelDT?.dtInfoFiltered || "(difilter dari _MAX_ total data)",
            zeroRecords: window.LaravelDT?.dtZeroRecords || "Tidak ada data ditemukan",
            paginate: {
                first: "&laquo;",
                previous: "&lsaquo;",
                next: "&rsaquo;",
                last: "&raquo;",
            },
        },
        dom:
            '<"dt-top-row"<"dataTables_length"l><"dt-top-right"fB>>' +
            "t" +
            '<"dt-bottom-row"<"dataTables_info"i><"dataTables_paginate"p>>',
        buttons: [
            {
                extend: "collection",
                text:
                    '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>' +
                    "</svg> " + (i18n.btnExport || "Export"),
                className: "btn-export-dropdown",
                buttons: [
                    { text: i18n.btnCopy  || "Copy",   action: function(e, dt){ exportToCopy(dt);  } },
                    { text: i18n.btnCsv   || "CSV",    action: function(e, dt){ exportToCSV(dt);   } },
                    { text: i18n.btnExcel || "Excel",  action: function(e, dt){ exportToExcel(dt); } },
                    { text: i18n.btnWord  || "Word",   action: function(e, dt){ exportToWord(dt);  } },
                    { text: i18n.btnPdf   || "PDF",    action: function(e, dt){ exportToPDF(dt);   } },
                    { text: i18n.btnPrint || "Print",  action: function(e, dt){ exportToPrint(dt); } },
                ],
            },
        ],
    });

    // ApexCharts Initialization
    if (window.kunjunganChartData) {
        var pieData = window.kunjunganChartData.pieData || [];
        var pieLabels = window.kunjunganChartData.pieLabels || [];
        var lineLabels = window.kunjunganChartData.lineLabels || [];
        var lineSeries = window.kunjunganChartData.lineSeries || [];

        if (document.querySelector("#pieChart") && pieData.length) {
            var pieOptions = {
                series: pieData,
                chart: {
                    type: 'pie',
                    height: 280,
                    toolbar: { show: true, tools: { download: true } }
                },
                labels: pieLabels,
                colors: ['#22c55e', '#a855f7', '#f59e0b'],
                legend: {
                    position: 'bottom',
                    fontSize: '12px',
                    fontFamily: 'Inter, sans-serif',
                    markers: { width: 12, height: 12, radius: 12 }
                },
                dataLabels: { enabled: true, formatter: function(val, opts) { return opts.w.config.series[opts.seriesIndex] + ' ' + (window.kunjunganI18n?.labelOrg || 'org'); } },
                tooltip: { y: { formatter: function(val) { return val + ' ' + (window.kunjunganI18n?.labelOrg || 'org'); } } }
            };
            var pieChart = new ApexCharts(document.querySelector("#pieChart"), pieOptions);
            pieChart.render();
            setTimeout(function() {
                window.dispatchEvent(new Event('resize'));
            }, 150);
        }

        if (document.querySelector("#lineChart") && lineSeries.length) {
            var lineOptions = {
                series: [{ name: window.kunjunganI18n?.lineSeriesName || 'Peserta Kunjungan', data: lineSeries }],
                chart: {
                    type: 'area',
                    height: 280,
                    toolbar: { show: true }
                },
                colors: ['#0ea5e9'],
                fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.3, opacityTo: 0.05, stops: [0, 90, 100] } },
                stroke: { curve: 'smooth', width: 3 },
                xaxis: {
                    categories: lineLabels,
                    tickAmount: window.innerWidth < 768 ? 6 : undefined,
                    labels: {
                        hideOverlappingLabels: true,
                        style: { colors: '#94a3b8', fontSize: '11px', fontFamily: 'Inter' }
                    }
                },
                yaxis: { labels: { style: { colors: '#94a3b8', fontSize: '11px', fontFamily: 'Inter' }, formatter: function(v) { return Math.round(v); } } },
                grid: { borderColor: '#f1f5f9', strokeDashArray: 3 },
                tooltip: { x: { show: true }, y: { formatter: function(v) { return v + ' ' + (window.kunjunganI18n?.labelOrg || 'org'); } } }
            };
            var lineChart = new ApexCharts(document.querySelector("#lineChart"), lineOptions);
            lineChart.render();
            setTimeout(function() {
                window.dispatchEvent(new Event('resize'));
            }, 150);
        }
    }
});
