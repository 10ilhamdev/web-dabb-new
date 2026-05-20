<?php

use Illuminate\Support\Facades\Response;


use App\Http\Controllers\ChatController;
use App\Http\Controllers\FeatureController;
use App\Http\Controllers\FeaturePageController;
use App\Http\Controllers\HomeContentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleDashboardController;
use App\Http\Controllers\Cms\SettingController;
use App\Http\Controllers\Cms\VirtualRoomController;
use App\Http\Controllers\Cms\VirtualBookPageController;
use App\Http\Controllers\Cms\VirtualSlideshowController;
use App\Http\Controllers\Cms\ProfileController as CmsProfileController;
use App\Http\Controllers\Cms\PenggunaController;
use App\Http\Controllers\Cms\RoleController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/gdrive-stream/{fileId}', [App\Http\Controllers\GoogleDriveStreamController::class, 'stream'])
    ->where('fileId', '[a-zA-Z0-9_-]+')
    ->name('gdrive.stream');
Route::get('/lang/{locale}', [HomeController::class, 'switchLocale'])->name('locale.switch');
Route::post('/api/chat', [ChatController::class, 'getBotResponse'])->name('api.chat');
Route::get('/vss-image-proxy', function (\Illuminate\Http\Request $request) {
    $url = $request->get('url');
    if (!$url) abort(404);
    if (str_contains($url, 'storage/')) {
        $url = str_replace(' ', '%20', $url);
    }
    if (strpos($url, '//') === 0) $url = 'https:' . $url;
    try {
        $response = \Illuminate\Support\Facades\Http::withoutVerifying()->timeout(15)->get($url);
        if ($response->successful()) {
            return response($response->body(), 200, [
                'Content-Type' => $response->header('Content-Type') ?? 'image/png',
                'Cache-Control' => 'public, max-age=86400',
                'Access-Control-Allow-Origin' => '*',
            ]);
        }
    } catch (\Exception $e) {
    }
    return redirect($url);
})->name('vss.image.proxy');

// Static pages
Route::get('/disclaimer', [SettingController::class, 'showDisclaimer'])->name('disclaimer');

// Public feature pages
Route::get('/halaman/{feature}/{pageNum?}', [FeaturePageController::class, 'publicShow'])
    ->where('pageNum', '[0-9]+')
    ->name('feature.page');

