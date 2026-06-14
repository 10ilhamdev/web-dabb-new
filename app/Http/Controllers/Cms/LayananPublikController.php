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
     * Ensure all array extra_data keys are always initialized to [] so that
     * intentionally emptied arrays (e.g. user deleted all form_fields) are
     * saved and re-appear as empty on next edit — not filled with defaults.
     */
    private function initExtraDataArrays(array $extraData): array
    {
        // All array-type extra_data keys must be initialized so that
        // intentionally emptied arrays (user deleted all items) are
        // persisted to the DB and reappear as empty (not filled with defaults).
        $arrayKeys = [
            'laraska_steps', 'statis_stages', 'statis_mech1_steps', 'statis_mech2_steps',
            'form_fields', 'consultation_form_fields', 'libur_dates', 'tutup_slots',
            'lib_objs', 'lib_cards', 'lib_rules', 'lib_procs', 'lib_photos',
        ];
        foreach ($arrayKeys as $key) {
            if (!isset($extraData[$key]) || !is_array($extraData[$key])) {
                $extraData[$key] = [];
            }
        }
        // lib_photos_names must also be preserved when cleared
        if (!isset($extraData['lib_photos_names'])) {
            $extraData['lib_photos_names'] = '';
        }
        return $extraData;
    }

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
        $extraData = $this->initExtraDataArrays($extraData);
        if ($request->hasFile('extra_data_file_upload')) {
            $extraData['file'] = $request->file('extra_data_file_upload')->store('features/layanan_publik/files', 'public');
        }
        if ($request->hasFile('extra_data_statis_direct_pdf')) {
            $extraData['statis_direct_pdf'] = $request->file('extra_data_statis_direct_pdf')->store('features/layanan_publik/files', 'public');
        }
        if ($request->hasFile('extra_data_statis_indirect_pdf')) {
            $extraData['statis_indirect_pdf'] = $request->file('extra_data_statis_indirect_pdf')->store('features/layanan_publik/files', 'public');
        }
        if ($request->hasFile('extra_data_lib_pdf')) {
            $extraData['lib_pdf'] = $request->file('extra_data_lib_pdf')->store('features/layanan_publik/files', 'public');
        }
        if ($request->hasFile('extra_data_lib_photos')) {
            $libPhotos = [];
            foreach ($request->file('extra_data_lib_photos') as $photo) {
                $libPhotos[] = $photo->store('features/layanan_publik/photos', 'public');
            }
            if (!empty($libPhotos)) {
                $extraData['lib_photos'] = $libPhotos;
            }
        }

        if ($validated['type'] === 'laraska') {
            if (!isset($extraData['laraska_steps'])) {
                $extraData['laraska_steps'] = [];
            } elseif (is_array($extraData['laraska_steps'])) {
                foreach ($extraData['laraska_steps'] as $k => $v) {
                    if (!empty($v['title'])) {
                        $extraData['laraska_steps'][$k]['title_en'] = $this->translationService->translate($v['title']);
                    }
                    if (!empty($v['desc'])) {
                        $extraData['laraska_steps'][$k]['desc_en'] = $this->translationService->translate($v['desc']);
                    }
                }
            }
        } elseif ($validated['type'] === 'statis') {
            $statisFields = [
                'statis_hours', 'statis_order_hours', 'statis_stage1', 'statis_stage2', 'statis_stage3', 'statis_stage4', 'statis_stage5',
                'statis_mech1_title', 'statis_mech1_req_title', 'statis_mech1_req_desc', 'statis_mech1_stage_title', 'statis_mech1_stage_desc',
                'statis_mech2_title', 'statis_mech2_online_title', 'statis_mech2_online_desc', 'statis_mech2_send_title', 'statis_mech2_send_desc'
            ];
            foreach ($statisFields as $sf) {
                if (!empty($extraData[$sf])) {
                    $extraData[$sf . '_en'] = $this->translationService->translate($extraData[$sf]);
                }
            }
            if (!isset($extraData['statis_stages'])) {
                $extraData['statis_stages'] = [];
            } elseif (is_array($extraData['statis_stages'])) {
                foreach ($extraData['statis_stages'] as $k => $v) {
                    if (!empty($v['title'])) {
                        $extraData['statis_stages'][$k]['title_en'] = $this->translationService->translate($v['title']);
                    }
                }
            }
            if (!isset($extraData['statis_mech1_steps'])) {
                $extraData['statis_mech1_steps'] = [];
            } elseif (is_array($extraData['statis_mech1_steps'])) {
                foreach ($extraData['statis_mech1_steps'] as $k => $v) {
                    if (!empty($v['title'])) {
                        $extraData['statis_mech1_steps'][$k]['title_en'] = $this->translationService->translate($v['title']);
                    }
                    if (!empty($v['desc'])) {
                        $extraData['statis_mech1_steps'][$k]['desc_en'] = $this->translationService->translate($v['desc']);
                    }
                }
            }
            if (!isset($extraData['statis_mech2_steps'])) {
                $extraData['statis_mech2_steps'] = [];
            } elseif (is_array($extraData['statis_mech2_steps'])) {
                foreach ($extraData['statis_mech2_steps'] as $k => $v) {
                    if (!empty($v['title'])) {
                        $extraData['statis_mech2_steps'][$k]['title_en'] = $this->translationService->translate($v['title']);
                    }
                    if (!empty($v['desc'])) {
                        $extraData['statis_mech2_steps'][$k]['desc_en'] = $this->translationService->translate($v['desc']);
                    }
                }
            }
        } elseif ($validated['type'] === 'konsultasi') {
            $konsultasiFields = [
                'consultation_desc', 'consultation_form_title', 'consultation_form_send', 'consultation_success'
            ];
            foreach ($konsultasiFields as $kf) {
                if (!empty($extraData[$kf])) {
                    $extraData[$kf . '_en'] = $this->translationService->translate($extraData[$kf]);
                }
            }
        } elseif ($validated['type'] === 'perpustakaan') {
            $libFields = [
                'lib_obj1', 'lib_obj2', 'lib_obj3', 'lib_visit_btn',
                'lib_card1_title', 'lib_card1_desc', 'lib_card2_title', 'lib_card2_desc', 'lib_card3_title', 'lib_card3_desc',
                'lib_hours', 'lib_rule1', 'lib_rule2', 'lib_rule3',
                'lib_proc_title', 'lib_proc1_title', 'lib_proc1_desc', 'lib_proc2_title', 'lib_proc2_desc', 'lib_proc3_title', 'lib_proc3_desc', 'lib_proc4_title', 'lib_proc4_desc'
            ];
            foreach ($libFields as $lf) {
                if (!empty($extraData[$lf])) {
                    $extraData[$lf . '_en'] = $this->translationService->translate($extraData[$lf]);
                }
            }
            if (!isset($extraData['lib_objs'])) {
                $extraData['lib_objs'] = [];
            } elseif (is_array($extraData['lib_objs'])) {
                foreach ($extraData['lib_objs'] as $k => $v) {
                    if (!empty($v['text'])) {
                        $extraData['lib_objs'][$k]['text_en'] = $this->translationService->translate($v['text']);
                    }
                }
            }
            if (!isset($extraData['lib_cards'])) {
                $extraData['lib_cards'] = [];
            } elseif (is_array($extraData['lib_cards'])) {
                foreach ($extraData['lib_cards'] as $k => $v) {
                    if (!empty($v['title'])) {
                        $extraData['lib_cards'][$k]['title_en'] = $this->translationService->translate($v['title']);
                    }
                    if (!empty($v['desc'])) {
                        $extraData['lib_cards'][$k]['desc_en'] = $this->translationService->translate($v['desc']);
                    }
                }
            }
            if (!isset($extraData['lib_rules'])) {
                $extraData['lib_rules'] = [];
            } elseif (is_array($extraData['lib_rules'])) {
                foreach ($extraData['lib_rules'] as $k => $v) {
                    if (!empty($v['text'])) {
                        $extraData['lib_rules'][$k]['text_en'] = $this->translationService->translate($v['text']);
                    }
                }
            }
            if (!isset($extraData['lib_procs'])) {
                $extraData['lib_procs'] = [];
            } elseif (is_array($extraData['lib_procs'])) {
                foreach ($extraData['lib_procs'] as $k => $v) {
                    if (!empty($v['title'])) {
                        $extraData['lib_procs'][$k]['title_en'] = $this->translationService->translate($v['title']);
                    }
                    if (!empty($v['desc'])) {
                        $extraData['lib_procs'][$k]['desc_en'] = $this->translationService->translate($v['desc']);
                    }
                }
            }
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
        if (!empty($extraData['consultation_form_fields']) && is_array($extraData['consultation_form_fields'])) {
            foreach ($extraData['consultation_form_fields'] as $k => $v) {
                if (!empty($v['label'])) {
                    $extraData['consultation_form_fields'][$k]['label_en'] = $this->translationService->translate($v['label']);
                }
                if (!empty($v['options'])) {
                    $extraData['consultation_form_fields'][$k]['options_en'] = $this->translationService->translate($v['options']);
                }
                if (!empty($v['placeholder'])) {
                    $extraData['consultation_form_fields'][$k]['placeholder_en'] = $this->translationService->translate($v['placeholder']);
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
        if (isset($extraData['show_consultation_form'])) {
            $extraData['show_consultation_form'] = (int) $extraData['show_consultation_form'];
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
        $extraData = $this->initExtraDataArrays($extraData);
        if ($request->hasFile('extra_data_file_upload')) {
            if (isset($layananPublik->extra_data['file'])) {
                Storage::disk('public')->delete($layananPublik->extra_data['file']);
            }
            $extraData['file'] = $request->file('extra_data_file_upload')->store('features/layanan_publik/files', 'public');
        } elseif ($request->has('extra_data.file')) {
            $extraData['file'] = $request->input('extra_data.file');
        } elseif (isset($layananPublik->extra_data['file'])) {
            Storage::disk('public')->delete($layananPublik->extra_data['file']);
            unset($extraData['file']);
        }

        if ($request->hasFile('extra_data_statis_direct_pdf')) {
            if (isset($layananPublik->extra_data['statis_direct_pdf'])) {
                Storage::disk('public')->delete($layananPublik->extra_data['statis_direct_pdf']);
            }
            $extraData['statis_direct_pdf'] = $request->file('extra_data_statis_direct_pdf')->store('features/layanan_publik/files', 'public');
        } elseif ($request->has('extra_data.statis_direct_pdf')) {
            $extraData['statis_direct_pdf'] = $request->input('extra_data.statis_direct_pdf');
        } elseif (isset($layananPublik->extra_data['statis_direct_pdf'])) {
            Storage::disk('public')->delete($layananPublik->extra_data['statis_direct_pdf']);
            unset($extraData['statis_direct_pdf']);
        }

        if ($request->hasFile('extra_data_statis_indirect_pdf')) {
            if (isset($layananPublik->extra_data['statis_indirect_pdf'])) {
                Storage::disk('public')->delete($layananPublik->extra_data['statis_indirect_pdf']);
            }
            $extraData['statis_indirect_pdf'] = $request->file('extra_data_statis_indirect_pdf')->store('features/layanan_publik/files', 'public');
        } elseif ($request->has('extra_data.statis_indirect_pdf')) {
            $extraData['statis_indirect_pdf'] = $request->input('extra_data.statis_indirect_pdf');
        } elseif (isset($layananPublik->extra_data['statis_indirect_pdf'])) {
            Storage::disk('public')->delete($layananPublik->extra_data['statis_indirect_pdf']);
            unset($extraData['statis_indirect_pdf']);
        }

        if ($request->hasFile('extra_data_lib_pdf')) {
            if (isset($layananPublik->extra_data['lib_pdf'])) {
                Storage::disk('public')->delete($layananPublik->extra_data['lib_pdf']);
            }
            $extraData['lib_pdf'] = $request->file('extra_data_lib_pdf')->store('features/layanan_publik/files', 'public');
        } elseif ($request->has('extra_data.lib_pdf')) {
            $extraData['lib_pdf'] = $request->input('extra_data.lib_pdf');
        } elseif (isset($layananPublik->extra_data['lib_pdf'])) {
            Storage::disk('public')->delete($layananPublik->extra_data['lib_pdf']);
            unset($extraData['lib_pdf']);
        }

        $oldLibPhotos = $layananPublik->extra_data['lib_photos'] ?? [];
        if (!is_array($oldLibPhotos)) {
            $oldLibPhotos = [];
        }
        if (isset($layananPublik->extra_data['lib_photo1']) && !in_array($layananPublik->extra_data['lib_photo1'], $oldLibPhotos)) {
            $oldLibPhotos[] = $layananPublik->extra_data['lib_photo1'];
        }
        if (isset($layananPublik->extra_data['lib_photo2']) && !in_array($layananPublik->extra_data['lib_photo2'], $oldLibPhotos)) {
            $oldLibPhotos[] = $layananPublik->extra_data['lib_photo2'];
        }

        $existingLibPhotos = $request->input('extra_data.existing_lib_photos', []);
        if (!is_array($existingLibPhotos)) {
            $existingLibPhotos = [];
        }

        foreach ($oldLibPhotos as $oldPhoto) {
            if (!in_array($oldPhoto, $existingLibPhotos)) {
                Storage::disk('public')->delete($oldPhoto);
            }
        }

        $libPhotos = $existingLibPhotos;
        if ($request->hasFile('extra_data_lib_photos')) {
            foreach ($request->file('extra_data_lib_photos') as $photo) {
                $libPhotos[] = $photo->store('features/layanan_publik/photos', 'public');
            }
        }

        if (!empty($libPhotos)) {
            $extraData['lib_photos'] = $libPhotos;
        } else {
            unset($extraData['lib_photos']);
        }
        unset($extraData['lib_photo1'], $extraData['lib_photo2']);

        if ($validated['type'] === 'laraska') {
            if (!isset($extraData['laraska_steps'])) {
                $extraData['laraska_steps'] = [];
            } elseif (is_array($extraData['laraska_steps'])) {
                foreach ($extraData['laraska_steps'] as $k => $v) {
                    if (!empty($v['title'])) {
                        $extraData['laraska_steps'][$k]['title_en'] = $this->translationService->translate($v['title']);
                    }
                    if (!empty($v['desc'])) {
                        $extraData['laraska_steps'][$k]['desc_en'] = $this->translationService->translate($v['desc']);
                    }
                }
            }
        } elseif ($validated['type'] === 'statis') {
            $statisFields = [
                'statis_hours', 'statis_order_hours', 'statis_stage1', 'statis_stage2', 'statis_stage3', 'statis_stage4', 'statis_stage5',
                'statis_mech1_title', 'statis_mech1_req_title', 'statis_mech1_req_desc', 'statis_mech1_stage_title', 'statis_mech1_stage_desc',
                'statis_mech2_title', 'statis_mech2_online_title', 'statis_mech2_online_desc', 'statis_mech2_send_title', 'statis_mech2_send_desc'
            ];
            foreach ($statisFields as $sf) {
                if (!empty($extraData[$sf])) {
                    $extraData[$sf . '_en'] = $this->translationService->translate($extraData[$sf]);
                }
            }
            if (!isset($extraData['statis_stages'])) {
                $extraData['statis_stages'] = [];
            } elseif (is_array($extraData['statis_stages'])) {
                foreach ($extraData['statis_stages'] as $k => $v) {
                    if (!empty($v['title'])) {
                        $extraData['statis_stages'][$k]['title_en'] = $this->translationService->translate($v['title']);
                    }
                }
            }
            if (!isset($extraData['statis_mech1_steps'])) {
                $extraData['statis_mech1_steps'] = [];
            } elseif (is_array($extraData['statis_mech1_steps'])) {
                foreach ($extraData['statis_mech1_steps'] as $k => $v) {
                    if (!empty($v['title'])) {
                        $extraData['statis_mech1_steps'][$k]['title_en'] = $this->translationService->translate($v['title']);
                    }
                    if (!empty($v['desc'])) {
                        $extraData['statis_mech1_steps'][$k]['desc_en'] = $this->translationService->translate($v['desc']);
                    }
                }
            }
            if (!isset($extraData['statis_mech2_steps'])) {
                $extraData['statis_mech2_steps'] = [];
            } elseif (is_array($extraData['statis_mech2_steps'])) {
                foreach ($extraData['statis_mech2_steps'] as $k => $v) {
                    if (!empty($v['title'])) {
                        $extraData['statis_mech2_steps'][$k]['title_en'] = $this->translationService->translate($v['title']);
                    }
                    if (!empty($v['desc'])) {
                        $extraData['statis_mech2_steps'][$k]['desc_en'] = $this->translationService->translate($v['desc']);
                    }
                }
            }
        } elseif ($validated['type'] === 'konsultasi') {
            $konsultasiFields = [
                'consultation_desc', 'consultation_form_title', 'consultation_form_send', 'consultation_success'
            ];
            foreach ($konsultasiFields as $kf) {
                if (!empty($extraData[$kf])) {
                    $extraData[$kf . '_en'] = $this->translationService->translate($extraData[$kf]);
                }
            }
        } elseif ($validated['type'] === 'perpustakaan') {
            $libFields = [
                'lib_obj1', 'lib_obj2', 'lib_obj3', 'lib_visit_btn',
                'lib_card1_title', 'lib_card1_desc', 'lib_card2_title', 'lib_card2_desc', 'lib_card3_title', 'lib_card3_desc',
                'lib_hours', 'lib_rule1', 'lib_rule2', 'lib_rule3',
                'lib_proc_title', 'lib_proc1_title', 'lib_proc1_desc', 'lib_proc2_title', 'lib_proc2_desc', 'lib_proc3_title', 'lib_proc3_desc', 'lib_proc4_title', 'lib_proc4_desc'
            ];
            foreach ($libFields as $lf) {
                if (!empty($extraData[$lf])) {
                    $extraData[$lf . '_en'] = $this->translationService->translate($extraData[$lf]);
                }
            }
            if (!isset($extraData['lib_objs'])) {
                $extraData['lib_objs'] = [];
            } elseif (is_array($extraData['lib_objs'])) {
                foreach ($extraData['lib_objs'] as $k => $v) {
                    if (!empty($v['text'])) {
                        $extraData['lib_objs'][$k]['text_en'] = $this->translationService->translate($v['text']);
                    }
                }
            }
            if (!isset($extraData['lib_cards'])) {
                $extraData['lib_cards'] = [];
            } elseif (is_array($extraData['lib_cards'])) {
                foreach ($extraData['lib_cards'] as $k => $v) {
                    if (!empty($v['title'])) {
                        $extraData['lib_cards'][$k]['title_en'] = $this->translationService->translate($v['title']);
                    }
                    if (!empty($v['desc'])) {
                        $extraData['lib_cards'][$k]['desc_en'] = $this->translationService->translate($v['desc']);
                    }
                }
            }
            if (!isset($extraData['lib_rules'])) {
                $extraData['lib_rules'] = [];
            } elseif (is_array($extraData['lib_rules'])) {
                foreach ($extraData['lib_rules'] as $k => $v) {
                    if (!empty($v['text'])) {
                        $extraData['lib_rules'][$k]['text_en'] = $this->translationService->translate($v['text']);
                    }
                }
            }
            if (!isset($extraData['lib_procs'])) {
                $extraData['lib_procs'] = [];
            } elseif (is_array($extraData['lib_procs'])) {
                foreach ($extraData['lib_procs'] as $k => $v) {
                    if (!empty($v['title'])) {
                        $extraData['lib_procs'][$k]['title_en'] = $this->translationService->translate($v['title']);
                    }
                    if (!empty($v['desc'])) {
                        $extraData['lib_procs'][$k]['desc_en'] = $this->translationService->translate($v['desc']);
                    }
                }
            }
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
        if (!empty($extraData['consultation_form_fields']) && is_array($extraData['consultation_form_fields'])) {
            foreach ($extraData['consultation_form_fields'] as $k => $v) {
                if (!empty($v['label'])) {
                    $extraData['consultation_form_fields'][$k]['label_en'] = $this->translationService->translate($v['label']);
                }
                if (!empty($v['options'])) {
                    $extraData['consultation_form_fields'][$k]['options_en'] = $this->translationService->translate($v['options']);
                }
                if (!empty($v['placeholder'])) {
                    $extraData['consultation_form_fields'][$k]['placeholder_en'] = $this->translationService->translate($v['placeholder']);
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
        if (isset($extraData['show_consultation_form'])) {
            $extraData['show_consultation_form'] = (int) $extraData['show_consultation_form'];
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

        $fileKeys = ['file', 'statis_direct_pdf', 'statis_indirect_pdf', 'lib_pdf', 'lib_photo1', 'lib_photo2'];
        foreach ($fileKeys as $fk) {
            if (isset($layananPublik->extra_data[$fk])) {
                Storage::disk('public')->delete($layananPublik->extra_data[$fk]);
            }
        }
        if (isset($layananPublik->extra_data['lib_photos']) && is_array($layananPublik->extra_data['lib_photos'])) {
            foreach ($layananPublik->extra_data['lib_photos'] as $lp) {
                Storage::disk('public')->delete($lp);
            }
        }

        $this->deleteAndShiftOrder($layananPublik, ['feature_id' => $feature->id]);

        return redirect()->route('cms.features.layanan_publik.index', $feature)
            ->with('success', __('cms.layanan_publik.flash.deleted'));
    }

    /**
     * Toggle visibility (is_active) of a layanan publik page.
     */
    public function toggleVisibility(Feature $feature, LayananPublik $layananPublik)
    {
        $layananPublik->update(['is_active' => !$layananPublik->is_active]);

        $msg = $layananPublik->is_active
            ? __('cms.common.flash.shown', ['name' => $layananPublik->title])
            : __('cms.common.flash.hidden', ['name' => $layananPublik->title]);

        return redirect()->back()->with('success', $msg);
    }
}
