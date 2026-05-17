function kontakKamiForm(initialExtraData = null) {
    return {
        files: [],
        alamat_section_title: initialExtraData?.alamat_section_title || "Informasi Kontak & Alamat",
        jam_section_title: initialExtraData?.jam_section_title || "Jadwal & Jam Operasional",
        cards_section_title: initialExtraData?.cards_section_title || "Saluran Layanan & Pengaduan",
        alamat_lengkap: initialExtraData?.alamat_lengkap || "",
        jam_operasional_desc: initialExtraData?.jam_operasional_desc || "Layanan tatap muka dan konsultasi kearsipan dibuka pada jadwal berikut:",
        jam_operasional_list: initialExtraData?.hasOwnProperty('jam_operasional_list') ? initialExtraData.jam_operasional_list : [
            { hari: "Senin - Kamis", jam: "08.00 - 15.00 WIB" },
            { hari: "Jumat", jam: "08.00 - 15.30 WIB" }
        ],
        telepon: initialExtraData?.telepon || "",
        whatsapp: initialExtraData?.whatsapp || "",
        email: initialExtraData?.email || "",
        instagram: initialExtraData?.instagram || "",
        twitter: initialExtraData?.twitter || "",
        facebook: initialExtraData?.facebook || "",
        youtube: initialExtraData?.youtube || "",
        cards: initialExtraData?.hasOwnProperty('cards') ? initialExtraData.cards : [
            { title: "Layanan Informasi", subtitle: "Untuk pertanyaan seputar kearsipan", icon: "phone" },
            { title: "Pengaduan Masyarakat", subtitle: "Sampaikan keluhan atau saran anda", icon: "message" }
        ],
        top_cards: initialExtraData?.hasOwnProperty('top_cards') ? initialExtraData.top_cards : [
            { title: "Bandung", subtitle: "Lokasi Strategis", icon: "map" },
            { title: "Jawa Barat", subtitle: "Depot Pertama", icon: "building" },
            { title: "5 Hari", subtitle: "Layanan", icon: "clock" }
        ],

        addJam() {
            this.jam_operasional_list.push({ hari: '', jam: '' });
        },
        removeJam(index) {
            this.jam_operasional_list.splice(index, 1);
        },

        addCard() {
            this.cards.push({ title: '', subtitle: '', icon: 'phone' });
        },
        removeCard(index) {
            this.cards.splice(index, 1);
        },

        addTopCard() {
            this.top_cards.push({ title: '', subtitle: '', icon: 'map' });
        },
        removeTopCard(index) {
            this.top_cards.splice(index, 1);
        },

        handleFiles(event) {
            const newFiles = Array.from(event.target.files);
            this.files = [...this.files, ...newFiles];
            this.updateInputFiles();
            this.renderPreviews();
        },

        updateInputFiles() {
            const dataTransfer = new DataTransfer();
            this.files.forEach(file => dataTransfer.items.add(file));
            const input = document.querySelector('input[type="file"][name="images[]"]');
            if (input) {
                input.files = dataTransfer.files;
            }
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
                    this.updateInputFiles();
                    this.renderPreviews();
                };
                div.appendChild(removeBtn);

                container.appendChild(div);
            });
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
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
