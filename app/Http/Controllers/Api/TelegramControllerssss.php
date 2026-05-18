<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;
use Illuminate\Support\Facades\Http;

class TelegramController extends Controller
{
    public function setWebhook()
    {
        $token = env('TELEGRAM_BOT_TOKEN');

        // Validasi: Pastikan token tidak kosong
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token Telegram tidak ditemukan di file .env'
            ], 500);
        }

        // Pastikan URL ini sesuai dengan route yang Anda buat (pakai /api jika di api.php)
        $webhookUrl = 'https://202.138.248.36:88/api/telegram/webhook';
        
        $alamatUrl = "https://api.telegram.org/bot{$token}/setWebhook";

        $response = Http::post($alamatUrl, [
            'url' => $webhookUrl,
        ]);

        return $response->json();
    }

    public function webhook(Request $request)
    {
        $update = $request->all();
        
        // Logging untuk debugging
        Log::info('Telegram Webhook:', $update);

        $chatId = $update['message']['chat']['id'] ?? null;
        $text = $update['message']['text'] ?? null;
        $username = $update['message']['from']['username'] ?? 'Unknown';

        if ($text) {
            // Contoh Logika Sederhana: Menangani Command
            if (str_starts_with($text, '/terima')) {
                // Logika parsing ID tiket dari pesan "/terima NIX240120a"
                $parts = explode(' ', $text);
                if (isset($parts[1])) {
                    $ticketId = trim($parts[1]);
                    // Lakukan logika update database di sini atau panggil Service
                    Log::info("User @$username mencoba menerima tiket ID: $ticketId");
                    
                    // Kirim balasan otomatis (opsional)
                    $this->replyMessage($chatId, "Permintaan terima tiket $ticketId diproses.");
                }
            }
        }

        return response()->json(['status' => 'ok']);
    }

    private function replyMessage($chatId, $text)
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $text,
        ]);
    }

}
