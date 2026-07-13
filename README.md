# Manual Book & Dokumentasi Proyek: Aplikasi Portal Instansi Kearsipan

Selamat datang di dokumentasi resmi proyek **Aplikasi Portal Instansi Kearsipan**. Dokumen ini dirancang sebagai panduan profesional bagi pengembang (*developer*), administrator sistem, dan pihak terkait untuk memahami, menginstal, mengonfigurasi, serta mengembangkan aplikasi ini.

---

## 📌 1. Ikhtisar Proyek (Project Overview)

**Portal Instansi Kearsipan** adalah platform berbasis web interaktif yang dikembangkan untuk mengelola, melestarikan, dan menyajikan arsip-arsip bersejarah serta administratif secara digital. Aplikasi ini menggabungkan fungsionalitas manajemen arsip tradisional dengan fitur interaktif modern seperti virtual tour 3D, buku elektronik interaktif (page-flip), dan asisten pintar berbasis kecerdasan buatan (AI).

### Tujuan Utama:
1. **Preservasi Digital**: Menyimpan dokumen penting dalam format digital agar terhindar dari kerusakan fisik.
2. **Aksesibilitas Publik**: Memudahkan masyarakat umum mengakses arsip bernilai sejarah tinggi secara online.
3. **Visualisasi Interaktif**: Memberikan pengalaman eksplorasi arsip yang imersif melalui visualisasi 3D dan buku virtual.
4. **Layanan Publik**: Menyediakan modul reservasi/kunjungan bagi warga yang ingin melakukan penelitian fisik di instansi kearsipan.

---

## 🛠️ 2. Arsitektur & Teknologi (Tech Stack)

Aplikasi dibangun menggunakan kombinasi framework modern untuk memastikan performa yang cepat, aman, dan mudah dikelola:

*   **Backend (Core Framework)**: Laravel (PHP)
*   **Database**: MySQL
*   **Frontend & Bundler**: Vite, Vanilla CSS, Tailwind CSS
*   **Pustaka Tambahan**:
    *   **Page-Flip JS**: Digunakan untuk efek animasi lembaran buku yang realistis pada penampil arsip.
    *   **Google Drive API**: Integrasi penyimpanan awan untuk dokumen berukuran besar.
    *   **Google OAuth 2.0**: Untuk fitur autentikasi (Sign-In dengan Google).
    *   **Google Gemini API**: Menenagai chatbot asisten arsip pintar di dalam aplikasi.
    *   **Google reCAPTCHA v2**: Proteksi form dari spam bot pada modul layanan publik.

---

## 🚀 3. Fitur Utama Aplikasi

### A. Landing Page & Welcome Screen
*   Halaman utama interaktif yang dirancang dengan desain premium, modern, dan responsif.
*   Menampilkan sekilas sejarah kearsipan, arsip populer, dan akses cepat ke layanan utama.

### B. Virtual Book (Buku Virtual Interaktif)
*   Menyajikan arsip dokumen/buku kuno dalam format digital interaktif.
*   Menggunakan pustaka `page-flip` yang memungkinkan pengguna membalik halaman secara visual layaknya membaca buku fisik.
*   Mendukung mode satu halaman (*single-page*) atau dua halaman (*double-page*) secara dinamis.

### C. Virtual 3D Tour
*   Simulasi ruangan depot arsip secara 3D interaktif.
*   Pengguna dapat mengeksplorasi lorong arsip, berinteraksi dengan rak penyimpanan, dan membaca informasi detail mengenai kategori arsip pada lokasi spesifik.

### D. Sistem Kunjungan & Layanan Publik
*   Kalender reservasi interaktif bagi masyarakat yang ingin berkunjung ke depot arsip fisik.
*   Sistem manajemen hari libur (*holiday check*) dan pembatasan kuota kunjungan harian secara otomatis.
*   Dilengkapi dengan Google reCAPTCHA v2 untuk keamanan formulir pendaftaran.

### E. AI Chatbot (Asisten Pintar)
*   Chatbot terintegrasi menggunakan Google Gemini API untuk membantu pengguna mencari informasi arsip secara cepat dan interaktif menggunakan bahasa alami (*Natural Language Processing*).

---

## ⚙️ 4. Panduan Instalasi (Installation Guide)

Ikuti langkah-langkah berikut untuk menjalankan proyek ini di lingkungan lokal (*local development*):

### Prasyarat (Prerequisites):
Pastikan perangkat Anda sudah terinstal:
*   PHP >= 8.2
*   Composer
*   Node.js (termasuk NPM)
*   MySQL Server (bisa menggunakan Laragon/XAMPP)

