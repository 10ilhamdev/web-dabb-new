<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function getBotResponse(Request $request)
    {
        $message = $request->input('message');

        if (! $message) {
            return response()->json(['reply' => 'Maaf, saya tidak mengerti maksud Anda.']);
        }

        try {
            // Api key dari ai studio Google Gemini (Disimpan di .env)
            $apiKey = env('GEMINI_API_KEY');
            $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key={$apiKey}";

            $systemInstruction = "Anda adalah Customer Support AI ramah berbahasa Indonesia dari DABB (Depot Arsip Berkelanjutan Bandung), instansi di bawah Arsip Nasional Republik Indonesia (ANRI). Tugas Anda menjawab pertanyaan pengunjung website. \nInformasi penting DABB:\n- Jam Operasional: Senin - Kamis, 07:30 - 16:00 WIB., Jum'at, 07:30 - 16:30 WIB,(Sabtu, Minggu, dan Hari Libur Nasional tutup).\n- Layanan: Layanan Baca Arsip, Konsultasi Kearsipan, Perawatan Arsip Keluarga (LARASKA), dan Pameran Arsip (onsite/virtual).\n- Biaya: Seluruh layanan GRATIS.\n- Cara Akses: Datang langsung ke Ruang Baca DABB atau daftar online.\nJawablah dengan ringkas, akurat, dan langsung ke intinya (jangan selalu mengulang salam pembuka jika tidak perlu).";
            $promptText = "Instruksi Sistem: {$systemInstruction}\n\nPertanyaan Pengunjung: {$message}\n\nJawaban AI:";

            // The user explicitly requested withoutVerifying() to bypass SSL issues on their local machine
            $response = Http::withoutVerifying()
                ->timeout(30)
                ->post($endpoint, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $promptText],
                            ],
                        ],
                    ],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'maxOutputTokens' => 1024,
                    ],
                ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    $replyText = $data['candidates'][0]['content']['parts'][0]['text'];
                    $replyText = str_replace(['**', '*'], '', $replyText); // Strip markdown bold/italics

                    return response()->json([
                        'reply' => $replyText,
                    ]);
                }
            }

            Log::error('Gemini API Error Response', ['status' => $response->status(), 'body' => $response->body()]);

            return response()->json(['reply' => 'Maaf, saya tidak dapat memproses jawaban saat ini.']);
        } catch (\Exception $e) {
            Log::error('Gemini Exception', ['message' => $e->getMessage()]);

            return response()->json(['reply' => 'Maaf, saat ini sistem AI sedang sibuk. Silakan coba lagi nanti.']);
        }
    }
}
