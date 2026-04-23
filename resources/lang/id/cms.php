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
        ],

        'delete_section' => [
            'title' => 'Hapus Seksi',
            'confirm' => 'Apakah Anda yakin ingin menghapus seksi :name?',
            'yes' => 'Ya, Hapus',
        ],

        'flash' => [
            'page_added' => 'Halaman berhasil ditambahkan.',
            'page_updated' => 'Halaman berhasil diperbarui.',
            'page_deleted' => 'Halaman berhasil dihapus.',
            'section_added' => 'Seksi berhasil ditambahkan.',
            'section_updated' => 'Seksi berhasil diperbarui.',
            'section_deleted' => 'Seksi berhasil dihapus.',
        ],

        // Public page
        'welcome' => 'Selamat datang di portal :name,',
        'search_placeholder' => 'Pencarian',
        'list_title' => 'Daftar :name',
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
            'stats' => 'Judul Seksi',
            'youtube' => 'Judul Seksi',
            'instagram' => 'Judul Seksi',
        ],

        'stats' => [
            'title' => 'Label Statistik',
            'desc' => 'Label teks untuk counter statistik pengunjung',
            'total' => 'Label Total Pengunjung',
            'today' => 'Label Pengunjung Hari Ini',
        ],

        'youtube' => [
            'title' => 'Video YouTube',
            'desc' => 'ID video YouTube yang ditampilkan di carousel (format: ID saja, contoh: F2NhNTiNxoY)',
            'video_label' => 'Video :number',
            'placeholder' => 'ID YouTube',
            'help' => 'Salin ID dari URL YouTube: youtube.com/watch?v=<strong>ID_DI_SINI</strong>',
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
        'label_desc' => 'Deskripsi',
        'label_thumbnail' => 'Thumbnail Ruangan',
        'thumbnail_help' => 'Gambar preview untuk daftar ruangan (JPG, PNG, WEBP)',
        'label_image_360' => 'Gambar 360°',
        'image_360_help' => 'Gambar equirectangular 360 derajat (JPG, PNG)',

        'hotspot_title' => 'Hotspot Navigasi',
        'hotspot_add' => 'Tambah',
        'hotspot_rooms_available' => 'Ruangan tersedia: :count',
        'hotspot_empty' => "Kosong. Klik 'Tambah'",

        'preview_title' => 'Preview Panorama 360°',
        'preview_desc' => 'Klik titik target di panorama untuk mengambil Yaw/Pitch, atau geser panorama untuk melihat',
        'preview_placeholder' => 'Preview belum tersedia',
        'preview_placeholder_sub' => 'Pilih gambar 360° terlebih dahulu',

        'btn_cancel' => 'Batal',
        'btn_save' => 'Simpan Perubahan',
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
        'form_desc_edit' => 'Atur informasi ruangan, warna, media dinding, dan hotspot navigasi',

        // Shared form
        'info_title' => 'Informasi Ruangan',
        'label_name' => 'Nama Ruangan',
        'label_desc' => 'Deskripsi',
        'label_thumbnail' => 'Thumbnail Ruangan',
        'thumbnail_help' => 'Gambar preview untuk daftar ruangan (JPG, PNG, WEBP)',
        'thumbnail_keep' => 'Biarkan kosong jika tidak ingin mengubah',

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
        'editor_props_title' => 'Properti Media yang Dipilih',
        'editor_props_delete' => 'Hapus',
        'editor_props_save' => 'Simpan Posisi',

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

        // Buttons
        'btn_cancel' => 'Batal',
        'btn_save' => 'Simpan Buku',
        'btn_save_changes' => 'Simpan Perubahan',
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

];