// Public Service Form Submissions
Route::post('/layanan-publik/visit', [App\Http\Controllers\PublicServiceSubmissionController::class, 'storeVisit'])->name('public.visit.store');
Route::post('/layanan-publik/consultation', [App\Http\Controllers\PublicServiceSubmissionController::class, 'storeConsultation'])->name('public.consultation.store');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [RoleDashboardController::class, 'index'])->name('dashboard');

    // Dynamic dashboard routes — registered from roles table at boot time
    Route::get('/dashboard/{slug}', [RoleDashboardController::class, 'show'])
        ->name('dashboard.role');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile/password', [ProfileController::class, 'password'])->name('profile.password');
    Route::get('/profile/activity', [ProfileController::class, 'activity'])->name('profile.activity');
    Route::delete('/profile/activity/logout-others', [ProfileController::class, 'logoutOtherBrowserSessions'])->name('profile.activity.logout-others');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/send-verification', [ProfileController::class, 'sendVerificationNotification'])->name('profile.send-verification');

    // CMS Home Content Editor (supports multiple beranda pages by feature_id)
    Route::middleware('role:cms.features')->prefix('cms/home/{feature_id}')->name('cms.home.')->group(function () {
        Route::get('/', [HomeContentController::class, 'edit'])->name('edit');
        Route::put('/', [HomeContentController::class, 'update'])->name('update');
    });

    // CMS Settings - Footer
    Route::middleware('role:cms.footer')->prefix('cms/settings')->name('cms.settings.')->group(function () {
        Route::post('/rte-upload', [SettingController::class, 'uploadRteMedia'])->name('rte.upload');
        Route::get('/footer', [SettingController::class, 'editFooter'])->name('footer.edit');
        Route::put('/footer', [SettingController::class, 'updateFooter'])->name('footer.update');
    });

    // CMS Settings - Disclaimer
    Route::middleware('role:cms.disclaimer')->prefix('cms/settings')->name('cms.settings.disclaimer.')->group(function () {
        Route::get('/disclaimer', [SettingController::class, 'editDisclaimer'])->name('edit');
        Route::put('/disclaimer', [SettingController::class, 'updateDisclaimer'])->name('update');
    });

    // CMS Reports (Akses dibuka untuk semua user auth, filter data per role dilakukan di ReportController)
    Route::prefix('cms/reports')->name('cms.reports.')->group(function () {
        Route::get('/kunjungan', [App\Http\Controllers\Cms\ReportController::class, 'kunjungan'])->name('kunjungan');
        Route::get('/pengunjung', [App\Http\Controllers\Cms\ReportController::class, 'pengunjungIndex'])->name('pengunjung');
        Route::get('/pengunjung/page-views', [App\Http\Controllers\Cms\ReportController::class, 'pengunjungPageViews'])->name('pengunjung.page_views');
        Route::get('/pengunjung/unique', [App\Http\Controllers\Cms\ReportController::class, 'pengunjungUnique'])->name('pengunjung.unique');
        Route::get('/konsultasi', [App\Http\Controllers\Cms\ReportController::class, 'konsultasi'])->name('konsultasi');
        Route::get('/online', [App\Http\Controllers\Cms\ReportController::class, 'online'])->name('online');
    });

    // CMS Features
    Route::middleware('role:cms.features')->prefix('cms/features')->name('cms.features.')->group(function () {
        Route::get('/', [FeatureController::class, 'index'])->name('index');
        Route::post('/', [FeatureController::class, 'store'])->name('store');
        Route::get('/{feature}/', [FeatureController::class, 'show'])->name('show.slash');
        Route::get('/{feature}', [FeatureController::class, 'show'])->name('show');
        Route::put('/{feature}', [FeatureController::class, 'update'])->name('update');
        Route::delete('/{feature}', [FeatureController::class, 'destroy'])->name('destroy');
        Route::put('/{feature}/content', [FeatureController::class, 'updateContent'])->name('update-content');
        Route::put('/{feature}/sub', [FeatureController::class, 'updateSub'])->name('update-sub');
        Route::delete('/{feature}/sub', [FeatureController::class, 'destroySub'])->name('destroy-sub');
        Route::patch('/{feature}/toggle-visibility', [FeatureController::class, 'toggleVisibility'])->name('toggle-visibility');

        // Feature Pages (multi-page content)
        Route::get('/{feature}/pages', [FeaturePageController::class, 'index'])->name('pages.index');
        Route::post('/{feature}/pages', [FeaturePageController::class, 'store'])->name('pages.store');
        Route::get('/{feature}/pages/{page}', [FeaturePageController::class, 'show'])->name('pages.show');
        Route::put('/{feature}/pages/{page}', [FeaturePageController::class, 'update'])->name('pages.update');
        Route::delete('/{feature}/pages/{page}', [FeaturePageController::class, 'destroy'])->name('pages.destroy');
        Route::patch('/{feature}/pages/{page}/toggle-visibility', [FeaturePageController::class, 'toggleVisibility'])->name('pages.toggle-visibility');

        // Page Sections
        Route::post('/{feature}/pages/{page}/sections', [FeaturePageController::class, 'storeSection'])->name('pages.sections.store');
        Route::put('/{feature}/pages/{page}/sections/{section}', [FeaturePageController::class, 'updateSection'])->name('pages.sections.update');
        Route::delete('/{feature}/pages/{page}/sections/{section}', [FeaturePageController::class, 'destroySection'])->name('pages.sections.destroy');

        // Virtual Room 360 Feature (yang lama)
        Route::get('/{feature}/virtual-rooms', [VirtualRoomController::class, 'index'])->name('virtual_rooms.index');
        Route::get('/{feature}/virtual-rooms/create', [VirtualRoomController::class, 'create'])->name('virtual_rooms.create');
        Route::post('/{feature}/virtual-rooms', [VirtualRoomController::class, 'store'])->name('virtual_rooms.store');
        Route::get('/{feature}/virtual-rooms/{room}/edit', [VirtualRoomController::class, 'edit'])->name('virtual_rooms.edit');
        Route::put('/{feature}/virtual-rooms/{room}', [VirtualRoomController::class, 'update'])->name('virtual_rooms.update');
        Route::delete('/{feature}/virtual-rooms/{room}', [VirtualRoomController::class, 'destroy'])->name('virtual_rooms.destroy');

        // Virtual 3D Rooms Feature (yang baru - 4 dinding 1 pintu)
        Route::get('/{feature}/virtual-3d-rooms', [App\Http\Controllers\Cms\Virtual3dRoomController::class, 'index'])->name('virtual_3d_rooms.index');
        Route::get('/{feature}/virtual-3d-rooms/create', [App\Http\Controllers\Cms\Virtual3dRoomController::class, 'create'])->name('virtual_3d_rooms.create');
        Route::post('/{feature}/virtual-3d-rooms', [App\Http\Controllers\Cms\Virtual3dRoomController::class, 'store'])->name('virtual_3d_rooms.store');
        Route::get('/{feature}/virtual-3d-rooms/{room}/edit', [App\Http\Controllers\Cms\Virtual3dRoomController::class, 'edit'])->name('virtual_3d_rooms.edit');
        Route::put('/{feature}/virtual-3d-rooms/{room}', [App\Http\Controllers\Cms\Virtual3dRoomController::class, 'update'])->name('virtual_3d_rooms.update');
        Route::delete('/{feature}/virtual-3d-rooms/{room}', [App\Http\Controllers\Cms\Virtual3dRoomController::class, 'destroy'])->name('virtual_3d_rooms.destroy');

        // Media Management untuk Virtual 3D Rooms
        Route::post('/{feature}/virtual-3d-rooms/{room}/media', [App\Http\Controllers\Cms\Virtual3dRoomController::class, 'uploadMedia'])->name('virtual_3d_rooms.media.store');
        Route::put('/{feature}/virtual-3d-rooms/{room}/media/{media}', [App\Http\Controllers\Cms\Virtual3dRoomController::class, 'updateMediaPosition'])->name('virtual_3d_rooms.media.update');
        Route::delete('/{feature}/virtual-3d-rooms/{room}/media/{media}', [App\Http\Controllers\Cms\Virtual3dRoomController::class, 'deleteMedia'])->name('virtual_3d_rooms.media.destroy');

        // Virtual Books (Multiple books per feature)
        Route::get('/{feature}/virtual-books', [App\Http\Controllers\Cms\BookController::class, 'index'])->name('virtual_books.index');
        Route::get('/{feature}/virtual-books/create', [App\Http\Controllers\Cms\BookController::class, 'create'])->name('virtual_books.create');
        Route::post('/{feature}/virtual-books', [App\Http\Controllers\Cms\BookController::class, 'store'])->name('virtual_books.store');
        Route::get('/{feature}/virtual-books/{book}/edit', [App\Http\Controllers\Cms\BookController::class, 'edit'])->name('virtual_books.edit');
        Route::put('/{feature}/virtual-books/{book}', [App\Http\Controllers\Cms\BookController::class, 'update'])->name('virtual_books.update');
        Route::delete('/{feature}/virtual-books/{book}', [App\Http\Controllers\Cms\BookController::class, 'destroy'])->name('virtual_books.destroy');

        // Virtual Book Pages (Pages within a book)
        Route::get('/{feature}/virtual-books/{book}/pages', [App\Http\Controllers\Cms\BookController::class, 'pages'])->name('virtual_books.pages.index');
        Route::get('/{feature}/virtual-books/{book}/pages/create', [VirtualBookPageController::class, 'create'])->name('virtual_books.pages.create');
        Route::post('/{feature}/virtual-books/{book}/pages', [VirtualBookPageController::class, 'store'])->name('virtual_books.pages.store');
        Route::get('/{feature}/virtual-books/{book}/pages/{virtualBookPage}/edit', [VirtualBookPageController::class, 'edit'])->name('virtual_books.pages.edit');
        Route::put('/{feature}/virtual-books/{book}/pages/{virtualBookPage}', [VirtualBookPageController::class, 'update'])->name('virtual_books.pages.update');
        Route::delete('/{feature}/virtual-books/{book}/pages/{virtualBookPage}', [VirtualBookPageController::class, 'destroy'])->name('virtual_books.pages.destroy');

        // Virtual Slideshow (Pameran Arsip Virtual SlideShow)
        Route::get('/{feature}/slideshow', [VirtualSlideshowController::class, 'index'])->name('slideshow.index');
        Route::get('/{feature}/slideshow/create', [VirtualSlideshowController::class, 'create'])->name('slideshow.create');
        Route::post('/{feature}/slideshow', [VirtualSlideshowController::class, 'store'])->name('slideshow.store');
        Route::get('/{feature}/slideshow/{slide}/edit', [VirtualSlideshowController::class, 'edit'])->name('slideshow.edit');
        Route::put('/{feature}/slideshow/{slide}', [VirtualSlideshowController::class, 'update'])->name('slideshow.update');
        Route::delete('/{feature}/slideshow/{slide}', [VirtualSlideshowController::class, 'destroy'])->name('slideshow.destroy');

        // Pages/Exhibition untuk Virtual Slideshow
        Route::get('/{feature}/slideshow/pages/create', [FeaturePageController::class, 'create'])->name('slideshow.pages.create');
        Route::post('/{feature}/slideshow/pages', [FeaturePageController::class, 'store'])->name('slideshow.pages.store');
        Route::get('/{feature}/slideshow/pages/{pageId}/edit', [FeaturePageController::class, 'edit'])->name('slideshow.pages.edit');
        Route::put('/{feature}/slideshow/pages/{pageId}', [FeaturePageController::class, 'update'])->name('slideshow.pages.update');
        Route::delete('/{feature}/slideshow/pages/{pageId}', [FeaturePageController::class, 'destroy'])->name('slideshow.pages.destroy');

        // Slides per VirtualSlideshowPage
        Route::get('/{feature}/slideshow/pages/{pageId}/slides', [VirtualSlideshowController::class, 'slidesIndex'])->name('slideshow.pages.slides.index');
        Route::get('/{feature}/slideshow/pages/{pageId}/slides/create', [VirtualSlideshowController::class, 'createSlide'])->name('slideshow.pages.slides.create');
        Route::post('/{feature}/slideshow/pages/{pageId}/slides', [VirtualSlideshowController::class, 'storeSlide'])->name('slideshow.pages.slides.store');
        Route::get('/{feature}/slideshow/pages/{pageId}/slides/{slide}/edit', [VirtualSlideshowController::class, 'editSlide'])->name('slideshow.pages.slides.edit');
        Route::put('/{feature}/slideshow/pages/{pageId}/slides/{slide}', [VirtualSlideshowController::class, 'updateSlide'])->name('slideshow.pages.slides.update');
        Route::delete('/{feature}/slideshow/pages/{pageId}/slides/{slide}', [VirtualSlideshowController::class, 'destroySlide'])->name('slideshow.pages.slides.destroy');

        // Profile Page (dropdown menu management)
        // Sub-menu CRUD (manages dropdown items under Profil feature)
        Route::post('/{feature}/profile/submenu', [CmsProfileController::class, 'storeSubMenu'])->name('profile.submenu.store');
        Route::put('/{feature}/profile/submenu/{sub}', [CmsProfileController::class, 'updateSubMenu'])->name('profile.submenu.update');
        Route::delete('/{feature}/profile/submenu/{sub}', [CmsProfileController::class, 'destroySubMenu'])->name('profile.submenu.destroy');

        // Profile pages (FeaturePages under a sub-feature - this is the main profile index)
        Route::get('/{feature}/profile/{sub}', [CmsProfileController::class, 'index'])->name('profile.index');
        Route::get('/{feature}/profile/{sub}/create', [CmsProfileController::class, 'create'])->name('profile.pages.create');
        Route::post('/{feature}/profile/{sub}', [CmsProfileController::class, 'store'])->name('profile.pages.store');
        Route::get('/{feature}/profile/{sub}/{page}', [CmsProfileController::class, 'show'])->name('profile.pages.show');
        Route::get('/{feature}/profile/{sub}/{page}/edit', [CmsProfileController::class, 'edit'])->name('profile.pages.edit');
        Route::put('/{feature}/profile/{sub}/{page}', [CmsProfileController::class, 'update'])->name('profile.pages.update');
        Route::delete('/{feature}/profile/{sub}/{page}', [CmsProfileController::class, 'destroy'])->name('profile.pages.destroy');
        Route::get('/{feature}/generate-profile-chart', [CmsProfileController::class, 'generateChart'])->name('profile.generate_chart');
        Route::get('/{feature}/profile-data-fields', [CmsProfileController::class, 'getDataFields'])->name('profile.data_fields');
        Route::post('/translate', [CmsProfileController::class, 'translate'])->name('profile.translate');

        // Profile Page Sections
        Route::post('/{feature}/profile/{sub}/{page}/sections', [CmsProfileController::class, 'storeSection'])->name('profile.sections.store');
        Route::put('/{feature}/profile/{sub}/{page}/sections/{section}', [CmsProfileController::class, 'updateSection'])->name('profile.sections.update');
        Route::delete('/{feature}/profile/{sub}/{page}/sections/{section}', [CmsProfileController::class, 'destroySection'])->name('profile.sections.destroy');

        // Publication Page (Pengumuman, Berita, Galeri)
        Route::get('/{feature}/publication', [\App\Http\Controllers\Cms\PublicationController::class, 'index'])->name('publication.index');
        Route::get('/{feature}/publication/create', [\App\Http\Controllers\Cms\PublicationController::class, 'create'])->name('publication.pages.create');
        Route::post('/{feature}/publication', [\App\Http\Controllers\Cms\PublicationController::class, 'store'])->name('publication.pages.store');
        Route::get('/{feature}/publication/{publication}/edit', [\App\Http\Controllers\Cms\PublicationController::class, 'edit'])->name('publication.pages.edit');
        Route::put('/{feature}/publication/{publication}', [\App\Http\Controllers\Cms\PublicationController::class, 'update'])->name('publication.pages.update');
        Route::delete('/{feature}/publication/{publication}', [\App\Http\Controllers\Cms\PublicationController::class, 'destroy'])->name('publication.pages.destroy');

        // Layanan Publik Page
        Route::get('/{feature}/layanan_publik', [\App\Http\Controllers\Cms\LayananPublikController::class, 'index'])->name('layanan_publik.index');
        Route::get('/{feature}/layanan_publik/create', [\App\Http\Controllers\Cms\LayananPublikController::class, 'create'])->name('layanan_publik.pages.create');
        Route::post('/{feature}/layanan_publik', [\App\Http\Controllers\Cms\LayananPublikController::class, 'store'])->name('layanan_publik.pages.store');
        Route::get('/{feature}/layanan_publik/{layananPublik}/edit', [\App\Http\Controllers\Cms\LayananPublikController::class, 'edit'])->name('layanan_publik.pages.edit');
        Route::put('/{feature}/layanan_publik/{layananPublik}', [\App\Http\Controllers\Cms\LayananPublikController::class, 'update'])->name('layanan_publik.pages.update');
        Route::delete('/{feature}/layanan_publik/{layananPublik}', [\App\Http\Controllers\Cms\LayananPublikController::class, 'destroy'])->name('layanan_publik.pages.destroy');

        // Pengelolaan Page
        Route::get('/{feature}/pengelolaan', [\App\Http\Controllers\Cms\PengelolaanController::class, 'index'])->name('pengelolaan.index');
        Route::get('/{feature}/pengelolaan/create', [\App\Http\Controllers\Cms\PengelolaanController::class, 'create'])->name('pengelolaan.pages.create');
        Route::post('/{feature}/pengelolaan', [\App\Http\Controllers\Cms\PengelolaanController::class, 'store'])->name('pengelolaan.pages.store');
        Route::get('/{feature}/pengelolaan/{pengelolaan}/edit', [\App\Http\Controllers\Cms\PengelolaanController::class, 'edit'])->name('pengelolaan.pages.edit');
        Route::put('/{feature}/pengelolaan/{pengelolaan}', [\App\Http\Controllers\Cms\PengelolaanController::class, 'update'])->name('pengelolaan.pages.update');
        Route::delete('/{feature}/pengelolaan/{pengelolaan}', [\App\Http\Controllers\Cms\PengelolaanController::class, 'destroy'])->name('pengelolaan.pages.destroy');

        // Kontak Kami Page
        Route::get('/{feature}/kontak_kami', [\App\Http\Controllers\Cms\KontakKamiController::class, 'index'])->name('kontak_kami.index');
        Route::get('/{feature}/kontak_kami/create', [\App\Http\Controllers\Cms\KontakKamiController::class, 'create'])->name('kontak_kami.pages.create');
        Route::post('/{feature}/kontak_kami', [\App\Http\Controllers\Cms\KontakKamiController::class, 'store'])->name('kontak_kami.pages.store');
        Route::get('/{feature}/kontak_kami/{kontakKami}/edit', [\App\Http\Controllers\Cms\KontakKamiController::class, 'edit'])->name('kontak_kami.pages.edit');
        Route::put('/{feature}/kontak_kami/{kontakKami}', [\App\Http\Controllers\Cms\KontakKamiController::class, 'update'])->name('kontak_kami.pages.update');
        Route::delete('/{feature}/kontak_kami/{kontakKami}', [\App\Http\Controllers\Cms\KontakKamiController::class, 'destroy'])->name('kontak_kami.pages.destroy');


        // Legacy routes - redirect to new structure
        Route::get('/{feature}/virtual-book-pages', function ($feature) {
            return redirect()->route('cms.features.virtual_books.index', $feature);
        });
    });

    // CMS Pengguna (User Management)
    // Role Management (Admin only - system-level config)
    Route::middleware('role:admin')->prefix('cms/pengguna')->name('cms.pengguna.')->group(function () {
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
        Route::post('/roles/{role}/sync', [RoleController::class, 'triggerSync'])->name('roles.sync');
        Route::get('/roles/tables', [RoleController::class, 'getTables'])->name('roles.tables');
        Route::get('/roles/tables/{table}/columns', [RoleController::class, 'getTableColumns'])->name('roles.tables.columns');
    });

    // Users CRUD (permission-based using role_permissions table)
    Route::middleware('role:pengguna.users')->prefix('cms/pengguna')->name('cms.pengguna.')->group(function () {
        Route::get('/', [PenggunaController::class, 'index'])->name('index');
        Route::get('/create', [PenggunaController::class, 'create'])->name('create');
        Route::post('/', [PenggunaController::class, 'store'])->name('store');
        Route::get('/{pengguna}/edit', [PenggunaController::class, 'edit'])->name('edit');
        Route::put('/{pengguna}', [PenggunaController::class, 'update'])->name('update');
        Route::delete('/{pengguna}', [PenggunaController::class, 'destroy'])->name('destroy');
        Route::post('/{pengguna}/resend-verification', [PenggunaController::class, 'resendVerification'])->name('resend-verification');
        Route::post('/{pengguna}/mark-verified', [PenggunaController::class, 'markVerified'])->name('mark-verified');
    });
});


