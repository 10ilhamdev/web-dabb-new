<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use App\Models\FeaturePage;
use App\Models\VirtualSlideshowPage;
use App\Models\VirtualSlideshowSlide;
use App\Services\TranslationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class VirtualSlideshowController extends Controller
{
    use \App\Traits\SwapsOrder;
    /**
     * Show pages table for this slideshow feature
     */
    public function index(Feature $feature)
    {
        $feature->load('parent');
        $pages = $feature->slideshowPages()->withCount('slideshowSlides')->with('slideshowSlides')->orderBy('order')->get();
        return view('cms.features.virtual_slideshow.index', compact('feature', 'pages'));
    }

    /**
     * Show slides for a specific page
     */
    public function slidesIndex(Feature $feature, $pageId)
    {
        $page = VirtualSlideshowPage::findOrFail($pageId);
        $feature->load('parent');
        $page->load('slideshowSlides');
        $slides = $page->slideshowSlides()->orderBy('order')->get();
        return view('cms.features.virtual_slideshow.pages.slides_index', compact('feature', 'page', 'slides'));
    }

    /**
     * Create new slide (legacy - for slides without page)
     */
    public function create(Feature $feature)
    {
        $feature->load('parent');
        $pages = $feature->slideshowPages()->orderBy('order')->get();
        return view('cms.features.virtual_slideshow.create', compact('feature', 'pages'));
    }

    /**
     * Store new slide (legacy)
     */
    public function store(Request $request, Feature $feature, TranslationService $translationService)
    {
        return $this->storeSlideData($request, $feature, null, $translationService);
    }

    /**
     * Create slide for specific page
     */
    public function createSlide(Feature $feature, $pageId)
    {
        $page = VirtualSlideshowPage::findOrFail($pageId);
        $feature->load('parent');

        $hasHeroSlide = VirtualSlideshowSlide::where('feature_page_id', $page->id)
            ->where('slide_type', 'hero')
            ->exists();

        $nextOrder = VirtualSlideshowSlide::where('feature_page_id', $page->id)->max('order') + 1;

        return view('cms.features.virtual_slideshow.pages.create', compact('feature', 'page', 'hasHeroSlide', 'nextOrder'));
    }

    /**
     * Store slide for specific page
     */
    public function storeSlide(Request $request, Feature $feature, $pageId, TranslationService $translationService)
    {
        $page = VirtualSlideshowPage::findOrFail($pageId);
        return $this->storeSlideData($request, $feature, $page, $translationService);
    }

    /**
     * Build caption value based on mode (single string or multi Q&A object)
     */
    private function buildCaptionValue(string $mode, ?string $singleCaption, ?array $qaItems): mixed
    {
        if ($mode === 'multi' && !empty($qaItems)) {
            $items = [];
            foreach ($qaItems as $qa) {
                $q = trim($qa['question'] ?? '');
                $a = trim($qa['answer'] ?? '');
                if ($q !== '' || $a !== '') {
                    $items[] = ['question' => $q, 'answer' => $a];
                }
            }
            if (!empty($items)) {
                return ['type' => 'multi', 'items' => $items];
            }
        }
        // Fall back to single caption
        return !empty($singleCaption) ? $singleCaption : null;
    }

    /**
     * Shared method to store slide data
     */
    private function storeSlideData(Request $request, Feature $feature, ?VirtualSlideshowPage $page, TranslationService $translationService)
    {
        $validated = $request->validate([
            'feature_page_id' => 'nullable|exists:virtual_slideshow_pages,id',
            'slide_type'  => 'required|in:hero,text,carousel,video,text_carousel',
            'carousel_media_type' => 'nullable|in:images,videos',
            'title'       => 'nullable|string|max:255',
            'subtitle'    => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'layout'      => 'required|in:left,right,center',
            'bg_color'   => 'nullable|string|max:20',
            'order'       => 'required|integer|min:0',
            'images'      => 'nullable|array',
            'images.*'    => 'image|mimes:jpg,jpeg,png,webp,gif',
            'image_urls'  => 'nullable|array',
            'image_urls.*'=> 'nullable|string',
            'carousel_videos' => 'nullable|array',
            'carousel_videos.*' => 'file',
            'carousel_video_urls' => 'nullable|array',
            'carousel_video_urls.*' => 'nullable|string',
            'video_url'   => 'nullable|string|max:500',
            'video_file' => 'nullable|file|mimes:mp4,webm,ogg',
            'info_popup_images'   => 'nullable|array',
            'info_popup_images.*' => 'nullable|string',
            'info_popup_new_images' => 'nullable|array',
            'info_popup_new_images.*' => 'nullable|string',
            'info_popup_carousel_videos' => 'nullable|array',
            'info_popup_carousel_videos.*' => 'nullable|string',
            'info_popup_video'    => 'nullable|string',
            'info_popup_video_url' => 'nullable|string',
            'info_popup_mode_images' => 'nullable|array',
            'info_popup_mode_images.*' => 'nullable|in:single,multi',
            'info_popup_qa_images' => 'nullable|array',
            'info_popup_mode_video' => 'nullable|in:single,multi',
            'info_popup_qa_video' => 'nullable|array',
            'info_popup_mode_video_url' => 'nullable|in:single,multi',
            'info_popup_qa_video_url' => 'nullable|array',
            'info_popup_mode_carousel_videos' => 'nullable|array',
            'info_popup_mode_carousel_videos.*' => 'nullable|in:single,multi',
            'info_popup_qa_carousel_videos' => 'nullable|array',
            'unified_video_order' => 'nullable',
        ]);

        // Determine which data to store based on slide type
        $slideType = $validated['slide_type'];

        // Server-side backup: prevent duplicate hero slides per page
        if ($slideType === 'hero' && $page) {
            $existingHero = VirtualSlideshowSlide::where('feature_page_id', $page->id)
                ->where('slide_type', 'hero')
                ->exists();
            if ($existingHero) {
                return back()->withErrors(['slide_type' => 'Halaman ini sudah memiliki slide Hero.'])->withInput();
            }
        }

        $imageTypes = ['hero', 'carousel'];
        $videoTypes = ['video'];
        $carouselVideoTypes = ['text_carousel'];
        // text_carousel can use either images or videos (based on carousel_media_type toggle)
        $textCarouselTypes = ['text_carousel'];
        $usesImagesForCarousel = $slideType === 'text_carousel' &&
                                  isset($validated['carousel_media_type']) &&
                                  $validated['carousel_media_type'] === 'images';

        $useImages = in_array($slideType, $imageTypes);
        $useVideo = in_array($slideType, $videoTypes);
        $useCarouselVideo = in_array($slideType, $carouselVideoTypes);
        $useCarouselImages = in_array($slideType, $carouselVideoTypes);

        // Add new uploads manually into an ordered list first
        $newUploadList = [];
        if (($useImages || $useCarouselImages) && $request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $newUploadList[] = $img->store('features/slideshow', 'public');
            }
        }

        // Process unified image order for editing and creating
        $unifiedImageOrder = [];
        $unifiedImageInput = $request->input('unified_image_order');
        if ($unifiedImageInput) {
            $decodedImageOrder = is_array($unifiedImageInput) ? $unifiedImageInput : json_decode($unifiedImageInput, true);
            $unifiedImageOrder = is_array($decodedImageOrder) ? $decodedImageOrder : [];
        }

        $orderedImageUrls = [];
        $orderedImagePaths = [];

        if (($useImages || $useCarouselImages)) {
            if (!empty($unifiedImageOrder)) {
                $newUploadsCopy = $newUploadList;
                foreach ($unifiedImageOrder as $item) {
                    $type = $item['type'] ?? null;
                    if ($type === 'newUpload') {
                        if (!empty($newUploadsCopy)) {
                            $orderedImagePaths[] = array_shift($newUploadsCopy);
                        }
                    } elseif ($type === 'url') {
                        $url = trim($item['urlValue'] ?? '');
                        if (!empty($url) && (str_starts_with($url, 'http://') || str_starts_with($url, 'https://'))) {
                            $orderedImageUrls[] = $url;
                        }
                    }
                }
            } else {
                // Fallback
                $orderedImagePaths = $newUploadList;
                if (!empty($validated['image_urls'])) {
                    $orderedImageUrls = array_values(array_filter(array_map('trim', $validated['image_urls']), function($url) {
                        return !empty($url);
                    }));
                }
            }
        }

        $imagePaths = $orderedImagePaths;
        $imageUrls = $orderedImageUrls;

        // Save original tracking for caption mappings, then generate final array for rendering
        $originalUnifiedImageOrder = $unifiedImageOrder;

        if (!empty($unifiedImageOrder)) {
            $normalizedImageOrder = [];
            $uploadSeq = 0;
            $urlSeq = 0;
            foreach ($originalUnifiedImageOrder as $item) {
                $type = $item['type'] ?? null;
                if ($type === 'existing' || $type === 'newUpload' || $type === 'upload') {
                    $normalizedImageOrder[] = [
                        'type' => 'upload',
                        'uploadIndex' => $uploadSeq
                    ];
                    $uploadSeq++;
                } elseif ($type === 'url' || $type === 'existingUrl') {
                    $normalizedImageOrder[] = [
                        'type' => 'url',
                        'urlIndex' => $urlSeq
                    ];
                    $urlSeq++;
                }
            }
            $unifiedImageOrder = $normalizedImageOrder;
        }

        // Upload carousel video files (only for text_carousel type)
        $carouselVideoPaths = [];
        if ($useCarouselVideo && $request->hasFile('carousel_videos')) {
            foreach ($request->file('carousel_videos') as $video) {
                $carouselVideoPaths[] = $video->store('features/slideshow/videos', 'public');
            }
        }

        // Get unified video order from form
        $unifiedOrder = [];
        $unifiedInput = $request->input('unified_video_order');
        if ($unifiedInput) {
            $decodedOrder = is_array($unifiedInput) ? $unifiedInput : json_decode($unifiedInput, true);
            $unifiedOrder = is_array($decodedOrder) ? $decodedOrder : [];
        }

        // Process carousel videos using unified order
        $orderedUrls = [];
        $orderedUploads = [];

        if ($useCarouselVideo) {
            if (!empty($unifiedOrder)) {
                // Process based on unified order
                foreach ($unifiedOrder as $item) {
                    $type = $item['type'] ?? null;

                    if ($type === 'url') {
                        $url = trim($item['urlValue'] ?? '');
                        if (!empty($url) && (str_starts_with($url, 'http://') || str_starts_with($url, 'https://'))) {
                            $orderedUrls[] = $url;
                        }
                    } elseif ($type === 'newUpload') {
                        // Use array_shift to take new uploads in the order they appear in unifiedOrder
                        if (!empty($carouselVideoPaths)) {
                            $orderedUploads[] = array_shift($carouselVideoPaths);
                        }
                    }
                }
            } else {
                // Fallback: use form order
                if (!empty($validated['carousel_video_urls'])) {
                    $orderedUrls = array_values(array_filter(array_map('trim', $validated['carousel_video_urls']), function($url) {
                        return !empty($url);
                    }));
                }
                $orderedUploads = $carouselVideoPaths;
            }
        }

        // Save original order for caption mapping (before normalization)
        $originalUnifiedOrder = $unifiedOrder;

        // Normalize unifiedOrder for store: convert newUpload entries to upload entries
        if ($useCarouselVideo && !empty($unifiedOrder)) {
            $normalizedOrder = [];
            $newIdx = 0;
            foreach ($unifiedOrder as $item) {
                $type = $item['type'] ?? null;
                if ($type === 'newUpload') {
                    $uploadPath = $orderedUploads[$newIdx] ?? null;
                    $normalizedOrder[] = [
                        'type' => 'upload',
                        'uploadPath' => $uploadPath,
                        'uploadIndex' => count(array_filter($normalizedOrder, fn($i) => $i['type'] === 'upload')),
                    ];
                    $newIdx++;
                } else {
                    $normalizedOrder[] = $item;
                }
            }
            $unifiedOrder = $normalizedOrder;
        }

        // Upload video file (only for video type)
        $videoFilePath = null;
        if ($useCarouselVideo && !empty($orderedUploads)) {
            // For text_carousel, store uploaded videos as JSON array in order
            $videoFilePath = json_encode(array_values($orderedUploads));
        } elseif ($useVideo && $request->hasFile('video_file')) {
            $videoFilePath = $request->file('video_file')->store('features/slideshow/videos', 'public');
        }

        // Get video URL (single video only) - enforce max 1 video
        $primaryVideoUrl = null;
        if ($useVideo) {
            $useVideoMethod = $request->input('video_method') === 'url';
            if ($useVideoMethod) {
                if (!empty($validated['video_url'])) {
                    $primaryVideoUrl = trim($validated['video_url']);
                }
                // URL method: clear video file
                if ($videoFilePath && !$useCarouselVideo) {
                    Storage::disk('public')->delete($videoFilePath);
                    $videoFilePath = null;
                }
            } else {
                // Upload method: ensure URL is null
                $primaryVideoUrl = null;
            }
        }

        // Build info_popup array
        $infoPopup = [];
        $captionModes = $validated['info_popup_mode_images'] ?? [];
        $captionQaData = $request->input('info_popup_qa_images', []);

        if (($useImages || $useCarouselImages)) {
            $captionToStorage = [];
            $urlStorageIdx = 0;
            $finalUnifiedIdx = 0;

            $jsNewUploadCount = count($newUploadList);

            if (!empty($unifiedImageOrder)) {
                $infoPopup['unified_image_order'] = $unifiedImageOrder;

                foreach ($originalUnifiedImageOrder as $item) {
                    $type = $item['type'] ?? null;
                    if ($type === 'newUpload') {
                        $idx = $item['newUploadIndex'] ?? 0;
                        $captionToStorage['newUploads_' . $idx] = $finalUnifiedIdx;
                        $finalUnifiedIdx++;
                    } elseif ($type === 'url') {
                        // URL elements in JS start at N + iteratedIdx
                        $jsBackendIdx = $jsNewUploadCount + $urlStorageIdx;
                        $captionToStorage['images_' . $jsBackendIdx] = $finalUnifiedIdx;
                        $urlStorageIdx++;
                        $finalUnifiedIdx++;
                    }
                }
            }

            // Process info_popup_images - maps to correct DB storage index
            if (!empty($validated['info_popup_images'])) {
                foreach ($validated['info_popup_images'] as $idx => $caption) {
                    $storageIdx = empty($unifiedImageOrder) ? $idx : ($captionToStorage['images_' . $idx] ?? null);
                    if ($storageIdx !== null) {
                        $mode = $captionModes[$idx] ?? 'single';
                        $qaItems = $captionQaData[$idx] ?? [];
                        $value = $this->buildCaptionValue($mode, $caption, $qaItems);
                        if ($value !== null) {
                            $infoPopup[(string)$storageIdx] = $value;
                        }
                    }
                }
            }

            // Process info_popup_new_images[] for new uploads
            if (!empty($validated['info_popup_new_images'])) {
                $newImageModes = $request->input('info_popup_mode_new_images', []);
                $newImageQaData = $request->input('info_popup_qa_new_images', []);
                foreach ($validated['info_popup_new_images'] as $idx => $caption) {
                    $storageIdx = empty($unifiedImageOrder) ? $idx : ($captionToStorage['newUploads_' . $idx] ?? null);
                    if ($storageIdx !== null) {
                        $mode = $newImageModes[$idx] ?? 'single';
                        $qaItems = $newImageQaData[$idx] ?? [];
                        $value = $this->buildCaptionValue($mode, $caption, $qaItems);
                        if ($value !== null) {
                            $infoPopup[(string)$storageIdx] = $value;
                        }
                    }
                }
            }
        }
        if ($useVideo) {
            $videoMode = $validated['info_popup_mode_video'] ?? 'single';
            $videoQaItems = $request->input('info_popup_qa_video', []);
            $videoValue = $this->buildCaptionValue($videoMode, $validated['info_popup_video'] ?? null, $videoQaItems);
            if ($videoValue !== null) {
                $infoPopup['video'] = $videoValue;
            }
            // Video URL caption
            $videoUrlMode = $validated['info_popup_mode_video_url'] ?? 'single';
            $videoUrlQaItems = $request->input('info_popup_qa_video_url', []);
            $videoUrlValue = $this->buildCaptionValue($videoUrlMode, $validated['info_popup_video_url'] ?? null, $videoUrlQaItems);
            if ($videoUrlValue !== null) {
                $infoPopup['video_url'] = $videoUrlValue;
            }
        }
        if ($useCarouselVideo) {
            // Store normalized order for reconstruction on load
            $infoPopup['carousel_video_order'] = $unifiedOrder;
            $infoPopup['carousel_media_type'] = $validated['carousel_media_type'] ?? 'images';

            if (!empty($validated['info_popup_carousel_videos'])) {
                $infoPopup['carousel_videos'] = [];

                // Build mapping using ORIGINAL order (before normalization)
                // because form sends keys like newUpload_X
                $captionToStorage = [];
                $urlStorageIdx = 0;
                $uploadStorageIdx = 0;

                foreach ($originalUnifiedOrder as $item) {
                    $type = $item['type'] ?? null;

                    if ($type === 'url') {
                        $urlIdx = $item['urlIndex'] ?? 0;
                        $captionToStorage['url_' . $urlIdx] = ['type' => 'url', 'storageIdx' => $urlStorageIdx];
                        $urlStorageIdx++;
                    } elseif ($type === 'newUpload') {
                        $newUploadIdx = $item['newUploadIndex'] ?? 0;
                        $captionToStorage['newUpload_' . $newUploadIdx] = ['type' => 'upload', 'storageIdx' => $uploadStorageIdx];
                        $uploadStorageIdx++;
                    }
                }

                // Process captions: remap newUpload_X keys to sequential upload_X keys
                $carouselVideoModes = $validated['info_popup_mode_carousel_videos'] ?? [];
                $carouselVideoQaData = $request->input('info_popup_qa_carousel_videos', []);

                foreach ($validated['info_popup_carousel_videos'] as $key => $caption) {
                    $mode = $carouselVideoModes[$key] ?? 'single';
                    $qaItems = $carouselVideoQaData[$key] ?? [];
                    $value = $this->buildCaptionValue($mode, $caption, $qaItems);
                    if ($value === null) continue;

                    if (str_starts_with($key, 'newUpload_')) {
                        $storageKey = 'upload_' . ($captionToStorage[$key]['storageIdx'] ?? 0);
                        $infoPopup['carousel_videos'][$storageKey] = $value;
                    } else {
                        $infoPopup['carousel_videos'][$key] = $value;
                    }
                }
            }
        }

        // Determine feature_page_id
        $featurePageId = $page ? $page->id : ($validated['feature_page_id'] ?? null);

        // Determine which media to store based on slide type
        $storeImages = ($useImages || $useCarouselImages) ? ($imagePaths ?: null) : null;
        $storeImageUrls = ($useImages || $useCarouselImages) ? ($imageUrls ?: null) : null;
        $storeCarouselVideos = $useCarouselVideo ? (!empty($orderedUploads) ? $orderedUploads : null) : null;
        $storeCarouselVideoUrls = $useCarouselVideo ? (!empty($orderedUrls) ? $orderedUrls : null) : null;

        // Hero: hanya boleh upload ATAU URL, tidak boleh keduanya
        if (($useImages || $useCarouselImages) && $slideType === 'hero') {
            $hasUploadedImages = !empty($imagePaths);
            $hasUrlImages = !empty($imageUrls);

            if ($hasUploadedImages && $hasUrlImages) {
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Hero hanya boleh memiliki 1 gambar. Pilih antara Upload File atau URL.'], 422);
                }
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['image_conflict' => 'Hero hanya boleh memiliki 1 gambar. Pilih antara Upload File atau URL, tidak boleh keduanya sekaligus.']);
            }
        }

        // Insert slide with order shifting (all slides at or after insert position shift up by 1)
        $this->insertAndShiftOrder(
            VirtualSlideshowSlide::class,
            (int) $validated['order'],
            ['feature_page_id' => $featurePageId],
            array_filter([
                'feature_id'           => $feature->id,
                'feature_page_id'      => $featurePageId,
                'slide_type'          => $validated['slide_type'],
                'title'               => $validated['title'] ?? null,
                'title_en'            => !empty($validated['title']) ? $translationService->translate($validated['title']) : null,
                'subtitle'            => $validated['subtitle'] ?? null,
                'subtitle_en'          => !empty($validated['subtitle']) ? $translationService->translate($validated['subtitle']) : null,
                'description'          => $validated['description'] ?? null,
                'description_en'       => !empty($validated['description']) ? $translationService->translate($validated['description']) : null,
                'images'              => $storeImages,
                'image_urls'          => $storeImageUrls,
                'video_url'           => $primaryVideoUrl,
                'video_file'          => $videoFilePath,
                'carousel_video_urls' => $storeCarouselVideoUrls,
                'layout'              => $validated['layout'],
                'bg_color'            => $validated['bg_color'] ?? null,
                'info_popup'          => $infoPopup ?: null,
            ], fn($v) => $v !== null)
        );

        // Redirect based on context
        if ($page) {
            return redirect()->route('cms.features.slideshow.pages.slides.index', [$feature, $page])
                ->with('success', __('cms.virtual_slideshow.flash.slide_created'));
        }

        return redirect()->route('cms.features.slideshow.index', $feature)
            ->with('success', __('cms.virtual_slideshow.flash.slide_created'));
    }

    /**
     * Edit slide (legacy)
     */
    public function edit(Feature $feature, VirtualSlideshowSlide $slide)
    {
        $feature->load('parent');
        $pages = $feature->pages()->orderBy('order')->get();
        return view('cms.features.virtual_slideshow.edit', compact('feature', 'slide', 'pages'));
    }

    /**
     * Update slide (legacy)
     */
    public function update(Request $request, Feature $feature, VirtualSlideshowSlide $slide, TranslationService $translationService)
    {
        return $this->updateSlideData($request, $feature, $slide, $translationService);
    }

    /**
     * Edit slide for specific page
     */
    public function editSlide(Feature $feature, $pageId, VirtualSlideshowSlide $slide)
    {
        $page = VirtualSlideshowPage::findOrFail($pageId);
        $feature->load('parent');
        $hasHeroSlide = VirtualSlideshowSlide::where('feature_page_id', $page->id)
            ->where('slide_type', 'hero')
            ->where('id', '!=', $slide->id)
            ->exists();
        return view('cms.features.virtual_slideshow.pages.edit', compact('feature', 'page', 'slide', 'hasHeroSlide'));
    }

    /**
     * Update slide for specific page
     */
    public function updateSlide(Request $request, Feature $feature, $pageId, VirtualSlideshowSlide $slide, TranslationService $translationService)
    {
        $page = VirtualSlideshowPage::findOrFail($pageId);
        return $this->updateSlideData($request, $feature, $slide, $translationService, $page);
    }

     /**
     * Shared method to update slide data
     */
    private function updateSlideData(Request $request, Feature $feature, VirtualSlideshowSlide $slide, TranslationService $translationService, ?VirtualSlideshowPage $page = null)
    {
        $validated = $request->validate([
            'feature_page_id' => 'nullable|exists:virtual_slideshow_pages,id',
            'slide_type'  => 'required|in:hero,text,carousel,video,text_carousel',
            'title'       => 'nullable|string|max:255',
            'subtitle'    => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'layout'      => 'required|in:left,right,center',
            'bg_color'   => 'nullable|string|max:20',
            'order'       => 'required|integer|min:0',
            'images'      => 'nullable|array',
            'images.*'    => 'nullable|file',
            'existing_images'     => 'nullable|array',
            'existing_images.*'   => 'string',
            'deleted_existing_images' => 'nullable|array',
            'deleted_existing_images.*' => 'string',
            'image_urls'  => 'nullable|array',
            'image_urls.*'=> 'nullable|string',
            'new_image_urls' => 'nullable|array',
            'new_image_urls.*' => 'nullable|string',
            'carousel_videos' => 'nullable|array',
            'carousel_videos.*' => 'file',
            'carousel_video_urls' => 'nullable|array',
            'carousel_video_urls.*' => 'nullable|string',
            'existing_carousel_videos' => 'nullable',
            'unified_video_order' => 'nullable',
            'carousel_media_type' => 'nullable|in:images,videos',
            'video_method' => 'nullable|in:url,upload',
            'video_url'   => 'nullable|string|max:500',
            'video_file' => 'nullable|file|mimes:mp4,webm,ogg',
            'delete_existing_video' => 'nullable|in:0,1',
            'clear_existing_url' => 'nullable|in:0,1',
            'info_popup_images'   => 'nullable|array',
            'info_popup_images.*' => 'nullable|string',
            'info_popup_new_images' => 'nullable|array',
            'info_popup_new_images.*' => 'nullable|string',
            'info_popup_existing_urls' => 'nullable|array',
            'info_popup_existing_urls.*' => 'nullable|string',
            'existing_image_urls' => 'nullable|array',
            'existing_image_urls.*' => 'nullable|string',
            'deleted_existing_image_urls' => 'nullable|array',
            'deleted_existing_image_urls.*' => 'string',
            'info_popup_mode_existing_urls' => 'nullable|array',
            'info_popup_mode_existing_urls.*' => 'nullable|in:single,multi',
            'info_popup_qa_existing_urls' => 'nullable|array',
            'info_popup_carousel_videos' => 'nullable|array',
            'info_popup_carousel_videos.*' => 'nullable|string',
            'info_popup_video'    => 'nullable|string',
            'info_popup_video_url' => 'nullable|string',
            'info_popup_mode_images' => 'nullable|array',
            'info_popup_mode_images.*' => 'nullable|in:single,multi',
            'info_popup_qa_images' => 'nullable|array',
            'info_popup_mode_video' => 'nullable|in:single,multi',
            'info_popup_qa_video' => 'nullable|array',
            'info_popup_mode_video_url' => 'nullable|in:single,multi',
            'info_popup_qa_video_url' => 'nullable|array',
            'info_popup_mode_carousel_videos' => 'nullable|array',
            'info_popup_mode_carousel_videos.*' => 'nullable|in:single,multi',
            'info_popup_qa_carousel_videos' => 'nullable|array',
        ]);

        // Server-side: prevent duplicate hero slides per page
        $slideType = $validated['slide_type'];
        if ($slideType === 'hero' && $page) {
            $existingHero = VirtualSlideshowSlide::where('feature_page_id', $page->id)
                ->where('slide_type', 'hero')
                ->where('id', '!=', $slide->id)
                ->exists();
            if ($existingHero) {
                return back()->withErrors(['slide_type' => 'Halaman ini sudah memiliki slide Hero.'])->withInput();
            }
        }

        // Determine which data to keep based on slide type
        $imageTypes = ['hero', 'carousel'];
        $videoTypes = ['video'];
        $carouselVideoTypes = ['text_carousel'];
        // text_carousel can use either images or videos (based on carousel_media_type toggle)
        $usesImagesForCarousel = $slideType === 'text_carousel' &&
                                  isset($validated['carousel_media_type']) &&
                                  $validated['carousel_media_type'] === 'images';

        $useImages = in_array($slideType, $imageTypes);
        $useVideo = in_array($slideType, $videoTypes);
        $useCarouselVideo = in_array($slideType, $carouselVideoTypes);
        $useCarouselImages = in_array($slideType, $carouselVideoTypes);

        // Get existing images from form (only enabled inputs will be submitted)
        // Preserve original indices so unified_image_order existingIndex references remain valid
        $existingImages = [];
        if (($useImages || $useCarouselImages) && !empty($validated['existing_images'])) {
            foreach ($validated['existing_images'] as $idx => $path) {
                if (!empty($path)) {
                    $existingImages[$idx] = $path;
                }
            }
        }

        // Delete removed images from storage
        $oldImages = $slide->images ?? [];
        $deletedImages = $validated['deleted_existing_images'] ?? [];

        if (!$useImages && !$useCarouselImages) {
            // Non-image type: delete all old images from storage
            foreach ($oldImages as $old) {
                Storage::disk('public')->delete($old);
            }
            $existingImages = [];
        } else {
            // Image type: delete images that were removed
            foreach ($oldImages as $old) {
                if (!in_array($old, $existingImages) && !in_array($old, $deletedImages)) {
                    Storage::disk('public')->delete($old);
                }
            }
        }

        // Add new uploads manually into an ordered list first
        $newUploadList = [];
        if (($useImages || $useCarouselImages) && $request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $newUploadList[] = $img->store('features/slideshow', 'public');
            }
        }

        $existingImageUrls = [];
        if ($useImages || $useCarouselImages) {
            $deletedUrls = $validated['deleted_existing_image_urls'] ?? [];
            if (!empty($validated['existing_image_urls'])) {
                foreach ($validated['existing_image_urls'] as $idx => $url) {
                    if (!empty($url) && !in_array((string)$idx, $deletedUrls)) {
                        $existingImageUrls[$idx] = $url;
                    }
                }
            }
        }

        // Process unified image order for editing and creating
        $unifiedImageOrder = [];
        $unifiedImageInput = $request->input('unified_image_order');
        if ($unifiedImageInput) {
            $decodedImageOrder = is_array($unifiedImageInput) ? $unifiedImageInput : json_decode($unifiedImageInput, true);
            $unifiedImageOrder = is_array($decodedImageOrder) ? $decodedImageOrder : [];
        }

        $orderedImageUrls = [];
        $orderedImagePaths = [];

        if (($useImages || $useCarouselImages)) {
            if (!empty($unifiedImageOrder)) {
                $newUploadsCopy = $newUploadList;
                foreach ($unifiedImageOrder as $item) {
                    $type = $item['type'] ?? null;
                    if ($type === 'existing') {
                        $idx = $item['existingIndex'] ?? 0;
                        if (isset($existingImages[$idx])) {
                            $orderedImagePaths[] = $existingImages[$idx];
                        }
                    } elseif ($type === 'newUpload') {
                        if (!empty($newUploadsCopy)) {
                            $orderedImagePaths[] = array_shift($newUploadsCopy);
                        }
                    } elseif ($type === 'existingUrl') {
                        $idx = $item['existingUrlIndex'] ?? 0;
                        if (isset($existingImageUrls[$idx])) {
                            $orderedImageUrls[] = $existingImageUrls[$idx];
                        }
                    } elseif ($type === 'url') {
                        $url = trim($item['urlValue'] ?? '');
                        if (!empty($url) && (str_starts_with($url, 'http://') || str_starts_with($url, 'https://'))) {
                            $orderedImageUrls[] = $url;
                        }
                    }
                }
            } else {
                // Fallback if no JS tracking: keep existing first, then new uploads, then URLs
                $orderedImagePaths = array_values($existingImages);
                foreach ($newUploadList as $f) {
                    $orderedImagePaths[] = $f;
                }
                // existing_image_urls are already kept via $existingImageUrls
                foreach (array_values($existingImageUrls) as $u) {
                    $orderedImageUrls[] = $u;
                }
                // new_image_urls from the "add new" section
                if (!empty($validated['new_image_urls'])) {
                    foreach (array_filter(array_map('trim', $validated['new_image_urls'])) as $u) {
                        if (!empty($u)) $orderedImageUrls[] = $u;
                    }
                }
            }
        }

        $imagePaths = $orderedImagePaths;
        $imageUrls = $orderedImageUrls;

        // Save original tracking for caption mappings, then generate final array for rendering
        $originalUnifiedImageOrder = $unifiedImageOrder;

        if (!empty($unifiedImageOrder)) {
            $normalizedImageOrder = [];
            $uploadSeq = 0;
            $urlSeq = 0;
            foreach ($originalUnifiedImageOrder as $item) {
                $type = $item['type'] ?? null;
                if ($type === 'existing' || $type === 'newUpload' || $type === 'upload') {
                    $normalizedImageOrder[] = [
                        'type' => 'upload',
                        'uploadIndex' => $uploadSeq
                    ];
                    $uploadSeq++;
                } elseif ($type === 'url' || $type === 'existingUrl') {
                    $normalizedImageOrder[] = [
                        'type' => 'url',
                        'urlIndex' => $urlSeq
                    ];
                    $urlSeq++;
                }
            }
            $unifiedImageOrder = $normalizedImageOrder;
        }

        // Handle carousel videos (for text_carousel type)
        $carouselVideoPaths = [];
        if ($useCarouselVideo && $request->hasFile('carousel_videos')) {
            foreach ($request->file('carousel_videos') as $video) {
                $carouselVideoPaths[] = $video->store('features/slideshow/videos', 'public');
            }
        }

        // Handle video file upload/delete for carousel videos
        $videoFilePath = $slide->video_file;
        $carouselVideoUrls = [];
        if ($useCarouselVideo) {
            // Get existing carousel videos from database
            $existingCarouselVideos = [];
            $oldVf = $slide->video_file;
            if ($oldVf) {
                if (is_array($oldVf)) {
                    $existingCarouselVideos = $oldVf;
                } elseif (is_string($oldVf) && str_starts_with($oldVf, '[')) {
                    $decoded = json_decode($oldVf, true);
                    $existingCarouselVideos = is_array($decoded) ? $decoded : [];
                }
            }

            // Get kept carousel videos from form
            $keptVideos = [];
            $existingInput = $request->input('existing_carousel_videos');
            if ($existingInput) {
                $decodedExisting = is_array($existingInput) ? $existingInput : json_decode($existingInput, true);
                $keptVideos = is_array($decodedExisting) ? $decodedExisting : [];
            }

            // Delete videos that are no longer in the kept list
            foreach ($existingCarouselVideos as $oldVideo) {
                if (!in_array($oldVideo, $keptVideos)) {
                    Storage::disk('public')->delete($oldVideo);
                }
            }

            // Get unified video order from form
            $unifiedOrder = [];
            $unifiedInput = $request->input('unified_video_order');
            if ($unifiedInput) {
                $decodedOrder = is_array($unifiedInput) ? $unifiedInput : json_decode($unifiedInput, true);
                $unifiedOrder = is_array($decodedOrder) ? $decodedOrder : [];
            }

            // Build ordered URLs and uploads based on unified order
            $orderedUrls = [];
            $orderedUploads = [];

            if (!empty($unifiedOrder)) {
                foreach ($unifiedOrder as $item) {
                    $type = $item['type'] ?? null;

                    if ($type === 'url') {
                        $url = trim($item['urlValue'] ?? '');
                        if (!empty($url) && (str_starts_with($url, 'http://') || str_starts_with($url, 'https://'))) {
                            $orderedUrls[] = $url;
                        }
                    } elseif ($type === 'upload') {
                        $uploadPath = $item['uploadPath'] ?? '';
                        if (!empty($uploadPath)) {
                            $orderedUploads[] = $uploadPath;
                        }
                    } elseif ($type === 'newUpload') {
                        // Use array_shift to take new uploads in the order they appear in unifiedOrder
                        // This ensures the new upload is placed at the correct position in orderedUploads
                        if (!empty($carouselVideoPaths)) {
                            $orderedUploads[] = array_shift($carouselVideoPaths);
                        }
                    }
                }
            } else {
                // Fallback: get URLs from form directly (carousel_video_urls[])
                if (!empty($validated['carousel_video_urls'])) {
                    $orderedUrls = array_values(array_filter(array_map('trim', $validated['carousel_video_urls']), function($url) {
                        return !empty($url);
                    }));
                }
                // Fallback: use all carouselVideoPaths
                $orderedUploads = $carouselVideoPaths;
            }

            // Set carouselVideoUrls from ordered URLs
            $carouselVideoUrls = $orderedUrls;

            // Save original order for caption mapping (before normalization)
            $originalUnifiedOrder = $unifiedOrder;

            // Normalize unifiedOrder: convert all newUpload entries to upload entries with resolved paths
            $normalizedOrder = [];
            $newUploadIdx = 0;
            foreach ($unifiedOrder as $item) {
                $type = $item['type'] ?? null;
                if ($type === 'newUpload') {
                    // Find the path that was stored for this new upload (orderedUploads contains both existing and new in order)
                    // We need to find the new upload path by counting newUploads seen so far
                    $uploadPath = null;
                    $newCount = 0;
                    foreach ($orderedUploads as $path) {
                        // Check if this path is from keptVideos (existing) or new
                        if (!in_array($path, $keptVideos)) {
                            if ($newCount === $newUploadIdx) {
                                $uploadPath = $path;
                                break;
                            }
                            $newCount++;
                        }
                    }
                    $normalizedOrder[] = [
                        'type' => 'upload',
                        'uploadPath' => $uploadPath,
                        'uploadIndex' => count(array_filter($normalizedOrder, fn($i) => $i['type'] === 'upload')),
                    ];
                    $newUploadIdx++;
                } else {
                    $normalizedOrder[] = $item;
                }
            }
            $unifiedOrder = $normalizedOrder;

            // Store uploads in video_file - ordered uploads (existing + new)
            $videoFilePath = !empty($orderedUploads) ? json_encode(array_values($orderedUploads)) : null;
        } elseif (!$useVideo) {
            // Clear video data if slide type doesn't use video
            if ($slide->video_file) {
                // Check if it's a JSON array (carousel videos)
                $existingFiles = $slide->video_file;
                if (is_string($existingFiles) && str_starts_with($existingFiles, '[')) {
                    $decoded = json_decode($existingFiles, true);
                    if (is_array($decoded)) {
                        foreach ($decoded as $oldFile) {
                            Storage::disk('public')->delete($oldFile);
                        }
                    }
                } else {
                    Storage::disk('public')->delete($existingFiles);
                }
                $videoFilePath = null;
            }
        } else {
            // Slide type uses video (single video)
            if ($request->input('delete_existing_video') === '1') {
                // Delete existing video file if user chose to delete
                if ($slide->video_file) {
                    Storage::disk('public')->delete($slide->video_file);
                    $videoFilePath = null;
                }
            } elseif ($request->hasFile('video_file')) {
                // Delete old video file if exists, then upload new one
                if ($slide->video_file) {
                    Storage::disk('public')->delete($slide->video_file);
                }
                $videoFilePath = $request->file('video_file')->store('features/slideshow/videos', 'public');
            }
        }

        // Determine primary video URL based on selected method
        // For video type: only one method (URL or Upload) is allowed, not both
        $primaryVideoUrl = null;
        if ($useVideo) {
            $useVideoMethod = $request->input('video_method') === 'url';
            $clearExistingUrl = $request->input('clear_existing_url') === '1';

            if ($useVideoMethod) {
                // Using URL method - get video URL (single video only)
                if (!empty($validated['video_url'])) {
                    $primaryVideoUrl = trim($validated['video_url']);
                }
                // Clear existing URL if user chose to delete it
                if ($clearExistingUrl) {
                    $primaryVideoUrl = null;
                }
                // URL method selected: clear any video file (enforce max 1 video)
                if ($videoFilePath && !$useCarouselVideo) {
                    Storage::disk('public')->delete($videoFilePath);
                    $videoFilePath = null;
                }
            } else {
                // Upload method selected: clear any video URL (enforce max 1 video)
                $primaryVideoUrl = null;
            }
        } else {
            // Non-video type: clear any existing video_url from database
            $primaryVideoUrl = null;
        }

        // Build info_popup - only include info if there's corresponding content
        $infoPopup = [];
        $captionModes = $validated['info_popup_mode_images'] ?? [];
        $captionQaData = $request->input('info_popup_qa_images', []);

        if (($useImages || $useCarouselImages)) {
            // Provide info_popup sequential mapping for info popups
            $captionToStorage = [];
            $urlStorageIdx = 0;
            $finalUnifiedIdx = 0;

            $jsExistingCount = count($existingImages);
            $jsNewUploadCount = count($newUploadList);

            if (!empty($unifiedImageOrder)) {
                $infoPopup['unified_image_order'] = $unifiedImageOrder;

                foreach ($originalUnifiedImageOrder as $item) {
                    $type = $item['type'] ?? null;
                    if ($type === 'existing') {
                        $idx = $item['existingIndex'] ?? 0;
                        $captionToStorage['images_' . $idx] = $finalUnifiedIdx;
                        $finalUnifiedIdx++;
                    } elseif ($type === 'newUpload') {
                        $idx = $item['newUploadIndex'] ?? 0;
                        $captionToStorage['newUploads_' . $idx] = $finalUnifiedIdx;
                        $finalUnifiedIdx++;
                    } elseif ($type === 'url') {
                        // URL elements in JS start at E + N + iteratedIdx
                        $jsBackendIdx = $jsExistingCount + $jsNewUploadCount + $urlStorageIdx;
                        $captionToStorage['images_' . $jsBackendIdx] = $finalUnifiedIdx;
                        $urlStorageIdx++;
                        $finalUnifiedIdx++;
                    } elseif ($type === 'existingUrl') {
                        $idx = $item['existingUrlIndex'] ?? 0;
                        $captionToStorage['existingUrl_' . $idx] = $finalUnifiedIdx;
                        $finalUnifiedIdx++;
                    }
                }
            }

            // Process info_popup_images - maps to correct unified DB storage index
            if (!empty($validated['info_popup_images'])) {
                foreach ($validated['info_popup_images'] as $idx => $caption) {
                    // Check if mapping exists, fallback to numeric $idx if direct
                    $storageIdx = empty($unifiedImageOrder) ? $idx : ($captionToStorage['images_' . $idx] ?? null);
                    if ($storageIdx !== null) {
                        $mode = $captionModes[$idx] ?? 'single';
                        $qaItems = $captionQaData[$idx] ?? [];
                        $value = $this->buildCaptionValue($mode, $caption, $qaItems);
                        if ($value !== null) {
                            $infoPopup[(string)$storageIdx] = $value;
                        }
                    }
                }
            }

            // Process info_popup_new_images[] for new uploads
            if (!empty($validated['info_popup_new_images'])) {
                $newImageModes = $request->input('info_popup_mode_new_images', []);
                $newImageQaData = $request->input('info_popup_qa_new_images', []);
                foreach ($validated['info_popup_new_images'] as $idx => $caption) {
                    // Mapping fallback: $jsExistingCount + $idx if no unified sequence
                    $storageIdx = empty($unifiedImageOrder) ? ($jsExistingCount + $idx) : ($captionToStorage['newUploads_' . $idx] ?? null);
                    if ($storageIdx !== null) {
                        $mode = $newImageModes[$idx] ?? 'single';
                        $qaItems = $newImageQaData[$idx] ?? [];
                        $value = $this->buildCaptionValue($mode, $caption, $qaItems);
                        if ($value !== null) {
                            $infoPopup[(string)$storageIdx] = $value;
                        }
                    }
                }
            }

            // Process info_popup_existing_urls[] for saved URL images
            if (!empty($validated['info_popup_existing_urls'])) {
                $existingUrlModes = $request->input('info_popup_mode_existing_urls', []);
                $existingUrlQaData = $request->input('info_popup_qa_existing_urls', []);
                // Count remaining existing image uploads (those not deleted)
                $remainingUploadCount = count($existingImages);
                foreach ($validated['info_popup_existing_urls'] as $urlIdx => $caption) {
                    // Use unified order mapping if available, fallback to sequential position
                    $storageIdx = !empty($unifiedImageOrder)
                        ? ($captionToStorage['existingUrl_' . $urlIdx] ?? ($remainingUploadCount + $urlIdx))
                        : ($remainingUploadCount + $urlIdx);
                    $mode = $existingUrlModes[$urlIdx] ?? 'single';
                    $qaItems = $existingUrlQaData[$urlIdx] ?? [];
                    $value = $this->buildCaptionValue($mode, $caption, $qaItems);
                    if ($value !== null) {
                        $infoPopup[(string)$storageIdx] = $value;
                    }
                }
            }
        }
        $hasVideo = !empty($primaryVideoUrl) || !empty($videoFilePath);
        if ($useVideo) {
            // Upload video caption (only when upload method is used or existing file)
            if ($hasVideo && empty($primaryVideoUrl)) {
                $videoMode = $validated['info_popup_mode_video'] ?? 'single';
                $videoQaItems = $request->input('info_popup_qa_video', []);
                $videoValue = $this->buildCaptionValue($videoMode, $validated['info_popup_video'] ?? null, $videoQaItems);
                if ($videoValue !== null) {
                    $infoPopup['video'] = $videoValue;
                }
            }
            // URL video caption (always when URL method is used)
            $useVideoUrlMethod = $request->input('video_method') === 'url';
            if ($useVideoUrlMethod && (!empty($primaryVideoUrl) || !empty($validated['video_url']))) {
                $videoUrlMode = $validated['info_popup_mode_video_url'] ?? 'single';
                $videoUrlQaItems = $request->input('info_popup_qa_video_url', []);
                $videoUrlValue = $this->buildCaptionValue($videoUrlMode, $validated['info_popup_video_url'] ?? null, $videoUrlQaItems);
                if ($videoUrlValue !== null) {
                    $infoPopup['video_url'] = $videoUrlValue;
                }
            }
        }
        if ($useCarouselVideo) {
            // Always store the normalized order for proper reconstruction on load
            $infoPopup['carousel_video_order'] = $unifiedOrder;
            $infoPopup['carousel_media_type'] = $validated['carousel_media_type'] ?? 'images';

            if (!empty($validated['info_popup_carousel_videos'])) {
                $infoPopup['carousel_videos'] = [];

                // Build mapping from caption key to storage info using ORIGINAL order (before normalization)
                // because form sends keys like newUpload_X which don't exist in normalized order
                $captionToStorage = [];
                $urlStorageIdx = 0;
                $uploadStorageIdx = 0;

                foreach ($originalUnifiedOrder as $item) {
                    $type = $item['type'] ?? null;

                    if ($type === 'url') {
                        $urlIdx = $item['urlIndex'] ?? 0;
                        $captionToStorage['url_' . $urlIdx] = ['type' => 'url', 'storageIdx' => $urlStorageIdx];
                        $urlStorageIdx++;
                    } elseif ($type === 'upload') {
                        $uploadIdx = $item['uploadIndex'] ?? 0;
                        $captionToStorage['upload_' . $uploadIdx] = ['type' => 'upload', 'storageIdx' => $uploadStorageIdx];
                        $uploadStorageIdx++;
                    } elseif ($type === 'newUpload') {
                        $newUploadIdx = $item['newUploadIndex'] ?? 0;
                        $captionToStorage['newUpload_' . $newUploadIdx] = ['type' => 'upload', 'storageIdx' => $uploadStorageIdx];
                        $uploadStorageIdx++;
                    }
                }

                // Process captions: remap newUpload_X keys to sequential upload_X keys
                $carouselVideoModes = $validated['info_popup_mode_carousel_videos'] ?? [];
                $carouselVideoQaData = $request->input('info_popup_qa_carousel_videos', []);

                foreach ($validated['info_popup_carousel_videos'] as $key => $caption) {
                    $mode = $carouselVideoModes[$key] ?? 'single';
                    $qaItems = $carouselVideoQaData[$key] ?? [];
                    $value = $this->buildCaptionValue($mode, $caption, $qaItems);
                    if ($value === null) continue;

                    if (str_starts_with($key, 'newUpload_')) {
                        $storageKey = 'upload_' . ($captionToStorage[$key]['storageIdx'] ?? 0);
                        $infoPopup['carousel_videos'][$storageKey] = $value;
                    } else {
                        $infoPopup['carousel_videos'][$key] = $value;
                    }
                }
            }
        }

        // Determine feature_page_id
        $featurePageId = $page ? $page->id : ($validated['feature_page_id'] ?? null);

        // Hero: hanya boleh upload ATAU URL, tidak boleh keduanya
        if (($useImages || $useCarouselImages) && $slideType === 'hero') {
            $hasUploadedImages = !empty($imagePaths);
            $hasUrlImages = !empty($imageUrls);

            if ($hasUploadedImages && $hasUrlImages) {
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Hero hanya boleh memiliki 1 gambar. Pilih antara Upload File atau URL.'], 422);
                }
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['image_conflict' => 'Hero hanya boleh memiliki 1 gambar. Pilih antara Upload File atau URL, tidak boleh keduanya sekaligus.']);
            }
        }

        $this->swapOrder($slide, (int) $validated['order'], (int) $slide->order, ['feature_page_id' => $slide->feature_page_id]);
        $slide->update([
            'feature_page_id' => $featurePageId,
            'slide_type'     => $validated['slide_type'],
            'title'          => $validated['title'] ?? null,
            'title_en'       => !empty($validated['title']) ? $translationService->translate($validated['title']) : null,
            'subtitle'       => $validated['subtitle'] ?? null,
            'subtitle_en'    => !empty($validated['subtitle']) ? $translationService->translate($validated['subtitle']) : null,
            'description'    => $validated['description'] ?? null,
            'description_en' => !empty($validated['description']) ? $translationService->translate($validated['description']) : null,
            'images'         => ($useImages || $useCarouselImages) ? ($imagePaths ?: null) : null,
            'image_urls'     => ($useImages || $useCarouselImages) ? ($imageUrls ?: null) : null,
            'carousel_video_urls' => $useCarouselVideo ? ($carouselVideoUrls ?: null) : null,
            'video_url'      => $useVideo ? $primaryVideoUrl : null,
            'video_file'     => ($useVideo || $useCarouselVideo) ? $videoFilePath : null,
            'layout'         => $validated['layout'],
            'bg_color'      => $validated['bg_color'] ?? null,
            'info_popup'    => $infoPopup ?: null,
            'order'          => $validated['order'],
        ]);

        // Redirect based on context
        if ($page) {
            return redirect()->route('cms.features.slideshow.pages.slides.index', [$feature, $page])
                ->with('success', __('cms.virtual_slideshow.flash.slide_updated'));
        }

        return redirect()->route('cms.features.slideshow.index', $feature)
            ->with('success', __('cms.virtual_slideshow.flash.slide_updated'));
    }

    /**
     * Destroy slide (legacy)
     */
    public function destroy(Feature $feature, VirtualSlideshowSlide $slide)
    {
        return $this->destroySlideData($feature, $slide);
    }

    /**
     * Destroy slide for specific page
     */
    public function destroySlide(Feature $feature, $pageId, VirtualSlideshowSlide $slide)
    {
        $page = VirtualSlideshowPage::findOrFail($pageId);
        return $this->destroySlideData($feature, $slide, $page);
    }

    /**
     * Shared method to destroy slide
     */
    private function destroySlideData(Feature $feature, VirtualSlideshowSlide $slide, ?VirtualSlideshowPage $page = null)
    {
        // Delete images from storage
        if ($slide->images) {
            foreach ($slide->images as $img) {
                Storage::disk('public')->delete($img);
            }
        }

        // Delete video file from storage
        if ($slide->video_file) {
            Storage::disk('public')->delete($slide->video_file);
        }

        $this->deleteAndShiftOrder($slide, ['feature_page_id' => $slide->feature_page_id]);

        // Redirect based on context
        if ($page) {
            return redirect()->route('cms.features.slideshow.pages.slides.index', [$feature, $page])
                ->with('success', __('cms.virtual_slideshow.flash.slide_deleted'));
        }

        return redirect()->route('cms.features.slideshow.index', $feature)
            ->with('success', __('cms.virtual_slideshow.flash.slide_deleted'));
    }

    /**
     * Toggle visibility (is_active) of a slideshow page.
     */
    public function togglePageVisibility(Feature $feature, $pageId)
    {
        $page = VirtualSlideshowPage::findOrFail($pageId);
        $page->update(['is_active' => !$page->is_active]);

        $msg = $page->is_active
            ? __('cms.common.flash.shown', ['name' => $page->title])
            : __('cms.common.flash.hidden', ['name' => $page->title]);

        return redirect()->back()->with('success', $msg);
    }
}
