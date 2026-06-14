<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use App\Models\KontakKami;
use App\Services\TranslationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KontakKamiController extends Controller
{
    use \App\Traits\SwapsOrder;

    public function __construct(private TranslationService $translationService)
    {}

    /**
     * Show the kontak_kami list for a feature.
     */
    public function index(Feature $feature)
    {
        $feature->load('parent');
        $pages = $feature->kontakKamis()
            ->orderBy('order')
            ->get();

        return view('cms.features.kontak_kami.index', compact('feature', 'pages'));
    }

    /**
     * Show the form for creating a new kontak_kami page.
     */
    public function create(Feature $feature)
    {
        $feature->load('parent');
        $nextOrder = $feature->kontakKamis()->max('order') + 1;
        return view('cms.features.kontak_kami.create', compact('feature', 'nextOrder'));
    }

    /**
     * Store a new kontak_kami page.
     */
    public function store(Request $request, Feature $feature)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
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
                $imagePaths[] = $image->store('features/kontak_kami', 'public');
            }
        }

        $rawExtraData = $validated['extra_data'] ?? [];
        $extraData = $this->sanitizeExtraData($rawExtraData);

        // Handle translations
        if (!empty($extraData['alamat_section_title'])) {
            $extraData['alamat_section_title_en'] = $this->translationService->translate($extraData['alamat_section_title']);
        }
        if (!empty($extraData['jam_section_title'])) {
            $extraData['jam_section_title_en'] = $this->translationService->translate($extraData['jam_section_title']);
        }
        if (!empty($extraData['cards_section_title'])) {
            $extraData['cards_section_title_en'] = $this->translationService->translate($extraData['cards_section_title']);
        }
        if (!empty($extraData['alamat_lengkap'])) {
            $extraData['alamat_lengkap_en'] = $this->translationService->translate($extraData['alamat_lengkap']);
        }
        if (!empty($extraData['jam_operasional_desc'])) {
            $extraData['jam_operasional_desc_en'] = $this->translationService->translate($extraData['jam_operasional_desc']);
        }
        if (!empty($extraData['cards']) && is_array($extraData['cards'])) {
            foreach ($extraData['cards'] as $k => $v) {
                if (!empty($v['title'])) {
                    $extraData['cards'][$k]['title_en'] = $this->translationService->translate($v['title']);
                }
                if (!empty($v['subtitle'])) {
                    $extraData['cards'][$k]['subtitle_en'] = $this->translationService->translate($v['subtitle']);
                }
            }
        }
        if (!empty($extraData['top_cards']) && is_array($extraData['top_cards'])) {
            foreach ($extraData['top_cards'] as $k => $v) {
                if (!empty($v['title'])) {
                    $extraData['top_cards'][$k]['title_en'] = $this->translationService->translate($v['title']);
                }
                if (!empty($v['subtitle'])) {
                    $extraData['top_cards'][$k]['subtitle_en'] = $this->translationService->translate($v['subtitle']);
                }
            }
        }
        if (!empty($extraData['jam_operasional_list']) && is_array($extraData['jam_operasional_list'])) {
            foreach ($extraData['jam_operasional_list'] as $k => $v) {
                if (!empty($v['hari'])) {
                    $extraData['jam_operasional_list'][$k]['hari_en'] = $this->translationService->translate($v['hari']);
                }
            }
        }

        $data = [
            'feature_id' => $feature->id,
            'title' => $validated['title'],
            'title_en' => $this->translationService->translate($validated['title']),
            'type' => 'kontak',
            'description' => $validated['description'] ?? null,
            'description_en' => ! empty($validated['description']) ? $this->translationService->translate($validated['description']) : null,
            'link_text' => $validated['link_text'] ?? null,
            'link_url' => $validated['link_url'] ?? null,
            'images' => $imagePaths ?: null,
            'published_at' => $validated['published_at'] ?? now(),
            'order' => $validated['order'],
            'extra_data' => !empty($extraData) ? $extraData : null,
        ];

        $insertOrder = (int) $validated['order'];
        $this->insertAndShiftOrder(KontakKami::class, $insertOrder, ['feature_id' => $feature->id], $data);

        return redirect()->route('cms.features.kontak_kami.index', $feature)
            ->with('success', __('cms.kontak_kami.flash.added'));
    }

    /**
     * Show the form for editing a kontak_kami page.
     */
    public function edit(Feature $feature, KontakKami $kontakKami)
    {
        $feature->load('parent');
        return view('cms.features.kontak_kami.edit', compact('feature', 'kontakKami'));
    }

    /**
     * Update a kontak_kami page.
     */
    public function update(Request $request, Feature $feature, KontakKami $kontakKami)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
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
        $oldImages = $kontakKami->images ?? [];
        foreach ($oldImages as $oldImage) {
            if (! in_array($oldImage, $existingImages)) {
                Storage::disk('public')->delete($oldImage);
            }
        }

        $imagePaths = $existingImages;
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('features/kontak_kami', 'public');
            }
        }

        $rawExtraData = $validated['extra_data'] ?? [];
        $extraData = $this->sanitizeExtraData($rawExtraData);

        // Handle translations
        if (!empty($extraData['alamat_section_title'])) {
            $extraData['alamat_section_title_en'] = $this->translationService->translate($extraData['alamat_section_title']);
        }
        if (!empty($extraData['jam_section_title'])) {
            $extraData['jam_section_title_en'] = $this->translationService->translate($extraData['jam_section_title']);
        }
        if (!empty($extraData['cards_section_title'])) {
            $extraData['cards_section_title_en'] = $this->translationService->translate($extraData['cards_section_title']);
        }
        if (!empty($extraData['alamat_lengkap'])) {
            $extraData['alamat_lengkap_en'] = $this->translationService->translate($extraData['alamat_lengkap']);
        }
        if (!empty($extraData['jam_operasional_desc'])) {
            $extraData['jam_operasional_desc_en'] = $this->translationService->translate($extraData['jam_operasional_desc']);
        }
        if (!empty($extraData['cards']) && is_array($extraData['cards'])) {
            foreach ($extraData['cards'] as $k => $v) {
                if (!empty($v['title'])) {
                    $extraData['cards'][$k]['title_en'] = $this->translationService->translate($v['title']);
                }
                if (!empty($v['subtitle'])) {
                    $extraData['cards'][$k]['subtitle_en'] = $this->translationService->translate($v['subtitle']);
                }
            }
        }
        if (!empty($extraData['top_cards']) && is_array($extraData['top_cards'])) {
            foreach ($extraData['top_cards'] as $k => $v) {
                if (!empty($v['title'])) {
                    $extraData['top_cards'][$k]['title_en'] = $this->translationService->translate($v['title']);
                }
                if (!empty($v['subtitle'])) {
                    $extraData['top_cards'][$k]['subtitle_en'] = $this->translationService->translate($v['subtitle']);
                }
            }
        }
        if (!empty($extraData['jam_operasional_list']) && is_array($extraData['jam_operasional_list'])) {
            foreach ($extraData['jam_operasional_list'] as $k => $v) {
                if (!empty($v['hari'])) {
                    $extraData['jam_operasional_list'][$k]['hari_en'] = $this->translationService->translate($v['hari']);
                }
            }
        }

        $data = [
            'title' => $validated['title'],
            'title_en' => $this->translationService->translate($validated['title']),
            'type' => 'kontak',
            'description' => $validated['description'] ?? null,
            'description_en' => ! empty($validated['description']) ? $this->translationService->translate($validated['description']) : null,
            'link_text' => $validated['link_text'] ?? null,
            'link_url' => $validated['link_url'] ?? null,
            'published_at' => $validated['published_at'] ?? $kontakKami->published_at,
            'images' => $imagePaths ?: null,
            'extra_data' => !empty($extraData) ? $extraData : null,
        ];

        $this->swapOrder($kontakKami, (int) $validated['order'], (int) $kontakKami->order, ['feature_id' => $feature->id]);
        $kontakKami->update($data);

        return redirect()->route('cms.features.kontak_kami.index', $feature)
            ->with('success', __('cms.kontak_kami.flash.updated'));
    }

    /**
     * Sanitize and validate extra_data structure.
     */
    private function sanitizeExtraData(array $input): array
    {
        $sanitized = [];

        $stringFields = [
            'alamat_section_title', 'jam_section_title', 'cards_section_title',
            'alamat_lengkap', 'jam_operasional_desc', 'telepon',
            'whatsapp', 'email', 'instagram', 'twitter', 'facebook', 'youtube'
        ];

        foreach ($stringFields as $field) {
            if (isset($input[$field]) && is_string($input[$field])) {
                $sanitized[$field] = trim(strip_tags($input[$field]));
            } else {
                $sanitized[$field] = '';
            }
        }

        // Sanitize cards
        $sanitized['cards'] = [];
        if (!empty($input['cards']) && is_array($input['cards'])) {
            foreach ($input['cards'] as $card) {
                if (is_array($card)) {
                    $sanitized['cards'][] = [
                        'title' => isset($card['title']) && is_string($card['title']) ? trim(strip_tags($card['title'])) : '',
                        'subtitle' => isset($card['subtitle']) && is_string($card['subtitle']) ? trim(strip_tags($card['subtitle'])) : '',
                        'icon' => isset($card['icon']) && is_string($card['icon']) ? trim(strip_tags($card['icon'])) : 'phone',
                    ];
                }
            }
        }

        // Sanitize top_cards
        $sanitized['top_cards'] = [];
        if (!empty($input['top_cards']) && is_array($input['top_cards'])) {
            foreach ($input['top_cards'] as $card) {
                if (is_array($card)) {
                    $sanitized['top_cards'][] = [
                        'title' => isset($card['title']) && is_string($card['title']) ? trim(strip_tags($card['title'])) : '',
                        'subtitle' => isset($card['subtitle']) && is_string($card['subtitle']) ? trim(strip_tags($card['subtitle'])) : '',
                        'icon' => isset($card['icon']) && is_string($card['icon']) ? trim(strip_tags($card['icon'])) : 'map',
                    ];
                }
            }
        }

        // Sanitize jam_operasional_list
        $sanitized['jam_operasional_list'] = [];
        if (!empty($input['jam_operasional_list']) && is_array($input['jam_operasional_list'])) {
            foreach ($input['jam_operasional_list'] as $jam) {
                if (is_array($jam)) {
                    $sanitized['jam_operasional_list'][] = [
                        'hari' => isset($jam['hari']) && is_string($jam['hari']) ? trim(strip_tags($jam['hari'])) : '',
                        'jam' => isset($jam['jam']) && is_string($jam['jam']) ? trim(strip_tags($jam['jam'])) : '',
                    ];
                }
            }
        }

        return $sanitized;
    }

    /**
     * Delete a kontak_kami page.
     */
    public function destroy(Feature $feature, KontakKami $kontakKami)
    {
        if ($kontakKami->images) {
            foreach ($kontakKami->images as $img) {
                Storage::disk('public')->delete($img);
            }
        }

        $this->deleteAndShiftOrder($kontakKami, ['feature_id' => $feature->id]);

        return redirect()->route('cms.features.kontak_kami.index', $feature)
            ->with('success', __('cms.kontak_kami.flash.deleted'));
    }

    /**
     * Toggle visibility (is_active) of a kontak kami page.
     */
    public function toggleVisibility(Feature $feature, KontakKami $kontakKami)
    {
        $kontakKami->update(['is_active' => !$kontakKami->is_active]);

        $msg = $kontakKami->is_active
            ? __('cms.common.flash.shown', ['name' => $kontakKami->title])
            : __('cms.common.flash.hidden', ['name' => $kontakKami->title]);

        return redirect()->back()->with('success', $msg);
    }
}
