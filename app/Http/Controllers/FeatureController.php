<?php

namespace App\Http\Controllers;

use App\Models\Feature;
use App\Services\TranslationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeatureController extends Controller
{
    use \App\Traits\SwapsOrder;
    /**
     * Display a listing of the top-level features.
     */
    public function index()
    {
        $features = Feature::whereNull('parent_id')->withCount(['subfeatures', 'pages'])->orderBy('order')->get();
        $dropdownFeatures = Feature::where('type', 'dropdown')
            ->orderBy('name')
            ->get(['id', 'name', 'parent_id']);

        return view('cms.features.index', compact('features', 'dropdownFeatures'));
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
            'page_type' => 'nullable|in:none,beranda,onsite,real,3d,book,slideshow,profile,publication,layanan_publik,pengelolaan,kontak_kami',
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
                return redirect()->route('cms.features.profile.index', $feature);
            }

            if ($feature->page_type === 'publication' && !request()->has('from')) {
                return redirect()->route('cms.features.publication.index', $feature);
            }

            if ($feature->page_type === 'layanan_publik' && !request()->has('from')) {
                return redirect()->route('cms.features.layanan_publik.index', $feature);
            }

            if ($feature->page_type === 'pengelolaan' && !request()->has('from')) {
                return redirect()->route('cms.features.pengelolaan.index', $feature);
            }

            if ($feature->page_type === 'kontak_kami' && !request()->has('from')) {
                return redirect()->route('cms.features.kontak_kami.index', $feature);
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


        // For dropdown types with slideshow, redirect to slideshow index (unless ?from=slideshow is set)
        if ($feature->type === 'dropdown' && $feature->page_type === 'slideshow' && !request()->has('from')) {
            return redirect()->route('cms.features.slideshow.index', $feature);
        }

        if ($feature->type === 'dropdown' && $feature->page_type === 'profile' && $feature->parent_id) {
            return redirect()->route('cms.features.profile.index', $feature);
        }

        if ($feature->type === 'dropdown' && $feature->page_type === 'profile' && !$feature->parent_id) {
            return redirect()->route('cms.features.profile.index', $feature);
        }

        if ($feature->type === 'dropdown' && $feature->page_type === 'publication' && !request()->has('from')) {
            return redirect()->route('cms.features.publication.index', $feature);
        }

        if ($feature->type === 'dropdown' && $feature->page_type === 'layanan_publik' && !request()->has('from')) {
            return redirect()->route('cms.features.layanan_publik.index', $feature);
        }

        if ($feature->type === 'dropdown' && $feature->page_type === 'pengelolaan' && !request()->has('from')) {
            return redirect()->route('cms.features.pengelolaan.index', $feature);
        }

        if ($feature->type === 'dropdown' && $feature->page_type === 'kontak_kami' && !request()->has('from')) {
            return redirect()->route('cms.features.kontak_kami.index', $feature);
        }

        // Sub-features of Profil should redirect to profile management
        $parent = Feature::find($feature->parent_id);
        if ($parent && strtolower($parent->name) === 'profil' && $feature->type === 'link') {
            return redirect()->route('cms.features.profile.index', $feature);
        }

        $feature->load(['subfeatures' => function ($query) {
            $query->withCount(['subfeatures', 'pages']);
        }, 'parent']);
        $feature->loadCount('pages');

        // All dropdown-type features (for "Pindah ke Menu" selector) — excluding the current feature
        $dropdownFeatures = Feature::where('type', 'dropdown')
            ->where('id', '!=', $feature->id)
            ->orderBy('name')
            ->get(['id', 'name', 'parent_id']);

        return view('cms.features.show', compact('feature', 'dropdownFeatures'));
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
            'page_type' => 'nullable|in:none,beranda,onsite,real,3d,book,slideshow,profile,publication,layanan_publik,pengelolaan,kontak_kami',
            'new_parent_id' => 'nullable|exists:features,id',
        ]);

        $validated['name_en'] = $translationService->translate($validated['name']);
        $newOrder = (int) $validated['order'];
        unset($validated['order']); // Remove order from validated data

        // Determine if we are moving to a different parent
        $newParentId = !empty($validated['new_parent_id']) ? (int) $validated['new_parent_id'] : null;
        unset($validated['new_parent_id']);
        $oldParentId = $feature->parent_id;
        $isMoving = $newParentId !== $oldParentId;

        if ($validated['type'] === 'link') {
            // Determine effective parent for path construction
            $effectiveParentId = $isMoving ? $newParentId : $feature->parent_id;

            // If page_type is beranda, set unique path based on feature name
            if (isset($validated['page_type']) && $validated['page_type'] === 'beranda') {
                $slug = \Illuminate\Support\Str::slug($validated['name']);
                $validated['path'] = '/' . $slug;
            } else {
                $slug = \Illuminate\Support\Str::slug($validated['name']);
                if (empty($effectiveParentId)) {
                    $validated['path'] = '/' . $slug;
                } else {
                    $parent = Feature::find($effectiveParentId);
                    $parentPath = $parent->path ?: ('/' . \Illuminate\Support\Str::slug($parent->name));
                    $validated['path'] = rtrim($parentPath, '/') . '/' . $slug;
                }
            }
        } else {
            $validated['path'] = null;
        }

        if ($isMoving) {
            DB::transaction(function () use ($feature, $oldParentId, $newParentId, $newOrder, $validated) {
                $oldOrder = (int) $feature->order;

                // 1. Temporarily set order to a safe negative value to avoid constraint conflicts
                $feature->update(['order' => -($feature->id)]);

                // 2. Close the gap in old parent (shift down items after old position)
                Feature::where('parent_id', $oldParentId)
                    ->where('order', '>', $oldOrder)
                    ->decrement('order');

                // 3. Make room in new parent (shift up items at or after target position)
                $maxNewOrder = Feature::where('parent_id', $newParentId)->max('order') ?? 0;
                $targetOrder = min($newOrder, $maxNewOrder + 1);
                $targetOrder = max(1, $targetOrder);

                Feature::where('parent_id', $newParentId)
                    ->where('order', '>=', $targetOrder)
                    ->orderBy('order', 'desc')
                    ->increment('order');

                // 4. Update the feature itself — change parent and order, plus other fields
                $feature->update(array_merge($validated, [
                    'parent_id' => $newParentId,
                    'order'     => $targetOrder,
                ]));
            });

            return redirect()->route('cms.features.index')
                ->with('success', __('cms.features.flash.feature_updated'));
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
            'name'          => 'required|string|max:255',
            'type'          => 'required|in:link,dropdown',
            'order'         => 'required|integer|min:0',
            'page_type'     => 'nullable|in:none,beranda,onsite,real,3d,book,slideshow,profile,publication,layanan_publik,pengelolaan,kontak_kami',
            'new_parent_id' => 'nullable|string',
        ]);

        if (!empty($validated['new_parent_id']) && $validated['new_parent_id'] !== 'top_level') {
            $request->validate([
                'new_parent_id' => 'exists:features,id',
            ]);
        }

        $validated['name_en'] = $translationService->translate($validated['name']);
        $newOrder = (int) $validated['order'];
        unset($validated['order']);

        // Determine if we are moving to a different parent
        $newParentRaw = $validated['new_parent_id'] ?? '';
        unset($validated['new_parent_id']);
        $oldParentId = $feature->parent_id;

        if ($newParentRaw === 'top_level') {
            $newParentId = null;
            $isMoving = true;
        } elseif ($newParentRaw !== '') {
            $newParentId = (int) $newParentRaw;
            $isMoving = $newParentId !== $oldParentId;
        } else {
            $newParentId = $oldParentId;
            $isMoving = false;
        }

        if ($validated['type'] === 'link') {
            // Determine effective parent for path construction
            $effectiveParentId = $isMoving ? $newParentId : $feature->parent_id;

            if (isset($validated['page_type']) && $validated['page_type'] === 'beranda') {
                $slug = \Illuminate\Support\Str::slug($validated['name']);
                $validated['path'] = '/' . $slug;
            } else {
                $slug = \Illuminate\Support\Str::slug($validated['name']);
                if (empty($effectiveParentId)) {
                    $validated['path'] = '/' . $slug;
                } else {
                    $parent = Feature::find($effectiveParentId);
                    $parentPath = $parent->path ?: ('/' . \Illuminate\Support\Str::slug($parent->name));
                    $validated['path'] = rtrim($parentPath, '/') . '/' . $slug;
                }
            }

            if (isset($validated['page_type']) && $validated['page_type'] === 'book') {
                $validated['is_virtual_book'] = true;
            } else {
                $validated['is_virtual_book'] = false;
            }
        } else {
            $validated['path'] = null;
        }

        if ($isMoving) {
            DB::transaction(function () use ($feature, $oldParentId, $newParentId, $newOrder, $validated) {
                $oldOrder = (int) $feature->order;

                // 1. Temporarily set order to a safe negative value to avoid constraint conflicts
                $feature->update(['order' => -($feature->id)]);

                // 2. Close the gap in old parent (shift down items after old position)
                Feature::where('parent_id', $oldParentId)
                    ->where('order', '>', $oldOrder)
                    ->decrement('order');

                // 3. Make room in new parent (shift up items at or after target position)
                $maxNewOrder = Feature::where('parent_id', $newParentId)->max('order') ?? 0;
                $targetOrder = min($newOrder, $maxNewOrder + 1);
                $targetOrder = max(1, $targetOrder);

                Feature::where('parent_id', $newParentId)
                    ->where('order', '>=', $targetOrder)
                    ->orderBy('order', 'desc')
                    ->increment('order');

                // 4. Update the feature itself — change parent and order, plus other fields
                $feature->update(array_merge($validated, [
                    'parent_id' => $newParentId,
                    'order'     => $targetOrder,
                ]));
            });

            if ($newParentId === null) {
                return redirect()->route('cms.features.index')
                    ->with('success', __('cms.features.flash.sub_updated'));
            }

            return redirect()->route('cms.features.show', $newParentId)
                ->with('success', __('cms.features.flash.sub_updated'));
        }

        // No move — just update in place
        $feature->update($validated);
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
