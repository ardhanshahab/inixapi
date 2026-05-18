<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Tickets;
use Carbon\Carbon;

class TelegramController extends Controller
{
    protected $telegram;

    public function __construct(TelegramService $telegram)
    {
        $this->telegram = $telegram;
    }

    public function handle(Request $request)
    {
        $update = $request->all();

        // Cek apakah ini Callback Query (Klik Tombol)
        if (isset($update['callback_query'])) {
            $callback = $update['callback_query'];
            $data = $callback['data']; // format: action:ticket_id
            $from = $callback['from']['username'] ?? $callback['from']['first_name']; // Nama PIC dari Telegram
            
            // Parsing data (contoh: accept:10)
            [$action, $ticketId] = explode(':', $data);
            
            $this->processAction($action, $ticketId, $from);
            
            return response()->json(['status' => 'ok']);
        }

        return response()->json(['status' => 'ignored']);
    }

    private function processAction($action, $ticketId, $picName)
    {
        $ticket = Ticket::find($ticketId);

        if (!$ticket) return;

        switch ($action) {
            case 'accept':
                if ($ticket->status !== 'Open') return; // Validasi

                $ticket->update([
                    'status' => 'In Progress',
                    'pic' => $picName, // Auto-set PIC dari akun Telegram yang klik
                    'tanggal_response' => Carbon::now()->toDateString(),
                    'jam_response' => Carbon::now()->toTimeString(),
                ]);

                // Kirim notifikasi baru dengan tombol Finish
                $this->telegram->sendMessage("✅ <b>Tiket #{$ticketId} diterima oleh {$picName}.</b>");
                $this->telegram->sendWorkInProgress($ticket);
                break;

            case 'reject':
                if ($ticket->status !== 'Open') return;

                $ticket->update([
                    'status' => 'Rejected',
                    'pic' => $picName,
                    'keterangan' => 'Ditolak via Telegram',
                    'tanggal_selesai' => Carbon::now()->toDateString(), // Dianggap selesai saat ditolak
                ]);

                $this->telegram->sendMessage("❌ <b>Tiket #{$ticketId} ditolak oleh {$picName}.</b>");
                break;

            case 'finish':
                if ($ticket->status !== 'In Progress') return;

                $ticket->update([
                    'status' => 'Closed',
                    'tanggal_selesai' => Carbon::now()->toDateString(),
                    'jam_selesai' => Carbon::now()->toTimeString(),
                    'penanganan' => 'Diselesaikan via Telegram Quick Action', // Default text
                ]);

                $this->telegram->sendMessage("🏁 <b>Tiket #{$ticketId} telah diselesaikan oleh {$picName}.</b>");
                break;
        }
    }
}
