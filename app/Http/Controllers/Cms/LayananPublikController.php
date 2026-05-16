<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use App\Models\LayananPublik;
use App\Services\TranslationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LayananPublikController extends Controller
{
    use \App\Traits\SwapsOrder;

    public function __construct(private TranslationService $translationService)
    {}

    /**
     * Show the layanan publik list for a sub-feature.
     */
    public function index(Feature $feature)
    {
        $feature->load('parent');
        $pages = $feature->layananPubliks()
            ->orderBy('order')
            ->get();

        return view('cms.features.layanan_publik.index', compact('feature', 'pages'));
    }

    /**
     * Show the form for creating a new layanan publik page.
     */
    public function create(Feature $feature)
    {
        $feature->load('parent');
        $nextOrder = $feature->layananPubliks()->max('order') + 1;
        return view('cms.features.layanan_publik.create', compact('feature', 'nextOrder'));
    }

    /**
     * Store a new layanan publik page.
     */
    public function store(Request $request, Feature $feature)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string|in:kunjungan,laraska,statis,konsultasi,perpustakaan,umum',
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
                $imagePaths[] = $image->store('features/layanan_publik', 'public');
            }
        }

        $extraData = $validated['extra_data'] ?? [];
        if ($request->hasFile('extra_data.file')) {
            $extraData['file'] = $request->file('extra_data.file')->store('features/layanan_publik/files', 'public');
        }

        if (!empty($extraData['jadwal_kunjungan'])) {
            $extraData['jadwal_kunjungan_en'] = $this->translationService->translate($extraData['jadwal_kunjungan']);
        }
        if (!empty($extraData['pengajuan_kunjungan'])) {
            $extraData['pengajuan_kunjungan_en'] = $this->translationService->translate($extraData['pengajuan_kunjungan']);
        }
        if (!empty($extraData['libur_dates']) && is_array($extraData['libur_dates'])) {
            foreach ($extraData['libur_dates'] as $k => $v) {
                if (!empty($v['reason'])) {
                    $extraData['libur_dates'][$k]['reason_en'] = $this->translationService->translate($v['reason']);
                }
            }
        }
        if (!empty($extraData['tutup_slots']) && is_array($extraData['tutup_slots'])) {
            $kuotaHarian = (int) ($extraData['kuota_harian'] ?? 4);
            $dateSums = [];
            foreach ($extraData['tutup_slots'] as $k => $v) {
                $date = $v['date'] ?? '';
                if ($date) {
                    if (!isset($dateSums[$date])) {
                        $dateSums[$date] = 0;
                    }
                    $q = (int) ($v['max_quota'] ?? 0);
                    if ($dateSums[$date] + $q > $kuotaHarian) {
                        $q = max(0, $kuotaHarian - $dateSums[$date]);
                    }
                    $dateSums[$date] += $q;
                    $extraData['tutup_slots'][$k]['max_quota'] = $q;
                }
                if (!empty($v['reason'])) {
                    $extraData['tutup_slots'][$k]['reason_en'] = $this->translationService->translate($v['reason']);
                }
            }
        }
        if (!empty($extraData['form_fields']) && is_array($extraData['form_fields'])) {
            foreach ($extraData['form_fields'] as $k => $v) {
                if (!empty($v['label'])) {
                    $extraData['form_fields'][$k]['label_en'] = $this->translationService->translate($v['label']);
                }
                if (!empty($v['options'])) {
                    $extraData['form_fields'][$k]['options_en'] = $this->translationService->translate($v['options']);
                }
            }
        }

        if (!empty($extraData['title_jadwal'])) {
            $extraData['title_jadwal_en'] = $this->translationService->translate($extraData['title_jadwal']);
        }
        if (!empty($extraData['title_pengajuan'])) {
            $extraData['title_pengajuan_en'] = $this->translationService->translate($extraData['title_pengajuan']);
        }
        if (isset($extraData['show_jadwal'])) {
            $extraData['show_jadwal'] = (int) $extraData['show_jadwal'];
        }
        if (isset($extraData['show_pengajuan'])) {
            $extraData['show_pengajuan'] = (int) $extraData['show_pengajuan'];
        }
        if (isset($extraData['show_kalender'])) {
            $extraData['show_kalender'] = (int) $extraData['show_kalender'];
        }
        if (isset($extraData['show_form'])) {
            $extraData['show_form'] = (int) $extraData['show_form'];
        }
        if (isset($extraData['auto_today_date'])) {
            $extraData['auto_today_date'] = (int) $extraData['auto_today_date'];
        } else {
            $extraData['auto_today_date'] = 0;
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
            'published_at' => !empty($extraData['auto_today_date']) ? now() : ($validated['published_at'] ?? now()),
            'order' => $validated['order'],
            'extra_data' => !empty($extraData) ? $extraData : null,
        ];

        $insertOrder = (int) $validated['order'];
        $this->insertAndShiftOrder(LayananPublik::class, $insertOrder, ['feature_id' => $feature->id], $data);

        return redirect()->route('cms.features.layanan_publik.index', $feature)
            ->with('success', __('cms.layanan_publik.flash.added'));
    }

    /**
     * Show the form for editing a layanan publik page.
     */
    public function edit(Feature $feature, LayananPublik $layananPublik)
    {
        $feature->load('parent');
        return view('cms.features.layanan_publik.edit', compact('feature', 'layananPublik'));
    }

    /**
     * Update a layanan publik page.
     */
    public function update(Request $request, Feature $feature, LayananPublik $layananPublik)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string|in:kunjungan,laraska,statis,konsultasi,perpustakaan,umum',
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
        $oldImages = $layananPublik->images ?? [];
        foreach ($oldImages as $oldImage) {
            if (! in_array($oldImage, $existingImages)) {
                Storage::disk('public')->delete($oldImage);
            }
        }

        $imagePaths = $existingImages;
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('features/layanan_publik', 'public');
            }
        }

        $extraData = $validated['extra_data'] ?? [];
        if ($request->hasFile('extra_data.file')) {
            if (isset($layananPublik->extra_data['file'])) {
                Storage::disk('public')->delete($layananPublik->extra_data['file']);
            }
            $extraData['file'] = $request->file('extra_data.file')->store('features/layanan_publik/files', 'public');
        } elseif (isset($layananPublik->extra_data['file'])) {
            $extraData['file'] = $layananPublik->extra_data['file'];
        }

        if (!empty($extraData['jadwal_kunjungan'])) {
            $extraData['jadwal_kunjungan_en'] = $this->translationService->translate($extraData['jadwal_kunjungan']);
        }
        if (!empty($extraData['pengajuan_kunjungan'])) {
            $extraData['pengajuan_kunjungan_en'] = $this->translationService->translate($extraData['pengajuan_kunjungan']);
        }
        if (!empty($extraData['libur_dates']) && is_array($extraData['libur_dates'])) {
            foreach ($extraData['libur_dates'] as $k => $v) {
                if (!empty($v['reason'])) {
                    $extraData['libur_dates'][$k]['reason_en'] = $this->translationService->translate($v['reason']);
                }
            }
        }
        if (!empty($extraData['tutup_slots']) && is_array($extraData['tutup_slots'])) {
            $kuotaHarian = (int) ($extraData['kuota_harian'] ?? 4);
            $dateSums = [];
            foreach ($extraData['tutup_slots'] as $k => $v) {
                $date = $v['date'] ?? '';
                if ($date) {
                    if (!isset($dateSums[$date])) {
                        $dateSums[$date] = 0;
                    }
                    $q = (int) ($v['max_quota'] ?? 0);
                    if ($dateSums[$date] + $q > $kuotaHarian) {
                        $q = max(0, $kuotaHarian - $dateSums[$date]);
                    }
                    $dateSums[$date] += $q;
                    $extraData['tutup_slots'][$k]['max_quota'] = $q;
                }
                if (!empty($v['reason'])) {
                    $extraData['tutup_slots'][$k]['reason_en'] = $this->translationService->translate($v['reason']);
                }
            }
        }
        if (!empty($extraData['form_fields']) && is_array($extraData['form_fields'])) {
            foreach ($extraData['form_fields'] as $k => $v) {
                if (!empty($v['label'])) {
                    $extraData['form_fields'][$k]['label_en'] = $this->translationService->translate($v['label']);
                }
                if (!empty($v['options'])) {
                    $extraData['form_fields'][$k]['options_en'] = $this->translationService->translate($v['options']);
                }
            }
        }

        if (!empty($extraData['title_jadwal'])) {
            $extraData['title_jadwal_en'] = $this->translationService->translate($extraData['title_jadwal']);
        }
        if (!empty($extraData['title_pengajuan'])) {
            $extraData['title_pengajuan_en'] = $this->translationService->translate($extraData['title_pengajuan']);
        }
        if (isset($extraData['show_jadwal'])) {
            $extraData['show_jadwal'] = (int) $extraData['show_jadwal'];
        }
        if (isset($extraData['show_pengajuan'])) {
            $extraData['show_pengajuan'] = (int) $extraData['show_pengajuan'];
        }
        if (isset($extraData['show_kalender'])) {
            $extraData['show_kalender'] = (int) $extraData['show_kalender'];
        }
        if (isset($extraData['show_form'])) {
            $extraData['show_form'] = (int) $extraData['show_form'];
        }
        if (isset($extraData['auto_today_date'])) {
            $extraData['auto_today_date'] = (int) $extraData['auto_today_date'];
        } else {
            $extraData['auto_today_date'] = 0;
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
            'published_at' => !empty($extraData['auto_today_date']) ? now() : ($validated['published_at'] ?? $layananPublik->published_at),
            'images' => $imagePaths ?: null,
            'extra_data' => !empty($extraData) ? $extraData : null,
        ];

        $this->swapOrder($layananPublik, (int) $validated['order'], (int) $layananPublik->order, ['feature_id' => $feature->id]);
        $layananPublik->update($data);

        return redirect()->route('cms.features.layanan_publik.index', $feature)
            ->with('success', __('cms.layanan_publik.flash.updated'));
    }

    /**
     * Delete a layanan publik page.
     */
    public function destroy(Feature $feature, LayananPublik $layananPublik)
    {
        if ($layananPublik->images) {
            foreach ($layananPublik->images as $img) {
                Storage::disk('public')->delete($img);
            }
        }

        if (isset($layananPublik->extra_data['file'])) {
            Storage::disk('public')->delete($layananPublik->extra_data['file']);
        }

        $this->deleteAndShiftOrder($layananPublik, ['feature_id' => $feature->id]);

        return redirect()->route('cms.features.layanan_publik.index', $feature)
            ->with('success', __('cms.layanan_publik.flash.deleted'));
    }
}
