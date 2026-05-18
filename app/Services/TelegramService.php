<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected $token;
    protected $chatId;

    public function __construct()
    {
        $this->token = env('TELEGRAM_BOT_TOKEN');
        $this->chatId = env('TELEGRAM_GROUP_ID');
    }

    // Fungsi kirim pesan dengan tombol
    public function sendTicketNotification($ticket)
    {
        $url = "https://api.telegram.org/bot{$this->token}/sendMessage";

        $message = "<b>🔥 NEW TICKET #{$ticket->id}</b>\n" .
                   "<b>User:</b> {$ticket->nama_karyawan}\n" .
                   "<b>Kendala:</b> {$ticket->detail_kendala}\n" .
                   "<i>Silakan pilih tindakan:</i>";

        // Tombol Terima & Tolak
        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '✅ Terima', 'callback_data' => "accept:{$ticket->id}"],
                    ['text' => '❌ Tolak', 'callback_data' => "reject:{$ticket->id}"]
                ]
            ]
        ];

        return $this->executeRequest($url, [
            'chat_id' => $this->chatId,
            'text' => $message,
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode($keyboard)
        ]);
    }

    // Fungsi update pesan untuk tombol 'Selesai'
    public function sendWorkInProgress($ticket)
    {
        $url = "https://api.telegram.org/bot{$this->token}/sendMessage";
        
        $message = "<b>⚙️ TICKET #{$ticket->id} IN PROGRESS</b>\n" .
                   "<b>PIC:</b> {$ticket->pic}\n" .
                   "<i>Tekan tombol di bawah jika sudah selesai:</i>";

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '🏁 Selesai (Finish)', 'callback_data' => "finish:{$ticket->id}"]
                ]
            ]
        ];

        return $this->executeRequest($url, [
            'chat_id' => $this->chatId,
            'text' => $message,
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode($keyboard)
        ]);
    }

    public function sendMessage($message)
    {
        $url = "https://api.telegram.org/bot{$this->token}/sendMessage";
        return $this->executeRequest($url, [
            'chat_id' => $this->chatId,
            'text' => $message,
            'parse_mode' => 'HTML'
        ]);
    }

    private function executeRequest($url, $data)
    {
        try {
            Http::post($url, $data);
        } catch (\Exception $e) {
            Log::error("Telegram Error: " . $e->getMessage());
        }
    }
}