function layananPublikForm(initialType = 'kunjungan', initialExtraData = null) {
    return {
        type: initialType,
        files: [],
        title_jadwal: initialExtraData?.title_jadwal || "",
        title_pengajuan: initialExtraData?.title_pengajuan || "",
        show_jadwal: initialExtraData?.show_jadwal ?? 1,
        show_pengajuan: initialExtraData?.show_pengajuan ?? 1,
        show_kalender: initialExtraData?.show_kalender ?? 1,
        show_form: initialExtraData?.show_form ?? 1,
        auto_today_date: initialExtraData?.auto_today_date ?? 0,
        jadwal_kunjungan: initialExtraData?.jadwal_kunjungan || "Senin - Kamis : 07:30 - 12:00 WIB dan 13:00 - 16:00 WIB\nJumat : 07:30 - 16:30 WIB",
        pengajuan_kunjungan: initialExtraData?.pengajuan_kunjungan || "Kapasitas dalam 1 (satu) hari Pagi maks. 2 kunjungan, Siang maks. 2 kunjungan. Mohon isi informasi rencana kunjungan Anda sesuai dengan jadwal yang tersedia. Adapun kepastian jadwal kunjungan akan diinformasikan kembali oleh petugas yang menangani.",
        kuota_harian: initialExtraData?.kuota_harian || (initialExtraData?.kuota_pagi ? (parseInt(initialExtraData.kuota_pagi) + parseInt(initialExtraData.kuota_siang)) : 4),
        libur_dates: initialExtraData?.libur_dates || [],
        tutup_slots: initialExtraData?.tutup_slots || [],
        form_fields: initialExtraData?.form_fields || [
            { id: 'name', label: 'Nama', type: 'text', required: true, options: '' },
            { id: 'email', label: 'Email', type: 'email', required: true, options: '' },
            { id: 'phone_office', label: 'No. Telpon Kantor', type: 'text', required: false, options: '' },
            { id: 'phone', label: 'No. Telpon', type: 'text', required: true, options: '' },
            { id: 'institution', label: 'Instansi', type: 'text', required: true, options: '' },
            { id: 'position', label: 'Jabatan (dalam Instansi)', type: 'text', required: true, options: '' },
            { id: 'visit_date', label: 'Tanggal Kunjungan', type: 'date', required: true, options: '' },
            { id: 'visit_time', label: 'Waktu Kunjungan', type: 'select', required: true, options: 'Pagi (07:30 - 12:00),Siang (13:00 - 16:00)' },
            { id: 'visitor_count', label: 'Jumlah Pengunjung', type: 'number', required: true, options: '' },
            { id: 'visit_purpose', label: 'Tujuan Kunjungan', type: 'select', required: true, options: 'Edukasi,Penelitian,Kunjungan Kerja,Lainnya' },
            { id: 'req_letter', label: 'Surat Permohonan', type: 'file', required: false, options: 'Format: pdf/doc, max 2MB' }
        ],

        toggleShow(field) {
            this[field] = this[field] == 1 ? 0 : 1;
        },

        addLibur() {
            this.libur_dates.push({ date: '', reason: 'Hari Libur / Tutup' });
        },
        removeLibur(index) {
            this.libur_dates.splice(index, 1);
        },
        addTutupSlot() {
            this.tutup_slots.push({ date: '', slot: 'pagi', max_quota: 0, reason: 'Slot Penuh / Ditutup' });
        },
        removeTutupSlot(index) {
            this.tutup_slots.splice(index, 1);
        },
        getMaxQuota(index) {
            let item = this.tutup_slots[index];
            if (!item || !item.date) return parseInt(this.kuota_harian) || 0;
            let otherSum = this.tutup_slots
                .filter((val, i) => i !== index && val.date === item.date)
                .reduce((sum, val) => sum + (parseInt(val.max_quota) || 0), 0);
            return Math.max(0, (parseInt(this.kuota_harian) || 0) - otherSum);
        },
        validateMaxQuota(index) {
            let maxAllowed = this.getMaxQuota(index);
            let item = this.tutup_slots[index];
            if (item && parseInt(item.max_quota) > maxAllowed) {
                item.max_quota = maxAllowed;
            }
        },
        addFormField() {
            this.form_fields.push({ id: 'field_' + Date.now(), label: 'Label Baru', type: 'text', required: false, options: '' });
        },
        removeFormField(index) {
            this.form_fields.splice(index, 1);
        },

        handleFiles(event) {
            const newFiles = Array.from(event.target.files);
            this.files = [...this.files, ...newFiles];
            this.renderPreviews();
        },

        renderPreviews() {
            const container = document.getElementById('file-previews');
            container.innerHTML = '';

            this.files.forEach((file, index) => {
                const div = document.createElement('div');
                div.className = 'relative group aspect-square rounded-lg overflow-hidden bg-gray-100 border border-gray-200';

                if (file.type.startsWith('image/')) {
                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(file);
                    img.className = 'w-full h-full object-cover';
                    div.appendChild(img);
                } else if (file.type.startsWith('video/')) {
                    const video = document.createElement('video');
                    video.src = URL.createObjectURL(file);
                    video.className = 'w-full h-full object-cover';
                    div.appendChild(video);
                }

                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity';
                removeBtn.innerHTML = '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
                removeBtn.onclick = () => {
                    this.files.splice(index, 1);
                    this.renderPreviews();
                };
                div.appendChild(removeBtn);

                container.appendChild(div);
            });
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialize RTE
    if (typeof RichTextEditor !== 'undefined') {
        const editor = new RichTextEditor("#div_editor1", {
            file_upload_handler: function(file, callback, optionalIndex, optionalFiles) {
                const formData = new FormData();
                formData.append('file', file);
                formData.append('_token', window.rteUploadUrl ? document.querySelector('meta[name="csrf-token"]').content : '');

                fetch(window.rteUploadUrl, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.url) {
                        callback(data.url);
                    }
                })
                .catch(error => console.error('Error uploading to RTE:', error));
            }
        });

        // Sync editor content to hidden input before submit
        const form = document.querySelector('form');
        form.onsubmit = function() {
            document.getElementById('description_input').value = editor.getHTMLCode();
        };
    }
});
