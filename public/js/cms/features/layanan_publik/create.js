function layananPublikForm(initialType = "kunjungan", initialExtraData = null) {
    // Helper: Check if value is explicitly set (not null/undefined) - for booleans/integers
    const isSet = (val) => val !== null && val !== undefined;

    // Only use default when value is truly undefined (not set in DB, or explicitly null)
    // Empty string → treat as "user cleared it", keep it empty
    const _ = (val, def) => {
        if (val !== undefined && val !== null) {
            return val;
        }
        return def;
    };

    // For arrays: only use defaults if truly undefined OR if explicitly cleared (null literal or empty array from form)
    // Detect "cleared" vs "never saved": if key exists in initialExtraData with value []
    // AND the data matches the current type, user deliberately emptied it → keep empty (don't restore defaults)
    const dataMatchesType =
        initialExtraData &&
        ((initialType === "kunjungan" &&
            (isSet(initialExtraData.show_jadwal) ||
                isSet(initialExtraData.form_fields))) ||
            (initialType === "laraska" &&
                (isSet(initialExtraData.laraska_hours) ||
                    isSet(initialExtraData.laraska_steps))) ||
            (initialType === "statis" &&
                (isSet(initialExtraData.statis_hours) ||
                    isSet(initialExtraData.statis_stages))) ||
            (initialType === "konsultasi" &&
                (isSet(initialExtraData.consultation_desc) ||
                    isSet(initialExtraData.consultation_form_fields))) ||
            (initialType === "perpustakaan" &&
                (isSet(initialExtraData.lib_objs) ||
                    isSet(initialExtraData.lib_cards))));

    // Helper: return empty array if array is empty (user deleted all items),
    // otherwise return the actual array
    const safeArray = (val, def = []) => {
        if (Array.isArray(val) && val.length > 0) return val;
        if (val === null || val === undefined) return def;
        if (Array.isArray(val) && val.length === 0) return val; // keep empty, user cleared it
        return def;
    };

    return {
        type: initialType,
        files: [],

        title_jadwal: _(initialExtraData?.title_jadwal, ""),
        title_pengajuan: _(initialExtraData?.title_pengajuan, ""),
        show_jadwal: _(initialExtraData?.show_jadwal, 1),
        show_pengajuan: _(initialExtraData?.show_pengajuan, 1),
        show_kalender: _(initialExtraData?.show_kalender, 1),
        show_form: _(initialExtraData?.show_form, 1),
        auto_today_date: _(initialExtraData?.auto_today_date, 0),
        jadwal_kunjungan: _(initialExtraData?.jadwal_kunjungan, ""),
        pengajuan_kunjungan: _(initialExtraData?.pengajuan_kunjungan, ""),
        kuota_harian: _(initialExtraData?.kuota_harian, ""),
        laraska_hours: _(initialExtraData?.laraska_hours, ""),
        maklumat_title: _(initialExtraData?.maklumat_title, ""),
        maklumat_content: _(initialExtraData?.maklumat_content, ""),
        maklumat_date: _(initialExtraData?.maklumat_date, ""),
        maklumat_director: _(initialExtraData?.maklumat_director, ""),
        laraska_mech_title: _(initialExtraData?.laraska_mech_title, ""),
        file_name: _(initialExtraData?.file_name, ""),
        statis_hours: _(initialExtraData?.statis_hours, ""),
        statis_order_hours: _(initialExtraData?.statis_order_hours, ""),
        statis_mech1_title: _(initialExtraData?.statis_mech1_title, ""),
        statis_direct_pdf_name: _(initialExtraData?.statis_direct_pdf_name, ""),
        statis_mech2_title: _(initialExtraData?.statis_mech2_title, ""),
        statis_indirect_pdf_name: _(initialExtraData?.statis_indirect_pdf_name, ""),
        lib_visit_btn: _(initialExtraData?.lib_visit_btn, ""),
        lib_redirect_url: _(initialExtraData?.lib_redirect_url, ""),
        lib_hours: _(initialExtraData?.lib_hours, ""),
        lib_proc_title: _(initialExtraData?.lib_proc_title, ""),
        lib_pdf_name: _(initialExtraData?.lib_pdf_name, ""),
        lib_photos_names: _(initialExtraData?.lib_photos_names, ""),
        consultation_desc: _(initialExtraData?.consultation_desc, ""),
        show_consultation_form: isSet(initialExtraData?.show_consultation_form)
            ? initialExtraData.show_consultation_form
            : 1,
        consultation_form_title: _(initialExtraData?.consultation_form_title, ""),
        consultation_form_send: _(initialExtraData?.consultation_form_send, ""),
        consultation_success: _(initialExtraData?.consultation_success, ""),

        // Arrays — use safeArray to preserve empty arrays (user deleted all)
        laraska_steps: safeArray(initialExtraData?.laraska_steps, []),
        statis_stages: safeArray(initialExtraData?.statis_stages, []),
        statis_mech1_steps: safeArray(initialExtraData?.statis_mech1_steps, []),
        statis_mech2_steps: safeArray(initialExtraData?.statis_mech2_steps, []),
        libur_dates: safeArray(initialExtraData?.libur_dates, []),
        tutup_slots: safeArray(initialExtraData?.tutup_slots, []),
        form_fields: safeArray(initialExtraData?.form_fields, []),
        consultation_form_fields: safeArray(initialExtraData?.consultation_form_fields, []),
        lib_objs: safeArray(initialExtraData?.lib_objs, []),
        lib_cards: safeArray(initialExtraData?.lib_cards, []),
        lib_rules: safeArray(initialExtraData?.lib_rules, []),
        lib_procs: safeArray(initialExtraData?.lib_procs, []),

        toggleShow(field) {
            this[field] = this[field] == 1 ? 0 : 1;
        },

        addLaraskaStep() {
            this.laraska_steps.push({
                title: `${this.laraska_steps.length + 1}. Langkah Baru`,
                desc: "",
            });
        },
        removeLaraskaStep(index) {
            this.laraska_steps.splice(index, 1);
        },

        addStatisStage() {
            this.statis_stages.push({
                title: `Tahap ${this.statis_stages.length + 1}`,
            });
        },
        removeStatisStage(index) {
            this.statis_stages.splice(index, 1);
        },
        addStatisMech1Step() {
            this.statis_mech1_steps.push({
                title: `${this.statis_mech1_steps.length + 1}. Langkah Baru`,
                desc: "",
            });
        },
        removeStatisMech1Step(index) {
            this.statis_mech1_steps.splice(index, 1);
        },
        addStatisMech2Step() {
            this.statis_mech2_steps.push({
                title: `${this.statis_mech2_steps.length + 1}. Langkah Baru`,
                desc: "",
            });
        },
        removeStatisMech2Step(index) {
            this.statis_mech2_steps.splice(index, 1);
        },

        addLibur() {
            this.libur_dates.push({ date: "", reason: "Hari Libur / Tutup" });
        },
        removeLibur(index) {
            this.libur_dates.splice(index, 1);
        },
        addTutupSlot() {
            this.tutup_slots.push({
                date: "",
                slot: "pagi",
                max_quota: 0,
                reason: "Slot Penuh / Ditutup",
            });
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
            this.form_fields.push({
                id: "field_" + Date.now(),
                label: "Label Baru",
                type: "text",
                required: false,
                options: "",
            });
        },
        removeFormField(index) {
            this.form_fields.splice(index, 1);
        },
        addConsultationFormField() {
            this.consultation_form_fields.push({
                id: "field_" + Date.now(),
                label: "Label Baru",
                type: "text",
                required: false,
                options: "",
                placeholder: "",
            });
        },
        removeConsultationFormField(index) {
            this.consultation_form_fields.splice(index, 1);
        },
        addLibObj() {
            this.lib_objs.push({
                text: `${this.lib_objs.length + 1}. Poin Tujuan Baru`,
            });
        },
        removeLibObj(index) {
            this.lib_objs.splice(index, 1);
        },
        addLibCard() {
            this.lib_cards.push({
                title: `Judul Fasilitas ${this.lib_cards.length + 1}`,
                desc: "",
            });
        },
        removeLibCard(index) {
            this.lib_cards.splice(index, 1);
        },
        addLibRule() {
            this.lib_rules.push({
                text: `${this.lib_rules.length + 1}. Aturan Baru`,
            });
        },
        removeLibRule(index) {
            this.lib_rules.splice(index, 1);
        },
        addLibProc() {
            this.lib_procs.push({
                title: `${this.lib_procs.length + 1}. Prosedur Baru`,
                desc: "",
            });
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
            const container = document.getElementById("file-previews");
            container.innerHTML = "";

            this.files.forEach((file, index) => {
                const div = document.createElement("div");
                div.className =
                    "relative group aspect-square rounded-lg overflow-hidden bg-gray-100 border border-gray-200";

                if (file.type.startsWith("image/")) {
                    const img = document.createElement("img");
                    img.src = URL.createObjectURL(file);
                    img.className = "w-full h-full object-cover";
                    div.appendChild(img);
                } else if (file.type.startsWith("video/")) {
                    const video = document.createElement("video");
                    video.src = URL.createObjectURL(file);
                    video.className = "w-full h-full object-cover";
                    div.appendChild(video);
                }

                const removeBtn = document.createElement("button");
                removeBtn.type = "button";
                removeBtn.className =
                    "absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity";
                removeBtn.innerHTML =
                    '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
                removeBtn.onclick = () => {
                    this.files.splice(index, 1);
                    this.renderPreviews();
                };
                div.appendChild(removeBtn);

                container.appendChild(div);
            });
        },
    };
}

document.addEventListener("DOMContentLoaded", function () {
    // Initialize RTE
    if (typeof RichTextEditor !== "undefined") {
        const editor = new RichTextEditor("#div_editor1", {
            file_upload_handler: function (
                file,
                callback,
                optionalIndex,
                optionalFiles,
            ) {
                const formData = new FormData();
                formData.append("file", file);
                formData.append(
                    "_token",
                    window.rteUploadUrl
                        ? document.querySelector('meta[name="csrf-token"]')
                              .content
                        : "",
                );

                fetch(window.rteUploadUrl, {
                    method: "POST",
                    body: formData,
                })
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.url) {
                            callback(data.url);
                        }
                    })
                    .catch((error) =>
                        console.error("Error uploading to RTE:", error),
                    );
            },
        });

        // Sync editor content to hidden input before submit
        const form = document.querySelector("form");
        form.onsubmit = function () {
            document.getElementById("description_input").value =
                editor.getHTMLCode();
        };
    }
});