// Storage files route - MUST be before auth middleware and catch-all routes
// Explicitly serve files from storage to prevent corruption issues
Route::get('/storage/{path}', function ($path) {
    $storagePath = storage_path('app/public/' . $path);

    // Security: prevent directory traversal
    if (strpos(realpath($storagePath), realpath(storage_path('app/public'))) !== 0) {
        abort(403, 'Unauthorized access to storage.');
    }

    if (!file_exists($storagePath)) {
        abort(404, 'File not found.');
    }

    return response()->file($storagePath);
})
    ->where('path', '.+');
require __DIR__ . '/auth.php';

Route::get('/{path}/detail/{id}', [FeaturePageController::class, 'publicShowPublicationDetail'])
    ->where('path', '.+')
    ->where('id', '[0-9]+')
    ->name('publication.detail');

Route::post('/publication/{id}/share', [FeaturePageController::class, 'publicIncrementShares'])
    ->where('id', '[0-9]+')
    ->name('publication.share.increment');

Route::post('/layanan-publik/{id}/share', [FeaturePageController::class, 'publicIncrementLayananPublikShares'])
    ->where('id', '[0-9]+')
    ->name('layanan_publik.share.increment');

Route::post('/pengelolaan/{id}/share', [FeaturePageController::class, 'publicIncrementPengelolaanShares'])
    ->where('id', '[0-9]+')
    ->name('pengelolaan.share.increment');

Route::post('/kontak-kami/{id}/share', [FeaturePageController::class, 'publicIncrementKontakKamiShares'])
    ->where('id', '[0-9]+')
    ->name('kontak_kami.share.increment');

Route::get('/{path}', [FeaturePageController::class, 'publicShowByPath'])
    ->where('path', '^(?!storage/|cms/|api/|dashboard|profile|auth).+')
    ->name('feature.path');
