<?php

return [

    /*
    |--------------------------------------------------------------------------
    | CMS — Manajemen Fitur (features/index)
    |--------------------------------------------------------------------------
    */

    'features' => [
        'title' => 'Manajemen Fitur',
        'card_title' => 'Manajemen Fitur CMS',
        'card_desc' => 'Kelola semua fitur yang ditampilkan di website',
        'add_button' => 'Tambah Fitur',

        // Table headers
        'col_name' => 'Nama Fitur',
        'col_type' => 'Tipe Menu',
        'col_sub_count' => 'Jumlah Sub Fitur',
        'col_order' => 'Urutan',
        'col_action' => 'Aksi',

        // Badges
        'type_dropdown' => 'Dropdown',
        'type_link' => 'Link',

        // Buttons
        'detail' => 'Detail',
        'hide' => 'Sembunyikan',
        'show_label' => 'Tampilkan',
        'visibility_modal' => [
            'title' => 'Sembunyikan Fitur',
            'subtitle' => 'Pilih mode penyembunyian untuk :name',
            'menu_only_title' => 'Sembunyikan dari Menu',
            'menu_only_desc' => 'Fitur tidak tampil di navigasi, <span class="font-medium text-green-600">tetapi URL masih bisa diakses</span> langsung oleh pengunjung.',
            'total_title' => 'Sembunyikan Total',
            'total_desc' => 'Fitur tidak tampil di navigasi dan <span class="font-medium text-red-600">URL tidak bisa diakses</span> — pengunjung akan mendapat halaman 404.',
            'cancel' => 'Batal',
        ],

        // Empty state
        'empty' => 'Belum ada fitur. Klik "+ Tambah Fitur" untuk menambahkan.',

        // Edit modal
        'edit_title' => 'Edit Fitur',

        // Add modal
        'add_title' => 'Tambah Fitur Baru',

        // Delete modal
        'delete' => [
            'title' => 'Hapus Fitur',
            'confirm' => 'Apakah Anda yakin ingin menghapus fitur :name? Tindakan ini tidak dapat dibatalkan.',
            'yes' => 'Ya, Hapus',
        ],

        // Form labels (shared between add/edit)
        'form' => [
            'name' => 'Nama Fitur',
            'type' => 'Tipe Menu',
            'path' => 'Path / URL',
            'path_placeholder' => 'Contoh: /beranda',
            'order' => 'Urutan',
            'name_placeholder' => 'Contoh: Beranda',
            'move_title' => 'Pindah ke Menu Lain',
            'move_help' => 'Pilih menu lain untuk memindahkan fitur ini ke dalam sub-menu',
            'move_keep' => '— Tetap di Menu Utama —',
            'login_required' => 'Wajib login sebelum mengakses halaman ini',
            'login_required_help' => 'Jika diaktifkan, pengunjung yang belum login akan diminta masuk terlebih dahulu',
        ],

        // Detail page (features/show)
        'detail_title' => 'Detail Fitur: :name',
        'type_label' => 'Tipe',

        // Sub-menu section (dropdown type)
        'sub' => [
            'list_title' => 'Daftar Sub Menu — :name',
            'list_desc' => 'Kelola sub menu yang ada di dalam menu :name',
            'add_button' => 'Tambah Sub Menu',
            'col_name' => 'Nama Sub Menu',
            'col_path' => 'Path / URL',
            'col_order' => 'Urutan',
            'col_action' => 'Aksi',
            'empty' => 'Belum ada sub menu. Klik "+ Tambah Sub Menu" untuk menambahkan.',

            // Add sub modal
            'add_title' => 'Tambah Sub Menu',

            // Edit sub modal
            'edit_title' => 'Edit Sub Menu',

            // Delete sub modal
            'delete' => [
                'title' => 'Hapus Sub Menu',
                'confirm' => 'Apakah Anda yakin ingin menghapus sub menu :name?',
                'yes' => 'Ya, Hapus',
            ],

            // Sub form labels
            'form' => [
                'name' => 'Nama Sub Menu',
                'path' => 'Path / URL',
                'path_placeholder' => 'Contoh: /profil/sejarah',
                'name_placeholder' => 'Contoh: Sejarah',
                'order' => 'Urutan',
                'move_title' => 'Pindah ke Menu Lain',
                'move_help' => 'Kosongkan untuk tetap di menu saat ini (:name)',
                'move_keep' => '— Tetap di menu saat ini —',
                'move_top' => 'Jadikan Menu Utama (Top Level)',
                'badge_sub' => '(sub-menu)',
            ],
        ],

        // Content editor (link type)
        'content' => [
            'title' => 'Editor Konten Halaman — :name',
            'desc' => 'Edit konten yang ditampilkan pada halaman :name',
            'label' => 'Konten Halaman',
            'placeholder' => 'Masukkan konten HTML atau teks untuk halaman ini...',
            'help' => 'Anda dapat menggunakan HTML untuk memformat konten.',
        ],

        // Flash messages
        'flash' => [
            'sub_added' => 'Sub menu berhasil ditambahkan.',
            'feature_added' => 'Fitur berhasil ditambahkan.',
            'feature_updated' => 'Fitur berhasil diperbarui.',
            'content_saved' => 'Konten halaman berhasil disimpan.',
            'feature_deleted' => 'Fitur berhasil dihapus.',
            'sub_updated' => 'Sub fitur berhasil diperbarui.',
            'sub_deleted' => 'Sub fitur berhasil dihapus.',
            'visibility_toggled' => 'Visibilitas fitur berhasil diubah.',
        ],

        'errors' => [
            'profile_requires_parent' => 'Fitur Profile harus memiliki parent_id yang valid.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | CMS — Halaman Fitur (feature pages)
    |--------------------------------------------------------------------------
    */

    'feature_pages' => [
        'title' => 'Manajemen Halaman — :name',
        'desc' => 'Kelola halaman-halaman yang ditampilkan pada fitur :name',
        'add_button' => 'Tambah Halaman',
        'back_to_feature' => 'Kembali ke Fitur',
        'visibility_modal' => [
            'title' => 'Sembunyikan Halaman',
            'subtitle' => 'Pilih mode penyembunyian untuk :name',
            'menu_only_title' => 'Sembunyikan dari Menu',
            'menu_only_desc' => 'Halaman tidak tampil di navigasi, <span class="font-medium text-green-600">tetapi URL masih bisa diakses</span> langsung oleh pengunjung.',
            'total_title' => 'Sembunyikan Total',
            'total_desc' => 'Halaman tidak tampil di navigasi dan <span class="font-medium text-red-600">URL tidak bisa diakses</span> — pengunjung akan mendapat halaman 404.',
            'cancel' => 'Batal',
        ],

        'col_title' => 'Judul Halaman',
        'col_sections' => 'Jumlah Seksi',
        'col_order' => 'Urutan',
        'col_action' => 'Aksi',

        'empty' => 'Belum ada halaman. Klik "+ Tambah Halaman" untuk menambahkan.',

        'add_title' => 'Tambah Halaman Baru',
        'edit_title' => 'Edit Halaman',

        'delete' => [
            'title' => 'Hapus Halaman',
            'confirm' => 'Apakah Anda yakin ingin menghapus halaman :name?',
            'yes' => 'Ya, Hapus',
        ],

        'form' => [
            'title' => 'Judul Halaman',
            'title_placeholder' => 'Contoh: Pameran Kontemporer',
            'description' => 'Deskripsi Halaman',
            'description_placeholder' => 'Deskripsi singkat halaman ini...',
            'order' => 'Urutan',
        ],

        // Sections
        'sections_title' => 'Seksi Halaman — :name',
        'sections_desc' => 'Kelola seksi-seksi konten pada halaman :name',
        'add_section' => 'Tambah Seksi',
        'add_section_title' => 'Tambah Seksi Baru',
        'edit_section_title' => 'Edit Seksi',

        'section_form' => [
            'title' => 'Judul Seksi',
            'title_placeholder' => 'Contoh: Fasilitas Mini Diorama',
            'description' => 'Deskripsi',
            'description_placeholder' => 'Deskripsi seksi ini...',
            'images' => 'Gambar',
            'images_help' => 'Upload gambar JPG/PNG/WebP, maks 2MB per file',
            'existing_images' => 'Gambar Saat Ini',
            'order' => 'Urutan',
            'add_new_image' => 'Tambah Gambar Baru',
        ],

        'delete_section' => [
            'title' => 'Hapus Seksi',
            'confirm' => 'Apakah Anda yakin ingin menghapus seksi :name?',
            'yes' => 'Ya, Hapus',
        ],
        'sections_empty' => 'Belum ada seksi. Klik "+ Tambah Seksi" untuk menambahkan.',

        'flash' => [
            'page_added' => 'Halaman berhasil ditambahkan.',
            'page_updated' => 'Halaman berhasil diperbarui.',
            'page_deleted' => 'Halaman berhasil dihapus.',
            'section_added' => 'Seksi berhasil ditambahkan.',
            'section_updated' => 'Seksi berhasil diperbarui.',
            'section_deleted' => 'Seksi berhasil dihapus.',
            'visibility_toggled' => 'Visibilitas halaman berhasil diubah.',
        ],

        // Public page
        'welcome' => 'Selamat datang di portal :name,',
        'search_placeholder' => 'Pencarian',
        'list_title' => 'Daftar :name',
    ],

    /*
    |--------------------------------------------------------------------------
    | CMS — Halaman Profil (profile_pages)
    |--------------------------------------------------------------------------
    */

    'profile_pages' => [
        'title' => 'Daftar Halaman: :name',
        'desc' => 'Kelola halaman profil untuk menu ini',
        'preview_title' => 'Preview Halaman Guest',
        'preview_desc' => 'Navigasi halaman yang akan ditampilkan di halaman publik',
        'page_label' => 'Halaman:',
        'nav_help' => 'Tombol navigasi akan muncul di halaman publik untuk berpindah antar halaman',
        'card_title' => 'Halaman Profil',
        'card_desc' => 'Kelola halaman profil. Section dikelola di halaman Edit.',
        'add_button' => 'Tambah Halaman',
        'col_no' => 'No',
        'col_title' => 'Judul',
        'col_type' => 'Tipe',
        'col_sections' => 'Section',
        'col_order' => 'Urutan',
        'col_action' => 'Aksi',
        'empty' => 'Belum ada halaman. Klik "Tambah Halaman" untuk menambahkan.',
        'type_default' => 'Default',
        'type_sdm_chart' => 'SDM (Grafik)',
        'type_struktur_image' => 'Struktur Organisasi',
        'type_tugas_fungsi' => 'Tugas dan Fungsi',
        'delete' => [
            'title' => 'Hapus Halaman?',
            'confirm' => 'Anda yakin ingin menghapus :name?',
            'cancel' => 'Batal',
            'yes' => 'Hapus',
        ],
        'form' => [
            'add_title' => 'Tambah Halaman',
            'edit_title' => 'Edit Halaman',
            'type' => 'Tipe Halaman',
            'type_help' => 'Pilih tipe sesuai konten yang akan ditampilkan',
            'title' => 'Judul Halaman',
            'title_placeholder' => 'Contoh: Tugas Pokok dan Fungsi',
            'description' => 'Deskripsi / Konten',
            'description_help' => 'Format teks menggunakan Rich Text Editor.',
            'link_settings' => 'Pengaturan Tautan',
            'link_text' => 'Teks Tautan',
            'link_text_placeholder' => 'Contoh: Pelajari Lebih Lanjut',
            'link_url' => 'URL Tautan',
            'subtitle_section' => 'Sub-judul',
            'subtitle' => 'Judul Tambahan',
            'subtitle_placeholder' => 'Contoh: Grafik Jumlah Pegawai Berdasarkan Usia',
            'subtitle_help' => 'Sub-judul yang akan ditampilkan di bawah judul utama',
            'chart_title' => 'Grafik SDM',
            'chart_desc' => 'Pilih data dan tipe grafik yang akan ditampilkan',
            'chart_roles' => 'Pilih Role User yang Akan Dihitung:',
            'chart_roles_help' => '* Kosongkan untuk menyertakan semua role',
            'chart_field' => 'Pilih Data yang Akan Ditampilkan:',
            'chart_field_placeholder' => '-- Pilih Field Data --',
            'chart_field_add' => 'Tambah',
            'chart_field_help' => 'Pilih field data untuk menambahkan grafik. Anda dapat menambahkan beberapa field.',
            'chart_config' => 'Konfigurasi Grafik:',
            'chart_config_empty' => 'Pilih field data di atas untuk menambahkan grafik',
            'chart_generate' => 'Generate Grafik',
            'chart_preview_empty' => 'Pilih field data dan tipe grafik, lalu klik "Generate Grafik"',
            'images_title' => 'Gambar Pendukung',
            'images_help' => 'Drag untuk ubah posisi focal point atau klik Posisi untuk preset. Max 10MB per file.',
            'images_upload_placeholder' => 'Klik atau drag untuk upload gambar',
            'order' => 'Urutan',
            'order_help' => 'Halaman dengan urutan lebih kecil akan ditampilkan lebih dulu',
            'cancel' => 'Batal',
            'save' => 'Simpan',
            'preview_header' => 'Preview Halaman',
            'preview_help' => 'Anda bisa drag gambar untuk mengubah posisinya atau ubah focal point',
            'preview_auto_update' => 'Preview otomatis terupdate saat Anda mengedit',
            'section_info_title' => 'Kelola Section Setelah Disimpan',
            'section_info_desc' => 'Setelah halaman disimpan, Anda dapat mengelola section (sub-konten) melalui halaman Edit.',
        ],
        'sections' => [
            'title' => 'Section Halaman',
            'desc' => 'Kelola sub-konten atau section untuk halaman ini',
            'add_button' => 'Tambah Section',
            'empty' => 'Belum ada section. Klik "Tambah Section" untuk menambahkan.',
            'add_title' => 'Tambah Section',
            'edit_title' => 'Edit Section',
            'form_title' => 'Judul Section',
            'form_title_placeholder' => 'Contoh: Tugas Pokok',
            'form_desc' => 'Deskripsi',
            'form_desc_placeholder' => 'Deskripsi section...',
            'form_order' => 'Urutan',
            'delete' => [
                'title' => 'Hapus Section?',
                'confirm' => 'Anda yakin ingin menghapus :name?',
                'cancel' => 'Batal',
                'yes' => 'Hapus',
            ]
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | CMS — Editor Beranda (home/edit)
    |--------------------------------------------------------------------------
    */

    'home' => [
        'title' => 'Editor Konten Halaman Beranda',
        'desc' => 'Kelola semua konten yang ditampilkan di halaman Beranda website',
        'view_page' => 'Lihat Halaman',

        'hero' => [
            'title' => 'Seksi Hero (Banner Utama)',
            'desc' => 'Teks utama dan tombol CTA di bagian atas halaman',
            'hero_title' => 'Judul Hero',
            'hero_cta' => 'Teks Tombol CTA',
            'background_label' => 'Background Hero (Gambar atau Video)',
            'unknown_type' => 'Tipe tidak dikenali',
            'current' => 'Saat ini:',
            'remove_background' => 'Hapus background (kembali ke video default)',
            'background_help' => 'Unggah <span class="font-semibold">gambar</span> (JPG/PNG/WebP/GIF/AVIF) atau <span class="font-semibold">video</span> (MP4/WebM/OGG/MOV) untuk menggantikan latar hero. Jika dikosongkan, website akan menggunakan video default (library-books.mp4).',
        ],

        'feature_strip' => [
            'title' => 'Feature Strip (Banner Bawah Hero)',
            'desc' => 'Dua kotak informasi di bawah hero',
            'left' => 'Teks Kiri',
            'middle' => 'Tombol Tengah',
            'middle_link' => 'Link Tombol Tengah',
            'right_button' => 'Tombol Kanan',
            'right_button_link' => 'Link Tombol Kanan',
            'right_text' => 'Teks Kanan',
            'related_links' => 'Tautan Terkait',
            'related_desc' => 'Tautan dengan foto yang dapat diklik',
            'related_title' => 'Judul',
            'related_photo' => 'Foto',
            'related_link' => 'Tautan',
            'add_related' => 'Tambah Tautan',
        ],

        'info' => [
            'title' => 'Seksi Informasi DABB',
            'desc' => 'Judul, gambar, dan dua paragraf informasi tentang DABB',
            'section' => 'Judul Seksi',
            'image1' => 'Gambar Paragraf 1',
            'image2' => 'Gambar Paragraf 2',
            'image_help' => 'JPG, PNG, atau WebP. Biarkan kosong jika tidak ingin mengubah.',
            'paragraph1' => 'Paragraf 1',
            'paragraph2' => 'Paragraf 2',
        ],

        'activities' => [
            'title' => 'Seksi Kegiatan Kearsipan',
            'desc' => '6 item kegiatan yang ditampilkan dalam kartu berwarna',
            'section' => 'Judul Seksi',
        ],

        'section_titles' => [
            'title' => 'Judul Seksi Lainnya',
            'desc' => 'Judul untuk seksi Galeri, Statistik, YouTube, Instagram, dll.',
            'related' => 'Judul Seksi',
            'gallery' => 'Pameran Arsip (Galeri)',
            'gallery_desc' => 'Judul seksi pameran arsip pada halaman beranda',
            'gallery_help' => 'Konten galeri pameran arsip diambil otomatis dari data pameran virtual.',
            'stats' => 'Judul Seksi',
            'youtube' => 'Judul Seksi',
            'instagram' => 'Judul Seksi',
        ],

        'stats' => [
            'title' => 'Label Statistik',
            'desc' => 'Label teks untuk counter statistik pengunjung',
            'total' => 'Label Total Pengunjung',
            'today' => 'Label Pengunjung Hari Ini',
            'image_label' => 'Gambar Statistik',
            'help' => 'Angka statistik pengunjung dihitung otomatis berdasarkan jumlah akses halaman oleh pengunjung.',
        ],

        'youtube' => [
            'title' => 'Video YouTube',
            'desc' => 'ID video YouTube yang ditampilkan di carousel (format: ID saja, contoh: F2NhNTiNxoY)',
            'video_label' => 'Video :number',
            'placeholder' => 'ID YouTube',
            'help' => 'Salin ID dari URL YouTube: youtube.com/watch?v=<strong>ID_DI_SINI</strong>',
            'add_video' => 'Tambah Video',
        ],

        'instagram' => [
            'title' => 'Instagram Feed',
            'desc' => 'Kode post Instagram yang ditampilkan di halaman beranda',
            'username_label' => 'Username Instagram',
            'username_help' => 'Masukkan username Instagram tanpa @',
            'post_label' => 'Post :number',
            'placeholder' => 'Kode Post Instagram',
            'add_post' => 'Tambah Post',
            'help' => 'Salin kode dari URL Instagram: instagram.com/p/<strong>KODE_DI_SINI</strong>/',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | CMS — Ruangan Virtual 360° (virtual_rooms)
    |--------------------------------------------------------------------------
    */

    'virtual_rooms' => [
        'breadcrumb_parent' => 'Pameran Virtual Real',
        'breadcrumb_active' => 'Dashboard',
        'breadcrumb_form_parent' => 'Pameran Virtual Real / Daftar Ruangan',
        'breadcrumb_edit' => 'Edit Ruangan',
        'breadcrumb_create' => 'Tambah Ruangan',

        'page_title' => 'Manajemen Halaman — :name',
        'page_desc' => 'Kelola ruangan virtual dan hotspot navigasi untuk :name 360 derajat',
        'view_exhibition' => 'Lihat Pameran Virtual',
        'add_room' => 'Tambah Ruangan Virtual',

        'stat_total_rooms' => 'Total Ruangan',
        'stat_total_rooms_sub' => 'Ruangan virtual aktif',
        'stat_total_hotspots' => 'Total Hotspot',
        'stat_total_hotspots_sub' => 'Titik navigasi aktif',
        'stat_avg_hotspots' => 'Rata-rata Hotspot',
        'stat_avg_hotspots_sub' => 'Per ruangan',

        'table_title' => 'Daftar Ruangan Virtual',
        'col_no' => 'No',
        'col_thumbnail' => 'Thumbnail',
        'col_name' => 'Nama Ruangan',
        'col_desc' => 'Deskripsi',
        'col_hotspot' => 'Hotspot',
        'col_action' => 'Aksi',
        'empty' => 'Belum ada ruangan virtual yang ditambahkan.',
        'delete_confirm' => 'Yakin ingin menghapus ruangan ini?',

        // Form (create/edit)
        'form_title_create' => 'Tambah Ruangan Virtual',
        'form_title_edit' => 'Edit Ruangan Virtual',
        'form_desc' => 'Perbarui informasi ruangan dan atur hotspot navigasi',
        'back_to_list' => 'Kembali ke Daftar Ruangan',
        'info_title' => 'Informasi Ruangan',
        'label_name' => 'Nama Ruangan',
        'label_desc' => 'Deskripsi Ruangan',
        'label_thumbnail' => 'Thumbnail Ruangan',
        'thumbnail_help' => 'Gambar preview untuk daftar ruangan (JPG, PNG, WEBP)',
        'label_image_360' => 'Gambar 360°',
        'image_360_help' => 'Gambar equirectangular 360 derajat (JPG, PNG)',

        'hotspot_title' => 'Hotspot Navigasi',
        'hotspot_add' => 'Tambah',
        'hotspot_rooms_available' => 'Ruangan tersedia: :count',
        'hotspot_empty' => "Kosong. Klik 'Tambah'",
        'hotspot_label_index' => 'Hotspot :number',
        'label_tooltip' => 'Teks Tooltip',
        'label_target_room' => 'Target Ruangan',
        'label_delete_confirm' => 'Hapus hotspot ini?',
        'label_hotspot_type' => 'Tipe Hotspot',
        'type_floor' => 'Lantai (3D Datar)',
        'type_door' => 'Pintu (Vertikal)',

        'preview_title' => 'Preview Panorama 360°',
        'preview_desc' => 'Klik titik target di panorama untuk mengambil Yaw/Pitch, atau geser panorama untuk melihat',
        'preview_placeholder' => 'Preview belum tersedia',
        'preview_placeholder_sub' => 'Pilih gambar 360° terlebih dahulu',

        'btn_cancel' => 'Batal',
        'btn_save' => 'Simpan Perubahan',

        // Flash messages
        'flash' => [
            'created' => 'Ruangan virtual berhasil ditambahkan.',
            'updated' => 'Ruangan virtual berhasil diperbarui.',
            'deleted' => 'Ruangan virtual berhasil dihapus.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | CMS — Ruangan Virtual 3D (virtual_3d_rooms)
    |--------------------------------------------------------------------------
    */

    'virtual_3d_rooms' => [
        'breadcrumb_parent' => 'Ruangan Virtual 3D',
        'breadcrumb_edit' => 'Edit: :name',
        'breadcrumb_create' => 'Tambah Ruangan',

        'page_title' => 'Ruangan Virtual 3D — :name',
        'page_desc' => 'Kelola ruangan virtual dengan 4 dinding dan pintu interaktif',
        'view_exhibition' => 'Lihat Pameran Virtual',
        'add_room' => 'Tambah Ruangan 3D',

        'stat_total_rooms' => 'Total Ruangan',
        'stat_total_rooms_sub' => 'Ruangan virtual 3D aktif',
        'stat_total_media' => 'Total Media',
        'stat_total_media_sub' => 'Gambar &amp; video di dinding',
        'stat_avg_media' => 'Rata-rata Media',
        'stat_avg_media_sub' => 'Per ruangan',

        'table_title' => 'Daftar Ruangan Virtual 3D',
        'col_no' => 'No',
        'col_thumbnail' => 'Thumbnail',
        'col_name' => 'Nama Ruangan',
        'col_desc' => 'Deskripsi',
        'col_media' => 'Media',
        'col_action' => 'Aksi',
        'empty' => 'Belum ada ruangan virtual 3D yang ditambahkan.',
        'delete_confirm' => 'Yakin ingin menghapus ruangan ini? Semua media di dinding akan ikut terhapus.',

        // Create form
        'form_title_create' => 'Tambah Ruangan Virtual 3D',
        'form_desc_create' => 'Atur informasi ruangan, warna dinding/lantai/atap, dan hotspot navigasi',
        'back_to_list' => 'Kembali ke Daftar Ruangan',

        // Edit form
        'form_title_edit' => 'Edit Ruangan: :name',

        // Flash messages
        'flash' => [
            'created' => 'Ruangan Virtual 3D berhasil ditambahkan. Sekarang Anda bisa menambahkan media ke dinding ruangan.',
            'updated' => 'Ruangan Virtual 3D berhasil diperbarui.',
            'deleted' => 'Ruangan Virtual 3D berhasil dihapus.',
        ],
        'form_desc_edit' => 'Atur informasi ruangan, warna, media dinding, dan hotspot navigasi',

        // Shared form
        'info_title' => 'Informasi Ruangan',
        'label_name' => 'Nama Ruangan',
        'label_desc' => 'Deskripsi',
        'label_thumbnail' => 'Thumbnail Ruangan',
        'thumbnail_help' => 'Gambar preview untuk daftar ruangan (JPG, PNG, WEBP)',
        'thumbnail_keep' => 'Biarkan kosong jika tidak ingin mengubah',
        'label_diameter_front' => 'Diameter Dinding Depan (cm)',
        'label_diameter_back' => 'Diameter Dinding Belakang (cm)',
        'label_diameter_left' => 'Diameter Dinding Kiri (cm)',
        'label_diameter_right' => 'Diameter Dinding Kanan (cm)',
        'diameter_help' => 'Ukuran diameter dinding dalam centimeter (default: 1000)',

        'colors_title' => 'Warna Ruangan',
        'label_wall_color' => 'Warna Dinding',
        'label_floor_color' => 'Warna Lantai',
        'label_ceiling_color' => 'Warna Atap',

        'door_title' => 'Pengaturan Pintu / Hotspot',
        'door_desc' => 'Pintu berada di dinding belakang ruangan 3D dan bisa mengarahkan pengunjung ke halaman atau ruangan lain.',
        'door_desc_edit' => 'Pintu di dinding belakang untuk navigasi ke halaman/ruangan lain',
        'label_door_type' => 'Tipe Tautan Pintu',
        'door_type_none' => 'Tidak Aktif (Hanya Visual)',
        'door_type_room' => 'Arahkan ke Ruangan Lain',
        'door_type_url' => 'Tautan Bebas (URL)',
        'label_target_room' => 'Target Ruangan',
        'target_room_placeholder' => '— Pilih Ruangan —',
        'rooms_available' => 'Ruangan tersedia: :count',
        'label_target_url' => 'Target URL',
        'label_door_label' => 'Label Pintu (Opsional)',
        'door_label_placeholder' => 'Contoh: KELUAR',

        'media_title' => 'Media Dinding (Foto / Video)',
        'media_save_first' => 'Simpan ruangan terlebih dahulu',
        'media_save_first_sub' => 'Setelah menyimpan, Anda akan diarahkan ke halaman edit untuk menambah foto/video ke dinding ruangan.',
        'media_items' => ':count item',
        'media_selected_wall' => 'Dinding Terpilih',
        'media_wall_front' => 'Dinding Depan',
        'media_wall_hint' => 'Pilih dinding di panel <strong>Editor Posisi Media</strong> di sebelah kanan',
        'media_type_label' => 'Tipe Media',
        'media_type_image' => 'Gambar (JPG/PNG)',
        'media_type_video' => 'Video (MP4)',
        'media_file_label' => 'File Upload',
        'media_upload_btn' => 'Unggah &amp; Tambah ke Dinding',
        'media_wall_label' => 'Dinding: :wall',
        'media_delete' => 'Hapus',
        'media_empty' => 'Belum ada media. Unggah file di atas.',
        'media_upload_success' => 'Media berhasil diunggah!',
        'media_upload_choose' => 'Pilih file untuk diunggah!',
        'media_upload_failed' => 'Unggah gagal.',
        'media_save_success' => 'Posisi & ukuran berhasil disimpan!',
        'media_save_failed' => 'Gagal menyimpan posisi.',
        'media_delete_confirm' => 'Yakin hapus media ini dari dinding?',
        'media_delete_success' => 'Media berhasil dihapus.',
        'media_delete_failed' => 'Gagal menghapus media.',
        'media_empty_wall' => 'Belum ada media di dinding ini',
        'media_count' => 'item',

        'preview_title' => 'Preview Ruangan 3D',
        'preview_desc' => 'Preview langsung ruangan 3D sesuai pengaturan warna Anda',
        'preview_desc_edit' => 'Preview langsung ruangan sesuai pengaturan warna Anda',
        'preview_front' => 'DEPAN',
        'preview_back' => 'BELAKANG',
        'preview_left' => 'KIRI',
        'preview_right' => 'KANAN',
        'preview_floor' => 'LANTAI',
        'preview_ceiling' => 'ATAP',
        'preview_door' => 'PINTU',
        'preview_btn_default' => 'Default',
        'preview_btn_front' => 'Depan',
        'preview_btn_left' => 'Kiri',
        'preview_btn_right' => 'Kanan',
        'preview_btn_back' => 'Belakang',
        'preview_btn_top' => 'Atas',

        'editor_title' => 'Editor Posisi Media di Dinding',
        'editor_desc' => 'Geser media untuk mengatur posisi di dinding. Klik media untuk menampilkan properti.',
        'editor_wall_front' => 'Dinding Depan',
        'editor_wall_left' => 'Dinding Kiri',
        'editor_wall_right' => 'Dinding Kanan',
        'editor_wall_back' => 'Dinding Belakang',
        'editor_wall_title_front' => 'DINDING DEPAN',
        'editor_wall_title_left'  => 'DINDING KIRI',
        'editor_wall_title_right' => 'DINDING KANAN',
        'editor_wall_title_back'  => 'DINDING BELAKANG',
        'editor_door_settings_for' => 'Pengaturan pintu untuk',
        'editor_props_title' => 'Properti Media yang Dipilih',
        'editor_props_delete' => 'Hapus',
        'editor_props_save' => 'Simpan Posisi',
        'media_desc_label' => 'Keterangan / Caption',
        'media_desc_placeholder' => 'Masukkan keterangan detail (mendukung format HTML)...',
        'caption_single' => 'Teks Tunggal',
        'caption_multi_qa' => 'Pertanyaan & Jawaban (Q&A)',
        'question' => 'Pertanyaan',
        'answer' => 'Jawaban',
        'add_qa' => 'Tambah Q&A',

        'btn_cancel' => 'Batal',
        'btn_save_create' => 'Simpan Ruangan',
        'btn_save_edit' => 'Simpan Perubahan',
    ],

    /*
    |--------------------------------------------------------------------------
    | CMS — Buku Virtual
    |--------------------------------------------------------------------------
    */

    'virtual_books' => [
        'breadcrumb_parent' => 'CMS',
        'breadcrumb_list' => 'Daftar Buku',
        'breadcrumb_create' => 'Tambah Buku',
        'breadcrumb_edit' => 'Edit Buku',

        'page_title' => 'Daftar Buku: :name',
        'page_desc' => 'Kelola buku dalam fitur ini',
        'add_button' => 'Tambah Buku',
        'table_title' => 'Daftar Buku',

        'col_cover' => 'Cover',
        'col_title' => 'Judul Buku',
        'col_pages' => 'Jml Halaman',
        'col_order' => 'Urutan',
        'col_action' => 'Aksi',

        'no_cover' => 'No Cover',
        'page_count' => ':count halaman',
        'detail_title' => 'Detail - Kelola Halaman',
        'edit_cover' => 'Edit Cover Buku',
        'empty' => 'Belum ada buku. Klik "Tambah Buku" untuk membuat buku pertama.',

        'delete' => [
            'title' => 'Hapus Buku',
            'confirm' => 'Yakin ingin menghapus buku',
            'confirm_warn' => '? Semua halaman juga akan dihapus.',
            'yes' => 'Ya, Hapus',
        ],

        // Create form
        'create_title' => 'Tambah Buku Baru',
        'create_desc' => 'Buat buku baru dalam fitur :name',
        'back_to_list' => 'Kembali ke Daftar Buku',

        // Edit form
        'edit_title' => 'Edit Buku: :name',
        'edit_desc' => 'Perbarui pengaturan cover buku',
        'book_settings' => 'Pengaturan Buku',

        // Form fields
        'form' => [
            'title' => 'Judul Buku',
            'title_placeholder' => 'Masukkan judul buku',
            'cover' => 'Cover Buku',
            'cover_help' => 'JPG, PNG, atau WebP.',
            'cover_help_optional' => 'JPG, PNG, atau WebP. Opsional.',
            'remove_cover' => 'Hapus cover',
            'remove_back_cover' => 'Hapus cover belakang',
            'additional_text' => 'Teks Tambahan (Opsional)',
            'additional_text_help' => 'Tambahkan teks seperti subjudul atau deskripsi sampul',
            'additional_text_placeholder' => 'Teks tambahan :number',
            'add_text' => 'Tambah Teks',
            'back_cover' => 'Sampul Belakang',
            'back_title' => 'Judul Buku (Belakang)',
            'back_title_placeholder' => 'Judul untuk sampul belakang (opsional)',
            'back_cover_label' => 'Cover Buku (Belakang)',
            'back_text' => 'Teks Tambahan (Belakang)',
            'back_text_help' => 'Tambahkan teks untuk sampul belakang',
            'thumbnail' => 'Thumbnail Daftar',
            'thumbnail_will_save' => 'Thumbnail yang akan disimpan:',
            'thumbnail_new_will_save' => 'Thumbnail baru yang akan disimpan:',
            'remove_thumbnail' => 'Hapus thumbnail',
            'remove' => 'Hapus',
            'cancel_remove' => 'Batal',
            'generate_thumbnail' => 'Generate dari Preview',
            'generate_help' => 'Atau upload manual. Generate akan membuat thumbnail dari preview buku.',
            'order' => 'Urutan',
            'order_help' => 'Urutan tampilan buku dalam fitur',
            'pdf_section' => 'File PDF (Opsional)',
            'upload_pdf' => 'Upload PDF',
            'pdf_desc' => 'Jika diupload, buku akan menggunakan PDF ini sebagai isi flipbook.',
            'book_info' => 'Informasi Buku',
            'author' => 'Penulis',
            'dimensions' => 'Dimensi',
            'total_pages' => 'Jumlah Halaman',
            'weight' => 'Berat',
            'language' => 'Bahasa',
            'publisher' => 'Penerbit',
            'publication_year' => 'Tahun Terbit',
            'isbn' => 'ISBN',
            'synopsis' => 'Sinopsis',
            'description' => 'Deskripsi (Ditampilkan di Halaman Detail Buku)',
        ],

        // Preview
        'preview_title' => 'Preview Cover Buku',
        'preview_placeholder' => 'Upload cover untuk preview',
        'preview_default_title' => 'Judul Buku',
        'preview_back_title' => 'Preview Sampul Belakang',
        'preview_back_placeholder' => 'Upload cover belakang',
        'zoom_out' => 'Perkecil',
        'zoom_in' => 'Perbesar',
        'reset_position' => 'Reset Posisi',
        'drag_hint' => 'Geser elemen untuk mengatur posisi | Scroll pada gambar untuk ubah ukuran',

        // Flash messages
        'flash' => [
            'created' => 'Buku berhasil ditambahkan',
            'updated' => 'Buku berhasil diperbarui',
            'deleted' => 'Buku berhasil dihapus',
        ],

        // Buttons
        'btn_cancel' => 'Batal',
        'btn_save' => 'Simpan Halaman',
        'btn_save_changes' => 'Simpan Perubahan',

        // JS messages
        'pdf_loading' => 'Menghitung halaman PDF...',
        'pdf_success' => 'Berhasil mendeteksi :count halaman',
        'pdf_failed' => 'Gagal membaca halaman PDF',
        'upload_failed' => 'Gagal mengunggah file.',
        'pdf_info' => 'Buku ini menggunakan file PDF. Halaman manual di bawah akan diabaikan pada tampilan pameran.',
    ],

    /*
    |--------------------------------------------------------------------------
    | CMS — Halaman Buku Virtual
    |--------------------------------------------------------------------------
    */

    'virtual_book_pages' => [
        'breadcrumb_parent' => 'Buku Virtual',
        'breadcrumb_list' => 'Halaman Buku',
        'breadcrumb_create' => 'Tambah Halaman',
        'breadcrumb_edit' => 'Edit Halaman',

        'page_title' => 'Halaman: :name',
        'page_desc' => 'Kelola halaman dalam buku ini',
        'edit_cover' => 'Edit Cover',
        'add_button' => 'Tambah Halaman',
        'no_cover' => 'No Cover',
        'page_count' => ':count halaman',
        'table_title' => 'Daftar Halaman Buku',

        'col_thumbnail' => 'Thumbnail',
        'col_title' => 'Judul',
        'col_type' => 'Tipe',
        'col_order' => 'Urutan',
        'col_action' => 'Aksi',

        'no_thumb' => 'No Thumb',
        'type_cover' => 'Sampul Depan',
        'type_back_cover' => 'Sampul Belakang',
        'type_content' => 'Halaman Isi',
        'empty' => 'Belum ada halaman. Klik "Tambah Halaman" untuk memulai.',

        'delete' => [
            'title' => 'Hapus Halaman',
            'confirm' => 'Yakin ingin menghapus halaman',
            'yes' => 'Ya, Hapus',
        ],

        // Create form
        'create_title' => 'Tambah Halaman Buku',
        'create_desc' => 'Tambahkan halaman baru untuk buku virtual',
        'back_to_list' => 'Kembali ke Daftar',

        // Edit form
        'edit_title' => 'Edit Halaman: :name',
        'edit_desc' => 'Perbarui informasi halaman buku virtual',

        // Form fields
        'form' => [
            'images_title' => 'Gambar Halaman',
            'upload_images' => 'Upload Gambar (Bisa Banyak)',
            'upload_images_help' => 'JPG, PNG, atau WebP. Maks 2MB per gambar. Bisa upload beberapa gambar sekaligus.',
            'current_images' => 'Gambar Saat Ini',
            'existing_label' => 'Ada',
            'remove_all_images' => 'Hapus semua gambar',
            'upload_new_images' => 'Upload Gambar Baru',
            'upload_new_images_help' => 'JPG, PNG, atau WebP. Maks 2MB per gambar.',
            'page_info' => 'Informasi Halaman',
            'title' => 'Judul Halaman',
            'title_placeholder' => 'Masukkan judul halaman',
            'content' => 'Konten Teks',
            'content_placeholder' => 'Masukkan konten teks halaman',
            'image_size' => 'Ukuran Gambar (%)',
            'image_size_help' => 'Atur tinggi gambar dalam halaman',
            'image_fit_mode' => 'Mode Tampilan Gambar',
            'image_fit_contained' => 'Dalam Batas Konten',
            'image_fit_fullbleed' => 'Penuh (Full Bleed)',
            'image_fit_mode_help' => 'Pilih "Dalam Batas Konten" agar gambar dibatasi oleh judul & footer. Pilih "Penuh" agar gambar menutupi seluruh halaman.',
            'order' => 'Urutan',
            'order_help' => 'Urutan tampilan halaman dalam buku',
            'thumbnail_title' => 'Thumbnail Halaman',
            'current_thumbnail' => 'Thumbnail Saat Ini',
            'remove_thumbnail' => 'Hapus thumbnail',
            'upload_thumbnail' => 'Upload Thumbnail',
            'upload_new_thumbnail' => 'Upload Thumbnail Baru',
            'thumbnail_will_save' => 'Thumbnail yang akan disimpan:',
            'thumbnail_new_will_save' => 'Thumbnail baru yang akan disimpan:',
            'remove' => 'Hapus',
            'cancel_remove' => 'Batal',
            'generate_thumbnail' => 'Generate dari Preview',
            'generate_help' => 'Atau upload manual. Generate akan membuat thumbnail dari preview halaman.',
        ],

        // Preview
        'preview_title' => 'Preview Halaman',
        'preview_hint' => 'Geser langsung elemen di preview dengan cursor',
        'default_title' => 'Judul Halaman',
        'new_label' => 'Baru :number',

        // Buttons
        'btn_cancel' => 'Batal',
        'btn_save' => 'Simpan Halaman',
        'btn_save_changes' => 'Simpan Perubahan',

        // JS messages
        'js' => [
            'generating' => 'Generating...',
            'generate_failed' => 'Gagal generate thumbnail: ',
            'generate_btn' => 'Generate dari Preview',
            'preview_not_found' => 'Preview buku tidak ditemukan',
            'upload_cover_first' => 'Silakan upload cover buku terlebih dahulu',
        ],

        // Flash messages
        'flash' => [
            'created' => 'Halaman buku berhasil ditambahkan',
            'updated' => 'Halaman buku berhasil diperbarui',
            'deleted' => 'Halaman buku berhasil dihapus',
        ],
    ],

    // Opsi tipe halaman (shared: show.blade.php sub menu modals)
    'page_types' => [
        'label' => 'Tipe Halaman',
        'none' => 'Tidak Ada',
        'beranda' => 'Beranda',
        'onsite' => 'Pameran Arsip Onsite',
        'real' => 'Pameran Arsip Virtual Real (360°)',
        '3d' => 'Pameran Arsip Virtual 3D',
        'book' => 'Pameran Arsip Virtual Buku',
        'slideshow' => 'Pameran Arsip Virtual SlideShow',
        'profile' => 'Profil',
        'publication' => 'Publikasi',
        'layanan_publik' => 'Layanan Publik',
        'pengelolaan' => 'Pengelolaan',
        'kontak_kami' => 'Kontak Kami',
    ],

    /*
    |--------------------------------------------------------------------------
    | Common (shared across CMS pages)
    |--------------------------------------------------------------------------
    */

    'common' => [
        'cancel' => 'Batal',
        'save_changes' => 'Simpan Perubahan',
        'save_content' => 'Simpan Konten',
        'back' => 'Kembali',
        'required' => '*',
        'saved_successfully' => 'Pengaturan berhasil disimpan.',
        'download' => 'Unduh',
        'zoom' => 'Perbesar',
        'hide' => 'Sembunyikan',
        'show' => 'Tampilkan',
        'delete' => 'Hapus',
    ],

    /*
    |--------------------------------------------------------------------------
    | CMS — Virtual Slideshow
    |--------------------------------------------------------------------------
    */

    'virtual_slideshow' => [
        'title' => 'Pameran Arsip Virtual Slideshow',

        // Table columns
        'col_order' => 'Urutan',
        'col_thumbnail' => 'Thumbnail',
        'col_title' => 'Judul',
        'col_type' => 'Tipe',
        'col_slides' => 'Jumlah Slide',
        'col_content' => 'Konten',
        'col_action' => 'Aksi',

        // Index page
        'pages_list_title' => 'Daftar Halaman / Exhibition',
        'pages_list_desc' => 'Kelola halaman pameran arsip virtual dan konten slide di dalamnya.',
        'add_page' => 'Tambah Halaman',
        'empty_pages' => 'Belum ada halaman. Buat halaman terlebih dahulu di menu "Kelola Halaman".',
        'slides_count' => ':count slides',
        'manage_slides' => 'Kelola Slides',
        'edit_page' => 'Edit Halaman',
        'view_public' => 'Lihat Halaman Publik',

        // Delete modals
        'delete_page_title' => 'Hapus Halaman',
        'delete_page_confirm' => 'Apakah Anda yakin ingin menghapus halaman',
        'delete_slide_title' => 'Hapus Slide',
        'delete_slide_confirm' => 'Apakah Anda yakin ingin menghapus slide',
        'delete_video_upload_title' => 'Hapus Video Upload',
        'delete_video_upload_confirm' => 'Apakah Anda yakin ingin menghapus video yang diupload ini?',
        'delete_video_url_title' => 'Hapus Video URL',
        'delete_video_url_confirm' => 'Apakah Anda yakin ingin menghapus video URL ini?',

        // Create/Edit page form
        'create_page_title' => 'Tambah Halaman Exhibition',
        'edit_page_title' => 'Edit Halaman Exhibition',
        'page_info' => 'Informasi Halaman',
        'page_title_label' => 'Judul Halaman',
        'page_title_placeholder' => 'Judul halaman exhibition...',
        'page_desc_label' => 'Deskripsi',
        'page_desc_placeholder' => 'Deskripsi singkat...',
        'page_order_label' => 'Urutan',
        'page_order_help' => 'Urutan tampilan di halaman publik',
        'page_thumbnail_label' => 'Thumbnail',
        'upload_image_hint' => 'Klik untuk upload gambar',
        'thumbnail_optional' => 'Opsional. Jika tidak diisi, thumbnail akan otomatis dari slide pertama.',
        'thumbnail_edit_help' => 'Opsional. Jika tidak diisi, thumbnail tetap seperti sebelumnya.',
        'current_thumbnail' => 'Thumbnail saat ini',
        'save_page' => 'Simpan Halaman',
        'update_page' => 'Perbarui Halaman',

        // Slides index
        'manage_slides_title' => 'Kelola Slides: :title',
        'slides_list_title' => 'Daftar Slide',
        'slides_list_desc' => 'Atur urutan slide dan kelola konten interaktif.',
        'add_slide' => 'Tambah Slide',
        'add_first_slide' => 'Tambah Slide Pertama',
        'empty_slides' => 'Belum ada slide. Klik "Tambah Slide" untuk memulai.',
        'untitled' => '(tanpa judul)',
        'images_count' => ':count gambar',
        'has_video' => 'Video',
        'info_popup_count' => ':count info popup',
        'view_exhibition' => 'Lihat Halaman Publik (Exhibition #:order)',

        // Slide types
        'type_hero' => 'Hero',
        'type_text' => 'Teks',
        'type_carousel' => 'Carousel',
        'type_video' => 'Video',
        'type_text_carousel' => 'Teks + Carousel',
        'type_hero_desc' => 'Banner pembuka',
        'type_text_desc' => 'Konten teks saja',
        'type_carousel_desc' => 'Slideshow gambar',
        'type_video_desc' => 'Embed video',
        'type_text_carousel_desc' => 'Layout terbagi',

        // Create/Edit slide form
        'create_slide_title' => 'Tambah Slide Baru',
        'edit_slide_title' => 'Edit Slide',
        'page_label' => 'Halaman: :title',
        'errors_found' => 'Terdapat kesalahan:',
        'step1_type' => '1. Pilih Tipe Slide',
        'step2_content' => '2. Konten',
        'step3_media' => '3. Media',
        'step4_video' => '4. Video',
        'slide_title_label' => 'Judul',
        'optional' => 'opsional',
        'slide_subtitle_label' => 'Sub-judul',
        'slide_desc_label' => 'Deskripsi / Konten Teks',
        'desc_toolbar_hint' => 'opsional - gunakan toolbar untuk formatting',
        'layout_label' => 'Layout',
        'layout_left' => 'Teks Kiri, Gambar Kanan',
        'layout_center' => 'Tengah',
        'layout_right' => 'Gambar Kiri, Teks Kanan',
        'bg_color_label' => 'Warna Background',
        'order_label' => 'Urutan',
        'media_type_images' => 'Gambar',
        'media_type_videos' => 'Video',
        'method_upload' => 'Upload File',
        'method_url' => 'URL',
        'image_upload_hint' => 'Klik untuk pilih gambar (bisa banyak)',
        'image_url_placeholder' => 'https://contoh.com/gambar.jpg atau link Google Drive',
        'add_image_url' => 'Tambah URL Gambar',
        'open_link' => 'Buka link',
        'popup_caption_images' => 'Info Popup Caption per Gambar',
        'popup_caption_hint' => 'klik tombol ? akan menampilkan teks ini',
        'upload_images_first' => 'Upload atau masukkan URL gambar terlebih dahulu untuk mengisi popup caption.',
        'hero_single_image' => 'Hero hanya bisa memiliki 1 gambar.',
        'hero_image_upload_hint' => 'Klik untuk pilih gambar (hanya 1)',
        'hero_exists_title' => 'Tidak dapat Memilih Hero',
        'hero_exists_error' => 'Halaman ini sudah memiliki slide Hero. Hanya 1 Hero yang diperbolehkan per halaman.',
        'hero_url_restriction' => 'Hero hanya bisa memiliki 1 gambar. Hapus gambar upload terlebih dahulu.',
        'hero_upload_restriction' => 'Hero hanya bisa memiliki 1 gambar. Hapus gambar URL terlebih dahulu.',
        'hero_limit_warning' => 'Hanya 1 gambar yang diperbolehkan untuk Hero. Hapus gambar yang ada terlebih dahulu.',
        'carousel_video_url_placeholder' => 'https://youtube.com/watch?v=... atau link Google Drive',
        'add_video_url' => 'Tambah URL Video',
        'carousel_video_upload_hint' => 'Klik untuk pilih video (bisa banyak, .mp4, .webm)',
        'popup_caption_videos' => 'Info Popup Caption per Video',
        'add_videos_first' => 'Tambahkan video terlebih dahulu untuk mengisi popup caption.',
        'single_video_url_placeholder' => 'https://youtube.com/watch?v=..., Google Drive, atau URL video lainnya',
        'preview' => 'Preview',
        'popup_video_url' => 'Info Popup Caption Video (URL)',
        'video_upload_hint' => 'Klik untuk pilih video (.mp4, .webm)',
        'popup_video_upload' => 'Info Popup Caption Video (Upload)',
        'save_slide' => 'Simpan Slide',
        'update_slide' => 'Perbarui Slide',
        'caption_single' => 'Caption Tunggal',
        'caption_multi_qa' => 'Multi Tanya-Jawab',
        'question' => 'Pertanyaan',
        'answer' => 'Jawaban',
        'add_qa' => '+ Tambah Tanya-Jawab',
        'existing_images' => 'Gambar upload yang sudah ada',
        'existing_video_url' => 'Video URL yang sudah ada',
        'existing_video_upload' => 'Video upload yang sudah ada',
        'add_new_images' => 'Tambah gambar baru (upload)',
        'popup_existing_images' => 'Info Popup Caption (gambar upload)',
        'popup_url_images' => 'Info Popup Caption (gambar URL)',
        'image_number' => 'Gambar :number',
        'view' => 'Lihat',
        'open' => 'Buka',

        // Common
        'cancel' => 'Batal',
        'delete' => 'Hapus',

        // Flash messages
        'flash' => [
            'page_created' => 'Halaman exhibition berhasil dibuat.',
            'page_updated' => 'Halaman exhibition berhasil diperbarui.',
            'page_deleted' => 'Halaman exhibition berhasil dihapus.',
            'slide_created' => 'Slide berhasil ditambahkan.',
            'slide_updated' => 'Slide berhasil diperbarui.',
            'slide_deleted' => 'Slide berhasil dihapus.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | CMS — Profile Page
    |--------------------------------------------------------------------------
    */

    'profile' => [
        'breadcrumb_parent' => 'CMS',
        'breadcrumb_active' => 'Profil',

        'page_title' => 'Halaman Profil — :name',
        'page_desc' => 'Kelola halaman profil untuk fitur :name',
        'view_page' => 'Lihat Halaman Publik',

        // Profile page types
        'type_default' => 'Default',
        'type_sdm_chart' => 'SDM (Grafik)',
        'type_struktur_image' => 'Struktur Organisasi',
        'type_tugas_fungsi' => 'Tugas dan Fungsi',

        // Pages list
        'col_title' => 'Judul Halaman',
        'col_type' => 'Tipe Halaman',
        'col_sections' => 'Bagian',
        'col_order' => 'Urutan',
        'col_action' => 'Aksi',
        'empty' => 'Belum ada halaman profil. Klik "+ Tambah Halaman" untuk membuat.',
        'add_button' => 'Tambah Halaman',

        // Add/Edit modal
        'add_title' => 'Tambah Halaman Profil',
        'edit_title' => 'Edit Halaman Profil',
        'create_title' => 'Tambah Halaman',
        'form_title_label' => 'Judul Halaman',
        'form_title_placeholder' => 'Masukkan judul halaman',
        'form_type_label' => 'Tipe Halaman',
        'form_type_help' => 'Pilih tipe halaman. Setiap tipe memiliki kolom yang berbeda.',
        'form_description_label' => 'Konten',
        'form_description_placeholder' => 'Masukkan konten halaman...',
        'form_subtitle_label' => 'Sub-judul',
        'form_subtitle_placeholder' => 'Masukkan sub-judul',
        'form_link_text_label' => 'Teks Link',
        'form_link_text_placeholder' => 'contoh: Selengkapnya',
        'form_link_url_label' => 'URL Link',
        'form_link_url_placeholder' => 'https://contoh.com',
        'form_logo_label' => 'Logo',
        'form_logo_help' => 'PNG atau WebP dengan background transparan. Maks 2MB.',
        'form_order_label' => 'Urutan',
        'form_chart_section' => 'Grafik (SDM)',
        'form_generate_chart' => 'Generate Grafik',
        'form_generate_chart_desc' => 'Buat grafik secara otomatis dari data user internal (Admin & Pegawai saja).',
        'form_chart_pie' => 'Grafik Pie (Jenis Kelamin)',
        'form_chart_bar' => 'Grafik Bar (Kelompok Umur)',
        'form_chart_preview' => 'Preview Grafik',
        'form_chart_no_data' => 'Belum ada data grafik. Klik "Generate Grafik" untuk membuat.',
        'form_chart_no_users' => 'Data user internal tidak ditemukan. Tambahkan user Admin dan Pegawai terlebih dahulu.',
        'form_gambar_section' => 'Gambar',
        'form_gambar_help' => 'Unggah gambar untuk bagian ini. Maks 2MB per gambar.',
        'btn_save_return' => 'Simpan & Kembali',

        // Delete
        'delete_title' => 'Hapus Halaman Profil',
        'delete_confirm' => 'Apakah Anda yakin ingin menghapus halaman',
        'delete_yes' => 'Ya, Hapus',

        // Flash
        'flash' => [
            'page_added' => 'Halaman profil berhasil ditambahkan.',
            'page_updated' => 'Halaman profil berhasil diperbarui.',
            'page_deleted' => 'Halaman profil berhasil dihapus.',
        ],

        // Buttons
        'btn_cancel' => 'Batal',
        'btn_save' => 'Simpan Halaman',
        'btn_save_changes' => 'Simpan Perubahan',

        // Sections (for page section management)
        'sections_title' => 'Bagian — :name',
        'sections_desc' => 'Kelola bagian untuk halaman profil ini. Bagian dapat berisi judul, deskripsi, dan gambar.',
        'sections_list' => 'Daftar Bagian',
        'add_section' => 'Tambah Bagian',
        'add_section_title' => 'Tambah Bagian',
        'edit_section_title' => 'Edit Bagian',
        'section_order' => 'Urutan: :order',
        'empty_sections' => 'Belum ada bagian. Klik "+ Tambah Bagian" untuk membuat.',
        'section_form_title' => 'Judul Bagian',
        'section_form_title_placeholder' => 'Masukkan judul bagian',
        'section_form_description' => 'Deskripsi',
        'section_form_description_placeholder' => 'Masukkan deskripsi (opsional)',
        'section_form_images' => 'Gambar',
        'section_form_add_images' => 'Unggah Gambar',
        'section_form_add_more_images' => 'Tambah Gambar Lainnya',
        'section_form_images_help' => 'Pilih satu atau lebih gambar (JPEG, PNG, WebP). Maks 2MB per gambar.',
        'section_form_order' => 'Urutan',

        // Delete section
        'delete_section_title' => 'Hapus Bagian',
        'delete_section_confirm' => 'Apakah Anda yakin ingin menghapus bagian',
        'delete_section_yes' => 'Ya, Hapus',

        // Public
        'chart_pie' => 'Grafik Pie (Jenis Kelamin)',
        'chart_bar' => 'Grafik Bar (Kelompok Umur)',
        'public_empty' => 'Belum ada halaman profil yang tersedia.',
    ],

    /*
    |--------------------------------------------------------------------------
    | CMS — Manajemen Pengguna (pengguna/index)
    |--------------------------------------------------------------------------
    */
    'pengguna' => [
        'title' => 'Manajemen Pengguna',
        'subtitle' => 'Daftar Pengguna',
        'breadcrumb' => 'Pengguna',
        'list' => 'Daftar Pengguna',

        // Stats
        'stats_total' => 'Total Pengguna',
        'stats_admin' => 'Admin',
        'stats_pegawai' => 'Pegawai',
        'stats_eksternal' => 'User Eksternal',
        'stats_verified' => 'Terverifikasi',
        'stats_total_sub' => 'Jumlah seluruh pengguna',
        'stats_admin_sub' => 'Akun administrator',
        'stats_pegawai_sub' => 'Akun pegawai ANRI',
        'stats_eksternal_sub' => 'Akun selain admin & pegawai',
        'stats_verified_sub' => 'Email sudah diverifikasi',

        // Filters
        'filter_role' => 'Pilih Peran',
        'filter_status' => 'Pilih Status',
        'filter_verified_all' => 'Semua Status',
        'filter_verified_yes' => 'Terverifikasi',
        'filter_verified_no' => 'Belum Verifikasi',

        // Table
        'col_user' => 'Pengguna',
        'col_email' => 'Email',
        'col_username' => 'Username',
        'col_role' => 'Peran',
        'col_status' => 'Status',
        'col_joined' => 'Bergabung',
        'col_action' => 'Aksi',

        // Buttons
        'add_button' => 'Tambah Pengguna',
        'edit_button' => 'Edit',
        'delete_button' => 'Hapus',
        'cancel' => 'Batal',
        'save' => 'Simpan',
        'update' => 'Perbarui',
        'back' => 'Kembali',

        // Forms
        'create_title' => 'Tambah Pengguna Baru',
        'create_subtitle' => 'Buat akun pengguna baru untuk sistem',
        'edit_title' => 'Edit Pengguna',
        'edit_subtitle' => 'Perbarui informasi pengguna',
        'form_name' => 'Nama Lengkap',
        'form_name_placeholder' => 'Masukkan nama lengkap',
        'form_username' => 'Username',
        'form_username_placeholder' => 'Wajib diisi',
        'form_email' => 'Email',
        'form_email_placeholder' => 'contoh@email.com',
        'form_role' => 'Peran',
        'form_role_placeholder' => '-- Pilih Peran --',
        'form_password' => 'Password',
        'form_password_placeholder' => 'Minimal 8 karakter',
        'form_password_confirmation' => 'Konfirmasi Password',
        'form_password_optional' => 'Kosongkan jika tidak ingin mengubah password',
        'form_photo' => 'Foto Profil',
        'form_photo_help' => 'JPG/PNG maksimal 2MB. Opsional.',
        'form_photo_current' => 'Foto saat ini',

        // Data profil role
        'form_profile_title' => 'Data Profil Pengguna',
        'form_profile_desc' => 'Data tambahan sesuai peran pengguna. Semua kolom bersifat opsional.',
        'form_nip' => 'NIP',
        'form_nip_placeholder' => 'Masukkan NIP (18 digit)',
        'form_jenis_kelamin' => 'Jenis Kelamin',
        'form_tempat_lahir' => 'Tempat Lahir',
        'form_tempat_lahir_placeholder' => 'Contoh: Jakarta',
        'form_tanggal_lahir' => 'Tanggal Lahir',
        'form_kartu_identitas' => 'Kartu Identitas (Upload)',
        'form_kartu_identitas_help' => 'JPG/PNG/PDF maksimal 2MB. Opsional.',
        'form_kartu_identitas_current' => 'File saat ini',
        'form_kartu_identitas_view' => 'Lihat file',
        'form_nomor_kartu_identitas' => 'Nomor Kartu Identitas',
        'form_nomor_kartu_identitas_placeholder' => 'Masukkan nomor KTP/KTM/NIK',
        'form_alamat' => 'Alamat',
        'form_alamat_placeholder' => 'Alamat lengkap',
        'form_nomor_whatsapp' => 'Nomor WhatsApp',
        'form_nomor_whatsapp_placeholder' => 'Contoh: 0831xxxxxxxx',
        'form_agama' => 'Agama',
        'form_agama_placeholder' => '— Pilih Agama —',
        'form_jabatan' => 'Jabatan',
        'form_jabatan_placeholder' => '— Pilih Jabatan —',
        'form_pangkat_golongan' => 'Pangkat / Golongan',
        'form_pangkat_golongan_placeholder' => '— Pilih Pangkat —',
        'form_jenis_keperluan' => 'Jenis Keperluan',
        'form_jenis_keperluan_placeholder' => '— Pilih Keperluan —',
        'form_judul_keperluan' => 'Judul Keperluan',
        'form_judul_keperluan_placeholder' => 'Contoh: Penelitian Skripsi',
        'keperluan_register_only' => 'Hanya Daftar Akun',
        'keperluan_research' => 'Penelitian',
        'keperluan_visit' => 'Kunjungan',

        // Status badges
        'status_verified' => 'Terverifikasi',
        'status_pending' => 'Menunggu',

        // Delete
        'delete_title' => 'Hapus Pengguna',
        'delete_confirm' => 'Apakah Anda yakin ingin menghapus pengguna :name? Tindakan ini tidak dapat dibatalkan.',
        'delete_yes' => 'Ya, Hapus',

        // Flash
        'created_successfully' => 'Pengguna berhasil ditambahkan.',
        'updated_successfully' => 'Pengguna berhasil diperbarui.',
        'deleted_successfully' => 'Pengguna berhasil dihapus.',
        'cannot_delete_self' => 'Anda tidak dapat menghapus akun Anda sendiri.',
        'already_verified' => 'Email pengguna ini sudah terverifikasi.',
        'verification_sent' => 'Tautan verifikasi berhasil dikirim.',
        'marked_verified' => 'Email :name telah ditandai terverifikasi.',

        // Empty
        'empty' => 'Belum ada pengguna.',

        // Export / DataTables buttons
        'btn_copy' => 'Copy',
        'btn_csv' => 'CSV',
        'btn_excel' => 'Excel',
        'btn_word' => 'Word',
        'btn_pdf' => 'PDF',
        'btn_print' => 'Print',
        'btn_export' => 'Ekspor',
        'filter_section_title' => 'Filter',
    ],

    /*
    |--------------------------------------------------------------------------
    | CMS — Manajemen Peran (roles/index)
    |--------------------------------------------------------------------------
    */
    'roles' => [
        'title' => 'Manajemen Peran',
        'subtitle' => 'Daftar Peran Pengguna',
        'breadcrumb' => 'Peran',

        // Stats
        'stats_total' => 'Total Peran',
        'stats_system' => 'Sistem',
        'stats_custom' => 'Kustom',

        // Table
        'col_no' => 'No',
        'col_name' => 'Nama Peran',
        'col_label' => 'Label',
        'col_table' => 'Tabel Profil',
        'col_type' => 'Tipe',
        'col_users' => 'Jumlah User',

        // Badges
        'type_system' => 'Sistem',
        'type_custom' => 'Kustom',

        // Buttons
        'add_button' => 'Tambah Peran',

        // Forms
        'create_title' => 'Tambah Peran Baru',
        'create_subtitle' => 'Buat peran pengguna baru',
        'edit_title' => 'Edit Peran',

        'form_name' => 'Nama Peran',
        'form_name_placeholder' => 'Contoh: mitra',
        'form_name_help' => 'Nama unik (lowercase, tanpa spasi). Digunakan sebagai key di database.',
        'form_name_warning' => 'Peringatan: hanya huruf kecil, angka, dan underscore (tanpa spasi & tanpa huruf besar).',
        'form_label' => 'Label Tampilan',
        'form_label_placeholder' => 'Contoh: Mitra / Partner',
        'form_type' => 'Tipe Peran',
        'form_type_help' => 'Peran sistem tidak dapat dihapus. Peran kustom dapat dihapus jika tidak memiliki pengguna.',
        'form_table_name' => 'Nama Tabel Profil',
        'form_table_name_placeholder' => 'Contoh: user_mitras',
        'form_table_name_help' => 'Nama tabel di database untuk menyimpan data profil peran ini.',
        'form_relation_name' => 'Nama Relasi Model',
        'form_relation_name_placeholder' => 'Contoh: userMitra',
        'form_relation_name_help' => 'Nama method relasi di model User. Contoh: userMitra.',
        'form_description' => 'Deskripsi',
        'form_description_placeholder' => 'Deskripsi singkat peran ini...',
        'form_registerable' => 'Pendaftaran Akun',
        'form_registerable_help' => 'Tampilkan di Pendaftaran (aktifkan agar role ini tampil di pilihan registrasi publik).',

        'name_system_locked' => 'Nama peran sistem tidak dapat diubah.',

        // Validation errors
        'validation_name_unique' => 'Nama peran sudah digunakan. Silakan pilih nama lain.',
        'validation_name_regex' => 'Nama peran hanya boleh mengandung huruf kecil, angka, dan underscore (tanpa spasi dan tanpa huruf besar).',
        'validation_table_name_unique' => 'Nama Tabel Profil sudah digunakan oleh peran lain.',
        'validation_table_name_regex' => 'Nama tabel hanya boleh mengandung huruf kecil, angka, dan underscore.',
        'validation_relation_name_unique' => 'Nama Relasi Model sudah digunakan oleh peran lain.',
        'validation_relation_name_regex' => 'Nama relasi harus camelCase: huruf kecil diawal, lalu huruf/angka.',
        'validation_table_name_required' => 'Nama Tabel Profil wajib diisi.',
        'validation_relation_name_required' => 'Nama Relasi Model wajib diisi.',

        // Delete
        'delete_confirm' => 'Apakah Anda yakin ingin menghapus peran ":name"? Peran yang memiliki user tidak dapat dihapus.',

        // Flash
        'created_successfully' => 'Peran berhasil ditambahkan.',
        'updated_successfully' => 'Peran berhasil diperbarui.',
        'deleted_successfully' => 'Peran berhasil dihapus.',
        'cannot_delete_with_users' => 'Peran ini tidak dapat dihapus karena masih memiliki pengguna.',
        'cannot_delete_has_users' => 'Peran ini tidak dapat dihapus karena masih memiliki :count pengguna.',
        'cannot_delete_system' => 'Peran sistem tidak dapat dihapus.',

        // Permissions
        'permissions_title' => 'Izin Menu',
        'permissions_desc' => 'Atur akses menu sidebar untuk peran ini.',
        'permissions_access' => 'Akses Menu',

        // Columns management
        'col_columns' => 'Kolom',
        'columns_count' => 'kolom',
        'columns_title' => 'Struktur Kolom Tabel',
        'columns_desc' => 'Tentukan kolom-kolom yang ada di tabel profil peran ini. Kolom akan otomatis dibuat di database.',
        'add_column' => 'Tambah Kolom',
        'select_template' => 'Pilih Template',
        'empty_template' => 'Kosong',
        'column' => 'Kolom',
        'table_structure' => 'Struktur Tabel',
        'no_columns' => 'Belum ada kolom yang ditambahkan.',
        'col_column_name' => 'Nama Kolom (DB)',
        'col_column_type' => 'Tipe Data',
        'col_column_label' => 'Label Tampilan',
        'col_nullable' => 'Nullable',
        'col_unique' => 'Unique',
        'col_column_length' => 'Panjang',
        'col_options' => 'Opsi',
        'sync_columns' => 'Sinkronkan Kolom',
        'sync_confirm' => 'Sinkronkan kolom dari tabel database ke form ini? Kolom yang ada akan diperbarui.',
        'columns_synced' => 'Kolom berhasil disinkronkan dari tabel database.',
        'col_references_table' => 'Tabel Referensi',
        'col_references_column' => 'Kolom Referensi',
        'col_on_delete' => 'Saat Hapus',
        'col_on_update' => 'Saat Ubah',
        'col_primary' => 'Primary',
        'col_unsigned' => 'Unsigned',
        'col_auto_increment' => 'Auto Increment',
        'col_foreign' => 'Foreign Key',
        'col_default' => 'Default',

        // Validation/error messages
        'error_unsigned_not_supported' => "Kolom ':column': UNSIGNED tidak didukung untuk tipe ':type'.",
        'error_auto_increment_integer_only' => "Kolom ':column': AUTO_INCREMENT hanya didukung untuk tipe integer MySQL.",
        'error_auto_increment_not_null' => "Kolom ':column': AUTO_INCREMENT tidak boleh NULL.",
        'error_column_prefix' => "Kolom ':column' (MySQL :code): :message",
        'error_mysql_prefix' => "MySQL Error :code: :message",

        // MySQL type rules — attribute visibility (shown as help text in form)
        'rule_no_length' => "Tipe ':type' tidak mendukung atribut panjang/karakter.",
        'rule_no_unique' => "Tipe ':type' tidak mendukung UNIQUE (BLOB/TEXT/JSON).",
        'rule_no_unsigned' => "Tipe ':type' tidak mendukung UNSIGNED.",
        'rule_no_auto_increment' => "Tipe ':type' tidak mendukung AUTO_INCREMENT (hanya integer).",
        'rule_no_foreign' => "Tipe ':type' tidak mendukung FOREIGN KEY (hanya integer/varchar/char).",
        'rule_no_primary' => "Tipe ':type' tidak mendukung PRIMARY KEY (hanya integer).",

        // MySQL type constraints (explained for users)
        'type_constraint_year' => "YEAR: nilai 2-digit (00-69→2000-2069, 70-99→1970-1999) atau 4-digit (1901-2155).",
        'type_constraint_json' => "JSON: menyimpan data JSON yang tervalidasi. Tidak mendukung panjang karakter.",
        'type_constraint_spatial' => "Spatial types (GEOMETRY/POINT/POLYGON, dll): tidak mendukung panjang/unsigned/unique/auto_increment. Membutuhkan SRID (default 0).",
        'type_constraint_bit' => "BIT: format BIT(M) dengan M=1-64. Contoh: BIT(8) untuk 1 byte.",
        'type_constraint_boolean' => "BOOLEAN: disimpan sebagai TINYINT(1). Nilai 0=FALSE, 1=TRUE.",
        'type_constraint_binary' => "BINARY/VARBINARY: menyimpan data biner (file, gambar, dll). Panjang opsional untuk BINARY, wajib untuk VARBINARY.",
        'type_constraint_real' => "REAL: sinonim untuk DOUBLE(53) di MySQL. Ketepatan ~15 digit desimal.",
        'type_constraint_enum' => "ENUM: jumlah nilai maks 65,535. Nilai tidak boleh kosong atau duplikat. Urutan matter (ordinal).",
        'type_constraint_set' => "SET: mirip ENUM tapi bisa menyimpan BEBERAPA nilai sekaligus (max 64 anggota).",

        // Column pre-validation errors (store / update)
        'column_enum_space_empty' => 'Kolom #:index: nilai ENUM/SET harus dipisah koma TANPA spasi. Gunakan format: IV,IB,VIP (tanpa spasi).',
        'column_enum_space_in_value' => "Kolom #:index: nilai ENUM/SET tidak boleh mengandung spasi. Ubah ':part' menjadi ':clean'. Contoh: IV,IB,VIP",
        'column_enum_invalid_char' => "Kolom #:index: nilai ENUM/SET ':value' mengandung karakter tidak valid. Karakter kutip (' \"), backslash (\\), dan koma (,) tidak diizinkan di dalam value.",
        'column_name_empty' => 'Kolom #:index: Nama kolom tidak boleh kosong.',
        'column_name_has_space' => "Kolom #:index: Nama kolom ':name' tidak boleh mengandung spasi. Gunakan underscore: nomor_kartu.",
        'column_name_invalid_pattern' => "Kolom #:index: Nama kolom ':name' hanya boleh huruf, angka, dan underscore. Tidak boleh dimulai dengan angka.",
        'column_enum_required' => "Kolom #:index: ENUM wajib memiliki minimal satu opsi di field 'Options'. Contoh: IV,IB,VIP",
        'column_set_required' => "Kolom #:index: SET wajib memiliki minimal satu opsi di field 'Options'. Contoh: option_1,option_2",
        'column_enum_duplicate' => "Kolom #:index: Nilai ENUM/SET tidak boleh duplikat. Nilai duplikat: ':values'",
        'column_name_prefix' => "Kolom ':column':",

        // ENUM/SET editor
        'enum_editor_btn' => 'Buka Editor',
        'enum_editor_title' => 'ENUM/SET Value Editor',
        'enum_editor_subtitle' => 'Kelola nilai opsi dengan mudah, tanpa masalah spasi.',
        'enum_editor_help' => 'Tekan Enter pada input untuk menambahkan baris baru. Gunakan tombol panah ↑↓ untuk memindahkan posisi.',
        'enum_editor_add' => 'Tambah Nilai',
        'enum_editor_value_placeholder' => 'Ketik nilai di sini...',
        'enum_editor_move_up' => 'Pindah ke atas',
        'enum_editor_move_down' => 'Pindah ke bawah',
        'enum_editor_remove' => 'Hapus',

        // MySQL error code mapped messages
        'mysql_1064' => 'Sintaks SQL tidak valid. Periksa kombinasi tipe, panjang, unsigned, nullability, dan default value.',
        'mysql_1264' => 'Nilai data existing tidak kompatibel dengan tipe kolom baru (out of range). Ubah/hapus data lama terlebih dahulu.',
        'mysql_1366' => 'Ada nilai data existing yang tidak bisa dikonversi ke tipe kolom baru (incorrect value).',
        'mysql_1364' => 'Kolom wajib (NOT NULL) tidak memiliki default yang valid.',
        'mysql_1048' => 'Kolom NOT NULL tidak boleh berisi NULL.',
        'mysql_1062' => 'Gagal menerapkan UNIQUE/PRIMARY karena ada data duplikat existing.',
        'mysql_1452' => 'Gagal menerapkan foreign key: ada data child yang tidak memiliki parent (integritas referensi gagal).',
        'mysql_1451' => 'Gagal mengubah/menghapus karena masih direferensikan oleh foreign key lain.',
        'mysql_1075' => 'AUTO_INCREMENT tidak valid. Pastikan hanya satu kolom auto_increment, kolom tersebut bertipe integer, dan memiliki key.',
        'mysql_1171' => 'PRIMARY KEY harus NOT NULL. Ubah kolom menjadi NOT NULL terlebih dahulu.',

        // DataTables / Filters
        'search_placeholder' => 'Cari...',
        'filter_type' => 'Pilih filter Tipe',
        'filter_columns' => 'Pilih filter Kolom',
        'filter_columns_none' => 'Tanpa kolom',
        'filter_columns_has' => 'Memiliki kolom',

        // Empty
        'empty' => 'Belum ada peran.',

        // DataTables (i18n)
        'datatable_info' => 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
        'datatable_info_empty' => 'Tidak ada data',
        'datatable_info_filtered' => '(difilter dari _MAX_ total data)',
        'datatable_zero_records' => 'Tidak ada data ditemukan',
        'datatable_search_placeholder' => 'Cari...',

        // Role labels (i18n)
        'labels' => [
            'administrator' => 'Administrator',
            'pegawai' => 'Pegawai',
            'umum' => 'Umum',
            'pelajar_mahasiswa' => 'Pelajar / Mahasiswa',
            'instansi_swasta' => 'Instansi / Swasta',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | CMS — Publikasi
    |--------------------------------------------------------------------------
    */
    'publication' => [
        'title' => 'Manajemen Publikasi: :name',
        'desc' => 'Kelola Pengumuman, Berita, dan Galeri untuk menu ini',
        'list_title' => 'Daftar Konten Publikasi',
        'list_desc' => 'Urutan akan menentukan posisi tampilan di halaman publik',
        'add_button' => 'Tambah Konten',
        'back' => 'Kembali',

        // Table columns
        'col_no' => 'No',
        'col_title' => 'Judul',
        'col_type' => 'Tipe',
        'col_date' => 'Tanggal',
        'col_order' => 'Urutan',
        'col_action' => 'Aksi',

        // Types
        'type_announcement' => 'Pengumuman',
        'type_news' => 'Berita/Artikel',
        'type_gallery' => 'Galeri',

        // Form
        'create_title' => 'Tambah Konten Publikasi',
        'edit_title' => 'Edit Konten Publikasi',
        'label_type' => 'Tipe',
        'label_title' => 'Judul',
        'placeholder_title' => 'Masukkan judul konten...',
        'label_description' => 'Deskripsi / Isi Konten',
        'label_gallery' => 'Media Galeri',
        'hint_gallery' => 'Klik atau drag media ke sini',
        'hint_gallery_sub' => 'Mendukung format gambar dan video',
        'gallery_info_create' => 'Halaman Galeri akan secara otomatis mengumpulkan seluruh media gambar dan video dari sistem. Media yang Anda tambahkan di bawah ini akan muncul sebagai prioritas di urutan awal.',
        'gallery_info_edit' => 'Halaman Galeri akan secara otomatis mengumpulkan seluruh media gambar dan video dari sistem. Media yang Anda tambahkan/simpan di bawah ini akan muncul sebagai prioritas di urutan awal.',
        'delete_media' => 'Hapus Media',
        'label_pdf' => 'File Dokumen (PDF)',
        'hint_pdf' => 'Format PDF maks 5MB',
        'hint_pdf_edit' => 'Unggah untuk mengganti file lama',
        'current_doc' => 'Dokumen Saat Ini',
        'view_pdf' => 'Lihat File PDF',

        'sidebar_title' => 'Pengaturan Halaman',
        'label_order' => 'Urutan',
        'label_date' => 'Tanggal',
        'label_subtitle' => 'Sub-judul / Ringkasan',
        'placeholder_subtitle' => 'Ringkasan singkat berita...',

        'btn_save' => 'Simpan Konten',
        'btn_update' => 'Perbarui Konten',
        'btn_cancel' => 'Batal',

        // Delete modal
        'delete_title' => 'Hapus Konten?',
        'delete_confirm' => 'Anda yakin ingin menghapus :name?',
        'delete_yes' => 'Hapus',
        'delete_no' => 'Batal',

        // Empty state
        'empty' => 'Belum ada konten publikasi. Klik "Tambah Konten" untuk menambahkan.',

        // Flash
        'flash' => [
            'added' => 'Konten publikasi berhasil ditambahkan.',
            'updated' => 'Konten publikasi berhasil diperbarui.',
            'deleted' => 'Konten publikasi berhasil dihapus.',
        ],
    ],

    'layanan_publik' => [
        'title' => 'Manajemen Layanan Publik: :name',
        'desc' => 'Kelola Halaman Layanan Publik (Kunjungan, LARASKA, Arsip Statis, dll)',
        'list_title' => 'Daftar Konten Layanan Publik',
        'list_desc' => 'Urutan akan menentukan posisi tampilan di halaman publik',
        'add_button' => 'Tambah Konten',
        'back' => 'Kembali',

        // Table columns
        'col_no' => 'No',
        'col_title' => 'Judul',
        'col_type' => 'Tipe Layanan',
        'col_date' => 'Tanggal',
        'col_order' => 'Urutan',
        'col_action' => 'Aksi',

        // Types
        'type_kunjungan' => 'Pendaftaran Kunjungan',
        'type_laraska' => 'LARASKA',
        'type_statis' => 'Layanan Arsip Statis',
        'type_konsultasi' => 'Konsultasi Kearsipan',
        'type_perpustakaan' => 'Perpustakaan',
        'type_umum' => 'Layanan Umum',

        // Form
        'create_title' => 'Tambah Konten Layanan Publik',
        'edit_title' => 'Edit Konten Layanan Publik',
        'label_require_login' => 'Wajib login sebelum mengakses halaman ini',
        'label_type' => 'Tipe Layanan',
        'label_title' => 'Judul',
        'placeholder_title' => 'Masukkan judul konten...',
        'label_description' => 'Deskripsi / Maklumat / Prosedur',
        'label_gallery' => 'Media Galeri',
        'hint_gallery' => 'Klik atau drag media ke sini',
        'hint_gallery_sub' => 'Mendukung format gambar dan video',
        'label_pdf' => 'File Dokumen / Formulir (PDF)',
        'hint_pdf' => 'Format PDF maks 5MB',
        'hint_pdf_edit' => 'Unggah untuk mengganti file lama',
        'current_doc' => 'Dokumen Saat Ini',
        'view_pdf' => 'Lihat File PDF',

        'sidebar_title' => 'Pengaturan Halaman',
        'label_order' => 'Urutan',
        'label_date' => 'Tanggal',
        'label_subtitle' => 'Sub-judul / Ringkasan',
        'placeholder_subtitle' => 'Ringkasan singkat layanan...',

        'btn_save' => 'Simpan Konten',
        'btn_update' => 'Perbarui Konten',
        'btn_cancel' => 'Batal',

        // Delete modal
        'delete_title' => 'Hapus Konten?',
        'delete_confirm' => 'Anda yakin ingin menghapus :name?',
        'delete_yes' => 'Hapus',
        'delete_no' => 'Batal',

        // Empty state
        'empty' => 'Belum ada konten layanan publik. Klik "Tambah Konten" untuk menambahkan.',

        // Flash
        'flash' => [
            'added' => 'Konten layanan publik berhasil ditambahkan.',
            'updated' => 'Konten layanan publik berhasil diperbarui.',
            'deleted' => 'Konten layanan publik berhasil dihapus.',
        ],

        // Kunjungan settings
        'kunjungan_settings_title' => 'Pengaturan Layanan Pendaftaran Kunjungan',
        'kunjungan_settings_desc' => 'Konfigurasi jadwal, aturan kapasitas, kuota harian, hari libur, slot tutup, dan kolom formulir.',
        'section1_title' => '1. Jadwal Kunjungan',
        'btn_hide_guest' => 'Sembunyikan bagian ini di halaman guest',
        'btn_show_guest' => 'Tampilkan bagian ini di halaman guest',
        'status_show' => 'Tampil',
        'status_hide' => 'Disembunyikan',
        'section1_label_title' => 'Judul Bagian 1 (Opsional, default: Jadwal Kunjungan)',
        'section1_placeholder_title' => 'Jadwal Kunjungan',
        'section1_label_desc' => 'Teks Keterangan Jadwal',

        'section2_title' => '2. Pengajuan Kunjungan',
        'section2_label_title' => 'Judul Bagian 2 (Opsional, default: Pengajuan Kunjungan)',
        'section2_placeholder_title' => 'Pengajuan Kunjungan',
        'section2_label_desc' => 'Aturan / Kapasitas Pengajuan',

        'section3_title' => '3. Kalender Kunjungan',
        'section3a_title' => '3a. Pengaturan Hari Libur / Tanggal Tutup Khusus',
        'section3a_desc' => 'Tanggal yang diatur di bawah ini adalah hari libur/tutup tambahan (opsional).',
        'info_auto_title' => 'Informasi Otomatisasi:',
        'info_auto_desc' => 'Hari Libur Nasional, Cuti Bersama, serta Sabtu & Minggu otomatis dideteksi dari kalender resmi. Anda tidak perlu menambahkannya secara manual di sini, cukup tambahkan tanggal tutup khusus internal instansi jika ada.',
        'btn_add_holiday' => 'Tambah Hari Libur',
        'label_holiday_date' => 'Tanggal Libur',
        'label_holiday_reason' => 'Keterangan / Alasan',
        'placeholder_holiday_reason' => 'Contoh: Libur Nasional / Tutup',
        'btn_delete' => 'Hapus',
        'empty_holidays' => 'Belum ada hari libur khusus yang ditambahkan.',

        'section3b_title' => '3b. Pengaturan Jumlah Slot Maksimal Harian',
        'section3b_desc' => 'Tentukan batas kuota maksimal pendaftaran untuk setiap harinya (berlaku otomatis setiap hari jika tidak ada pengaturan khusus di 3c).',
        'label_daily_quota' => 'Jumlah Slot Maksimal Kunjungan / Hari',

        'section3c_title' => '3c. Pengaturan Kuota Khusus / Penutupan Slot Jam Tertentu (Pagi / Siang)',
        'section3c_desc' => 'Atur jumlah slot maksimal pendaftar pada tanggal & waktu tertentu (isi 0 jika ingin menutup slot sepenuhnya).',
        'btn_add_close_slot' => 'Tambah Penutupan Slot',
        'label_date' => 'Tanggal',
        'label_slot_time' => 'Waktu / Slot',
        'slot_pagi' => 'Pagi (07:30 - 12:00)',
        'slot_siang' => 'Siang (13:00 - 16:00)',
        'label_max_slot' => 'Jumlah Slot (Maks)',
        'placeholder_close_slot' => '0 = Tutup',
        'label_max_hint' => 'Maks slot : :max',
        'label_close_reason' => 'Keterangan / Alasan',
        'placeholder_close_reason' => 'Contoh: Kuota Pagi Penuh',
        'empty_close_slots' => 'Belum ada penutupan slot khusus yang ditambahkan.',

        'section4_title' => '4. Pengaturan Daftar Kolom Formulir Kunjungan',
        'btn_add_form_field' => 'Tambah Kolom Form',
        'label_field_id' => 'ID Field (Unik)',
        'label_field_label' => 'Label Formulir',
        'label_field_type' => 'Tipe Input',
        'type_text' => 'Teks Singkat (text)',
        'type_email' => 'Email (email)',
        'type_number' => 'Angka (number)',
        'type_date' => 'Tanggal (date)',
        'type_select' => 'Pilihan (select)',
        'type_file' => 'Unggah File (file)',
        'type_textarea' => 'Teks Panjang (textarea)',
        'label_required' => 'Wajib (Required)',
        'label_options_select' => 'Opsi Pilihan (Pisahkan dengan koma)',
        'label_options_file' => 'Catatan / Format File',
        'placeholder_options_select' => 'Contoh: Edukasi, Penelitian, Kunjungan Kerja',
        'placeholder_options_file' => 'Contoh: Format pdf/doc, max 2MB',

        'label_auto_today' => 'Update otomatis ke tanggal hari ini',
        'btn_edit' => 'Edit',

        'laraska_settings_title' => 'Pengaturan Layanan LARASKA',
        'laraska_settings_desc' => 'Atur teks jam pelayanan, maklumat pelayanan, dan tahapan mekanisme LARASKA.',
        'label_laraska_hours' => 'Teks Waktu Pelayanan',
        'maklumat_box_title' => 'Maklumat Pelayanan',
        'label_maklumat_title' => 'Judul Maklumat Pelayanan',
        'label_maklumat_content' => 'Isi Maklumat Pelayanan',
        'label_maklumat_date' => 'Tempat & Tanggal Maklumat',
        'label_maklumat_director' => 'Pejabat Maklumat',
        'label_laraska_mech_title' => 'Judul Mekanisme Layanan',
        'label_laraska_step1_title' => 'Judul Langkah 1',
        'label_laraska_step1_desc' => 'Deskripsi Langkah 1',
        'label_laraska_step2_title' => 'Judul Langkah 2',
        'label_laraska_step2_desc' => 'Deskripsi Langkah 2',
        'label_laraska_step3_title' => 'Judul Langkah 3',
        'label_laraska_step3_desc' => 'Deskripsi Langkah 3',
        'label_laraska_step4_title' => 'Judul Langkah 4',
        'label_laraska_step4_desc' => 'Deskripsi Langkah 4',
        'btn_add_laraska_step' => 'Tambah Langkah Mekanisme',

        // Statis
        'statis_settings_title' => 'Pengaturan Layanan Arsip Statis',
        'statis_settings_desc' => 'Atur waktu layanan, tahapan penelusuran, serta mekanisme dan file panduan untuk layanan arsip statis.',
        'label_statis_hours' => 'Waktu Pelayanan',
        'label_statis_order_hours' => 'Waktu Pemesanan Arsip',
        'statis_stages_title' => 'Tahapan Penelusuran Arsip (Lingkaran Alur)',
        'btn_add_statis_stage' => 'Tambah Tahapan Alur',
        'label_stage_name' => 'Nama Tahap',
        'statis_mech1_title' => 'Mekanisme Layanan Langsung (Datang ke Lokasi)',
        'label_statis_mech1_title' => 'Judul Mekanisme Langsung',
        'label_statis_direct_pdf' => 'File Panduan PDF (Mekanisme Langsung)',
        'btn_add_statis_mech1_step' => 'Tambah Langkah Mekanisme Langsung',
        'label_statis_step_title_direct' => 'Judul Langkah',
        'label_statis_step_desc' => 'Deskripsi Langkah',
        'statis_mech2_title' => 'Mekanisme Layanan Tidak Langsung (Online)',
        'label_statis_mech2_title' => 'Judul Mekanisme Tidak Langsung',
        'label_statis_indirect_pdf' => 'File Panduan PDF (Mekanisme Tidak Langsung)',
        'btn_add_statis_mech2_step' => 'Tambah Langkah Mekanisme Tidak Langsung',
        'label_statis_step_title_indirect' => 'Judul Langkah',
        'btn_delete_step' => 'Hapus Langkah',
        'btn_delete_stage' => 'Hapus Tahap',
        'placeholder_auto_file' => 'Nama file (otomatis terisi saat upload)',
        'btn_delete_doc' => 'Hapus Dokumen',
        'btn_delete_photo' => 'Hapus Foto',

        // Konsultasi
        'konsultasi_settings_title' => 'Pengaturan Konsultasi Kearsipan',
        'konsultasi_settings_desc' => 'Atur deskripsi layanan dan konfigurasi formulir konsultasi kearsipan yang ditampilkan di halaman guest.',
        'label_consultation_desc' => 'Deskripsi Layanan Konsultasi',
        'consultation_form_general_title' => 'Pengaturan Umum Formulir Konsultasi',
        'label_consultation_form_title' => 'Judul Formulir',
        'label_consultation_form_send' => 'Label Tombol Kirim',
        'label_consultation_success' => 'Pesan Sukses (setelah submit)',
        'consultation_form_fields_title' => 'Pengaturan Daftar Kolom Formulir Konsultasi',
        'label_placeholder' => 'Placeholder',
        'placeholder_placeholder' => 'Contoh isian...',

        // Perpustakaan
        'perpustakaan_settings_title' => 'Pengaturan Layanan Perpustakaan',
        'perpustakaan_settings_desc' => 'Atur tujuan, fasilitas, waktu pelayanan, tata tertib, prosedur, serta file panduan perpustakaan.',
        'lib_objs_title' => 'Tujuan Perpustakaan',
        'btn_add_lib_obj' => 'Tambah Poin Tujuan',
        'placeholder_lib_obj' => 'Tulis poin tujuan...',
        'lib_visit_btn_title' => 'Tombol Kunjungi Website Perpustakaan',
        'label_lib_visit_btn' => 'Label Tombol',
        'label_lib_redirect_url' => 'URL Website Perpustakaan (opsional)',
        'hint_lib_redirect_url' => 'Jika kosong, akan memunculkan popup info default.',
        'lib_cards_title' => 'Fasilitas & Layanan Perpustakaan (Cards)',
        'btn_add_lib_card' => 'Tambah Fasilitas',
        'label_lib_card' => 'Fasilitas',
        'label_lib_card_title' => 'Judul Fasilitas',
        'label_lib_card_desc' => 'Deskripsi Fasilitas',
        'placeholder_title_general' => 'Judul...',
        'placeholder_desc_general' => 'Deskripsi...',
        'label_lib_hours' => 'Waktu Pelayanan',
        'lib_rules_title' => 'Tata Tertib (Poin-poin)',
        'btn_add_lib_rule' => 'Tambah Aturan',
        'placeholder_lib_rule' => 'Aturan...',
        'lib_photos_title' => 'Foto Fasilitas Perpustakaan',
        'label_lib_photos' => 'Pilih Beberapa Foto (Bisa lebih dari 1)',
        'label_lib_photos_edit' => 'Tambah Beberapa Foto Baru (Bisa lebih dari 1)',
        'btn_choose_images' => 'Choose Images',
        'placeholder_lib_photos_names' => 'Nama file (otomatis)',
        'hint_lib_photos' => 'Anda dapat memilih banyak foto sekaligus saat jendela pemilihan file terbuka.',
        'hint_lib_photos_edit' => 'Foto baru yang dipilih di sini akan ditambahkan ke daftar foto yang sudah ada.',
        'lib_procs_title' => 'Judul Alur Prosedur & Tahapan',
        'btn_add_lib_proc' => 'Tambah Prosedur',
        'placeholder_lib_proc_title' => 'Judul utama prosedur...',
        'label_lib_proc' => 'Prosedur',
        'label_lib_proc_title' => 'Judul Prosedur',
        'label_lib_proc_desc' => 'Deskripsi Prosedur',
        'label_lib_pdf' => 'File Panduan PDF Perpustakaan',
        'btn_choose_file' => 'Choose File',
        'current_photos_title' => 'Foto Terunggah Saat Ini (Hapus jika ingin mengganti/menghilangkan):',
    ],


    'pengelolaan' => [
        'title' => 'Manajemen Pengelolaan: :name',
        'desc' => 'Kelola Halaman Pengelolaan (Penyusutan, Penyimpanan, Preservasi, Pengolahan, Pemanfaatan, Penjangkauan, Akuisisi)',
        'list_title' => 'Daftar Konten Pengelolaan',
        'list_desc' => 'Urutan akan menentukan posisi tampilan di halaman publik',
        'add_button' => 'Tambah Konten',
        'edit_button' => 'Edit Konten',
        'back' => 'Kembali',

        // Table columns
        'col_no' => 'No',
        'col_title' => 'Judul',
        'col_type' => 'Tipe Halaman',
        'col_date' => 'Tanggal',
        'col_order' => 'Urutan',
        'col_action' => 'Aksi',

        // Types
        'type_penyusutan' => 'Penyusutan Arsip',
        'type_penyimpanan' => 'Penyimpanan Arsip',
        'type_preservasi' => 'Preservasi Arsip',
        'type_pengolahan' => 'Pengolahan Arsip Statis',
        'type_pemanfaatan' => 'Pemanfaatan Arsip',
        'type_penjangkauan' => 'Penjangkauan Arsip',
        'type_akuisisi' => 'Akuisisi Arsip',

        // Form
        'create_title' => 'Tambah Konten Pengelolaan',
        'edit_title' => 'Edit Konten Pengelolaan',
        'label_require_login' => 'Wajib login sebelum mengakses halaman ini',
        'label_type' => 'Tipe Halaman',
        'label_title' => 'Judul',
        'placeholder_title' => 'Masukkan judul konten...',
        'label_description' => 'Deskripsi / Isi Konten',
        'label_gallery' => 'Media Gambar',
        'hint_gallery' => 'Klik atau drag gambar ke sini',
        'hint_gallery_sub' => 'Mendukung format jpg, png, webp',
        'label_pdf' => 'File Dokumen / Panduan / Formulir (PDF)',
        'hint_pdf' => 'Format PDF maks 5MB',
        'hint_pdf_edit' => 'Unggah untuk mengganti file lama',
        'current_doc' => 'Dokumen Saat Ini',
        'view_pdf' => 'Lihat File PDF',

        'sidebar_title' => 'Pengaturan Halaman',
        'label_order' => 'Urutan',
        'label_date' => 'Tanggal',
        'label_subtitle' => 'Sub-judul / Ringkasan',
        'placeholder_subtitle' => 'Ringkasan singkat...',

        'btn_save' => 'Simpan Konten',
        'btn_update' => 'Perbarui Konten',
        'btn_cancel' => 'Batal',
        'btn_edit' => 'Edit',

        // Delete modal
        'delete_title' => 'Hapus Konten?',
        'delete_confirm' => 'Anda yakin ingin menghapus :name?',
        'delete_yes' => 'Hapus',
        'delete_no' => 'Batal',

        // Empty state
        'empty' => 'Belum ada konten pengelolaan. Klik "Tambah Konten" untuk menambahkan.',

        // Flash
        'flash' => [
            'added' => 'Konten pengelolaan berhasil ditambahkan.',
            'updated' => 'Konten pengelolaan berhasil diperbarui.',
            'deleted' => 'Konten pengelolaan berhasil dihapus.',
        ],

        // Sub-type specific labels
        'penyusutan_settings_title' => 'Pengaturan Penyusutan Arsip',
        'penyusutan_settings_desc' => 'Atur gambar dan deskripsi penyusutan arsip.',
        'penyimpanan_settings_title' => 'Pengaturan Penyimpanan Arsip',
        'penyimpanan_settings_desc' => 'Atur prinsip penyimpanan, sistem penyimpanan (cards), fasilitas, dan jenis ruang.',
        'preservasi_settings_title' => 'Pengaturan Preservasi Arsip',
        'preservasi_settings_desc' => 'Atur daftar poin preservasi, teks restorasi, dan tahapan restorasi.',
        'pengolahan_settings_title' => 'Pengaturan Pengolahan Arsip Statis',
        'pengolahan_settings_desc' => 'Atur daftar poin pengolahan, infografis, dan file mekanisme layanan.',
        'pemanfaatan_settings_title' => 'Pengaturan Pemanfaatan Arsip',
        'pemanfaatan_settings_desc' => 'Atur kutipan undang-undang, daftar akses arsip, dan file mekanisme.',
        'penjangkauan_settings_title' => 'Pengaturan Penjangkauan Arsip',
        'penjangkauan_settings_desc' => 'Atur daftar kegiatan penjangkauan dan file panduan/katalog.',
        'akuisisi_settings_title' => 'Pengaturan Akuisisi Arsip',
        'akuisisi_settings_desc' => 'Atur tahapan akuisisi dan file formulir/pedoman akuisisi.',

        // Custom fields
        'label_prinsip' => 'Prinsip Penyimpanan (Teks/Boks)',
        'label_sistem_title' => 'Daftar Sistem Penyimpanan (Cards)',
        'btn_add_sistem' => 'Tambah Sistem Penyimpanan',
        'label_fasilitas_title' => 'Daftar Fasilitas & Sarana',
        'btn_add_fasilitas' => 'Tambah Fasilitas',
        'label_ruang_title' => 'Daftar Jenis Ruang Penyimpanan',
        'btn_add_ruang' => 'Tambah Ruang',
        'label_preservasi_list' => 'Daftar Kegiatan Preservasi',
        'btn_add_preservasi' => 'Tambah Kegiatan Preservasi',
        'label_restorasi_desc' => 'Deskripsi Restorasi Arsip',
        'label_restorasi_list' => 'Daftar Tahapan Restorasi',
        'btn_add_restorasi' => 'Tambah Tahapan Restorasi',
        'label_pengolahan_list' => 'Daftar Poin Pengolahan',
        'btn_add_pengolahan' => 'Tambah Poin Pengolahan',
        'label_mekanisme_title' => 'Judul Mekanisme Layanan',
        'label_mekanisme_desc' => 'Deskripsi Mekanisme Layanan',
        'label_pemanfaatan_quote' => 'Kutipan Dasar Hukum (Boks)',
        'label_akses_list' => 'Daftar Akses / Jenis Akses Arsip',
        'btn_add_akses' => 'Tambah Akses',
        'label_kegiatan_list' => 'Daftar Kegiatan Penjangkauan',
        'btn_add_kegiatan' => 'Tambah Kegiatan',
        'label_tahapan_list' => 'Daftar Tahapan Akuisisi',
        'btn_add_tahapan' => 'Tambah Tahapan',

        // Additional dynamic form & section labels
        'label_prinsip_title' => 'Judul Bagian Prinsip Penyimpanan',
        'label_prinsip_desc' => 'Deskripsi Bagian Prinsip Penyimpanan',
        'label_prinsip_list' => 'Daftar Poin Prinsip Penyimpanan',
        'btn_add_poin' => 'Tambah Poin',
        'item_prinsip' => 'Prinsip',
        'btn_delete_poin' => 'Hapus Poin',
        'label_poin_title' => 'Judul Poin',
        'placeholder_prinsip_title' => 'Contoh: Keutuhan (Integrity)',
        'label_poin_desc' => 'Deskripsi Poin',
        'placeholder_prinsip_desc' => 'Contoh: Arsip disimpan tanpa mengubah susunan asli...',

        'label_sistem_section_title' => 'Judul Bagian Sistem Penyimpanan',
        'placeholder_sistem_title' => 'Contoh: Sistem Penyimpanan',
        'item_sistem' => 'Sistem',
        'label_sistem_name' => 'Judul Sistem',
        'label_choose_icon' => 'Pilih Ikon',
        'label_desc_general' => 'Deskripsi',

        'label_fasilitas_section_title' => 'Judul Bagian Fasilitas',
        'placeholder_fasilitas_title' => 'Contoh: Fasilitas Penyimpanan',
        'label_upload_fasilitas' => 'Upload Gambar Fasilitas (Bisa Lebih dari Satu)',
        'hint_upload_multiple' => 'Format: JPG, PNG, WebP. Bisa pilih banyak file sekaligus.',

        'label_ruang_section_title' => 'Judul Bagian Ruang',
        'placeholder_ruang_title' => 'Contoh: Ruang Penyimpanan',
        'label_upload_ruang' => 'Upload Gambar Ruang (Bisa Lebih dari Satu)',

        'label_preservasi_section_title' => 'Judul Bagian Kegiatan Preservasi',
        'placeholder_preservasi_title' => 'Contoh: Kegiatan Preservasi',

        'label_restorasi_section_title' => 'Judul Bagian Restorasi Arsip',
        'placeholder_restorasi_title' => 'Contoh: RESTORASI ARSIP',

        'label_pengolahan_section_title' => 'Judul Bagian Pengolahan',
        'placeholder_pengolahan_title' => 'Contoh: Tahapan Pengolahan',

        'label_akses_section_title' => 'Judul Bagian Daftar Akses',
        'placeholder_akses_title' => 'Contoh: Layanan Akses & Pemanfaatan',
        'item_akses' => 'Akses',
        'label_akses_name' => 'Judul Akses',

        'label_kegiatan_section_title' => 'Judul Bagian Daftar Kegiatan Penjangkauan',
        'placeholder_kegiatan_title' => 'Contoh: Program & Kegiatan Penjangkauan',
        'item_kegiatan' => 'Kegiatan',
        'label_kegiatan_name' => 'Judul Kegiatan',
        'label_button_text' => 'Teks Tombol (Kanan Atas)',
        'placeholder_button_text' => 'Contoh: Kunjungi',
        'label_button_url' => 'Link URL Tombol',
        'placeholder_button_url' => 'https://...',

        'label_tahapan_section_title' => 'Judul Bagian Daftar Tahapan Akuisisi',
        'placeholder_tahapan_title' => 'Contoh: Tahapan & Prosedur Akuisisi',
        'item_tahapan' => 'Tahapan',
        'label_tahapan_name' => 'Judul Tahapan',

        'label_current_images_del' => 'Gambar Saat Ini (Centang untuk menghapus):',
        'btn_delete_check' => 'Hapus',

        'icons' => [
            'sistem_clipboard' => '📋 Clipboard (Klasifikasi / Nomor)',
            'sistem_archive' => '🗃️ Kotak Arsip (Subjek / Berkas)',
            'sistem_book' => '📖 Buku (Abjad / Panduan)',
            'sistem_calendar' => '📅 Kalender (Tanggal / Kronologis)',
            'sistem_map' => '🗺️ Peta (Wilayah / Geografis)',
            'sistem_document' => '📄 Dokumen (File / Catatan)',
            'sistem_lock' => '🔒 Gembok (Keamanan / Rahasia)',
            'sistem_database' => '🗄️ Database (Server / Penyimpanan)',
            'sistem_tag' => '🏷️ Tag (Label / Kategori)',
            'sistem_folder' => '📁 Folder (Direktori / Berkas)',
            'sistem_check' => '✔️ Ceklis (Verifikasi / Selesai)',
            'sistem_star' => '⭐ Bintang (Unggulan / Penting)',

            'akses_clipboard' => '📋 Clipboard (Klasifikasi / Daftar)',
            'akses_archive' => '🗃️ Kotak Arsip (Subjek / Berkas)',
            'akses_book' => '📖 Buku (Abjad / Panduan)',
            'akses_calendar' => '📅 Kalender (Tanggal / Kronologis)',
            'akses_map' => '🗺️ Peta (Wilayah / Geografis)',
            'akses_document' => '📄 Dokumen (File / Catatan)',
            'akses_lock' => '🔒 Gembok (Keamanan / Rahasia)',
            'akses_database' => '🗄️ Database (Server / Penyimpanan)',

            'kegiatan_clipboard' => '📋 Clipboard (Daftar / Pameran)',
            'kegiatan_archive' => '🗃️ Kotak Arsip (Koleksi / Berkas)',
            'kegiatan_book' => '📖 Buku (Publikasi / Naskah)',
            'kegiatan_calendar' => '📅 Kalender (Kegiatan / Kerjasama)',
            'kegiatan_map' => '🗺️ Peta (Wilayah / Geografis)',
            'kegiatan_document' => '📄 Dokumen (File / Catatan)',
            'kegiatan_lock' => '🔒 Gembok (Keamanan / Rahasia)',
            'kegiatan_database' => '🗄️ Database (Server / Penyimpanan)',
            'kegiatan_users' => '👥 Pengguna (Sosialisasi / Edukasi)',
            'kegiatan_globe' => '🌐 Globe (Internasional / Website)',
            'kegiatan_tag' => '🏷️ Tag (Label / Kategori)',
            'kegiatan_folder' => '📁 Folder (Direktori / Berkas)',
            'kegiatan_check' => '✔️ Ceklis (Verifikasi / Selesai)',
            'kegiatan_star' => '⭐ Bintang (Unggulan / Penting)',
        ],
    ],


    'kontak_kami' => [
        'title' => 'Manajemen Kontak Kami: :name',
        'desc' => 'Kelola Halaman Kontak Kami (Kontak Utama, Cabang, Pusat, atau Lainnya)',
        'list_title' => 'Daftar Kontak',
        'list_desc' => 'Urutan akan menentukan posisi tampilan di halaman publik',
        'add_button' => 'Tambah Kontak',
        'edit_button' => 'Edit Kontak',
        'back' => 'Kembali',

        // Table columns
        'col_no' => 'No',
        'col_title' => 'Judul / Nama Kontak',
        'col_type' => 'Tipe Kontak',
        'col_date' => 'Tanggal',
        'col_order' => 'Urutan',
        'col_action' => 'Aksi',

        // Types
        'type_kontak' => 'Kontak Utama',
        'type_cabang' => 'Kantor Cabang',
        'type_pusat' => 'Kantor Pusat',
        'type_lainnya' => 'Lainnya',

        // Form
        'create_title' => 'Tambah Kontak Kami',
        'edit_title' => 'Edit Kontak Kami',
        'label_type' => 'Tipe Kontak',
        'label_title' => 'Judul / Nama Kantor',
        'placeholder_title' => 'Contoh: Kantor Pusat ANRI / Layanan Informasi...',
        'label_description' => 'Deskripsi / Informasi Tambahan',
        'label_gallery' => 'Media Gambar / Foto Kantor',
        'hint_gallery' => 'Klik atau drag gambar ke sini',
        'hint_gallery_sub' => 'Mendukung format jpg, png, webp',

        'sidebar_title' => 'Pengaturan Halaman',
        'label_order' => 'Urutan',
        'label_date' => 'Tanggal',
        'label_subtitle' => 'Sub-judul / Ringkasan',
        'placeholder_subtitle' => 'Ringkasan singkat...',

        'btn_save' => 'Simpan Kontak',
        'btn_update' => 'Perbarui Kontak',
        'btn_cancel' => 'Batal',
        'btn_edit' => 'Edit',

        // Delete modal
        'delete_title' => 'Hapus Kontak?',
        'delete_confirm' => 'Anda yakin ingin menghapus :name?',
        'delete_yes' => 'Hapus',
        'delete_no' => 'Batal',

        // Empty state
        'empty' => 'Belum ada data kontak kami. Klik "Tambah Kontak" untuk menambahkan.',

        // Flash
        'flash' => [
            'added' => 'Konten kontak kami berhasil ditambahkan.',
            'updated' => 'Konten kontak kami berhasil diperbarui.',
            'deleted' => 'Konten kontak kami berhasil dihapus.',
        ],

        // Custom fields
        'label_alamat' => 'Alamat Lengkap',
        'placeholder_alamat' => 'Contoh: Jl. Ampera Raya No.7, Cilandak, Jakarta Selatan...',
        'label_jam_desc' => 'Deskripsi Jam Operasional',
        'label_jam_list' => 'Daftar Jam Operasional',
        'btn_add_jam' => 'Tambah Jadwal',
        'label_telepon' => 'Nomor Telepon',
        'placeholder_telepon' => 'Contoh: (021) 7805851',
        'label_whatsapp' => 'Nomor WhatsApp',
        'placeholder_whatsapp' => 'Contoh: 6281234567890',
        'label_email' => 'Alamat Email',
        'placeholder_email' => 'Contoh: info@anri.go.id',
        'label_instagram' => 'Instagram (URL / Username)',
        'label_twitter' => 'Twitter / X (URL / Username)',
        'label_facebook' => 'Facebook (URL / Username)',
        'label_youtube' => 'YouTube (URL / Username)',
        'label_cards_title' => 'Daftar Layanan / Kontak Card',
        'btn_add_card' => 'Tambah Card',

        // Dynamic sections & extra data
        'top_cards_title' => 'Kartu Informasi Utama / Highlight (Top Cards)',
        'top_cards_desc' => 'Tambahkan kartu highlight (seperti Lokasi, Depot, Layanan) yang muncul di bagian atas halaman',
        'btn_add_top_card' => 'Tambah Kartu',
        'top_card_item_prefix' => 'Highlight ',
        'btn_delete_top_card' => 'Hapus Kartu',
        'label_top_card_title' => 'Judul Utama / Angka (Contoh: Bandung, 5 Hari)',
        'placeholder_top_card_title' => 'Contoh: Bandung',
        'label_top_card_subtitle' => 'Label / Sub-judul (Contoh: Lokasi Strategis)',
        'placeholder_top_card_subtitle' => 'Contoh: Lokasi Strategis',
        'label_choose_icon' => 'Pilih Ikon',
        'icon_map' => '📍 Lokasi (Pin Peta)',
        'icon_building' => '🏢 Bangunan / Gedung (Depot/Kantor)',
        'icon_clock' => '⏰ Jam / Waktu (Layanan/Durasi)',
        'icon_phone' => '📞 Telepon (Call Center)',
        'icon_message' => '💬 Pesan / Chat',
        'icon_mail' => '📧 Email / Surat',
        'icon_globe' => '🌐 Website / Portal',

        'alamat_section_header' => 'Informasi Kontak & Alamat',
        'alamat_section_desc' => 'Lengkapi data alamat, email, telepon, dan media sosial kantor',
        'label_section_title_guest' => 'Judul Bagian (Ditampilkan di Halaman Guest)',
        'placeholder_alamat_section_title' => 'Informasi Kontak & Alamat',
        'placeholder_instagram' => 'https://instagram.com/...',
        'placeholder_twitter' => 'https://twitter.com/...',
        'placeholder_facebook' => 'https://facebook.com/...',
        'placeholder_youtube' => 'https://youtube.com/...',

        'jam_section_header' => 'Jadwal / Jam Operasional',
        'jam_section_desc' => 'Atur hari dan jam kerja layanan',
        'placeholder_jam_section_title' => 'Jadwal & Jam Operasional',
        'label_jam_hari' => 'Hari Kerja',
        'placeholder_jam_hari' => 'Senin - Kamis',
        'label_jam_waktu' => 'Jam Operasional',
        'placeholder_jam_waktu' => '08.00 - 15.00 WIB',
        'btn_delete_jam' => 'Hapus Jadwal',

        'cards_section_desc' => 'Tambahkan kartu informasi layanan, pengaduan, atau saluran kontak khusus',
        'placeholder_cards_section_title' => 'Saluran Layanan & Pengaduan',
        'card_item_prefix' => 'Kartu ',
        'btn_delete_card' => 'Hapus Kartu',
        'label_card_title' => 'Judul Layanan / Kontak',
        'placeholder_card_title' => 'Contoh: Layanan Informasi',
        'label_card_subtitle' => 'Sub-judul / Deskripsi Singkat',
        'placeholder_card_subtitle' => 'Contoh: Untuk pertanyaan seputar kearsipan',
        'icon_phone_card' => '📞 Telepon (Layanan / Call Center)',
        'icon_message_card' => '💬 Pesan / Chat (Pengaduan / Konsultasi)',
        'icon_mail_card' => '📧 Email (Surat / Bantuan)',
        'icon_map_card' => '📍 Lokasi (Alamat / Cabang)',
        'icon_clock_card' => '⏰ Jam (Waktu Operasional)',
        'icon_globe_card' => '🌐 Website (Portal Online)',
    ],

    'none' => [
        'info_none' => 'Belum ada konten yang tersedia.',
    ],

    'reports' => [
        // Kunjungan
        'kunjungan_title' => 'Laporan Pendaftaran Kunjungan',
        'kunjungan_subtitle' => 'Monitoring data Layanan Pendaftaran Kunjungan serta grafik perbandingan tujuan dan tren kunjungan.',
        'filter_start' => 'Mulai:',
        'filter_end' => 'Sampai:',
        'btn_filter' => 'Filter',
        'btn_reset' => 'Reset',
        'btn_cancel' => 'Batal',
        'total_visitor' => 'Total Peserta Kunjungan',
        'total_visitor_sub' => 'Seluruh data terdaftar',
        'purpose_edukasi' => 'Tujuan Edukasi',
        'purpose_edukasi_sub' => 'Kunjungan sekolah & kampus',
        'purpose_penelitian' => 'Tujuan Penelitian',
        'purpose_penelitian_sub' => 'Riset & studi literatur',
        'purpose_kunker' => 'Kunjungan Kerja',
        'purpose_kunker_sub' => 'Studi banding & instansi',
        'chart_purpose_title' => 'Tujuan Kunjungan',
        'chart_purpose_sub' => 'Distribusi peserta berdasarkan jenis keperluan',
        'chart_trend_title' => 'Tren Kunjungan Harian',
        'chart_trend_sub' => 'Jumlah peserta kunjungan selama 30 hari terakhir',
        'chart_trend_approved_title' => 'Tren Kunjungan Disetujui',
        'chart_trend_approved_sub' => 'Grafik jumlah peserta kunjungan yang disetujui',
        'chart_trend_rejected_title' => 'Tren Kunjungan Ditolak',
        'chart_trend_rejected_sub' => 'Grafik jumlah peserta kunjungan yang ditolak',
        'table_kunjungan_title' => 'Riwayat Pendaftaran Kunjungan',
        'table_kunjungan_sub' => 'Daftar lengkap pemohon layanan kunjungan',
        'col_no' => 'No',
        'col_name_inst' => 'Nama & Instansi',
        'col_contact' => 'Kontak',
        'col_date_time' => 'Tanggal & Waktu',
        'col_count' => 'Jumlah',
        'col_purpose' => 'Tujuan',
        'col_action' => 'Aksi',
        'time_pagi' => 'PAGI',
        'time_siang' => 'SIANG',
        'label_org' => 'org',
        'label_org_full' => 'Orang',
        'btn_detail' => 'Lihat Detail',
        'btn_download' => 'Unduh Surat',
        'empty_kunjungan' => 'Belum ada data pendaftaran kunjungan.',
        'modal_kunjungan_title' => 'Detail Pendaftaran Kunjungan',
        'modal_name' => 'Nama Lengkap',
        'modal_email' => 'Email',
        'modal_phone' => 'No. Telepon',
        'modal_inst' => 'Instansi',
        'modal_position' => 'Jabatan',
        'modal_visit_date' => 'Tanggal Kunjungan',
        'modal_visit_time' => 'Sesi Waktu',
        'modal_visitor_count' => 'Jumlah Peserta',
        'modal_purpose' => 'Tujuan',
        'modal_form_data' => 'Data Tambahan Form',
        'status' => 'Status',
        'status_approved' => 'Disetujui',
        'status_rejected' => 'Ditolak',
        'status_pending' => 'Menunggu',
        'remarks' => 'Keterangan',
        'file_attachment' => 'File Lampiran',
        'view_file' => 'Lihat File',
        'btn_close' => 'Tutup',

        // Pengunjung
        'pengunjung_title' => 'Monitoring Pengunjung Website',
        'pengunjung_subtitle' => 'Monitoring statistik pengunjung website berdasarkan halaman yang diakses.',
        'pengunjung_index_subtitle' => 'Pilih jenis metrik pengunjung yang ingin dianalisis.',
        'page_views_title' => 'Total Kunjungan (Page Views)',
        'page_views_desc' => 'Total halaman yang diakses pengunjung. 1 pengunjung bisa menghasilkan banyak page views.',
        'unique_visitors_title' => 'Total Pengunjung Unik',
        'unique_visitors_desc' => 'Dihitung berdasarkan IP unik pengunjung per harinya. Menunjukkan jumlah individu aktual.',
        'btn_view_detail' => 'Lihat Detail',
        'btn_back' => 'Kembali',
        'page_views_header_title' => 'Monitoring Pengunjung (Page Views)',
        'page_views_header_desc' => 'Monitoring statistik total kunjungan halaman website.',
        'unique_visitors_header_title' => 'Monitoring Pengunjung (Unique Visitors)',
        'unique_visitors_header_desc' => 'Monitoring statistik pengunjung unik (berdasarkan IP) per harinya.',
        'chart_unique_title' => 'Laporan Pengunjung Unik',
        'chart_unique_series' => 'Jumlah Pengunjung',
        'chart_unique_unit' => 'Pengunjung',
        'preset_today' => 'Hari Ini',
        'preset_week' => '7 Hari',
        'preset_month' => '30 Hari',
        'preset_year' => '1 Tahun',
        'preset_custom' => 'Kustom',
        'total_views' => 'Total Kunjungan Halaman',
        'page_breakdown' => 'Rincian Per Halaman',
        'filter_page' => 'Filter Halaman',
        'select_page_display' => 'Pilih Halaman yang Ditampilkan:',
        'merge_sub_features' => 'Gabungkan Sub Fitur',
        'select_all' => 'Pilih Semua',
        'deselect_all' => 'Kosongkan',
        'chart_views_title' => 'Grafik Kunjungan Per Halaman',
        'chart_views_sub' => 'Distribusi jumlah akses berdasarkan kategori menu website',
        'table_pengunjung_title' => 'Riwayat Akses Halaman (Log Pengunjung)',
        'table_pengunjung_sub' => 'Catatan aktivitas akses halaman website secara real-time',
        'col_ip' => 'Alamat IP',
        'col_path' => 'Halaman (Path)',
        'col_device' => 'Perangkat / Browser',
        'col_access_time' => 'Waktu Akses',
        'empty_pengunjung' => 'Belum ada data log kunjungan.',

        // Konsultasi
        'konsultasi_title' => 'Laporan Konsultasi Kearsipan',
        'konsultasi_subtitle' => 'Monitoring data pengajuan Layanan Konsultasi Kearsipan dari masyarakat dan instansi.',
        'total_consultation' => 'Total Pengajuan Konsultasi',
        'total_consultation_sub' => 'Seluruh riwayat konsultasi terdaftar',
        'table_konsultasi_title' => 'Riwayat Pengajuan Konsultasi Kearsipan',
        'table_konsultasi_sub' => 'Daftar lengkap pemohon layanan konsultasi dan bimbingan kearsipan',
        'col_topic' => 'Topik Konsultasi',
        'col_submit_date' => 'Tanggal Pengajuan',
        'empty_konsultasi' => 'Belum ada data pengajuan konsultasi kearsipan.',
        'modal_konsultasi_title' => 'Detail Pengajuan Konsultasi',
        'modal_topic' => 'Topik Konsultasi',
        'modal_submit_date' => 'Tanggal Pengajuan',
        'btn_download_attachment' => 'Unduh Lampiran',
        'status_replied' => 'Dibalas',
        'status_waiting' => 'Menunggu',
        'btn_reply' => 'Balas Pesan',
        'btn_delete' => 'Hapus Data',
        'msg_replied_already' => 'Pesan balasan sudah pernah dikirim.',
        'msg_reply_success' => 'Pesan balasan berhasil dikirim ke email pemohon.',
        'msg_reply_fail' => 'Gagal mengirim pesan balasan. Pastikan pengaturan email server sudah benar.',
        'msg_del_no_access' => 'Anda tidak memiliki hak akses untuk menghapus data konsultasi.',
        'msg_del_success' => 'Data konsultasi berhasil dihapus.',
        'swal_success' => 'Berhasil!',
        'swal_fail' => 'Gagal!',
        'swal_error' => 'Error!',
        'swal_error_sys' => 'Terjadi kesalahan pada sistem.',
        'swal_del_title' => 'Hapus Data?',
        'swal_del_text' => 'Data konsultasi ini akan dihapus permanen.',
        'swal_del_confirm' => 'Ya, Hapus!',
        'swal_cancel' => 'Batal',
        'swal_deleted' => 'Terhapus!',
        'reply_modal_title' => 'Balas Konsultasi',
        'reply_modal_to' => 'Kepada',
        'reply_modal_msg' => 'Pesan Balasan',
        'reply_modal_placeholder' => 'Ketik pesan balasan Anda di sini...',
        'btn_send_reply' => 'Kirim Balasan',
        'btn_sending' => 'Mengirim...',

        // Online
        'online_title' => 'Laporan Pengguna Online',
        'online_subtitle' => 'Monitoring riwayat pengguna online, waktu aktif, serta aktivitas akses halaman website.',
        'online_stat_realtime' => 'Pengguna Online Saat Ini',
        'online_stat_realtime_sub' => 'Aktif dalam 5 menit terakhir',
        'online_stat_active' => 'Total Pengguna Aktif',
        'online_stat_active_sub' => 'Sepanjang tanggal terpilih',
        'online_stat_peak' => 'Jam Puncak Online',
        'online_stat_peak_sub' => 'Waktu tersibuk',
        'online_stat_avg' => 'Rata-rata Online / Jam',
        'online_stat_avg_sub' => 'Pengguna per jam aktif',
        'chart_online_title' => 'Grafik Riwayat Pengguna Online Per Jam',
        'chart_online_sub' => 'Distribusi jumlah pengguna aktif di setiap jam pada tanggal terpilih',
        'table_realtime_title' => 'Daftar Pengguna Online (Real-time)',
        'table_realtime_sub' => 'Status aktivitas terakhir dari seluruh pengguna sistem',
        'table_activity_title' => 'Riwayat Aktivitas Pengguna',
        'table_activity_sub' => 'Rangkuman total akses halaman dan interaksi pengguna sepanjang tanggal terpilih',
        'my_profile_title' => 'Data Diri Sendiri',
        'my_profile_sub' => 'Informasi akun dan status aktivitas online Anda saat ini',
        'my_activity_title' => 'Riwayat Aktivitas Saya',
        'my_activity_sub' => 'Rangkuman total akses halaman dan interaksi Anda sepanjang tanggal terpilih',
        'col_user' => 'Pengguna',
        'col_role' => 'Peran (Role)',
        'col_last_activity' => 'Aktivitas Terakhir',
        'col_page_views' => 'Jumlah Kunjungan Halaman',
        'col_last_access' => 'Akses Terakhir',
        'empty_online' => 'Belum ada pengguna online saat ini.',
        'empty_activity' => 'Belum ada aktivitas pengguna pada tanggal ini.',
        'preset_yesterday' => 'H-1 (Kemarin)',
        'filter_date' => 'Tanggal:',
        'online_stat_peak_desc' => 'Waktu tersibuk dengan jumlah pengguna online aktif tertinggi dalam satu jam',
        'peak_active_users' => 'Puncak<br>Pengguna Aktif',
        'peak_hours_list' => 'Daftar Jam Puncak Terekam (:count Jam)',
        'empty_peak_hours' => 'Belum ada data jam puncak yang terekam pada tanggal ini.',
        'tooltip_active_users' => 'Pengguna Aktif',
        'user_label' => 'Pengguna',
    ],

    'datatable' => [
        'info' => 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
        'info_empty' => 'Tidak ada data',
        'info_filtered' => '(difilter dari _MAX_ total data)',
        'zero_records' => 'Tidak ada data ditemukan',
        'search_placeholder' => 'Cari...',
        'paginate' => [
            'first' => 'Pertama',
            'last' => 'Terakhir',
            'next' => 'Selanjutnya',
            'previous' => 'Sebelumnya',
        ],
    ],
];
