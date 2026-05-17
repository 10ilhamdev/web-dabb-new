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
        $except = ['_token', 'g-recaptcha-response', 'surat_file'];
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
