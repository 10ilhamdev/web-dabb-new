/* global $, jQuery */

function penggunaManager() {
    return {
        deleteModal: { open: false, id: null, name: '' },
        openDeleteModal(id, name) {
            this.deleteModal = { open: true, id, name };
        }
    };
}

// Expose to Alpine (since Alpine may have evaluated x-data before this loads)
window.penggunaManager = penggunaManager;

$(function () {
    if (!$('#tablePengguna').length) return;

    const i18n = window.penggunaI18n || {};

    // Custom filtering: role column (3) and status column (4)
    $.fn.dataTable.ext.search.push(function (settings, data) {
        if (settings.nTable.id !== 'tablePengguna') return true;

        const roleVal = ($('#filter-role').val() || '').toLowerCase();
        const statusVal = ($('#filter-status').val() || '').toLowerCase();

        const roleCell = (data[3] || '').toLowerCase();
        const statusCell = (data[4] || '').toLowerCase();

        if (roleVal && roleCell.indexOf(roleVal) === -1) return false;

        const matchesStatus = function (val) {
            if (!val) return true;
            if (val === 'verified') {
                return statusCell.indexOf('verif') !== -1 || statusCell.indexOf('terverifik') !== -1;
            }
            if (val === 'pending') {
                return statusCell.indexOf('pending') !== -1
                    || statusCell.indexOf('menunggu') !== -1
                    || statusCell.indexOf('belum') !== -1;
            }
            return true;
        };

        if (!matchesStatus(statusVal)) return false;

        return true;
    });

    const table = $('#tablePengguna').DataTable({
        columnDefs: [
            { orderable: false, targets: [0, 6] }
        ],
        order: [[5, 'desc']],
        dom:
            '<"dt-top-row"<"dataTables_length"l><"dt-top-right"fB>>' +
            't' +
            '<"dt-bottom-row"<"dataTables_info"i><"dataTables_paginate"p>>',
        buttons: [
            {
                extend: 'collection',
                text: i18n.btnExport || 'Export',
                className: 'btn-export-dropdown',
                buttons: [
                    {
                        extend: 'copyHtml5',
                        text: i18n.btnCopy || 'Copy',
                        exportOptions: { columns: [0, 1, 2, 3, 4, 5] }
                    },
                    {
                        extend: 'csvHtml5',
                        text: i18n.btnCsv || 'CSV',
                        filename: 'pengguna',
                        exportOptions: { columns: [0, 1, 2, 3, 4, 5] }
                    },
                    {
                        extend: 'excelHtml5',
                        text: i18n.btnExcel || 'Excel',
                        filename: 'pengguna',
                        exportOptions: { columns: [0, 1, 2, 3, 4, 5] }
                    },
                    {
                        text: i18n.btnWord || 'Word',
                        className: 'btn-export-word',
                        action: function (e, dt) {
                            exportToWord(dt);
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        text: i18n.btnPdf || 'PDF',
                        filename: 'pengguna',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        exportOptions: { columns: [0, 1, 2, 3, 4, 5] }
                    },
                    {
                        extend: 'print',
                        text: i18n.btnPrint || 'Print',
                        exportOptions: { columns: [0, 1, 2, 3, 4, 5] }
                    }
                ]
            }
        ],
        initComplete: function () {
            // Inject Add User button into the top-right toolbar next to search
            const $topRight = $('#tablePengguna_wrapper .dt-top-right');
            if ($topRight.length && !$topRight.find('.btn-add-user').length && i18n.urlCreate) {
                const label = i18n.btnAddUser || 'Tambah Pengguna';
                const $btn = $(
                    '<a class="btn-add-user" href="' + i18n.urlCreate + '">' +
                        '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>' +
                        '</svg>' +
                        '<span></span>' +
                    '</a>'
                );
                $btn.find('span').text(label);
                $topRight.append($btn);
            }
        }
    });

    // Wire filter changes to redraw DataTable
    $('#filter-role, #filter-status').on('change', function () {
        table.draw();
    });
});

// Simple Word export: build a minimal .doc (HTML with Word MIME) from the table
function exportToWord(dt) {
    const header =
        '<html xmlns:o="urn:schemas-microsoft-com:office:office" ' +
        'xmlns:w="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40">' +
        '<head><meta charset="utf-8"><title>Pengguna</title></head><body>';
    const footer = '</body></html>';

    // Rebuild a simple HTML table from DT data (exclude Action column = index 6)
    let html = '<table border="1" cellspacing="0" cellpadding="6" style="border-collapse:collapse;font-family:Arial,sans-serif;font-size:11pt;">';
    const cols = [0, 1, 2, 3, 4, 5];
    const headers = dt.columns(cols).header().toArray();
    html += '<thead><tr>';
    headers.forEach(function (th) {
        html += '<th style="background:#f3f4f6;text-align:left;">' + (th.innerText || th.textContent || '').trim() + '</th>';
    });
    html += '</tr></thead><tbody>';
    dt.rows({ search: 'applied' }).every(function () {
        const $node = $(dt.row(this).node());
        html += '<tr>';
        cols.forEach(function (c) {
            const cellText = $node.find('td').eq(c).text().trim();
            html += '<td>' + escapeHtml(cellText) + '</td>';
        });
        html += '</tr>';
    });
    html += '</tbody></table>';

    const sourceHTML = header + html + footer;
    const blob = new Blob(['\ufeff', sourceHTML], { type: 'application/msword' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'pengguna.doc';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

function escapeHtml(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}
