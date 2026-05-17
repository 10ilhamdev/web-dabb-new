function pengelolaanForm(initialType = 'penyusutan', initialExtraData = null, initialImages = []) {
    return {
        type: initialType,
        files: [],
        existingImages: initialImages || [],
        file_name: initialExtraData?.file_name || "",

        // Penyimpanan
        prinsip_title: initialExtraData?.hasOwnProperty('prinsip_title') ? (initialExtraData.prinsip_title || "") : (initialType === 'penyimpanan' ? "" : "Prinsip Penyimpanan Arsip Statis"),
        prinsip_desc: initialExtraData?.hasOwnProperty('prinsip_desc') ? (initialExtraData.prinsip_desc || "") : (initialType === 'penyimpanan' ? "" : (initialExtraData?.prinsip_penyimpanan ?? "Penyimpanan arsip statis dilaksanakan berdasarkan prinsip:")),
        prinsip_list: initialExtraData?.hasOwnProperty('prinsip_list') ? initialExtraData.prinsip_list : (initialType === 'penyimpanan' ? [] : [
            { title: "Keutuhan (Integrity)", desc: "Arsip disimpan tanpa mengubah susunan asli dan konteks penciptaannya." },
            { title: "Keaslian (Authenticity)", desc: "Arsip dijaga agar tetap asli dan tidak mengalami manipulasi." },
            { title: "Keamanan (Security)", desc: "Arsip terlindungi dari kehilangan, pencurian, maupun bencana." },
            { title: "Kemudahan Akses (Accessibility)", desc: "Penataan arsip memungkinkan proses temu kembali secara cepat dan tepat." }
        ]),
        sistem_title: initialExtraData?.hasOwnProperty('sistem_title') ? (initialExtraData.sistem_title || "") : (initialType === 'penyimpanan' ? "" : "Sistem Penyimpanan"),
        sistem_penyimpanan: initialExtraData?.hasOwnProperty('sistem_penyimpanan') ? initialExtraData.sistem_penyimpanan : (initialType === 'penyimpanan' ? [] : [
            { title: "Sistem Abjad (Alphabetical)", desc: "Penyimpanan arsip berdasarkan urutan abjad dari nama orang, badan, atau wilayah.", icon: "book" },
            { title: "Sistem Subjek (Subject)", desc: "Penyimpanan arsip berdasarkan masalah atau pokok persoalan yang terkandung dalam arsip.", icon: "archive" },
            { title: "Sistem Nomor (Numerical)", desc: "Penyimpanan arsip menggunakan kode angka atau nomor urut sebagai pedoman penyimpanan.", icon: "clipboard" },
            { title: "Sistem Wilayah (Geographical)", desc: "Penyimpanan arsip berdasarkan pembagian wilayah atau daerah asal arsip.", icon: "map" },
            { title: "Sistem Tanggal (Chronological)", desc: "Penyimpanan arsip berdasarkan urutan waktu (tahun, bulan, tanggal) penciptaan arsip.", icon: "calendar" }
        ]),
        fasilitas_title: initialExtraData?.hasOwnProperty('fasilitas_title') ? (initialExtraData.fasilitas_title || "") : (initialType === 'penyimpanan' ? "" : "Fasilitas Penyimpanan"),
        fasilitas_list: initialExtraData?.hasOwnProperty('fasilitas_list') ? initialExtraData.fasilitas_list : (initialType === 'penyimpanan' ? [] : [
            { text: "Rak Arsip (Stationary Shelving / Mobile Files)" },
            { text: "Filing Cabinet tahan api" },
            { text: "Kotak Arsip (Boks Arsip bebas asam)" },
            { text: "Alat pengukur suhu dan kelembaban (Thermohygrometer)" },
            { text: "Sistem pemadam kebakaran otomatis (APAR / Smoke Detector)" }
        ]),
        ruang_title: initialExtraData?.hasOwnProperty('ruang_title') ? (initialExtraData.ruang_title || "") : (initialType === 'penyimpanan' ? "" : "Ruang Penyimpanan"),
        ruang_list: initialExtraData?.hasOwnProperty('ruang_list') ? initialExtraData.ruang_list : (initialType === 'penyimpanan' ? [] : [
            { text: "Ruang Penyimpanan Arsip Tekstual (Suhu 20-22°C, Kelembaban 50-60%)" },
            { text: "Ruang Penyimpanan Arsip Media Baru / Foto / Film / Audio (Suhu 15-18°C, Kelembaban 40-50%)" },
            { text: "Ruang Penyimpanan Arsip Elektronik / Server" }
        ]),

        // Preservasi
        preservasi_title: initialExtraData?.hasOwnProperty('preservasi_title') ? (initialExtraData.preservasi_title || "") : (initialType === 'preservasi' ? "" : "Kegiatan Preservasi"),
        preservasi_list: initialExtraData?.hasOwnProperty('preservasi_list') ? initialExtraData.preservasi_list : (initialType === 'preservasi' ? [] : [
            { text: "Pembersihan arsip secara berkala dari debu dan kotoran" },
            { text: "Pengaturan suhu dan kelembaban ruang penyimpanan secara ketat" },
            { text: "Fumigasi untuk mencegah dan membasmi serangga, jamur, atau hama perusak arsip" },
            { text: "Penggunaan boks arsip bebas asam (acid-free) untuk penyimpanan permanen" },
            { text: "Alih media (digitalisasi dan mikrofilm) untuk pelestarian informasi arsip" }
        ]),
        restorasi_title: initialExtraData?.hasOwnProperty('restorasi_title') ? (initialExtraData.restorasi_title || "") : (initialType === 'preservasi' ? "" : "RESTORASI ARSIP"),
        restorasi_desc: initialExtraData?.hasOwnProperty('restorasi_desc') ? (initialExtraData.restorasi_desc || "") : (initialType === 'preservasi' ? "" : "Restorasi arsip adalah tindakan khusus untuk memperbaiki dan memulihkan fisik arsip yang mengalami kerusakan akibat faktor usia, bencana, jamur, atau penanganan yang salah, agar kembali kukuh dan informasinya dapat terselamatkan."),
        restorasi_list: initialExtraData?.hasOwnProperty('restorasi_list') ? initialExtraData.restorasi_list : (initialType === 'preservasi' ? [] : [
            { text: "1. Identifikasi dan pengujian kondisi fisik serta tingkat keasaman kertas" },
            { text: "2. Pembersihan mekanis dan kimiawi untuk menghilangkan noda dan jamur" },
            { text: "3. Deasidifikasi (penghilangan asam) untuk menghentikan proses kerapuhan kertas" },
            { text: "4. Menambal dan menyambung kertas yang robek menggunakan tisu jepang dan lem perekat khusus" },
            { text: "5. Sizing dan penjilidan kembali arsip yang telah direstorasi" }
        ]),

        // Pengolahan
        pengolahan_title: initialExtraData?.hasOwnProperty('pengolahan_title') ? (initialExtraData.pengolahan_title || "") : (initialType === 'pengolahan' ? "" : "Tahapan Pengolahan"),
        pengolahan_title_en: initialExtraData?.hasOwnProperty('pengolahan_title_en') ? (initialExtraData.pengolahan_title_en || "") : (initialType === 'pengolahan' ? "" : "Processing Stages"),
        pengolahan_list: initialExtraData?.hasOwnProperty('pengolahan_list') ? initialExtraData.pengolahan_list : (initialType === 'pengolahan' ? [] : [
            { text: "Meneliti dan merekonstruksi struktur serta penataan asal arsip (prinsip asal usul dan aturan asli)" },
            { text: "Melakukan deskripsi arsip (mencatat informasi isi, tanggal, pencipta, dan kondisi fisik)" },
            { text: "Menyusun skema pengaturan arsip berdasarkan fungsi atau struktur organisasi pencipta" },
            { text: "Pembuatan sarana bantu penemuan kembali (Finding Aids) seperti Daftar Arsip, Inventaris Arsip, dan Guide Arsip" },
            { text: "Pemasukan data deskripsi ke dalam Sistem Informasi Kearsipan Nasional (SIKN / JIKN)" }
        ]),

        // Pemanfaatan
        mekanisme_title: initialExtraData?.hasOwnProperty('mekanisme_title') ? (initialExtraData.mekanisme_title || "") : (initialType === 'pemanfaatan' ? "" : "Mekanisme Pemanfaatan dan Akses Arsip"),
        mekanisme_desc: initialExtraData?.hasOwnProperty('mekanisme_desc') ? (initialExtraData.mekanisme_desc || "") : (initialType === 'pemanfaatan' ? "" : "Masyarakat dapat mengakses dan memanfaatkan khazanah arsip statis yang tersimpan di ANRI untuk berbagai kepentingan, seperti penelitian akademis, penulisan sejarah, pembuktian hukum, dan edukasi masyarakat."),
        pemanfaatan_quote: initialExtraData?.hasOwnProperty('pemanfaatan_quote') ? (initialExtraData.pemanfaatan_quote || "") : (initialType === 'pemanfaatan' ? "" : "Berdasarkan Undang-Undang Nomor 43 Tahun 2009 tentang Kearsipan, arsip statis yang dikelola oleh lembaga kearsipan terbuka untuk umum bagi kepentingan penelitian, sejarah, dan ilmu pengetahuan, kecuali arsip yang dinyatakan tertutup berdasarkan ketentuan peraturan perundang-undangan."),
        akses_title: initialExtraData?.hasOwnProperty('akses_title') ? (initialExtraData.akses_title || "") : (initialType === 'pemanfaatan' ? "" : "Layanan Akses & Pemanfaatan"),
        akses_title_en: initialExtraData?.hasOwnProperty('akses_title_en') ? (initialExtraData.akses_title_en || "") : (initialType === 'pemanfaatan' ? "" : "Access & Utilization Services"),
        akses_list: initialExtraData?.hasOwnProperty('akses_list') ? initialExtraData.akses_list : (initialType === 'pemanfaatan' ? [] : [
            { title: "Akses Arsip Terbuka", desc: "Arsip statis yang secara umum dapat diakses langsung oleh publik di Ruang Baca Arsip atau melalui JIKN/SIKN.", icon: "clipboard" },
            { title: "Akses Arsip Terbatas / Tertutup", desc: "Arsip yang memerlukan izin khusus atau masa retensi kerahasiaan tertentu sebelum balances dibuka untuk umum.", icon: "lock" },
            { title: "Layanan Reproduksi Arsip", desc: "Pemustaka dapat meminta salinan/reproduksi arsip dalam bentuk cetak atau digital sesuai ketentuan penerimaan negara bukan pajak (PNBP).", icon: "book" }
        ]),

        // Penjangkauan
        kegiatan_title: initialExtraData?.hasOwnProperty('kegiatan_title') ? (initialExtraData.kegiatan_title || "") : (initialType === 'penjangkauan' ? "" : "Program & Kegiatan Penjangkauan"),
        kegiatan_list: initialExtraData?.hasOwnProperty('kegiatan_list') ? initialExtraData.kegiatan_list : (initialType === 'penjangkauan' ? [] : [
            { title: "Pameran Arsip (Virtual & Langsung)", desc: "Menyajikan koleksi arsip tematik kepada masyarakat luas untuk meningkatkan kesadaran sejarah dan kebangsaan.", icon: "clipboard", button_label: "Kunjungi", button_url: "#" },
            { title: "Sosialisasi dan Edukasi Kearsipan", desc: "Penyuluhan kepada pelajar, mahasiswa, dan komunitas tentang pentingnya arsip sebagai memori kolektif bangsa.", icon: "users", button_label: "Kunjungi", button_url: "#" },
            { title: "Penerbitan Naskah Sumber", desc: "Mempublikasikan buku atau kajian berbasis arsip statis yang dapat menjadi referensi bagi peneliti dan masyarakat.", icon: "book", button_label: "Kunjungi", button_url: "#" },
            { title: "Kerjasama Kearsipan Nasional & Internasional", desc: "Kolaborasi pameran, pertukaran informasi, dan pelestarian memori dunia (Memory of the World).", icon: "calendar", button_label: "Kunjungi", button_url: "#" }
        ]),

        // Akuisisi
        tahapan_list: initialExtraData?.hasOwnProperty('tahapan_list') ? initialExtraData.tahapan_list : (initialType === 'akuisisi' ? [] : [
            { title: "1. Monitoring dan Verifikasi", desc: "Melakukan pendataan, penilaian, dan verifikasi terhadap arsip bernilai guna permanen pada pencipta arsip (Kementerian/Lembaga/BUMN/Ormas/Perseorangan)." },
            { title: "2. Penilaian Arsip", desc: "Tim penilai melakukan pengujian untuk memastikan otentisitas, integritas, dan nilai guna statis arsip yang akan diakuisisi." },
            { title: "3. Persetujuan dan Penetapan", desc: "Penerbitan surat persetujuan penyerahan arsip statis dari Kepala ANRI atau pejabat yang berwenang." },
            { title: "4. Serah Terima Arsip", desc: "Pelaksanaan penyerahan arsip statis yang disertai dengan Berita Acara Serah Terima (BAST) dan Daftar Arsip Statis yang diserahkan." }
        ]),

        // Helper methods for adding/removing items
        addPrinsip() {
            this.prinsip_list.push({ title: '', desc: '' });
        },
        removePrinsip(index) {
            this.prinsip_list.splice(index, 1);
        },

        addSistem() {
            this.sistem_penyimpanan.push({ title: '', desc: '', icon: 'document' });
        },
        removeSistem(index) {
            this.sistem_penyimpanan.splice(index, 1);
        },

        addFasilitas() {
            this.fasilitas_list.push({ text: '' });
        },
        removeFasilitas(index) {
            this.fasilitas_list.splice(index, 1);
        },

        addRuang() {
            this.ruang_list.push({ text: '' });
        },
        removeRuang(index) {
            this.ruang_list.splice(index, 1);
        },

        addPreservasi() {
            this.preservasi_list.push({ text: '' });
        },
        removePreservasi(index) {
            this.preservasi_list.splice(index, 1);
        },

        addRestorasi() {
            this.restorasi_list.push({ text: '' });
        },
        removeRestorasi(index) {
            this.restorasi_list.splice(index, 1);
        },

        addPengolahan() {
            this.pengolahan_list.push({ text: '' });
        },
        removePengolahan(index) {
            this.pengolahan_list.splice(index, 1);
        },

        addAkses() {
            this.akses_list.push({ title: '', desc: '', icon: 'document' });
        },
        removeAkses(index) {
            this.akses_list.splice(index, 1);
        },

        addKegiatan() {
            this.kegiatan_list.push({ title: '', desc: '', icon: 'document', button_label: 'Kunjungi', button_url: '#' });
        },
        removeKegiatan(index) {
            this.kegiatan_list.splice(index, 1);
        },

        addTahapan() {
            this.tahapan_list.push({ title: '', desc: '' });
        },
        removeTahapan(index) {
            this.tahapan_list.splice(index, 1);
        },

        removeExistingImage(index) {
            this.existingImages.splice(index, 1);
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
        if (form) {
            form.onsubmit = function() {
                const descInput = document.getElementById('description_input');
                if (descInput && editor) {
                    descInput.value = editor.getHTMLCode();
                }
            };
        }
    }
});
