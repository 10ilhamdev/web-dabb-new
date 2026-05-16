<?php

namespace App\Http\Controllers;

use App\Models\Feature;
use App\Services\TranslationService;
use Illuminate\Http\Request;

class FeatureController extends Controller
{
    use \App\Traits\SwapsOrder;
    /**
     * Display a listing of the top-level features.
     */
    public function index()
    {
        $features = Feature::whereNull('parent_id')->withCount(['subfeatures', 'pages'])->orderBy('order')->get();

        return view('cms.features.index', compact('features'));
    }

    /**
     * Store a newly created feature (or sub-feature).
     */
    public function store(Request $request, TranslationService $translationService)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:link,dropdown',
            'order' => 'required|integer|min:0',
            'parent_id' => 'nullable|exists:features,id',
            'page_type' => 'nullable|in:none,beranda,onsite,real,3d,book,slideshow,profile,publication',
        ]);

        $validated['name_en'] = $translationService->translate($validated['name']);
        $order = (int) $validated['order'];
        unset($validated['order']);

        if ($validated['type'] === 'link') {
            // If page_type is beranda, set unique path based on feature name
            if (isset($validated['page_type']) && $validated['page_type'] === 'beranda') {
                $slug = \Illuminate\Support\Str::slug($validated['name']);
                $validated['path'] = '/' . $slug;
            } else {
                $slug = \Illuminate\Support\Str::slug($validated['name']);
                if (empty($validated['parent_id'])) {
                    $validated['path'] = '/' . $slug;
                } else {
                    $parent = Feature::find($validated['parent_id']);
                    $parentPath = $parent->path ?: ('/' . \Illuminate\Support\Str::slug($parent->name));
                    $validated['path'] = rtrim($parentPath, '/') . '/' . $slug;
                }
            }

            // Set is_virtual_book if type is book
            if (isset($validated['page_type']) && $validated['page_type'] === 'book') {
                $validated['is_virtual_book'] = true;
            }
        } else {
            $validated['path'] = null;
        }

        $scopeConditions = empty($validated['parent_id']) ? ['parent_id' => null] : ['parent_id' => $validated['parent_id']];

        // Use insertAndShiftOrder so existing features at or after the target order get shifted up
        $feature = $this->insertAndShiftOrder(Feature::class, $order, $scopeConditions, $validated);

        // If it's a sub-feature, redirect back to parent's show page
        if (! empty($validated['parent_id'])) {
            return redirect()->route('cms.features.show', $validated['parent_id'])
                ->with('success', __('cms.features.flash.sub_added'));
        }

        return redirect()->route('cms.features.index')
            ->with('success', __('cms.features.flash.feature_added'));
    }

    /**
     * Show the detail of a feature.
     * - If path = '/' (Beranda): redirect to dedicated Beranda editor
     * - If type = dropdown: show sub-features list
     * - If type = link: show content editor
     */
    public function show(Feature $feature)
    {
        // Beranda has a dedicated structured editor
        if ($feature->path === '/' || strtolower($feature->name) === 'beranda' || $feature->page_type === 'beranda') {
            return redirect()->route('cms.home.edit', $feature->id);
        }

        // If it's a dropdown, skip redirects and show sub-features list
        if ($feature->type !== 'dropdown') {
            // Redirect based on page_type
            if ($feature->page_type === 'onsite') {
                // Pameran Arsip Onsite - redirect to pages directly
                return redirect()->route('cms.features.pages.index', $feature);
            }

            if ($feature->page_type === 'real') {
                return redirect()->route('cms.features.virtual_rooms.index', $feature);
            }

            if ($feature->page_type === '3d') {
                return redirect()->route('cms.features.virtual_3d_rooms.index', $feature);
            }

            if ($feature->page_type === 'book' || $feature->is_virtual_book) {
                return redirect()->route('cms.features.virtual_books.index', $feature);
            }

            if ($feature->page_type === 'slideshow') {
                return redirect()->route('cms.features.slideshow.index', $feature);
            }

            if ($feature->page_type === 'profile') {
                // Redirect to profile pages index with both parent feature and sub-feature
                $parent = Feature::find($feature->parent_id);
                return redirect()->route('cms.features.profile.index', [$parent, $feature]);
            }

            if ($feature->page_type === 'publication' && !request()->has('from')) {
                return redirect()->route('cms.features.publication.index', $feature);
            }

            // Fallback to old logic based on name for backward compatibility
            if (strtolower($feature->name) === 'pameran virtual real') {
                return redirect()->route('cms.features.virtual_rooms.index', $feature);
            }

            if (strtolower($feature->name) === 'pameran virtual' || $feature->path === '/pameran/virtual') {
                return redirect()->route('cms.features.virtual_3d_rooms.index', $feature);
            }

            if (strtolower($feature->name) === 'pameran virtual buku' || str_contains(strtolower($feature->name), 'buku')) {
                return redirect()->route('cms.features.virtual_books.index', $feature);
            }
        }

        // For dropdown types, check if page_type is onsite and redirect to pages
        if ($feature->type === 'dropdown' && $feature->page_type === 'onsite') {
            return redirect()->route('cms.features.pages.index', $feature);
        }

        // For dropdown types with slideshow, redirect to slideshow index (unless ?from=slideshow is set)
        if ($feature->type === 'dropdown' && $feature->page_type === 'slideshow' && !request()->has('from')) {
            return redirect()->route('cms.features.slideshow.index', $feature);
        }

        if ($feature->type === 'dropdown' && $feature->page_type === 'profile') {
            $parent = Feature::find($feature->parent_id);
            return redirect()->route('cms.features.profile.index', [$parent, $feature]);
        }

        if ($feature->type === 'dropdown' && $feature->page_type === 'publication' && !request()->has('from')) {
            return redirect()->route('cms.features.publication.index', $feature);
        }

        // Sub-features of Profil should redirect to profile management
        $parent = Feature::find($feature->parent_id);
        if ($parent && strtolower($parent->name) === 'profil' && $feature->type === 'link') {
            return redirect()->route('cms.features.profile.index', [$parent, $feature]);
        }

        $feature->load(['subfeatures' => function ($query) {
            $query->withCount(['subfeatures', 'pages']);
        }, 'parent']);
        $feature->loadCount('pages');

        return view('cms.features.show', compact('feature'));
    }

    /**
     * Update the specified feature (name, type, path, order).
     */
    public function update(Request $request, Feature $feature, TranslationService $translationService)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:link,dropdown',
            'order' => 'required|integer|min:0',
            'page_type' => 'nullable|in:none,beranda,onsite,real,3d,book,slideshow,profile,publication',
        ]);

        $validated['name_en'] = $translationService->translate($validated['name']);
        $newOrder = (int) $validated['order'];
        unset($validated['order']); // Remove order from validated data

        if ($validated['type'] === 'link') {
            // If page_type is beranda, set unique path based on feature name
            if (isset($validated['page_type']) && $validated['page_type'] === 'beranda') {
                $slug = \Illuminate\Support\Str::slug($validated['name']);
                $validated['path'] = '/' . $slug;
            } else {
                $slug = \Illuminate\Support\Str::slug($validated['name']);
                if (empty($feature->parent_id)) {
                    $validated['path'] = '/' . $slug;
                } else {
                    $parent = Feature::find($feature->parent_id);
                    $parentPath = $parent->path ?: ('/' . \Illuminate\Support\Str::slug($parent->name));
                    $validated['path'] = rtrim($parentPath, '/') . '/' . $slug;
                }
            }
        } else {
            $validated['path'] = null;
        }

        // Update other fields first
        $feature->update($validated);

        // Then handle order change
        $this->swapOrder($feature, $newOrder, (int) $feature->order, ['parent_id' => $feature->parent_id]);

        return redirect()->route('cms.features.index')
            ->with('success', __('cms.features.flash.feature_updated'));
    }

    /**
     * Update the content of a link-type feature.
     */
    public function updateContent(Request $request, Feature $feature, TranslationService $translationService)
    {
        $validated = $request->validate([
            'content' => 'nullable|string',
        ]);

        $contentEn = null;
        if (! empty($validated['content'])) {
            $contentEn = $translationService->translate($validated['content']);
        }

        $feature->update([
            'content' => $validated['content'],
            'content_en' => $contentEn,
        ]);

        return redirect()->route('cms.features.show', $feature)
            ->with('success', __('cms.features.flash.content_saved'));
    }

    /**
     * Remove the specified feature.
     */
    public function destroy(Feature $feature)
    {
        $scopeConditions = ['parent_id' => $feature->parent_id];
        $this->deleteAndShiftOrder($feature, $scopeConditions);

        return redirect()->route('cms.features.index')
            ->with('success', __('cms.features.flash.feature_deleted'));
    }

    /**
     * Update a sub-feature (for dropdown detail page).
     */
    public function updateSub(Request $request, Feature $feature, TranslationService $translationService)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:link,dropdown',
            'order' => 'required|integer|min:0',
            'page_type' => 'nullable|in:none,beranda,onsite,real,3d,book,slideshow,profile,publication',
        ]);

        $validated['name_en'] = $translationService->translate($validated['name']);
        $newOrder = (int) $validated['order'];
        unset($validated['order']); // Remove order from validated data

        if ($validated['type'] === 'link') {
            // If page_type is beranda, set unique path based on feature name
            if (isset($validated['page_type']) && $validated['page_type'] === 'beranda') {
                $slug = \Illuminate\Support\Str::slug($validated['name']);
                $validated['path'] = '/' . $slug;
            } else {
                $slug = \Illuminate\Support\Str::slug($validated['name']);
                if (empty($feature->parent_id)) {
                    $validated['path'] = '/' . $slug;
                } else {
                    $parent = Feature::find($feature->parent_id);
                    $parentPath = $parent->path ?: ('/' . \Illuminate\Support\Str::slug($parent->name));
                    $validated['path'] = rtrim($parentPath, '/') . '/' . $slug;
                }
            }

            // Set is_virtual_book if type is book
            if (isset($validated['page_type']) && $validated['page_type'] === 'book') {
                $validated['is_virtual_book'] = true;
            } else {
                $validated['is_virtual_book'] = false;
            }
        } else {
            $validated['path'] = null;
        }

        // Update other fields first
        $feature->update($validated);

        // Then handle order change
        $this->swapOrder($feature, $newOrder, (int) $feature->order, ['parent_id' => $feature->parent_id]);

        return redirect()->route('cms.features.show', $feature->parent_id)
            ->with('success', __('cms.features.flash.sub_updated'));
    }

    /**
     * Delete a sub-feature.
     */
    public function destroySub(Feature $feature)
    {
        $parentId = $feature->parent_id;
        $scopeConditions = ['parent_id' => $parentId];
        $this->deleteAndShiftOrder($feature, $scopeConditions);

        return redirect()->route('cms.features.show', $parentId)
            ->with('success', __('cms.features.flash.sub_deleted'));
    }

    /**
     * Toggle the visibility of a feature.
     */
    public function toggleVisibility(Feature $feature)
    {
        $feature->update(['is_active' => ! $feature->is_active]);

        return back()->with('success', __('cms.features.flash.visibility_toggled'));
    }
}