### Langkah Langkah Pemasangan:

1.  **Clone Repositori**:
    ```bash
    git clone https://github.com/username/web-dabb-new.git
    cd web-dabb-new
    ```

2.  **Instalasi Dependensi PHP (Composer)**:
    ```bash
    composer install
    ```

3.  **Instalasi Dependensi Node (NPM)**:
    ```bash
    npm install
    ```

4.  **Konfigurasi Environment**:
    Salin file `.env.example` menjadi `.env`:
    ```bash
    cp .env.example .env
    ```
    Buka file `.env` dan konfigurasikan koneksi database Anda:
    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=dabb
    DB_USERNAME=root
    DB_PASSWORD=
    ```

5.  **Generate Application Key**:
    ```bash
    php artisan key:generate
    ```

6.  **Migrasi & Seed Database**:
    Jalankan migrasi untuk membuat tabel beserta data awal bawaan (*seeder*):
    ```bash
    php artisan migrate --seed
    ```

7.  **Hubungkan Folder Storage (Simpan Aset/File)**:
    Buat tautan simbolis (*symbolic link*) dari folder storage internal ke folder public agar file unggahan dan dokumen dapat diakses secara publik:
    ```bash
    php artisan storage:link
    ```

8.  **Menjalankan Server**:
    Jalankan server PHP Laravel dan server developer Vite secara bersamaan (di terminal terpisah):
    ```bash
    # Terminal 1 (Laravel Dev Server)
    php artisan serve
    
    # Terminal 2 (Vite Hot Reload Server)
    npm run dev
    ```

---

## 🔒 5. Konfigurasi Kunci API (API Keys Setup)

Agar fitur-fitur lanjutan dapat berfungsi, isi variabel berikut di file `.env` Anda:

```env
# URL Utama Aplikasi
APP_URL=http://localhost:8000

# Google Client OAuth (Untuk Login Google)
GOOGLE_CLIENT_ID="isi-client-id-google-anda"
GOOGLE_CLIENT_SECRET="isi-client-secret-google-anda"
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"

# Google Gemini API Key (Untuk Chatbot)
GEMINI_API_KEY="isi-gemini-api-key-anda"

# Google Drive API Key (Untuk render dokumen)
GOOGLE_DRIVE_API_KEY="isi-google-drive-api-key-anda"
```

---

## 📦 6. Panduan Deploy ke Production

Saat melakukan deploy proyek ke server *production* (seperti VPS, cPanel, atau Cloud Hosting), ikuti prosedur wajib berikut:

1.  **Matikan Server Developer Vite & Compile Assets**:
    Jalankan perintah berikut di lingkungan lokal atau CI/CD pipeline Anda untuk menghasilkan file statis yang dioptimalkan:
    ```bash
    npm run build
    ```
    *Langkah ini akan menghasilkan folder `public/build` yang berisi aset terkompresi. Konfigurasi `server.hmr.host` pada `vite.config.js` tidak akan dipanggil lagi.*

2.  **Sesuaikan Environment di Server**:
    Ubah nilai konfigurasi di `.env` server menjadi:
    ```env
    APP_ENV=production
    APP_DEBUG=false
    APP_URL=https://nama-domain-anda.com
    ```

3.  **Optimasi Laravel**:
    Jalankan perintah optimasi berikut di server agar memuat konfigurasi dan route lebih cepat:
    ```bash
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    ```

4.  **Hubungkan Folder Storage (Simpan Aset/File)**:
    Pastikan tautan simbolis (*symbolic link*) untuk folder penyimpanan publik sudah dibuat di server production agar file unggahan/arsip dapat diakses secara publik:
    ```bash
    php artisan storage:link
    ```

---

## 📂 7. Struktur Folder Utama (Key Directory Map)

*   `app/Http/Controllers/` : Logika bisnis dan pengendali request aplikasi.
*   `config/` : Pengaturan konfigurasi internal Laravel.
*   `database/migrations/` : Struktur tabel database aplikasi.
*   `public/` : Aset publik statis (gambar, favicon, folder build hasil compile Vite).
*   `resources/css/` & `resources/js/` : Aset sumber kode CSS dan Javascript mentah sebelum dibuild.
*   `resources/views/layouts/` : Kerangka layout utama aplikasi (misal `guest.blade.php` & `app.blade.php`).
*   `resources/views/pages/` : Halaman interaktif spesifik (3D tour, virtual book, dsb).
*   `routes/web.php` : Rute navigasi halaman web.

---

*Hak Cipta © 2026 - Instansi Kearsipan / Tim Pengembang.*
