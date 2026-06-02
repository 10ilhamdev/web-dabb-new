<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use App\Models\Publication;
use App\Services\TranslationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PublicationController extends Controller
{
    use \App\Traits\SwapsOrder;

    public function __construct(private TranslationService $translationService)
    {}

    /**
     * Show the publication list for a sub-feature.
     */
    public function index(Feature $feature)
    {
        $feature->load('parent');
        $pages = $feature->publications()
            ->orderBy('order')
            ->get();

        return view('cms.features.publication.index', compact('feature', 'pages'));
    }

    /**
     * Show the form for creating a new publication page.
     */
    public function create(Feature $feature)
    {
        $feature->load('parent');
        $nextOrder = $feature->publications()->max('order') + 1;
        return view('cms.features.publication.create', compact('feature', 'nextOrder'));
    }

    /**
     * Store a new publication page.
     */
    public function store(Request $request, Feature $feature)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string|in:pengumuman,berita,galeri',
            'description' => 'nullable|string',
            'subtitle' => 'nullable|string|max:255',
            'link_text' => 'nullable|string|max:255',
            'link_url' => 'nullable|string|max:500',
            'published_at' => 'nullable|date',
            'order' => 'required|integer|min:0',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'extra_data' => 'nullable|array',
        ]);

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('features/publication', 'public');
            }
        }

        $extraData = $validated['extra_data'] ?? [];
        if ($request->hasFile('extra_data.file')) {
            $extraData['file'] = $request->file('extra_data.file')->store('features/publication/files', 'public');
        }

        $data = [
            'feature_id' => $feature->id,
            'title' => $validated['title'],
            'title_en' => $this->translationService->translate($validated['title']),
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
            'description_en' => ! empty($validated['description']) ? $this->translationService->translate($validated['description']) : null,
            'subtitle' => $validated['subtitle'] ?? null,
            'subtitle_en' => ! empty($validated['subtitle']) ? $this->translationService->translate($validated['subtitle']) : null,
            'link_text' => $validated['link_text'] ?? null,
            'link_url' => $validated['link_url'] ?? null,
            'images' => $imagePaths ?: null,
            'published_at' => $validated['published_at'] ?? now(),
            'order' => $validated['order'],
            'extra_data' => !empty($extraData) ? $extraData : null,
        ];

        $insertOrder = (int) $validated['order'];
        $this->insertAndShiftOrder(Publication::class, $insertOrder, ['feature_id' => $feature->id], $data);

        return redirect()->route('cms.features.publication.index', $feature)
            ->with('success', __('cms.publication.flash.added'));
    }

    /**
     * Show the form for editing a publication page.
     */
    public function edit(Feature $feature, Publication $publication)
    {
        $feature->load('parent');
        return view('cms.features.publication.edit', compact('feature', 'publication'));
    }

    /**
     * Update a publication page.
     */
    public function update(Request $request, Feature $feature, Publication $publication)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string|in:pengumuman,berita,galeri',
            'description' => 'nullable|string',
            'subtitle' => 'nullable|string|max:255',
            'link_text' => 'nullable|string|max:255',
            'link_url' => 'nullable|string|max:500',
            'published_at' => 'nullable|date',
            'order' => 'required|integer|min:0',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'existing_images' => 'nullable|array',
            'extra_data' => 'nullable|array',
        ]);

        $existingImages = $validated['existing_images'] ?? [];
        $oldImages = $publication->images ?? [];
        foreach ($oldImages as $oldImage) {
            if (! in_array($oldImage, $existingImages)) {
                Storage::disk('public')->delete($oldImage);
            }
        }

        $imagePaths = $existingImages;
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('features/publication', 'public');
            }
        }

        $extraData = $validated['extra_data'] ?? [];
        if ($request->hasFile('extra_data.file')) {
            if (isset($publication->extra_data['file'])) {
                Storage::disk('public')->delete($publication->extra_data['file']);
            }
            $extraData['file'] = $request->file('extra_data.file')->store('features/publication/files', 'public');
        } elseif (isset($publication->extra_data['file'])) {
            $extraData['file'] = $publication->extra_data['file'];
        }

        $data = [
            'title' => $validated['title'],
            'title_en' => $this->translationService->translate($validated['title']),
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
            'description_en' => ! empty($validated['description']) ? $this->translationService->translate($validated['description']) : null,
            'subtitle' => $validated['subtitle'] ?? null,
            'subtitle_en' => ! empty($validated['subtitle']) ? $this->translationService->translate($validated['subtitle']) : null,
            'link_text' => $validated['link_text'] ?? null,
            'link_url' => $validated['link_url'] ?? null,
            'published_at' => $validated['published_at'] ?? $publication->published_at,
            'images' => $imagePaths ?: null,
            'extra_data' => !empty($extraData) ? $extraData : null,
        ];

        $this->swapOrder($publication, (int) $validated['order'], (int) $publication->order, ['feature_id' => $feature->id]);
        $publication->update($data);

        return redirect()->route('cms.features.publication.index', $feature)
            ->with('success', __('cms.publication.flash.updated'));
    }

    /**
     * Delete a publication page.
     */
    public function destroy(Feature $feature, Publication $publication)
    {
        $this->deleteAndShiftOrder($publication, ['feature_id' => $feature->id]);

        return redirect()->route('cms.features.publication.index', $feature)
            ->with('success', __('cms.publication.flash.deleted'));
    }
}
