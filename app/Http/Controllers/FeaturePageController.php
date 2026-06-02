<?php

namespace App\Http\Controllers;

use App\Models\Feature;
use App\Models\FeaturePage;
use App\Models\FeaturePageSection;
use App\Models\Publication;
use App\Models\VirtualSlideshowPage;
use App\Services\TranslationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class FeaturePageController extends Controller
{
    use \App\Traits\SwapsOrder;
    /**
     * List pages for a feature (CMS).
     */
    public function index(Feature $feature)
    {
        // Redirect to slideshow for slideshow page_type
        if ($feature->page_type === 'slideshow') {
            return redirect()->route('cms.features.slideshow.index', $feature);
        }

        // Redirect to profile for profile page_type
        if ($feature->page_type === 'profile') {
            return redirect()->route('cms.features.profile.index', [$feature]);
        }

        // Redirect to feature detail (sub-features list) for dropdown types with page_type 'none'
        if ($feature->type === 'dropdown' && $feature->page_type === 'none') {
            return redirect()->route('cms.features.show', $feature);
        }

        $feature->load(['pages' => function ($q) {
            $q->withCount('sections');
        }, 'parent']);

        return view('cms.features.pages.index', compact('feature'));
    }

    /**
     * Show the form for creating a new page.
     */
    public function create(Feature $feature)
    {
        $feature->load('parent');

        // Use different view based on page_type
        if ($feature->page_type === 'slideshow') {
            $nextOrder = VirtualSlideshowPage::where('feature_id', $feature->id)->max('order') + 1;
            return view('cms.features.virtual_slideshow.create', compact('feature', 'nextOrder'));
        }

        return view('cms.features.pages.create', compact('feature'));
    }

    /**
     * Show the form for editing a page.
     */
    public function edit(Feature $feature, $pageId)
    {
        $feature->load('parent');

        // Use VirtualSlideshowPage for slideshow page_type
        if ($feature->page_type === 'slideshow') {
            $page = VirtualSlideshowPage::findOrFail($pageId);
            return view('cms.features.virtual_slideshow.edit', compact('feature', 'page'));
        }

        $page = FeaturePage::findOrFail($pageId);
        return view('cms.features.pages.edit', compact('feature', 'page'));
    }

    /**
     * Store a new page for a feature.
     */
    public function store(Request $request, Feature $feature, TranslationService $translationService)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'required|integer|min:0',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $validated['feature_id'] = $feature->id;
        $validated['title_en'] = $translationService->translate($validated['title']);
        if (! empty($validated['description'])) {
            $validated['description_en'] = $translationService->translate($validated['description']);
        }

        if ($feature->page_type === 'slideshow' && $request->hasFile('thumbnail')) {
            $validated['thumbnail_path'] = $request->file('thumbnail')->store('features/pages/thumbnails', 'public');
        }

        $insertOrder = (int) $validated['order'];
        $scopeConditions = ['feature_id' => $feature->id];
        $extraAttributes = array_filter([
            'title' => $validated['title'],
            'title_en' => $validated['title_en'] ?? null,
            'description' => $validated['description'] ?? null,
            'description_en' => $validated['description_en'] ?? null,
            'thumbnail_path' => $validated['thumbnail_path'] ?? null,
        ], fn($v) => $v !== null);

        // Use VirtualSlideshowPage for slideshow page_type
        if ($feature->page_type === 'slideshow') {
            $this->insertAndShiftOrder(VirtualSlideshowPage::class, $insertOrder, $scopeConditions, $extraAttributes);
            return redirect()->route('cms.features.slideshow.index', $feature)
                ->with('success', __('cms.feature_pages.flash.page_added'));
        }

        $this->insertAndShiftOrder(FeaturePage::class, $insertOrder, $scopeConditions, $extraAttributes);

        return redirect()->route('cms.features.pages.index', $feature)
            ->with('success', __('cms.feature_pages.flash.page_added'));
    }

    /**
     * Show page detail - manage sections (CMS).
     */
    public function show(Feature $feature, FeaturePage $page)
    {
        $page->load('sections');
        $feature->load('parent');

        return view('cms.features.pages.show', compact('feature', 'page'));
    }

    /**
     * Update a page.
     */
    public function update(Request $request, Feature $feature, $pageId, TranslationService $translationService)
    {
        // Use VirtualSlideshowPage for slideshow page_type
        if ($feature->page_type === 'slideshow') {
            $page = VirtualSlideshowPage::findOrFail($pageId);
        } else {
            $page = FeaturePage::findOrFail($pageId);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'required|integer|min:0',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $validated['title_en'] = $translationService->translate($validated['title']);
        $validated['description_en'] = ! empty($validated['description'])
            ? $translationService->translate($validated['description'])
            : null;

        // Handle thumbnail only for slideshow pages
        if ($feature->page_type === 'slideshow') {
            // Handle remove thumbnail request
            if ($request->input('remove_thumbnail') === '1' && $page->thumbnail_path) {
                Storage::disk('public')->delete($page->thumbnail_path);
                $page->thumbnail_path = null;
            }

            // Handle new thumbnail upload
            if ($request->hasFile('thumbnail')) {
                if ($page->thumbnail_path) {
                    Storage::disk('public')->delete($page->thumbnail_path);
                }
                $validated['thumbnail_path'] = $request->file('thumbnail')->store('features/pages/thumbnails', 'public');
            }
        }

        $this->swapOrder($page, (int) $validated['order'], (int) $page->order, ['feature_id' => $page->feature_id]);
        $page->update($validated);

        if ($feature->page_type === 'slideshow') {
            return redirect()->route('cms.features.slideshow.index', $feature)
                ->with('success', __('cms.feature_pages.flash.page_updated'));
        }

        return redirect()->route('cms.features.pages.index', $feature)
            ->with('success', __('cms.feature_pages.flash.page_updated'));
    }

    /**
     * Delete a page.
     */
    public function destroy(Feature $feature, $pageId)
    {
        // Use VirtualSlideshowPage for slideshow page_type
        if ($feature->page_type === 'slideshow') {
            $page = VirtualSlideshowPage::findOrFail($pageId);
            $this->deleteAndShiftOrder($page, ['feature_id' => $page->feature_id]);
            return redirect()->route('cms.features.slideshow.index', $feature)
                ->with('success', __('cms.feature_pages.flash.page_deleted'));
        }

        $page = FeaturePage::findOrFail($pageId);
        $this->deleteAndShiftOrder($page, ['feature_id' => $page->feature_id]);

        return redirect()->route('cms.features.pages.index', $feature)
            ->with('success', __('cms.feature_pages.flash.page_deleted'));
    }

    /**
     * Store a new section for a page.
     */
    public function storeSection(Request $request, Feature $feature, FeaturePage $page, TranslationService $translationService)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'required|integer|min:0',
            'images' => 'nullable|array', // unlimited
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'image_positions' => 'nullable|array',
        ]);

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('features/sections', 'public');
            }
        }

        FeaturePageSection::create([
            'feature_page_id' => $page->id,
            'title' => $validated['title'],
            'title_en' => $translationService->translate($validated['title']),
            'description' => $validated['description'] ?? null,
            'description_en' => ! empty($validated['description'])
                ? $translationService->translate($validated['description'])
                : null,
            'images' => $imagePaths ?: null,
            'image_positions' => $validated['image_positions'] ?? null,
            'order' => $validated['order'],
        ]);

        return redirect()->route('cms.features.pages.show', [$feature, $page])
            ->with('success', __('cms.feature_pages.flash.section_added'));
    }

    /**
     * Update a section.
     */
    public function updateSection(Request $request, Feature $feature, FeaturePage $page, FeaturePageSection $section, TranslationService $translationService)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'required|integer|min:0',
            'images' => 'nullable|array', // unlimited
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'existing_images' => 'nullable|array',
            'existing_images.*' => 'string',
            'image_positions' => 'nullable|array',
        ]);

        // Keep existing images that weren't removed
        $existingImages = $validated['existing_images'] ?? [];

        // Delete removed images from storage
        $oldImages = $section->images ?? [];
        foreach ($oldImages as $oldImage) {
            if (! in_array($oldImage, $existingImages)) {
                Storage::disk('public')->delete($oldImage);
            }
        }

        // Add new uploaded images
        $imagePaths = $existingImages;
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('features/sections', 'public');
            }
        }

        $this->swapOrder($section, (int) $validated['order'], (int) $section->order, ['feature_page_id' => $section->feature_page_id]);
        $section->update([
            'title' => $validated['title'],
            'title_en' => $translationService->translate($validated['title']),
            'description' => $validated['description'] ?? null,
            'description_en' => ! empty($validated['description'])
                ? $translationService->translate($validated['description'])
                : null,
            'images' => $imagePaths ?: null,
            'image_positions' => $validated['image_positions'] ?? null,
            'order' => $validated['order'],
        ]);

        return redirect()->route('cms.features.pages.show', [$feature, $page])
            ->with('success', __('cms.feature_pages.flash.section_updated'));
    }

    /**
     * Delete a section.
     */
    public function destroySection(Feature $feature, FeaturePage $page, FeaturePageSection $section)
    {
        $this->deleteSectionImages($section);
        $this->deleteAndShiftOrder($section, ['feature_page_id' => $section->feature_page_id]);

        return redirect()->route('cms.features.pages.show', [$feature, $page])
            ->with('success', __('cms.feature_pages.flash.section_deleted'));
    }

    /**
     * Public: show feature page with sections (paginated).
     */
    public function publicShow(Feature $feature, ?int $pageNum = null, bool $requiresLoginModal = false, ?array $loginModalPreviews = null, ?string $loginModalPreview = null, ?array $loginModalRoomNames = null, ?string $loginModalRoomName = null, ?string $loginModalPrompt = null)
    {
        $feature->load('parent');

        if ($feature->page_type === 'layanan_publik') {
            $pages = $feature->layananPubliks()->where('is_active', true)->orderBy('order')->get();
            $pageNum = $pageNum ?? 1;
            $currentPage = $pages->values()->get($pageNum - 1);

            $requiresLoginModal = false;
            $loginModalPreviews = [];
            $loginModalPreview = null;
            $loginModalRoomNames = [];
            $loginModalRoomName = null;
            $loginModalPrompt = __('auth.login_required_prompt');

            if ($currentPage) {
                if (!empty($currentPage->extra_data['is_login_required']) && !\Illuminate\Support\Facades\Auth::check()) {
                    $requiresLoginModal = true;
                    $loginModalRoomName = app()->getLocale() === 'en' && $currentPage->title_en ? $currentPage->title_en : $currentPage->title;
                } else {
                    \Illuminate\Support\Facades\DB::table('layanan_publiks')->where('id', $currentPage->id)->increment('views');
                    $currentPage->views++;
                }
            }

            $locale = app()->getLocale();
            $sidebarData = $this->getSidebarData($locale);
            $popularNews = $sidebarData['popularNews'];
            $pameranArsip = $sidebarData['pameranArsip'];

            $kuotaHarian = 5; // default fallback
            if ($currentPage && isset($currentPage->extra_data)) {
                $kuotaHarian = (int) ($currentPage->extra_data['kuota_harian'] ?? 5);
            }

            $bookings = \App\Models\VisitRegistration::where('visit_date', '>=', now()->toDateString())
                ->where('visit_date', '<=', now()->addMonths(2)->toDateString())
                ->whereNotIn('status', ['rejected'])
                ->select('visit_date', 'visit_time')
                ->selectRaw('SUM(visitor_count) as total_visitors')
                ->groupBy('visit_date', 'visit_time')
                ->get()
                ->groupBy(function($item) {
                    return \Carbon\Carbon::parse($item->visit_date)->toDateString();
                })
                ->map(function ($group) {
                    return $group->pluck('total_visitors', 'visit_time')->toArray();
                })
                ->toArray();

            return view('pages.layanan_publik', [
                'feature'             => $feature,
                'pages'               => $pages,
                'currentPage'         => $currentPage,
                'currentPageNum'      => $pageNum,
                'totalPages'          => $pages->count(),
                'locale'              => $locale,
                'popularNews'         => $popularNews,
                'pameranArsip'        => $pameranArsip,
                'requiresLoginModal'  => $requiresLoginModal,
                'loginModalPreviews'  => $loginModalPreviews,
                'loginModalPreview'   => $loginModalPreview,
                'loginModalRoomNames' => $loginModalRoomNames,
                'loginModalRoomName'  => $loginModalRoomName,
                'loginModalPrompt'    => $loginModalPrompt,
                'bookings'            => $bookings,
                'kuotaHarian'         => $kuotaHarian,
            ]);
        }

        if ($feature->page_type === 'pengelolaan') {
            $pages = $feature->pengelolaans()->where('is_active', true)->orderBy('order')->get();
            $pageNum = $pageNum ?? 1;
            $currentPage = $pages->values()->get($pageNum - 1);

            $requiresLoginModal = false;
            $loginModalPreviews = [];
            $loginModalPreview = null;
            $loginModalRoomNames = [];
            $loginModalRoomName = null;
            $loginModalPrompt = __('auth.login_required_prompt');

            if ($currentPage) {
                if (!empty($currentPage->extra_data['is_login_required']) && !\Illuminate\Support\Facades\Auth::check()) {
                    $requiresLoginModal = true;
                    $loginModalRoomName = app()->getLocale() === 'en' && $currentPage->name_en ? $currentPage->name_en : $currentPage->name;
                } else {
                    \Illuminate\Support\Facades\DB::table('pengelolaans')->where('id', $currentPage->id)->increment('views');
                    $currentPage->views++;
                }
            }

            $locale = app()->getLocale();
            $sidebarData = $this->getSidebarData($locale);
            $popularNews = $sidebarData['popularNews'];
            $pameranArsip = $sidebarData['pameranArsip'];

            return view('pages.pengelolaan', [
                'feature'             => $feature,
                'pages'               => $pages,
                'currentPage'         => $currentPage,
                'currentPageNum'      => $pageNum,
                'totalPages'          => $pages->count(),
                'locale'              => $locale,
                'popularNews'         => $popularNews,
                'pameranArsip'        => $pameranArsip,
                'requiresLoginModal'  => $requiresLoginModal,
                'loginModalPreviews'  => $loginModalPreviews,
                'loginModalPreview'   => $loginModalPreview,
                'loginModalRoomNames' => $loginModalRoomNames,
                'loginModalRoomName'  => $loginModalRoomName,
                'loginModalPrompt'    => $loginModalPrompt,
            ]);
        }

        if ($feature->page_type === 'kontak_kami') {
            $pages = $feature->kontakKamis()->where('is_active', true)->orderBy('order')->get();
            $pageNum = $pageNum ?? 1;
            $currentPage = $pages->values()->get($pageNum - 1);
            if ($currentPage) {
                \Illuminate\Support\Facades\DB::table('kontak_kami')->where('id', $currentPage->id)->increment('views');
                $currentPage->views++;
            }

            $locale = app()->getLocale();
            $sidebarData = $this->getSidebarData($locale);
            $popularNews = $sidebarData['popularNews'];
            $pameranArsip = $sidebarData['pameranArsip'];

            return view('pages.kontak_kami', [
                'feature'             => $feature,
                'pages'               => $pages,
                'currentPage'         => $currentPage,
                'currentPageNum'      => $pageNum,
                'totalPages'          => $pages->count(),
                'locale'              => $locale,
                'popularNews'         => $popularNews,
                'pameranArsip'        => $pameranArsip,
            ]);
        }

        if ($feature->page_type === 'none') {
            return view('pages.none', [
                'feature'             => $feature,
                'pages'               => collect(),
                'currentPage'         => null,
                'currentPageNum'      => 1,
                'totalPages'          => 0,
            ]);
        }

        $pages = $feature->pages()->where('is_active', true)->withCount('sections')->orderBy('order')->get();

        if ($pages->isEmpty()) {
            abort(404);
        }

        $pageNum = $pageNum ?? 1;
        $currentPage = $pages->values()->get($pageNum - 1);

        if (! $currentPage) {
            abort(404);
        }

        $currentPage->load('sections');

        if ($feature->page_type === 'onsite') {
            return view('pages.onsite', [
                'feature'             => $feature,
                'pages'               => $pages,
                'currentPage'         => $currentPage,
                'currentPageNum'      => $pageNum,
                'totalPages'          => $pages->count(),
            ]);
        }

        $virtual3dRooms = $feature->virtual3dRooms()->with('media')->get();

        return view('pages.virtual_3d_tour', [
            'feature'             => $feature,
            'pages'               => $pages,
            'currentPage'         => $currentPage,
            'currentPageNum'      => $pageNum,
            'totalPages'          => $pages->count(),
            'requiresLoginModal'  => $requiresLoginModal,
            'loginModalPreviews'  => $loginModalPreviews ?? [],
            'loginModalPreview'   => $loginModalPreview,
            'loginModalRoomNames' => $loginModalRoomNames ?? [],
            'loginModalRoomName'  => $loginModalRoomName,
            'virtual3dRooms'      => $virtual3dRooms,
        ]);
    }

    /**
     * @internal wrapped call from publicShowByPath
     */
    private function publicShowWithModal(Feature $feature, int $pageNum, bool $requiresLoginModal)
    {
        return $this->publicShow($feature, $pageNum, $requiresLoginModal);
    }

    /**
     * Public: show feature page by path (e.g., /pameran/tetap).
     */
    public function publicShowByPath(Request $request)
    {
        $path = '/'.$request->path;
        $feature = Feature::where('path', $path)->firstOrFail();
        $feature->loadCount('pages');

        // Pages under /pameran/virtual or /pameran-arsip-virtual require authentication — show login modal if guest
        $requiresLoginModal = !Auth::check() && (
            str_contains($path, '/pameran/virtual') ||
            str_contains($path, '/pameran-virtual') ||
            str_contains($path, '/pameran-arsip-virtual')
        );

        // Resolve preview image for the login modal right panel
        $loginModalPreviews = [];
        $loginModalPreview = null;
        $loginModalRoomNames = [];
        $loginModalRoomName = null;

        // Force initialize to ensure they're always defined
        $loginModalPreviews = $loginModalPreviews ?? [];
        $loginModalRoomNames = $loginModalRoomNames ?? [];

        // Profile page type — load all profile pages from profiles table with their sections
        if ($feature->page_type === 'profile') {
            $allProfilePages = $feature->profiles()->with('sections')->orderBy('order')->get();
            $locale = app()->getLocale();

            // Ensure image_positions is loaded (it's auto-casted in Profile model)
            $allProfilePages->each(function ($page) {
                // Force access to trigger cast if needed
                $page->image_positions;
            });

            // Handle pagination with ?page=N parameter
            $totalPages = $allProfilePages->count();
            $pageNum = $request->input('page', 1);
            $currentPageIndex = max(0, min((int)$pageNum - 1, $totalPages - 1));
            $currentPage = $allProfilePages->values()->get($currentPageIndex);

            // Calculate isEven for alternating section backgrounds
            $isEven = ($currentPageIndex + 1) % 2 === 0;

            return view('pages.profile', compact(
                'feature', 'allProfilePages', 'locale', 'totalPages', 'currentPage', 'currentPageIndex', 'isEven'
            ));
        }

        // Publication page type
        if ($feature->page_type === 'publication') {
            $perPage = 10;
            $query = $feature->publications()
                ->where('is_active', true);

            // Server-side search
            if ($search = request('search')) {
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('title_en', 'like', "%{$search}%")
                      ->orWhere('published_at', 'like', "%{$search}%");
                });
            }

            // For news/publications, we want newest first (by published_at)
            // Using reorder() to clear the default 'order' from the relationship
            $query->reorder('published_at', 'desc')->orderBy('created_at', 'desc');

            $allPages = $query->paginate($perPage)->withQueryString();
            $locale = app()->getLocale();
            $currentPage = $allPages->first(); 

            // Sidebar data for news: Popular news
            $popularNews = collect();
            if ($currentPage && $currentPage->type === 'berita') {
                $popularNews = \Illuminate\Support\Facades\Cache::remember('pub_popular_news_' . $feature->id, 120, function () use ($feature) {
                    return $feature->publications()
                        ->select(['id', 'title', 'title_en', 'images', 'published_at', 'created_at', 'views', 'shares', 'type'])
                        ->where('type', 'berita')
                        ->where('is_active', true)
                        ->reorder('views', 'desc')
                        ->limit(5)
                        ->get();
                });
            }

            $allGalleryMedia = [];
            if ($currentPage && $currentPage->type === 'galeri') {
                $allGalleryMedia = \Illuminate\Support\Facades\Cache::remember('gallery_media_all', 600, fn () => $this->getAllSystemMedia([]));
            }

            return view('pages.publication', compact(
                'feature', 'allPages', 'locale', 'currentPage', 'popularNews', 'allGalleryMedia'
            ));
        }

        // Virtual Slideshow — show SimHive-style interactive page with page selection
        if ($feature->page_type === 'slideshow') {
            $pages = $feature->slideshowPages()->with('slideshowSlides')->orderBy('order')->get();
            $selectedPage = null;
            $slides = collect();
            $locale = app()->getLocale();

            // Set previews for login modal (array for carousel if multiple pages)
            if ($requiresLoginModal && $pages->isNotEmpty()) {
                foreach ($pages as $page) {
                    $previewUrl = null;
                    $pageTitle = app()->getLocale() === 'en' && $page->title_en ? $page->title_en : $page->title;
                    $firstSlide = $page->slideshowSlides->sortBy('order')->first();

                    if ($page->thumbnail_path) {
                        $previewUrl = asset('storage/'.$page->thumbnail_path);
                    } elseif ($firstSlide) {
                        $imgs = $firstSlide->images;
                        $urls = $firstSlide->image_urls;
                        if ($imgs && count($imgs) > 0) {
                            $previewUrl = asset('storage/'.$imgs[0]);
                        } elseif ($urls && count($urls) > 0) {
                            $previewUrl = $urls[0];
                        }
                    }

                    if ($previewUrl) {
                        $loginModalPreviews[] = $previewUrl;
                        $loginModalRoomNames[] = $pageTitle;
                    }
                }
            }

            // Check if specific page is requested
            $pageNum = $request->input('page');
            if ($pageNum) {
                $selectedPage = $pages->firstWhere('order', $pageNum);
                if ($selectedPage) {
                    $slides = $selectedPage->slideshowSlides->sortBy('order')->values();
                }
            }

            // Use first preview as fallback for selectedPage view
            $loginModalPreview = $loginModalPreviews[0] ?? null;
            $loginModalRoomName = $loginModalRoomNames[0] ?? null;

            // Use separate views for landing vs content
            if ($selectedPage) {
                return view('pages.virtual_slideshow_content', compact(
                    'feature', 'pages', 'selectedPage', 'slides', 'locale',
                    'requiresLoginModal', 'loginModalPreviews', 'loginModalPreview', 'loginModalRoomNames', 'loginModalRoomName'
                ));
            }

            return view('pages.virtual_slideshow_landing', compact(
                'feature', 'pages',
                'requiresLoginModal', 'loginModalPreviews', 'loginModalPreview', 'loginModalRoomNames', 'loginModalRoomName'
            ));
        }

        // Handle beranda page type - load content from language files
        // Check if there's a dedicated home_{id}.php file for this feature (except for original beranda)
        $homeFilePath = resource_path("lang/id/home_{$feature->id}.php");
        if ($feature->page_type === 'home' && $feature->id != 1 && File::exists($homeFilePath)) {
            $locale = app()->getLocale();
            $idContent = $this->loadBerandaContent($feature->id, 'id');
            $enContent = $this->loadBerandaContent($feature->id, 'en');
            $content = $locale === 'id' ? $idContent : $enContent;

            return view('welcome', compact('feature', 'content'));
        }
        if ($requiresLoginModal) {
            // No separate preview gathering needed here - handled in virtual3dRooms/virtualRooms sections
        }

        // Virtual 3D Rooms feature — show interactive 4-walls 3D room
        if (method_exists($feature, 'virtual3dRooms')) {
            $virtual3dRooms = $feature->virtual3dRooms()->with('media')->get();

            // Check subfeatures if the parent feature has no virtual 3d rooms
            if ($virtual3dRooms->isEmpty() && method_exists($feature, 'subfeatures')) {
                foreach ($feature->subfeatures as $sub) {
                    if (method_exists($sub, 'virtual3dRooms')) {
                        $virtual3dRooms = $virtual3dRooms->merge($sub->virtual3dRooms()->with('media')->get());
                    }
                }
            }

            if ($virtual3dRooms->isNotEmpty()) {
                // Add room thumbnails for modal carousel if needed
                if ($requiresLoginModal) {
                    foreach ($virtual3dRooms as $room) {
                        $imgPath = $room->thumbnail_path ?? null;
                        if ($imgPath) {
                            $loginModalPreviews[] = asset('storage/'.$imgPath);
                            $loginModalRoomNames[] = $room->translated_name;
                        }
                    }
                }

                return view('pages.virtual_3d_tour', compact(
                    'feature', 'virtual3dRooms', 'requiresLoginModal',
                    'loginModalPreviews', 'loginModalPreview', 'loginModalRoomNames', 'loginModalRoomName'
                ));
            }
        }

        // Virtual rooms feature (360) — show dedicated 360° tour page
        if (method_exists($feature, 'virtualRooms')) {
            $virtualRooms = $feature->virtualRooms()->withCount('hotspots')->with('hotspots')->get();
            if ($virtualRooms->isNotEmpty()) {
                if ($requiresLoginModal) {
                    foreach ($virtualRooms as $room) {
                        $imgPath = $room->thumbnail_path ?? $room->image_360_path ?? null;
                        if ($imgPath) {
                            $loginModalPreviews[] = asset('storage/'.$imgPath);
                            $loginModalRoomNames[] = $room->translated_name;
                        }
                    }
                    $loginModalRoomName = $loginModalRoomNames[0] ?? null;
                }

                return view('pages.virtual_tour', compact(
                    'feature', 'virtualRooms', 'requiresLoginModal',
                    'loginModalPreviews', 'loginModalPreview', 'loginModalRoomNames', 'loginModalRoomName'
                ));
            }
        }

        // Virtual Book Pages - show flip book
        if ($feature->is_virtual_book || $feature->books()->exists()) {
            $books = $feature->books()->with('pages')->orderBy('order')->get();

            // Set previews for login modal (array for carousel if multiple books)
            if ($requiresLoginModal && $books->isNotEmpty()) {
                foreach ($books as $book) {
                    $previewUrl = null;
                    $bookTitle = app()->getLocale() === 'en' && $book->title_en ? $book->title_en : $book->title;

                    if ($book->thumbnail) {
                        $previewUrl = asset('storage/'.$book->thumbnail);
                    } elseif ($book->cover_image) {
                        $previewUrl = asset('storage/'.$book->cover_image);
                    } elseif ($feature->book_cover) {
                        $previewUrl = asset('storage/'.$feature->book_cover);
                    } elseif ($feature->book_thumbnail) {
                        $previewUrl = asset('storage/'.$feature->book_thumbnail);
                    }

                    if ($previewUrl) {
                        $loginModalPreviews[] = $previewUrl;
                        $loginModalRoomNames[] = $bookTitle;
                    }
                }
            }

            // Fallback for single book view
            $loginModalPreview = $loginModalPreviews[0] ?? null;
            $loginModalRoomName = $loginModalRoomNames[0] ?? null;

            $readBookId = request('read');
            $detailBookId = request('detail');

            if ($readBookId) {
                $book = $books->firstWhere('id', $readBookId);
                if ($book) {
                    // Update preview for specific book if needed
                    if ($requiresLoginModal) {
                        $previewUrl = null;
                        if ($book->thumbnail) {
                            $previewUrl = asset('storage/'.$book->thumbnail);
                        } elseif ($book->cover_image) {
                            $previewUrl = asset('storage/'.$book->cover_image);
                        }
                        if ($previewUrl) {
                            $loginModalPreview = $previewUrl;
                            $loginModalRoomName = app()->getLocale() === 'en' && $book->title_en ? $book->title_en : $book->title;
                        }
                    }
                    return view('pages.virtual_book_viewer', compact(
                        'feature', 'book', 'requiresLoginModal',
                        'loginModalPreviews', 'loginModalPreview', 'loginModalRoomNames', 'loginModalRoomName'
                    ));
                }
            }

            if ($detailBookId) {
                $book = $books->firstWhere('id', $detailBookId);
                if ($book) {
                    return view('pages.virtual_book_detail', compact(
                        'feature', 'book', 'requiresLoginModal',
                        'loginModalPreviews', 'loginModalPreview', 'loginModalRoomNames', 'loginModalRoomName'
                    ));
                }
            }

            return view('pages.virtual_book_grid', compact(
                'feature', 'books', 'requiresLoginModal',
                'loginModalPreviews', 'loginModalPreview', 'loginModalRoomNames', 'loginModalRoomName'
            ));
        }

        if ($feature->page_type === 'layanan_publik') {
            if ($feature->layananPubliks()->where('is_active', true)->count() > 0) {
                return $this->publicShow($feature, 1, $requiresLoginModal, $loginModalPreviews, $loginModalPreview, $loginModalRoomNames, $loginModalRoomName);
            }

            $locale = app()->getLocale();
            $sidebarData = $this->getSidebarData($locale);
            $popularNews = $sidebarData['popularNews'];
            $pameranArsip = $sidebarData['pameranArsip'];

            return view('pages.layanan_publik', [
                'feature'             => $feature,
                'pages'               => collect(),
                'currentPage'         => null,
                'currentPageNum'      => 1,
                'totalPages'          => 1,
                'locale'              => $locale,
                'popularNews'         => $popularNews,
                'pameranArsip'        => $pameranArsip,
            ]);
        }

        if ($feature->page_type === 'pengelolaan') {
            if ($feature->pengelolaans()->where('is_active', true)->count() > 0) {
                return $this->publicShow($feature, 1, $requiresLoginModal, $loginModalPreviews, $loginModalPreview, $loginModalRoomNames, $loginModalRoomName);
            }

            $locale = app()->getLocale();
            $sidebarData = $this->getSidebarData($locale);
            $popularNews = $sidebarData['popularNews'];
            $pameranArsip = $sidebarData['pameranArsip'];

            return view('pages.pengelolaan', [
                'feature'             => $feature,
                'pages'               => collect(),
                'currentPage'         => null,
                'currentPageNum'      => 1,
                'totalPages'          => 1,
                'locale'              => $locale,
                'popularNews'         => $popularNews,
                'pameranArsip'        => $pameranArsip,
            ]);
        }

        if ($feature->page_type === 'kontak_kami') {
            if ($feature->kontakKamis()->where('is_active', true)->count() > 0) {
                return $this->publicShow($feature, 1, $requiresLoginModal, $loginModalPreviews, $loginModalPreview, $loginModalRoomNames, $loginModalRoomName);
            }

            $locale = app()->getLocale();
            $sidebarData = $this->getSidebarData($locale);
            $popularNews = $sidebarData['popularNews'];
            $pameranArsip = $sidebarData['pameranArsip'];

            return view('pages.kontak_kami', [
                'feature'             => $feature,
                'pages'               => collect(),
                'currentPage'         => null,
                'currentPageNum'      => 1,
                'totalPages'          => 1,
                'locale'              => $locale,
                'popularNews'         => $popularNews,
                'pameranArsip'        => $pameranArsip,
            ]);
        }

        if ($feature->pages_count > 0) {
            return $this->publicShow($feature, 1, $requiresLoginModal, $loginModalPreviews, $loginModalPreview, $loginModalRoomNames, $loginModalRoomName);
        }

        $virtual3dRooms = $feature->virtual3dRooms()->with('media')->get();
        if ($virtual3dRooms->isEmpty() && method_exists($feature, 'subfeatures')) {
            foreach ($feature->subfeatures as $sub) {
                if (method_exists($sub, 'virtual3dRooms')) {
                    $virtual3dRooms = $virtual3dRooms->merge($sub->virtual3dRooms()->with('media')->get());
                }
            }
        }

        return view('pages.virtual_3d_tour', compact(
            'feature', 'requiresLoginModal', 'loginModalPreviews', 'loginModalPreview', 'loginModalRoomNames', 'loginModalRoomName', 'virtual3dRooms'
        ));
    }

    /**
     * Load beranda content from language files.
     */
    public function publicShowPublicationDetail(Request $request, $path, $id)
    {
        $fullPath = '/' . $path;
        $feature = Feature::where('path', $fullPath)->firstOrFail();
        $publication = $feature->publications()->where('id', $id)->where('is_active', true)->firstOrFail();
        $locale = app()->getLocale();

        // Increment views
        \Illuminate\Support\Facades\DB::table('publications')->where('id', $publication->id)->increment('views');
        $publication->views++;

        // Popular news for sidebar
        $popularNews = \Illuminate\Support\Facades\Cache::remember('sidebar_popular_news_' . $locale, 60, function() use ($feature) {
            return $feature->publications()
                ->select(['id', 'title', 'title_en', 'images', 'published_at', 'created_at', 'views', 'shares', 'type'])
                ->where('type', 'berita')
                ->where('is_active', true)
                ->reorder('views', 'desc')
                ->limit(5)
                ->get();
        });

        return view('pages.publication_detail', compact(
            'feature', 'publication', 'locale', 'popularNews'
        ));
    }

    public function publicIncrementShares(Request $request, $id)
    {
        $publication = Publication::findOrFail($id);
        \Illuminate\Support\Facades\DB::table('publications')->where('id', $publication->id)->increment('shares');
        $publication->shares++;
        return response()->json(['success' => true, 'shares' => $publication->shares]);
    }

    public function publicIncrementLayananPublikShares(Request $request, $id)
    {
        $layananPublik = \App\Models\LayananPublik::findOrFail($id);
        \Illuminate\Support\Facades\DB::table('layanan_publiks')->where('id', $layananPublik->id)->increment('shares');
        $layananPublik->shares++;
        return response()->json(['success' => true, 'shares' => $layananPublik->shares]);
    }

    public function publicIncrementPengelolaanShares(Request $request, $id)
    {
        $pengelolaan = \App\Models\Pengelolaan::findOrFail($id);
        \Illuminate\Support\Facades\DB::table('pengelolaans')->where('id', $pengelolaan->id)->increment('shares');
        $pengelolaan->shares++;
        return response()->json(['success' => true, 'shares' => $pengelolaan->shares]);
    }

    public function publicIncrementKontakKamiShares(Request $request, $id)
    {
        $kontakKami = \App\Models\KontakKami::findOrFail($id);
        \Illuminate\Support\Facades\DB::table('kontak_kami')->where('id', $kontakKami->id)->increment('shares');
        $kontakKami->shares++;
        return response()->json(['success' => true, 'shares' => $kontakKami->shares]);
    }

    private function getFeatureTranslations($featureId, $locale): array
    {
        // For feature ID 1, use original home.php
        if ($featureId == 1) {
            $path = resource_path("lang/{$locale}/home.php");
        } else {
            $path = resource_path("lang/{$locale}/home_{$featureId}.php");
        }

        if (File::exists($path)) {
            return include $path;
        }

        return [];
    }

    private function loadBerandaContent(int $featureId, string $locale): array
    {
        // For feature ID 1, use original home.php
        if ($featureId == 1) {
            $path = resource_path("lang/{$locale}/home.php");
        } else {
            $path = resource_path("lang/{$locale}/home_{$featureId}.php");
        }

        if (File::exists($path)) {
            return include $path;
        }

        return [];
    }

    private function getAllSystemMedia(array $manualMedia = [])
    {
        $allMedia = collect($manualMedia);

        $models = [
            [\App\Models\Publication::class, 'images'],
            [\App\Models\FeaturePageSection::class, 'images'],
            [\App\Models\VirtualSlideshowPage::class, 'thumbnail_path'],
            [\App\Models\VirtualSlideshowSlide::class, 'images'],
            [\App\Models\VirtualSlideshowSlide::class, 'video_file'],
            [\App\Models\Profile::class, 'images'],
            [\App\Models\ProfileSection::class, 'images'],
            [\App\Models\Virtual3dRoom::class, 'thumbnail_path'],
            [\App\Models\Virtual3dMedia::class, 'path'],
            [\App\Models\VirtualRoom::class, 'thumbnail_path'],
            [\App\Models\VirtualRoom::class, 'image_360_path'],
            [\App\Models\Book::class, 'thumbnail'],
            [\App\Models\Book::class, 'cover_image'],
            [\App\Models\VirtualBookPage::class, 'image_path'],
        ];

        foreach ($models as [$class, $column]) {
            try {
                $class::whereNotNull($column)->select($column)->cursor()->each(function($row) use ($column, &$allMedia) {
                    $val = $row->{$column};
                    if (is_array($val)) {
                        foreach ($val as $v) if ($v) $allMedia->push($v);
                    } elseif ($val) {
                        $allMedia->push($val);
                    }
                });
            } catch (\Exception $e) {
                // Skip if model or column doesn't exist
                continue;
            }
        }

        // Filter for images and videos only, and verify existence if local
        return $allMedia->filter(function($path) {
            // Check extension
            if (!preg_match('/\.(jpg|jpeg|png|webp|gif|mp4|webm|ogg)$/i', $path)) {
                return false;
            }
            
            // If it's a full URL, keep it
            if (preg_match('/^https?:\/\//', $path)) {
                return true;
            }

            // Check existence if local
            $cleanPath = preg_replace('/^storage\//', '', $path);
            $fullDiskPath = storage_path('app/public/' . $cleanPath);
            
            return is_file($fullDiskPath);
        })->unique()->values()->toArray();
    }

    private function deleteSectionImages(FeaturePageSection $section): void
    {
        if ($section->images) {
            foreach ($section->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }
    }

    /**
     * Toggle the visibility of a page.
     */
    public function toggleVisibility(Feature $feature, FeaturePage $page)
    {
        $page->update(['is_active' => ! $page->is_active]);

        return back()->with('success', __('cms.feature_pages.flash.visibility_toggled'));
    }

    /**
     * Helper to retrieve and cache lightweight sidebar data.
     */
    private function getSidebarData(string $locale)
    {
        return \Illuminate\Support\Facades\Cache::remember('sidebar_data_' . $locale, 60, function() {
            $news = \App\Models\Publication::select(['id', 'title', 'title_en', 'images', 'published_at', 'created_at', 'views', 'shares', 'type'])
                ->where('type', 'berita')
                ->where('is_active', true)
                ->orderBy('views', 'desc')
                ->limit(5)
                ->get()
                ->map(function($pn) {
                    return (object)[
                        'id' => $pn->id,
                        'title' => $pn->title,
                        'title_en' => $pn->title_en,
                        'images' => $pn->images,
                        'published_at' => $pn->published_at ? \Carbon\Carbon::parse($pn->published_at) : null,
                        'created_at' => $pn->created_at ? \Carbon\Carbon::parse($pn->created_at) : null,
                        'views' => $pn->views,
                        'shares' => $pn->shares,
                        'type' => $pn->type,
                    ];
                });

            $pameran = collect();
            foreach (\App\Models\Virtual3dRoom::with('feature')->orderBy('id', 'desc')->limit(3)->get() as $room) {
                if (!$room->feature || !$room->feature->path) continue;
                $pameran->push((object)[
                    'title' => $room->translated_name ?? $room->name,
                    'image' => $room->thumbnail_path ? asset('storage/' . $room->thumbnail_path) : null,
                    'link'  => url($room->feature->path),
                    'date'  => $room->created_at,
                ]);
            }
            foreach (\App\Models\Book::with('feature')->orderBy('id', 'desc')->limit(2)->get() as $book) {
                if (!$book->feature || !$book->feature->path) continue;
                $pameran->push((object)[
                    'title' => $book->translated_title ?? $book->title,
                    'image' => ($book->thumbnail ?: $book->cover_image) ? asset('storage/' . ($book->thumbnail ?: $book->cover_image)) : null,
                    'link'  => url($book->feature->path),
                    'date'  => $book->created_at,
                ]);
            }
            return ['popularNews' => $news, 'pameranArsip' => $pameran];
        });
    }
}
