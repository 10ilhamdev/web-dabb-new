<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use App\Models\Pengelolaan;
use App\Services\TranslationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengelolaanController extends Controller
{
    use \App\Traits\SwapsOrder;

    public function __construct(private TranslationService $translationService)
    {}

    /**
     * Show the pengelolaan list for a sub-feature.
     */
    public function index(Feature $feature)
    {
        $feature->load('parent');
        $pages = $feature->pengelolaans()
            ->orderBy('order')
            ->get();

        return view('cms.features.pengelolaan.index', compact('feature', 'pages'));
    }

    /**
     * Show the form for creating a new pengelolaan page.
     */
    public function create(Feature $feature)
    {
        $feature->load('parent');
        $nextOrder = $feature->pengelolaans()->max('order') + 1;
        return view('cms.features.pengelolaan.create', compact('feature', 'nextOrder'));
    }

    /**
     * Store a new pengelolaan page.
     */
    public function store(Request $request, Feature $feature)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string|in:penyusutan,penyimpanan,preservasi,pengolahan,pemanfaatan,penjangkauan,akuisisi',
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
                $imagePaths[] = $image->store('features/pengelolaan', 'public');
            }
        }

        $extraData = $validated['extra_data'] ?? [];

        // Ensure active type's list fields are preserved as empty arrays if cleared
        if ($validated['type'] === 'pengolahan') {
            if (!isset($extraData['pengolahan_list'])) $extraData['pengolahan_list'] = [];
        } elseif ($validated['type'] === 'penyimpanan') {
            if (!isset($extraData['prinsip_list'])) $extraData['prinsip_list'] = [];
            if (!isset($extraData['sistem_penyimpanan'])) $extraData['sistem_penyimpanan'] = [];
            if (!isset($extraData['fasilitas_list'])) $extraData['fasilitas_list'] = [];
            if (!isset($extraData['ruang_list'])) $extraData['ruang_list'] = [];
        } elseif ($validated['type'] === 'preservasi') {
            if (!isset($extraData['preservasi_list'])) $extraData['preservasi_list'] = [];
            if (!isset($extraData['restorasi_list'])) $extraData['restorasi_list'] = [];
        } elseif ($validated['type'] === 'pemanfaatan') {
            if (!isset($extraData['akses_list'])) $extraData['akses_list'] = [];
        } elseif ($validated['type'] === 'penjangkauan') {
            if (!isset($extraData['kegiatan_list'])) $extraData['kegiatan_list'] = [];
        } elseif ($validated['type'] === 'akuisisi') {
            if (!isset($extraData['tahapan_list'])) $extraData['tahapan_list'] = [];
        }

        if ($request->hasFile('extra_data_file_upload')) {
            $extraData['file'] = $request->file('extra_data_file_upload')->store('features/pengelolaan/files', 'public');
        }

        $fasilitasImages = [];
        if ($request->hasFile('fasilitas_images')) {
            foreach ($request->file('fasilitas_images') as $image) {
                $fasilitasImages[] = $image->store('features/pengelolaan/fasilitas', 'public');
            }
        }
        if (!empty($fasilitasImages)) {
            $extraData['fasilitas_images'] = $fasilitasImages;
        }

        $ruangImages = [];
        if ($request->hasFile('ruang_images')) {
            foreach ($request->file('ruang_images') as $image) {
                $ruangImages[] = $image->store('features/pengelolaan/ruang', 'public');
            }
        }
        if (!empty($ruangImages)) {
            $extraData['ruang_images'] = $ruangImages;
        }

        // Handle specific extra_data fields translations
        if (!empty($extraData['prinsip_title'])) {
            $extraData['prinsip_title_en'] = $this->translationService->translate($extraData['prinsip_title']);
        }
        if (!empty($extraData['prinsip_desc'])) {
            $extraData['prinsip_desc_en'] = $this->translationService->translate($extraData['prinsip_desc']);
        } elseif (!empty($extraData['prinsip_penyimpanan'])) {
            $extraData['prinsip_desc'] = $extraData['prinsip_penyimpanan'];
            $extraData['prinsip_desc_en'] = $this->translationService->translate($extraData['prinsip_penyimpanan']);
            unset($extraData['prinsip_penyimpanan']);
        }
        if (!empty($extraData['prinsip_list']) && is_array($extraData['prinsip_list'])) {
            foreach ($extraData['prinsip_list'] as $k => $v) {
                if (!empty($v['title'])) {
                    $extraData['prinsip_list'][$k]['title_en'] = $this->translationService->translate($v['title']);
                }
                if (!empty($v['desc'])) {
                    $extraData['prinsip_list'][$k]['desc_en'] = $this->translationService->translate($v['desc']);
                }
                if (!empty($v['text'])) {
                    $extraData['prinsip_list'][$k]['text_en'] = $this->translationService->translate($v['text']);
                }
            }
        }
        if (!empty($extraData['sistem_title'])) {
            $extraData['sistem_title_en'] = $this->translationService->translate($extraData['sistem_title']);
        }
        if (!empty($extraData['sistem_penyimpanan']) && is_array($extraData['sistem_penyimpanan'])) {
            foreach ($extraData['sistem_penyimpanan'] as $k => $v) {
                if (!empty($v['title'])) {
                    $extraData['sistem_penyimpanan'][$k]['title_en'] = $this->translationService->translate($v['title']);
                }
                if (!empty($v['desc'])) {
                    $extraData['sistem_penyimpanan'][$k]['desc_en'] = $this->translationService->translate($v['desc']);
                }
            }
        }
        if (!empty($extraData['fasilitas_title'])) {
            $extraData['fasilitas_title_en'] = $this->translationService->translate($extraData['fasilitas_title']);
        }
        if (!empty($extraData['ruang_title'])) {
            $extraData['ruang_title_en'] = $this->translationService->translate($extraData['ruang_title']);
        }
        if (!empty($extraData['fasilitas_list']) && is_array($extraData['fasilitas_list'])) {
            foreach ($extraData['fasilitas_list'] as $k => $v) {
                if (!empty($v['text'])) {
                    $extraData['fasilitas_list'][$k]['text_en'] = $this->translationService->translate($v['text']);
                }
            }
        }
        if (!empty($extraData['ruang_list']) && is_array($extraData['ruang_list'])) {
            foreach ($extraData['ruang_list'] as $k => $v) {
                if (!empty($v['text'])) {
                    $extraData['ruang_list'][$k]['text_en'] = $this->translationService->translate($v['text']);
                }
            }
        }
        if (!empty($extraData['preservasi_title'])) {
            $extraData['preservasi_title_en'] = $this->translationService->translate($extraData['preservasi_title']);
        }
        if (!empty($extraData['restorasi_title'])) {
            $extraData['restorasi_title_en'] = $this->translationService->translate($extraData['restorasi_title']);
        }
        if (!empty($extraData['pengolahan_title'])) {
            $extraData['pengolahan_title_en'] = $this->translationService->translate($extraData['pengolahan_title']);
        }
        if (!empty($extraData['akses_title'])) {
            $extraData['akses_title_en'] = $this->translationService->translate($extraData['akses_title']);
        }
        if (!empty($extraData['preservasi_list']) && is_array($extraData['preservasi_list'])) {
            foreach ($extraData['preservasi_list'] as $k => $v) {
                if (!empty($v['text'])) {
                    $extraData['preservasi_list'][$k]['text_en'] = $this->translationService->translate($v['text']);
                }
            }
        }
        if (!empty($extraData['restorasi_desc'])) {
            $extraData['restorasi_desc_en'] = $this->translationService->translate($extraData['restorasi_desc']);
        }
        if (!empty($extraData['restorasi_list']) && is_array($extraData['restorasi_list'])) {
            foreach ($extraData['restorasi_list'] as $k => $v) {
                if (!empty($v['text'])) {
                    $extraData['restorasi_list'][$k]['text_en'] = $this->translationService->translate($v['text']);
                }
            }
        }
        if (!empty($extraData['pengolahan_list']) && is_array($extraData['pengolahan_list'])) {
            foreach ($extraData['pengolahan_list'] as $k => $v) {
                if (!empty($v['text'])) {
                    $extraData['pengolahan_list'][$k]['text_en'] = $this->translationService->translate($v['text']);
                }
            }
        }
        if (!empty($extraData['mekanisme_title'])) {
            $extraData['mekanisme_title_en'] = $this->translationService->translate($extraData['mekanisme_title']);
        }
        if (!empty($extraData['mekanisme_desc'])) {
            $extraData['mekanisme_desc_en'] = $this->translationService->translate($extraData['mekanisme_desc']);
        }
        if (!empty($extraData['pemanfaatan_quote'])) {
            $extraData['pemanfaatan_quote_en'] = $this->translationService->translate($extraData['pemanfaatan_quote']);
        }
        if (!empty($extraData['akses_list']) && is_array($extraData['akses_list'])) {
            foreach ($extraData['akses_list'] as $k => $v) {
                if (!empty($v['title'])) {
                    $extraData['akses_list'][$k]['title_en'] = $this->translationService->translate($v['title']);
                }
                if (!empty($v['desc'])) {
                    $extraData['akses_list'][$k]['desc_en'] = $this->translationService->translate($v['desc']);
                }
            }
        }
        if (!empty($extraData['kegiatan_title'])) {
            $extraData['kegiatan_title_en'] = $this->translationService->translate($extraData['kegiatan_title']);
        }
        if (!empty($extraData['kegiatan_list']) && is_array($extraData['kegiatan_list'])) {
            foreach ($extraData['kegiatan_list'] as $k => $v) {
                if (!empty($v['title'])) {
                    $extraData['kegiatan_list'][$k]['title_en'] = $this->translationService->translate($v['title']);
                }
                if (!empty($v['desc'])) {
                    $extraData['kegiatan_list'][$k]['desc_en'] = $this->translationService->translate($v['desc']);
                }
                if (!empty($v['button_label'])) {
                    $extraData['kegiatan_list'][$k]['button_label_en'] = $this->translationService->translate($v['button_label']);
                }
            }
        }
        if (!empty($extraData['tahapan_list']) && is_array($extraData['tahapan_list'])) {
            foreach ($extraData['tahapan_list'] as $k => $v) {
                if (!empty($v['title'])) {
                    $extraData['tahapan_list'][$k]['title_en'] = $this->translationService->translate($v['title']);
                }
                if (!empty($v['desc'])) {
                    $extraData['tahapan_list'][$k]['desc_en'] = $this->translationService->translate($v['desc']);
                }
            }
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
        $this->insertAndShiftOrder(Pengelolaan::class, $insertOrder, ['feature_id' => $feature->id], $data);

        return redirect()->route('cms.features.pengelolaan.index', $feature)
            ->with('success', __('cms.pengelolaan.flash.added'));
    }

    /**
     * Show the form for editing a pengelolaan page.
     */
    public function edit(Feature $feature, Pengelolaan $pengelolaan)
    {
        $feature->load('parent');
        return view('cms.features.pengelolaan.edit', compact('feature', 'pengelolaan'));
    }

    /**
     * Update a pengelolaan page.
     */
    public function update(Request $request, Feature $feature, Pengelolaan $pengelolaan)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string|in:penyusutan,penyimpanan,preservasi,pengolahan,pemanfaatan,penjangkauan,akuisisi',
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
        $oldImages = $pengelolaan->images ?? [];
        foreach ($oldImages as $oldImage) {
            if (! in_array($oldImage, $existingImages)) {
                Storage::disk('public')->delete($oldImage);
            }
        }

        $imagePaths = $existingImages;
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('features/pengelolaan', 'public');
            }
        }

        $extraData = $validated['extra_data'] ?? [];

        // Ensure active type's list fields are preserved as empty arrays if cleared
        if ($validated['type'] === 'pengolahan') {
            if (!isset($extraData['pengolahan_list'])) $extraData['pengolahan_list'] = [];
        } elseif ($validated['type'] === 'penyimpanan') {
            if (!isset($extraData['prinsip_list'])) $extraData['prinsip_list'] = [];
            if (!isset($extraData['sistem_penyimpanan'])) $extraData['sistem_penyimpanan'] = [];
            if (!isset($extraData['fasilitas_list'])) $extraData['fasilitas_list'] = [];
            if (!isset($extraData['ruang_list'])) $extraData['ruang_list'] = [];
        } elseif ($validated['type'] === 'preservasi') {
            if (!isset($extraData['preservasi_list'])) $extraData['preservasi_list'] = [];
            if (!isset($extraData['restorasi_list'])) $extraData['restorasi_list'] = [];
        } elseif ($validated['type'] === 'pemanfaatan') {
            if (!isset($extraData['akses_list'])) $extraData['akses_list'] = [];
        } elseif ($validated['type'] === 'penjangkauan') {
            if (!isset($extraData['kegiatan_list'])) $extraData['kegiatan_list'] = [];
        } elseif ($validated['type'] === 'akuisisi') {
            if (!isset($extraData['tahapan_list'])) $extraData['tahapan_list'] = [];
        }

        if ($request->hasFile('extra_data_file_upload')) {
            if (isset($pengelolaan->extra_data['file'])) {
                Storage::disk('public')->delete($pengelolaan->extra_data['file']);
            }
            $extraData['file'] = $request->file('extra_data_file_upload')->store('features/pengelolaan/files', 'public');
        } elseif ($request->has('extra_data.file')) {
            $extraData['file'] = $request->input('extra_data.file');
        } elseif (isset($pengelolaan->extra_data['file'])) {
            Storage::disk('public')->delete($pengelolaan->extra_data['file']);
            unset($extraData['file']);
        }

        // 1. Fasilitas Images
        $existingFasilitas = $request->input('extra_data.existing_fasilitas_images', []);
        $removeFasilitas = $request->input('remove_fasilitas_images', []);
        $keptFasilitas = array_diff($existingFasilitas, $removeFasilitas);

        $oldFasilitas = !empty($pengelolaan->extra_data['fasilitas_images']) && is_array($pengelolaan->extra_data['fasilitas_images'])
            ? $pengelolaan->extra_data['fasilitas_images']
            : (!empty($pengelolaan->extra_data['fasilitas_image']) ? [$pengelolaan->extra_data['fasilitas_image']] : []);

        foreach ($oldFasilitas as $oldImg) {
            if (!in_array($oldImg, $keptFasilitas)) {
                Storage::disk('public')->delete($oldImg);
            }
        }

        $fasilitasImages = array_values($keptFasilitas);
        if ($request->hasFile('fasilitas_images')) {
            foreach ($request->file('fasilitas_images') as $image) {
                $fasilitasImages[] = $image->store('features/pengelolaan/fasilitas', 'public');
            }
        }
        if (!empty($fasilitasImages)) {
            $extraData['fasilitas_images'] = $fasilitasImages;
        } else {
            unset($extraData['fasilitas_images']);
            unset($extraData['fasilitas_image']);
        }

        // 2. Ruang Images
        $existingRuang = $request->input('extra_data.existing_ruang_images', []);
        $removeRuang = $request->input('remove_ruang_images', []);
        $keptRuang = array_diff($existingRuang, $removeRuang);

        $oldRuang = !empty($pengelolaan->extra_data['ruang_images']) && is_array($pengelolaan->extra_data['ruang_images'])
            ? $pengelolaan->extra_data['ruang_images']
            : (!empty($pengelolaan->extra_data['ruang_image']) ? [$pengelolaan->extra_data['ruang_image']] : []);

        foreach ($oldRuang as $oldImg) {
            if (!in_array($oldImg, $keptRuang)) {
                Storage::disk('public')->delete($oldImg);
            }
        }

        $ruangImages = array_values($keptRuang);
        if ($request->hasFile('ruang_images')) {
            foreach ($request->file('ruang_images') as $image) {
                $ruangImages[] = $image->store('features/pengelolaan/ruang', 'public');
            }
        }
        if (!empty($ruangImages)) {
            $extraData['ruang_images'] = $ruangImages;
        } else {
            unset($extraData['ruang_images']);
            unset($extraData['ruang_image']);
        }

        // Handle specific extra_data fields translations
        if (!empty($extraData['prinsip_title'])) {
            $extraData['prinsip_title_en'] = $this->translationService->translate($extraData['prinsip_title']);
        }
        if (!empty($extraData['prinsip_desc'])) {
            $extraData['prinsip_desc_en'] = $this->translationService->translate($extraData['prinsip_desc']);
        } elseif (!empty($extraData['prinsip_penyimpanan'])) {
            $extraData['prinsip_desc'] = $extraData['prinsip_penyimpanan'];
            $extraData['prinsip_desc_en'] = $this->translationService->translate($extraData['prinsip_penyimpanan']);
            unset($extraData['prinsip_penyimpanan']);
        }
        if (!empty($extraData['prinsip_list']) && is_array($extraData['prinsip_list'])) {
            foreach ($extraData['prinsip_list'] as $k => $v) {
                if (!empty($v['title'])) {
                    $extraData['prinsip_list'][$k]['title_en'] = $this->translationService->translate($v['title']);
                }
                if (!empty($v['desc'])) {
                    $extraData['prinsip_list'][$k]['desc_en'] = $this->translationService->translate($v['desc']);
                }
                if (!empty($v['text'])) {
                    $extraData['prinsip_list'][$k]['text_en'] = $this->translationService->translate($v['text']);
                }
            }
        }
        if (!empty($extraData['sistem_title'])) {
            $extraData['sistem_title_en'] = $this->translationService->translate($extraData['sistem_title']);
        }
        if (!empty($extraData['sistem_penyimpanan']) && is_array($extraData['sistem_penyimpanan'])) {
            foreach ($extraData['sistem_penyimpanan'] as $k => $v) {
                if (!empty($v['title'])) {
                    $extraData['sistem_penyimpanan'][$k]['title_en'] = $this->translationService->translate($v['title']);
                }
                if (!empty($v['desc'])) {
                    $extraData['sistem_penyimpanan'][$k]['desc_en'] = $this->translationService->translate($v['desc']);
                }
            }
        }
        if (!empty($extraData['fasilitas_title'])) {
            $extraData['fasilitas_title_en'] = $this->translationService->translate($extraData['fasilitas_title']);
        }
        if (!empty($extraData['ruang_title'])) {
            $extraData['ruang_title_en'] = $this->translationService->translate($extraData['ruang_title']);
        }
        if (!empty($extraData['fasilitas_list']) && is_array($extraData['fasilitas_list'])) {
            foreach ($extraData['fasilitas_list'] as $k => $v) {
                if (!empty($v['text'])) {
                    $extraData['fasilitas_list'][$k]['text_en'] = $this->translationService->translate($v['text']);
                }
            }
        }
        if (!empty($extraData['ruang_list']) && is_array($extraData['ruang_list'])) {
            foreach ($extraData['ruang_list'] as $k => $v) {
                if (!empty($v['text'])) {
                    $extraData['ruang_list'][$k]['text_en'] = $this->translationService->translate($v['text']);
                }
            }
        }
        if (!empty($extraData['preservasi_title'])) {
            $extraData['preservasi_title_en'] = $this->translationService->translate($extraData['preservasi_title']);
        }
        if (!empty($extraData['restorasi_title'])) {
            $extraData['restorasi_title_en'] = $this->translationService->translate($extraData['restorasi_title']);
        }
        if (!empty($extraData['pengolahan_title'])) {
            $extraData['pengolahan_title_en'] = $this->translationService->translate($extraData['pengolahan_title']);
        }
        if (!empty($extraData['akses_title'])) {
            $extraData['akses_title_en'] = $this->translationService->translate($extraData['akses_title']);
        }
        if (!empty($extraData['preservasi_list']) && is_array($extraData['preservasi_list'])) {
            foreach ($extraData['preservasi_list'] as $k => $v) {
                if (!empty($v['text'])) {
                    $extraData['preservasi_list'][$k]['text_en'] = $this->translationService->translate($v['text']);
                }
            }
        }
        if (!empty($extraData['restorasi_desc'])) {
            $extraData['restorasi_desc_en'] = $this->translationService->translate($extraData['restorasi_desc']);
        }
        if (!empty($extraData['restorasi_list']) && is_array($extraData['restorasi_list'])) {
            foreach ($extraData['restorasi_list'] as $k => $v) {
                if (!empty($v['text'])) {
                    $extraData['restorasi_list'][$k]['text_en'] = $this->translationService->translate($v['text']);
                }
            }
        }
        if (!empty($extraData['pengolahan_list']) && is_array($extraData['pengolahan_list'])) {
            foreach ($extraData['pengolahan_list'] as $k => $v) {
                if (!empty($v['text'])) {
                    $extraData['pengolahan_list'][$k]['text_en'] = $this->translationService->translate($v['text']);
                }
            }
        }
        if (!empty($extraData['mekanisme_title'])) {
            $extraData['mekanisme_title_en'] = $this->translationService->translate($extraData['mekanisme_title']);
        }
        if (!empty($extraData['mekanisme_desc'])) {
            $extraData['mekanisme_desc_en'] = $this->translationService->translate($extraData['mekanisme_desc']);
        }
        if (!empty($extraData['pemanfaatan_quote'])) {
            $extraData['pemanfaatan_quote_en'] = $this->translationService->translate($extraData['pemanfaatan_quote']);
        }
        if (!empty($extraData['akses_list']) && is_array($extraData['akses_list'])) {
            foreach ($extraData['akses_list'] as $k => $v) {
                if (!empty($v['title'])) {
                    $extraData['akses_list'][$k]['title_en'] = $this->translationService->translate($v['title']);
                }
                if (!empty($v['desc'])) {
                    $extraData['akses_list'][$k]['desc_en'] = $this->translationService->translate($v['desc']);
                }
            }
        }
        if (!empty($extraData['kegiatan_title'])) {
            $extraData['kegiatan_title_en'] = $this->translationService->translate($extraData['kegiatan_title']);
        }
        if (!empty($extraData['kegiatan_list']) && is_array($extraData['kegiatan_list'])) {
            foreach ($extraData['kegiatan_list'] as $k => $v) {
                if (!empty($v['title'])) {
                    $extraData['kegiatan_list'][$k]['title_en'] = $this->translationService->translate($v['title']);
                }
                if (!empty($v['desc'])) {
                    $extraData['kegiatan_list'][$k]['desc_en'] = $this->translationService->translate($v['desc']);
                }
                if (!empty($v['button_label'])) {
                    $extraData['kegiatan_list'][$k]['button_label_en'] = $this->translationService->translate($v['button_label']);
                }
            }
        }
        if (!empty($extraData['tahapan_list']) && is_array($extraData['tahapan_list'])) {
            foreach ($extraData['tahapan_list'] as $k => $v) {
                if (!empty($v['title'])) {
                    $extraData['tahapan_list'][$k]['title_en'] = $this->translationService->translate($v['title']);
                }
                if (!empty($v['desc'])) {
                    $extraData['tahapan_list'][$k]['desc_en'] = $this->translationService->translate($v['desc']);
                }
            }
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
            'published_at' => $validated['published_at'] ?? $pengelolaan->published_at,
            'images' => $imagePaths ?: null,
            'extra_data' => !empty($extraData) ? $extraData : null,
        ];

        $this->swapOrder($pengelolaan, (int) $validated['order'], (int) $pengelolaan->order, ['feature_id' => $feature->id]);
        $pengelolaan->update($data);

        return redirect()->route('cms.features.pengelolaan.index', $feature)
            ->with('success', __('cms.pengelolaan.flash.updated'));
    }

    /**
     * Delete a pengelolaan page.
     */
    public function destroy(Feature $feature, Pengelolaan $pengelolaan)
    {
        if ($pengelolaan->images) {
            foreach ($pengelolaan->images as $img) {
                Storage::disk('public')->delete($img);
            }
        }

        if (isset($pengelolaan->extra_data['file'])) {
            Storage::disk('public')->delete($pengelolaan->extra_data['file']);
        }

        $this->deleteAndShiftOrder($pengelolaan, ['feature_id' => $feature->id]);

        return redirect()->route('cms.features.pengelolaan.index', $feature)
            ->with('success', __('cms.pengelolaan.flash.deleted'));
    }
}
