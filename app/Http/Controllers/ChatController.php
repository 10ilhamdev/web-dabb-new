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

            $locale = session('locale', 'id');

            if ($locale === 'en') {
                $systemInstruction = "You are a friendly Customer Support AI in English from DABB (Depot Arsip Berkelanjutan Bandung), an agency under the National Archives of the Republic of Indonesia (ANRI).

Important rules:
1. Limit your answers ONLY to questions about the system/features of this website (such as menu, profile, virtual exhibition, reservation/visit, etc.) and archiving/records management topics (such as archiving consultation, LARASKA, archives repository, Ruang Baca DABB, etc.).
2. If asked about things outside this website system or outside the field of archiving (for example: food recipes, programming, math, sports, general news, etc.), you MUST decline to answer by stating that it is outside your scope of service (for example: 'Sorry, that is outside my scope of service. I can only answer questions related to the DABB website system and archiving-related matters.').
3. Keep your answers concise, accurate, and straight to the point.";
                $promptText = "System Instruction: {$systemInstruction}\n\nVisitor Question: {$message}\n\nAI Answer:";
            } else {
                $systemInstruction = "Anda adalah Customer Support AI ramah berbahasa Indonesia dari DABB (Depot Arsip Berkelanjutan Bandung), instansi di bawah Arsip Nasional Republik Indonesia (ANRI).

Aturan penting:
1. Batasi jawaban Anda HANYA untuk pertanyaan seputar sistem/fitur website ini (seperti menu, profil, pameran virtual, reservasi kunjungan, dll.) dan topik kearsipan (seperti pengelolaan arsip, konsultasi kearsipan, LARASKA, depo arsip, Ruang Baca DABB, dll.).
2. Jika ditanya hal yang di luar sistem website ini atau di luar ranah kearsipan (misalnya: resep makanan, pemrograman, matematika, olahraga, berita umum, dll.), Anda WAJIB menolak menjawab dengan menyatakan bahwa hal tersebut di luar ranah/lingkup bantuan Anda (misalnya: 'Maaf, hal tersebut di luar ranah atau lingkup layanan saya. Saya hanya dapat menjawab pertanyaan seputar sistem website DABB dan hal-hal yang berkaitan dengan kearsipan.').
3. Jawablah dengan ringkas, akurat, dan langsung ke intinya (hindari basa-basi berlebih).";
                $promptText = "Instruksi Sistem: {$systemInstruction}\n\nPertanyaan Pengunjung: {$message}\n\nJawaban AI:";
            }

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
