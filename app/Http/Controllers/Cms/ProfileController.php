<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use App\Models\Profile;
use App\Models\ProfileSection;
use App\Models\UserAdmin;
use App\Models\UserPegawai;
use App\Services\TranslationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    use \App\Traits\SwapsOrder;

    public function __construct(private TranslationService $translationService)
    {}

    /**
     * Show the profile sub-menu list (index page for Profil dropdown).
     */
    public function index(Feature $feature, Feature $sub)
    {
        $feature->load('parent');
        $sub->load('parent');

        $pages = $sub->profiles()
            ->withCount('sections')
            ->orderBy('order')
            ->get();

        return view('cms.features.profile.index', compact('feature', 'sub', 'pages'));
    }

    /**
     * Store a new sub-menu item (dropdown child of Profil).
     */
    public function storeSubMenu(Request $request, Feature $feature)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'nullable|string',
            'order' => 'required|integer|min:0',
        ]);

        $data = [
            'name' => $validated['name'],
            'name_en' => $this->translationService->translate($validated['name']),
            'type' => 'link',
            'page_type' => 'profile',
            'parent_id' => $feature->id,
            'content' => $validated['content'] ?? null,
            'content_en' => ! empty($validated['content']) ? $this->translationService->translate($validated['content']) : null,
            'order' => $validated['order'],
        ];

        $insertOrder = (int) $validated['order'];
        $this->insertAndShiftOrder(Feature::class, $insertOrder, ['parent_id' => $feature->id], $data);

        return redirect()->route('cms.features.show', $feature)
            ->with('success', __('cms.features.flash.sub_added'));
    }

    /**
     * Update a sub-menu item.
     */
    public function updateSubMenu(Request $request, Feature $feature, Feature $sub)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'nullable|string',
            'order' => 'required|integer|min:0',
        ]);

        $data = [
            'name' => $validated['name'],
            'name_en' => $this->translationService->translate($validated['name']),
            'content' => $validated['content'] ?? null,
            'content_en' => ! empty($validated['content']) ? $this->translationService->translate($validated['content']) : null,
        ];

        $this->swapOrder($sub, (int) $validated['order'], (int) $sub->order, ['parent_id' => $feature->id]);
        $sub->update($data);

        return redirect()->route('cms.features.show', $feature)
            ->with('success', __('cms.features.flash.sub_updated'));
    }

    /**
     * Delete a sub-menu item.
     */
    public function destroySubMenu(Feature $feature, Feature $sub)
    {
        // Delete associated pages and their sections/images
        foreach ($sub->pages as $page) {
            $this->deletePageResources($page);
            $page->sections()->delete();
            $page->delete();
        }

        $this->deleteAndShiftOrder($sub, ['parent_id' => $feature->id]);

        return redirect()->route('cms.features.show', $feature)
            ->with('success', __('cms.features.flash.sub_deleted'));
    }

    /**
     * Show the form for creating a new profile page.
     */
    public function create(Feature $feature, Feature $sub)
    {
        $sub->load('parent');
        $pages = $sub->pages()->orderBy('order')->get();
        return view('cms.features.profile.pages.create', compact('feature', 'sub', 'pages'));
    }

    /**
     * Redirect to edit page — section management is now inline.
     */
    public function show(Feature $feature, Feature $sub, Profile $page)
    {
        return redirect()->route('cms.features.profile.pages.edit', [$feature, $sub, $page]);
    }

    /**
     * Show the form for editing a profile page.
     */
    public function edit(Feature $feature, Feature $sub, Profile $page)
    {
        $sub->load('parent');
        return view('cms.features.profile.pages.edit', compact('feature', 'sub', 'page'));
    }

    /**
     * Store a new profile page.
     */
    public function store(Request $request, Feature $feature, Feature $sub)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'type' => 'nullable|string|in:default,sdm_chart,struktur_image,tugas_fungsi',
            'description' => 'nullable|string',
            'subtitle' => 'nullable|string|max:255',
            'link_text' => 'nullable|string|max:255',
            'link_url' => 'nullable|string|max:500',
            'chart_data' => 'nullable|json',
            'logo' => 'nullable|image|mimes:png,webp|max:2048',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'existing_images' => 'nullable|array',
            'existing_images.*' => 'string',
            'image_positions' => 'nullable|array',
            'image_widths' => 'nullable|array',
            'image_heights' => 'nullable|array',
            'image_offset_x' => 'nullable|array',
            'image_offset_y' => 'nullable|array',
            'order' => 'required|integer|min:0',
        ]);

        $existingImages = $validated['existing_images'] ?? [];
        $imagePaths = $existingImages;
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('features/profile', 'public');
            }
        }

        // Combine image positions with dimensions and offsets
        $imagePositions = [];
        $positions = $validated['image_positions'] ?? [];
        $widths = $validated['image_widths'] ?? [];
        $heights = $validated['image_heights'] ?? [];
        $offsetsX = $validated['image_offset_x'] ?? [];
        $offsetsY = $validated['image_offset_y'] ?? [];

        for ($i = 0; $i < count($positions); $i++) {
            $imagePositions[$i] = [
                'position' => $positions[$i] ?? '50% 50%',
                'width' => $widths[$i] ?? 200,
                'height' => $heights[$i] ?? 150,
                'offsetX' => $offsetsX[$i] ?? 0,
                'offsetY' => $offsetsY[$i] ?? 0,
            ];
        }

        $data = [
            'feature_id' => $sub->id,
            'title' => $validated['title'] ?? '',
            'title_en' => ! empty($validated['title']) ? $this->translationService->translate($validated['title']) : null,
            'type' => $validated['type'] ?? 'default',
            'description' => $validated['description'] ?? null,
            'description_en' => ! empty($validated['description'])
                ? $this->translationService->translate($validated['description'])
                : null,
            'subtitle' => $validated['subtitle'] ?? null,
            'subtitle_en' => ! empty($validated['subtitle'])
                ? $this->translationService->translate($validated['subtitle'])
                : null,
            'link_text' => $validated['link_text'] ?? null,
            'link_url' => $validated['link_url'] ?? null,
            'chart_data' => $validated['chart_data'] ? json_decode($validated['chart_data'], true) : null,
            'images' => $imagePaths ?: null,
            'image_positions' => $imagePositions ?: null,
            'order' => $validated['order'],
        ];

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('features/profile', 'public');
        }

        $insertOrder = (int) $validated['order'];
        $this->insertAndShiftOrder(Profile::class, $insertOrder, ['feature_id' => $sub->id], $data);

        return redirect()->route('cms.features.profile.index', [$feature, $sub])
            ->with('success', __('cms.profile.flash.page_added'));
    }

    /**
     * Update a profile page.
     */
    public function update(Request $request, Feature $feature, Feature $sub, Profile $page)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'type' => 'nullable|string|in:default,sdm_chart,struktur_image,tugas_fungsi',
            'description' => 'nullable|string',
            'subtitle' => 'nullable|string|max:255',
            'link_text' => 'nullable|string|max:255',
            'link_url' => 'nullable|string|max:500',
            'chart_data' => 'nullable|json',
            'logo' => 'nullable|image|mimes:png,webp|max:2048',
            'remove_logo' => 'nullable|in:1',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'existing_images' => 'nullable|array',
            'existing_images.*' => 'string',
            'image_positions' => 'nullable|array',
            'image_widths' => 'nullable|array',
            'image_heights' => 'nullable|array',
            'image_offset_x' => 'nullable|array',
            'image_offset_y' => 'nullable|array',
            'order' => 'required|integer|min:0',
        ]);

        $existingImages = $validated['existing_images'] ?? [];
        $oldImages = $page->images ?? [];
        foreach ($oldImages as $oldImage) {
            if (! in_array($oldImage, $existingImages)) {
                Storage::disk('public')->delete($oldImage);
            }
        }
        $imagePaths = $existingImages;
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('features/profile', 'public');
            }
        }

        // Combine image positions with dimensions and offsets
        $imagePositions = [];
        $positions = $validated['image_positions'] ?? [];
        $widths = $validated['image_widths'] ?? [];
        $heights = $validated['image_heights'] ?? [];
        $offsetsX = $validated['image_offset_x'] ?? [];
        $offsetsY = $validated['image_offset_y'] ?? [];

        for ($i = 0; $i < count($positions); $i++) {
            $imagePositions[$i] = [
                'position' => $positions[$i] ?? '50% 50%',
                'width' => $widths[$i] ?? 200,
                'height' => $heights[$i] ?? 150,
                'offsetX' => $offsetsX[$i] ?? 0,
                'offsetY' => $offsetsY[$i] ?? 0,
            ];
        }

        $data = [
            'title' => $validated['title'] ?? '',
            'title_en' => ! empty($validated['title']) ? $this->translationService->translate($validated['title']) : null,
            'type' => $validated['type'] ?? 'default',
            'description' => $validated['description'] ?? null,
            'description_en' => ! empty($validated['description'])
                ? $this->translationService->translate($validated['description'])
                : null,
            'subtitle' => $validated['subtitle'] ?? null,
            'subtitle_en' => ! empty($validated['subtitle']) ? $this->translationService->translate($validated['subtitle']) : null,
            'link_text' => $validated['link_text'] ?? null,
            'link_url' => $validated['link_url'] ?? null,
            'chart_data' => $validated['chart_data'] ? json_decode($validated['chart_data'], true) : null,
            'images' => $imagePaths ?: null,
            'image_positions' => $imagePositions ?: null,
        ];

        if ($request->hasFile('logo')) {
            if ($page->logo_path) {
                Storage::disk('public')->delete($page->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('features/profile', 'public');
        } elseif (isset($validated['remove_logo']) && $page->logo_path) {
            Storage::disk('public')->delete($page->logo_path);
            $data['logo_path'] = null;
        }

        $this->swapOrder($page, (int) $validated['order'], (int) $page->order, ['feature_id' => $sub->id]);
        $page->update($data);

        return redirect()->route('cms.features.profile.index', [$feature, $sub])
            ->with('success', __('cms.profile.flash.page_updated'));
    }

    /**
     * Delete a profile page.
     */
    public function destroy(Feature $feature, Feature $sub, Profile $page)
    {
        $this->deletePageResources($page);
        $page->sections()->delete();
        $this->deleteAndShiftOrder($page, ['feature_id' => $sub->id]);

        return redirect()->route('cms.features.profile.index', [$feature, $sub])
            ->with('success', __('cms.profile.flash.page_deleted'));
    }

    /**
     * Store a section for a profile page.
     */
    public function storeSection(Request $request, Feature $feature, Feature $sub, Profile $page, TranslationService $translationService)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'order' => 'required|integer|min:0',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'image_positions' => 'nullable|array',
        ]);

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('features/sections', 'public');
            }
        }

        ProfileSection::create([
            'profile_id' => $page->id,
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

        return redirect()->route('cms.features.profile.pages.show', [$feature, $sub, $page])
            ->with('success', __('cms.profile.flash.section_added'));
    }

    /**
     * Update a section for a profile page.
     */
    public function updateSection(Request $request, Feature $feature, Feature $sub, Profile $page, ProfileSection $section, TranslationService $translationService)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'order' => 'required|integer|min:0',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'existing_images' => 'nullable|array',
            'existing_images.*' => 'string',
            'image_positions' => 'nullable|array',
        ]);

        $existingImages = $validated['existing_images'] ?? [];
        $oldImages = $section->images ?? [];
        foreach ($oldImages as $oldImage) {
            if (! in_array($oldImage, $existingImages)) {
                Storage::disk('public')->delete($oldImage);
            }
        }

        $imagePaths = $existingImages;
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('features/sections', 'public');
            }
        }

        $this->swapOrder($section, (int) $validated['order'], (int) $section->order, ['profile_id' => $section->profile_id]);
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

        return redirect()->route('cms.features.profile.pages.show', [$feature, $sub, $page])
            ->with('success', __('cms.profile.flash.section_updated'));
    }

    /**
     * Delete a section from a profile page.
     */
    public function destroySection(Feature $feature, Feature $sub, Profile $page, ProfileSection $section)
    {
        if ($section->images) {
            foreach ($section->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }
        $this->deleteAndShiftOrder($section, ['profile_id' => $section->profile_id]);

        return redirect()->route('cms.features.profile.pages.show', [$feature, $sub, $page])
            ->with('success', __('cms.profile.flash.section_deleted'));
    }

    /**
     * Get available data fields from admin and pegawai tables.
     * Returns fields that can be used for chart generation.
     */
    public function getDataFields(Feature $feature)
    {
        // Columns to exclude (technical or sensitive)
        $excludeFields = ['user_id', 'id', 'created_at', 'updated_at', 'password', 'remember_token', 'email_verified_at', 'google_id', 'photo', 'kartu_identitas', 'nomor_kartu_identitas'];

        // Fetch distinct column names and labels from all roles
        $fields = \App\Models\RoleColumn::whereNotIn('column_name', $excludeFields)
            ->select('column_name', 'column_label')
            ->distinct()
            ->get();

        $result = [];
        foreach ($fields as $field) {
            // Use label from DB, or fallback to pretty name
            $result[$field->column_name] = $field->column_label ?: ucwords(str_replace('_', ' ', $field->column_name));
        }

        // Add role field manually as it's a base field in users table
        $result['role'] = 'Role / Grup User';

        // Sort by label
        asort($result);

        return response()->json($result);
    }

    /**
     * Generate chart data for SDM page type.
     * Accepts config parameter in JSON format with field -> chart type mapping
     * config: {"jenis_kelamin": "pie,bar", "agama": "pie", "tanggal_lahir": "bar", ...}
     */
    public function generateChart(Request $request, Feature $feature)
    {
        $config = $request->input('config', '{}');
        $configData = json_decode($config, true);
        $rolesParam = $request->input('roles', '[]');
        $selectedRoles = json_decode($rolesParam, true);

        if (empty($configData)) {
            return response()->json([]);
        }

        $rolesQuery = \App\Models\Role::whereNotNull('relation_name');
        if (!empty($selectedRoles) && is_array($selectedRoles)) {
            $rolesQuery->whereIn('name', $selectedRoles);
        }
        $roles = $rolesQuery->get();
        $allUsers = collect();
        foreach ($roles as $role) {
            $relation = $role->relation_name;
            $modelClass = 'App\\Models\\' . ucfirst($relation);
            if (class_exists($modelClass)) {
                try {
                    $roleUsers = $modelClass::all();
                    // Inject role label for grouping
                    foreach ($roleUsers as $u) { $u->role = $role->label; }
                    $allUsers = $allUsers->concat($roleUsers);
                } catch (\Exception $e) { /* skip if table missing */ }
            }
        }

        $result = [];

        foreach ($configData as $field => $chartTypes) {
            $types = is_array($chartTypes) ? $chartTypes : explode(',', $chartTypes);
            $types = array_map('trim', $types);
            $types = array_filter($types); // Remove empty

            if (empty($types)) {
                continue;
            }

            $chartData = $this->generateChartDataForField($allUsers, $field);

            foreach ($types as $type) {
                if ($type === 'pie') {
                    $result[$field . '_pie'] = [
                        'field' => $field,
                        'type' => 'pie',
                        'labels' => $chartData['labels'],
                        'data' => $chartData['data'],
                        'colors' => $this->generateColors(count($chartData['labels'])),
                        'title' => $chartData['title'],
                    ];
                } elseif ($type === 'bar') {
                    $result[$field . '_bar'] = [
                        'field' => $field,
                        'type' => 'bar',
                        'labels' => $chartData['labels'],
                        'data' => $chartData['data'],
                        'colors' => $this->generateColors(count($chartData['labels'])),
                        'title' => $chartData['title'],
                    ];
                }
            }
        }

        return response()->json($result);
    }

    /**
     * Generate chart data for a specific field.
     */
    private function generateChartDataForField($users, string $field): array
    {
        // Field labels (fallback to dynamic lookup)
        $label = \App\Models\RoleColumn::where('column_name', $field)->value('column_label');
        if (!$label) {
            if ($field === 'role') $label = 'Role / Grup User';
            else $label = ucwords(str_replace('_', ' ', $field));
        }

        // Special handling for tanggal_lahir (age groups)
        if ($field === 'tanggal_lahir') {
            $ageGroups = [
                '< 25' => 0,
                '25-35' => 0,
                '36-45' => 0,
                '46-55' => 0,
                '56-65' => 0,
                '> 65' => 0,
            ];

            foreach ($users as $user) {
                if ($user->tanggal_lahir) {
                    try {
                        $age = Carbon::parse($user->tanggal_lahir)->age;
                        if ($age < 25) { $ageGroups['< 25']++; }
                        elseif ($age < 36) { $ageGroups['25-35']++; }
                        elseif ($age < 46) { $ageGroups['36-45']++; }
                        elseif ($age < 56) { $ageGroups['46-55']++; }
                        elseif ($age < 66) { $ageGroups['56-65']++; }
                        else { $ageGroups['> 65']++; }
                    } catch (\Exception $e) { /* skip */ }
                }
            }

            return [
                'title' => $label,
                'labels' => array_keys($ageGroups),
                'data' => array_values($ageGroups),
            ];
        }

        // Regular field grouping
        $counts = $users
            ->whereNotNull($field)
            ->groupBy($field)
            ->map(fn($group) => $group->count())
            ->toArray();

        // Sort by count descending
        arsort($counts);

        return [
            'title' => $label,
            'labels' => array_keys($counts),
            'data' => array_values($counts),
        ];
    }

    private function generateColors(int $count): array
    {
        $palette = ['#36c5f0', '#0a0b1e', '#85d7ff', '#2eb67d', '#174e93', '#10b981', '#06b6d4', '#6366f1'];
        $colors = [];
        for ($i = 0; $i < $count; $i++) {
            $colors[] = $palette[$i % count($palette)];
        }
        return $colors;
    }

    private function deletePageResources(Profile $page): void
    {
        if ($page->logo_path) {
            Storage::disk('public')->delete($page->logo_path);
        }

        if ($page->images) {
            foreach ($page->images as $img) {
                Storage::disk('public')->delete($img);
            }
        }

        foreach ($page->sections as $section) {
            if ($section->images) {
                foreach ($section->images as $img) {
                    Storage::disk('public')->delete($img);
                }
            }
        }
    }
}
