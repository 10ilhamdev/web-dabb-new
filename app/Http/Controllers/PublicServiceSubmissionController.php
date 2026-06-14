<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VisitRegistration;
use App\Models\ArchivalConsultation;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class PublicServiceSubmissionController extends Controller
{
    public function storeVisit(Request $request)
    {
        $captchaAns = $request->input('captcha_ans');
        $expected = session('math_captcha_result');
        if (is_null($expected) || $captchaAns != $expected) {
            return response()->json([
                'success' => false,
                'message' => 'Jawaban Captcha salah! Silakan coba lagi.'
            ]);
        }

        $all = $request->all();

        // Helper to find field by keywords
        $findField = function($keywords, $default = null) use ($all) {
            foreach ($keywords as $kw) {
                if (isset($all[$kw]) && !empty($all[$kw])) return $all[$kw];
                foreach ($all as $k => $v) {
                    if (str_contains(strtolower($k), $kw) && !empty($v) && !is_array($v)) return $v;
                }
            }
            return $default;
        };

        $name = $findField(['name', 'nama', 'pengunjung', 'lengkap'], 'Pengunjung ' . date('Y-m-d H:i'));
        $email = $findField(['email', 'surel', 'mail'], 'visitor@example.com');
        $phone = $findField(['phone', 'telepon', 'telp', 'wa', 'whatsapp', 'hp'], null);
        $institution = $findField(['institution', 'instansi', 'asal', 'perusahaan', 'sekolah', 'kampus'], 'Umum');
        $position = $findField(['position', 'jabatan', 'pekerjaan', 'posisi', 'status'], 'Masyarakat Umum');
        $visitDate = $findField(['visit_date', 'date', 'tanggal', 'tgl', 'waktu'], Carbon::now()->toDateString());
        $visitTime = $findField(['visit_time', 'time', 'jam', 'sesi'], 'pagi');
        $visitorCount = (int) $findField(['visitor_count', 'count', 'jumlah', 'jml', 'peserta'], 1);
        $visitPurpose = $findField(['visit_purpose', 'purpose', 'keperluan', 'tujuan', 'maksud'], 'edukasi');

        // Normalize inputs
        $visitTime = str_contains(strtolower($visitTime), 'siang') ? 'siang' : 'pagi';
        try {
            $visitDate = Carbon::parse($visitDate)->toDateString();
        } catch (\Exception $e) {
            $visitDate = Carbon::now()->toDateString();
        }

        // Validate Date & Time
        $currentPage = \App\Models\LayananPublik::where('type', 'kunjungan')->first() 
            ?? \App\Models\LayananPublik::where('title', 'like', '%kunjungan%')->first();

        if ($currentPage) {
            $extraData = $currentPage->extra_data ?? [];

            // 1. Weekend Check (Saturday & Sunday)
            $carbonDate = Carbon::parse($visitDate);
            $dayOfWeek = $carbonDate->dayOfWeek; // 0 = Sunday, 6 = Saturday
            $isWeekend = ($dayOfWeek === 0 || $dayOfWeek === 6);

            // 2. Google Calendar Holidays Check
            $year = $carbonDate->year;
            $googleHolidays = array_merge(
                \App\Services\GoogleCalendarHolidayService::getHolidays($year - 1),
                \App\Services\GoogleCalendarHolidayService::getHolidays($year),
                \App\Services\GoogleCalendarHolidayService::getHolidays($year + 1)
            );
            $isGoogleHoliday = isset($googleHolidays[$visitDate]);

            // 3. Custom Holidays Check
            $customLibur = !empty($extraData['libur_dates']) 
                ? collect($extraData['libur_dates'])->pluck('reason', 'date')->toArray() 
                : [];
            $isCustomHoliday = isset($customLibur[$visitDate]);

            if ($isWeekend || $isGoogleHoliday || $isCustomHoliday) {
                $reason = $isWeekend 
                    ? 'Akhir Pekan' 
                    : ($googleHolidays[$visitDate] ?? $customLibur[$visitDate] ?? 'Hari Libur / Tutup');
                return response()->json([
                    'success' => false,
                    'message' => 'Pendaftaran gagal: Tanggal ' . Carbon::parse($visitDate)->format('d M Y') . ' adalah hari libur (' . $reason . ').'
                ]);
            }

            // 4. Closed Slots Check
            $tutupSlots = !empty($extraData['tutup_slots']) 
                ? collect($extraData['tutup_slots'])->groupBy('date')->toArray() 
                : [];

            $maxQuota = null;
            $usePerSessionQuota = false;

            if (isset($tutupSlots[$visitDate])) {
                $tutupInfo = $tutupSlots[$visitDate];
                $tutupSlotTypes = collect($tutupInfo)->pluck('slot')->toArray();
                $fullSlot = collect($tutupInfo)->firstWhere('slot', 'full');
                $pagiSlot = collect($tutupInfo)->firstWhere('slot', 'pagi');
                $siangSlot = collect($tutupInfo)->firstWhere('slot', 'siang');

                $isFullClosed = (
                    ($fullSlot && $fullSlot['max_quota'] == 0) ||
                    (in_array('full', $tutupSlotTypes) && (!$fullSlot || $fullSlot['max_quota'] == 0)) ||
                    (in_array('pagi', $tutupSlotTypes) && (!$pagiSlot || $pagiSlot['max_quota'] == 0) && in_array('siang', $tutupSlotTypes) && (!$siangSlot || $siangSlot['max_quota'] == 0))
                );

                if ($isFullClosed) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Pendaftaran gagal: Tanggal ' . Carbon::parse($visitDate)->format('d M Y') . ' ditutup penuh.'
                    ]);
                }

                // Check slot specific close
                if ($visitTime === 'pagi' && (
                    ($fullSlot && $fullSlot['max_quota'] == 0) ||
                    ($pagiSlot && $pagiSlot['max_quota'] == 0) ||
                    in_array('full', $tutupSlotTypes)
                )) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Pendaftaran gagal: Slot pagi pada tanggal ' . Carbon::parse($visitDate)->format('d M Y') . ' telah ditutup.'
                    ]);
                }

                if ($visitTime === 'siang' && (
                    ($fullSlot && $fullSlot['max_quota'] == 0) ||
                    ($siangSlot && $siangSlot['max_quota'] == 0) ||
                    in_array('full', $tutupSlotTypes)
                )) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Pendaftaran gagal: Slot siang pada tanggal ' . Carbon::parse($visitDate)->format('d M Y') . ' telah ditutup.'
                    ]);
                }

                // Per-session custom quota from 3c tutup_slots
                if ($visitTime === 'pagi' && $pagiSlot) {
                    $maxQuota = (int) $pagiSlot['max_quota'];
                    $usePerSessionQuota = true;
                } elseif ($visitTime === 'siang' && $siangSlot) {
                    $maxQuota = (int) $siangSlot['max_quota'];
                    $usePerSessionQuota = true;
                }
            }

            // 5. Quota Check
            if ($maxQuota === null) {
                // No tutup_slots override -> use kuota_harian as TOTAL daily quota (3b)
                // pagi and siang SHARE the same pool
                $maxQuota = (int) ($extraData['kuota_harian'] ?? 5);
                $usePerSessionQuota = false;
            }

            // Count existing bookings (exclude rejected)
            if ($usePerSessionQuota) {
                // Per-session: only count this specific time slot (from 3c override)
                $currentBooked = \App\Models\VisitRegistration::whereDate('visit_date', $visitDate)
                    ->where('visit_time', $visitTime)
                    ->whereNotIn('status', ['rejected'])
                    ->sum('visitor_count');
            } else {
                // Total daily: count ALL sessions (pagi + siang combined) against kuota_harian
                $currentBooked = \App\Models\VisitRegistration::whereDate('visit_date', $visitDate)
                    ->whereNotIn('status', ['rejected'])
                    ->sum('visitor_count');
            }

            $remainingQuota = max(0, $maxQuota - $currentBooked);

            if ($visitorCount > $remainingQuota) {
                $slotLabel = $visitTime === 'pagi' ? 'pagi' : 'siang';
                $quotaLabel = $usePerSessionQuota ? 'slot ' . $slotLabel : 'hari ini';
                return response()->json([
                    'success' => false,
                    'message' => 'Pendaftaran gagal: Jumlah peserta (' . $visitorCount . ' orang) melebihi sisa kuota ' . $quotaLabel . ' (' . $remainingQuota . ' orang tersisa, limit maksimal ' . $maxQuota . ').'
                ]);
            }
        }

        $suratPath = null;
        if ($request->hasFile('surat_file')) {
            $suratPath = $request->file('surat_file')->store('service_submissions', 'public');
        } else {
            // Check if any other file was uploaded
            foreach ($request->allFiles() as $file) {
                $suratPath = $file->store('service_submissions', 'public');
                break;
            }
        }

        // Collect dynamic fields
        $except = ['_token', 'g-recaptcha-response', 'captcha_ans', 'surat_file'];
        $formData = $request->except($except);

        VisitRegistration::create([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'institution' => $institution,
            'position' => $position,
            'visit_date' => $visitDate,
            'visit_time' => $visitTime,
            'visitor_count' => $visitorCount > 0 ? $visitorCount : 1,
            'visit_purpose' => $visitPurpose,
            'surat_file' => $suratPath,
            'form_data' => !empty($formData) ? $formData : null,
        ]);

        return response()->json([
            'success' => true,
            'message' => __('home.layanan_publik.form_success_message', ['default' => 'Formulir berhasil dikirim!']),
        ]);
    }

    public function storeConsultation(Request $request)
    {
        $all = $request->all();

        $findField = function($keywords, $default = null) use ($all) {
            foreach ($keywords as $kw) {
                if (isset($all[$kw]) && !empty($all[$kw])) return $all[$kw];
                foreach ($all as $k => $v) {
                    if (str_contains(strtolower($k), $kw) && !empty($v) && !is_array($v)) return $v;
                }
            }
            return $default;
        };

        $name = $findField(['name', 'nama', 'pengonsultasi', 'lengkap'], 'Konsultan ' . date('Y-m-d H:i'));
        $institution = $findField(['institution', 'instansi', 'asal', 'perusahaan', 'organisasi'], 'Umum');
        $email = $findField(['email', 'surel', 'mail'], 'consultant@example.com');
        $detail = $findField(['detail', 'pesan', 'pertanyaan', 'topik', 'masalah', 'konsultasi', 'keterangan'], 'Konsultasi kearsipan umum.');

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('consultations', 'public');
        } else {
            foreach ($request->allFiles() as $file) {
                $attachmentPath = $file->store('consultations', 'public');
                break;
            }
        }

        $except = ['_token', 'g-recaptcha-response', 'attachment'];
        $formData = $request->except($except);

        ArchivalConsultation::create([
            'name' => $name,
            'institution' => $institution,
            'email' => $email,
            'detail' => $detail,
            'attachment' => $attachmentPath,
            'form_data' => !empty($formData) ? $formData : null,
        ]);

        return response()->json([
            'success' => true,
            'message' => __('home.layanan_publik.consultation_success', ['default' => 'Formulir konsultasi berhasil dikirim!']),
        ]);
    }
}
