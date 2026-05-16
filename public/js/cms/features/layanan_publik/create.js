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
        laraska_hours: initialExtraData?.laraska_hours || "Senin - Jumat : 09:00 - 15:00 WIB\nIstirahat (Senin - Kamis) : 12:00 - 13:00 WIB\n(Jumat) : 11:30 - 13:00 WIB",
        maklumat_title: initialExtraData?.maklumat_title || "MAKLUMAT PELAYANAN",
        maklumat_content: initialExtraData?.maklumat_content || "\"Dengan ini, kami seluruh pelaksana kegiatan penyelamatan arsip dari dampak bencana menyatakan sanggup memberikan pelayanan LARASKA (Layanan Restorasi Arsip Keluarga) sesuai Standar Pelayanan yang telah ditetapkan dan apabila tidak menepati janji, kami siap menerima sanksi sesuai peraturan perundang-undangan yang berlaku.\"",
        maklumat_date: initialExtraData?.maklumat_date || "Jakarta, Juni 2024",
        maklumat_director: initialExtraData?.maklumat_director || "Direktur Pelindungan dan Penyelamatan Arsip",
        laraska_mech_title: initialExtraData?.laraska_mech_title || "Mekanisme Layanan LARASKA",
        file_name: initialExtraData?.file_name || "",
        laraska_step1_title: initialExtraData?.laraska_step1_title || "1. Pengajuan Layanan",
        laraska_step1_desc: initialExtraData?.laraska_step1_desc || "Pengguna layanan mengajukan permohonan restorasi arsip melalui website DABB atau datang langsung ke loket pelayanan.",
        laraska_step2_title: initialExtraData?.laraska_step2_title || "2. Pemeriksaan Kondisi Arsip",
        laraska_step2_desc: initialExtraData?.laraska_step2_desc || "Arsiparis memeriksa tingkat kerusakan arsip untuk menentukan metode restorasi yang tepat.",
        laraska_step3_title: initialExtraData?.laraska_step3_title || "3. Proses Restorasi Arsip",
        laraska_step3_desc: initialExtraData?.laraska_step3_desc || "Tim teknis melakukan perbaikan arsip keluarga di laboratorium restorasi DABB.",
        laraska_step4_title: initialExtraData?.laraska_step4_title || "4. Pengambilan Arsip",
        laraska_step4_desc: initialExtraData?.laraska_step4_desc || "Pengguna mengambil arsip yang telah selesai direstorasi beserta salinan digitalnya.",
        laraska_steps: initialExtraData?.laraska_steps || [
            { title: initialExtraData?.laraska_step1_title || "1. Pengajuan Layanan", desc: initialExtraData?.laraska_step1_desc || "Pengguna layanan mengajukan permohonan restorasi arsip melalui website DABB atau datang langsung ke loket pelayanan." },
            { title: initialExtraData?.laraska_step2_title || "2. Pemeriksaan Kondisi Arsip", desc: initialExtraData?.laraska_step2_desc || "Arsiparis memeriksa tingkat kerusakan arsip untuk menentukan metode restorasi yang tepat." },
            { title: initialExtraData?.laraska_step3_title || "3. Proses Restorasi Arsip", desc: initialExtraData?.laraska_step3_desc || "Tim teknis melakukan perbaikan arsip keluarga di laboratorium restorasi DABB." },
            { title: initialExtraData?.laraska_step4_title || "4. Pengambilan Arsip", desc: initialExtraData?.laraska_step4_desc || "Pengguna mengambil arsip yang telah selesai direstorasi beserta salinan digitalnya." }
        ],
        statis_hours: initialExtraData?.statis_hours || "Senin - Kamis : 08:30 - 15:00 WIB\nJumat : 08:30 - 15:30 WIB",
        statis_order_hours: initialExtraData?.statis_order_hours || "Senin - Kamis : 08:30 - 14:00 WIB\nJumat : 08:30 - 14:30 WIB",
        statis_stages: initialExtraData?.statis_stages || [
            { title: initialExtraData?.statis_stage1 || "Penelusuran Mandiri via JIKN / SIKN" },
            { title: initialExtraData?.statis_stage2 || "Konsultasi dengan Petugas Layanan" },
            { title: initialExtraData?.statis_stage3 || "Mengisi Formulir Permintaan" },
            { title: initialExtraData?.statis_stage4 || "Pencarian Arsip oleh Petugas" },
            { title: initialExtraData?.statis_stage5 || "Penyerahan & Pembacaan Arsip" }
        ],
        statis_mech1_title: initialExtraData?.statis_mech1_title || "Mekanisme Layanan Langsung",
        statis_mech1_steps: initialExtraData?.statis_mech1_steps || [
            { title: initialExtraData?.statis_mech1_req_title || "1. Persyaratan", desc: initialExtraData?.statis_mech1_req_desc || "Membawa KTP/Identitas resmi yang berlaku dan surat pengantar dari instansi (bagi peneliti/mahasiswa)." },
            { title: initialExtraData?.statis_mech1_stage_title || "2. Prosedur", desc: initialExtraData?.statis_mech1_stage_desc || "Melakukan pendaftaran di loket, menelusuri daftar arsip, dan mengisi form peminjaman arsip." }
        ],
        statis_direct_pdf_name: initialExtraData?.statis_direct_pdf_name || "",
        statis_mech2_title: initialExtraData?.statis_mech2_title || "Mekanisme Layanan Tidak Langsung",
        statis_mech2_steps: initialExtraData?.statis_mech2_steps || [
            { title: initialExtraData?.statis_mech2_online_title || "1. Pengajuan Online", desc: initialExtraData?.statis_mech2_online_desc || "Mengirimkan surat permohonan resmi atau mengisi formulir daring melalui tautan yang disediakan." },
            { title: initialExtraData?.statis_mech2_send_title || "2. Pengiriman Salinan", desc: initialExtraData?.statis_mech2_send_desc || "Petugas menelusuri arsip dan mengirimkan salinan digital (watermarked) ke email pemohon." }
        ],
        statis_indirect_pdf_name: initialExtraData?.statis_indirect_pdf_name || "",
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
        consultation_desc: initialExtraData?.consultation_desc || "Kami melayani berbagai jenis konsultasi kearsipan, meliputi: Konsultasi Pengelolaan Arsip Dinamis (Penciptaan, Penggunaan, Pemeliharaan, dan Penyusutan), Konsultasi Pengolahan Arsip Statis, serta Konsultasi Alih Media dan Digitalisasi Arsip.",
        show_consultation_form: initialExtraData?.show_consultation_form ?? 1,
        consultation_form_title: initialExtraData?.consultation_form_title || "Formulir Konsultasi Kearsipan",
        consultation_form_send: initialExtraData?.consultation_form_send || "Kirim Permintaan Konsultasi",
        consultation_success: initialExtraData?.consultation_success || "Permintaan konsultasi Anda telah terkirim. Petugas kami akan menghubungi Anda melalui email yang dicantumkan.",
        consultation_form_fields: initialExtraData?.consultation_form_fields || [
            { id: 'name', label: 'Nama Lengkap', type: 'text', required: true, options: '', placeholder: '' },
            { id: 'institution', label: 'Instansi / Lembaga / Pribadi', type: 'text', required: true, options: '', placeholder: '' },
            { id: 'email', label: 'Alamat Email', type: 'email', required: true, options: '', placeholder: '' },
            { id: 'detail', label: 'Rincian Konsultasi', type: 'textarea', required: true, options: '', placeholder: 'Tuliskan rincian atau topik konsultasi yang ingin Anda tanyakan...' }
        ],
        lib_objs: initialExtraData?.lib_objs || (initialExtraData?.lib_obj1 !== undefined ? [
            { text: initialExtraData.lib_obj1 },
            { text: initialExtraData.lib_obj2 || '' },
            { text: initialExtraData.lib_obj3 || '' }
        ].filter(o => o.text !== '') : [
            { text: "1. Mempermudah akses informasi terkait arsip dan sejarah." },
            { text: "2. Mendukung kegiatan penelitian dan penulisan ilmiah." },
            { text: "3. Menjadi sumber referensi dalam pengelolaan dan pelestarian arsip." }
        ]),
        lib_visit_btn: initialExtraData?.lib_visit_btn ?? "Kunjungi Website Perpustakaan",
        lib_redirect_url: initialExtraData?.lib_redirect_url ?? "",
        lib_cards: initialExtraData?.lib_cards || (initialExtraData?.lib_card1_title !== undefined ? [
            { title: initialExtraData.lib_card1_title, desc: initialExtraData.lib_card1_desc || '' },
            { title: initialExtraData.lib_card2_title || '', desc: initialExtraData.lib_card2_desc || '' },
            { title: initialExtraData.lib_card3_title || '', desc: initialExtraData.lib_card3_desc || '' }
        ].filter(c => c.title !== '' || c.desc !== '') : [
            { title: "Koleksi Buku", desc: "Tersedia ribuan koleksi buku kearsipan, sejarah, dan literatur umum." },
            { title: "Akses Digital", desc: "Akses e-book, jurnal ilmiah, dan artikel digital terintegrasi." },
            { title: "Ruang Baca dan Belajar", desc: "Ruang ber-AC yang nyaman dengan WiFi gratis untuk pemustaka." }
        ]),
        lib_hours: initialExtraData?.lib_hours ?? "Senin - Kamis : 08:30 - 15:00 WIB\nJumat : 08:30 - 15:30 WIB",
        lib_rules: initialExtraData?.lib_rules || (initialExtraData?.lib_rule1 !== undefined ? [
            { text: initialExtraData.lib_rule1 },
            { text: initialExtraData.lib_rule2 || '' },
            { text: initialExtraData.lib_rule3 || '' }
        ].filter(r => r.text !== '') : [
            { text: "1. Dilarang membawa makanan/minuman ke dalam ruang baca." },
            { text: "2. Tidak membawa tas ke meja baca (tersedia loker penyimpanan)." },
            { text: "3. Wajib menjaga ketenangan dan kebersihan perpustakaan." }
        ]),
        lib_proc_title: initialExtraData?.lib_proc_title ?? "PROSEDUR LAYANAN PERPUSTAKAAN",
        lib_procs: initialExtraData?.lib_procs || (initialExtraData?.lib_proc1_title !== undefined ? [
            { title: initialExtraData.lib_proc1_title, desc: initialExtraData.lib_proc1_desc || '' },
            { title: initialExtraData.lib_proc2_title || '', desc: initialExtraData.lib_proc2_desc || '' },
            { title: initialExtraData.lib_proc3_title || '', desc: initialExtraData.lib_proc3_desc || '' },
            { title: initialExtraData.lib_proc4_title || '', desc: initialExtraData.lib_proc4_desc || '' }
        ].filter(p => p.title !== '' || p.desc !== '') : [
            { title: "1. Registrasi / Login Akun", desc: "Pengunjung melakukan pendaftaran atau masuk menggunakan akun layanan." },
            { title: "2. Mengisi Buku Tamu", desc: "Pengunjung wajib mengisi buku tamu sebagai data administrasi pemustaka." },
            { title: "3. Menelusuri Katalog", desc: "Pengunjung dapat mencari dan memilih koleksi yang dibutuhkan melalui katalog." },
            { title: "4. Membaca di Ruang Baca", desc: "Koleksi yang terpilih dapat dibaca di ruang baca yang telah disediakan." }
        ]),
        lib_pdf_name: initialExtraData?.lib_pdf_name || "",
        lib_photos_names: initialExtraData?.lib_photos_names || "",

        toggleShow(field) {
            this[field] = this[field] == 1 ? 0 : 1;
        },

        addLaraskaStep() {
            this.laraska_steps.push({ title: `${this.laraska_steps.length + 1}. Langkah Baru`, desc: '' });
        },
        removeLaraskaStep(index) {
            this.laraska_steps.splice(index, 1);
        },

        addStatisStage() {
            this.statis_stages.push({ title: `Tahap ${this.statis_stages.length + 1}` });
        },
        removeStatisStage(index) {
            this.statis_stages.splice(index, 1);
        },
        addStatisMech1Step() {
            this.statis_mech1_steps.push({ title: `${this.statis_mech1_steps.length + 1}. Langkah Baru`, desc: '' });
        },
        removeStatisMech1Step(index) {
            this.statis_mech1_steps.splice(index, 1);
        },
        addStatisMech2Step() {
            this.statis_mech2_steps.push({ title: `${this.statis_mech2_steps.length + 1}. Langkah Baru`, desc: '' });
        },
        removeStatisMech2Step(index) {
            this.statis_mech2_steps.splice(index, 1);
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
        addConsultationFormField() {
            this.consultation_form_fields.push({ id: 'field_' + Date.now(), label: 'Label Baru', type: 'text', required: false, options: '', placeholder: '' });
        },
        removeConsultationFormField(index) {
            this.consultation_form_fields.splice(index, 1);
        },
        addLibObj() {
            this.lib_objs.push({ text: `${this.lib_objs.length + 1}. Poin Tujuan Baru` });
        },
        removeLibObj(index) {
            this.lib_objs.splice(index, 1);
        },
        addLibCard() {
            this.lib_cards.push({ title: `Judul Fasilitas ${this.lib_cards.length + 1}`, desc: '' });
        },
        removeLibCard(index) {
            this.lib_cards.splice(index, 1);
        },
        addLibRule() {
            this.lib_rules.push({ text: `${this.lib_rules.length + 1}. Aturan Baru` });
        },
        removeLibRule(index) {
            this.lib_rules.splice(index, 1);
        },
        addLibProc() {
            this.lib_procs.push({ title: `${this.lib_procs.length + 1}. Prosedur Baru`, desc: '' });
        },
        removeLibProc(index) {
            this.lib_procs.splice(index, 1);
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
