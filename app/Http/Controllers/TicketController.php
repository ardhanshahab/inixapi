<?php

namespace App\Http\Controllers;


use App\Services\TelegramService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Tickets;

class TicketController extends Controller
{
    protected $telegram;

    public function __construct(TelegramService $telegram)
    {
        $this->telegram = $telegram;
    }
	// 1. Tampilkan Halaman Utama (Satu-satunya View)
    public function index()
    {
        return view('tickets.index');
    }

    // 2. Fetch Data untuk Tabel (AJAX Load)
    public function getData()
    {
        $tickets = Tickets::latest()->get();
        return response()->json($tickets);
    }


	public function store(Request $request)
	{
		// 1. Validasi Input
		$validated = $request->validate([
			'nama_karyawan' => 'required|string',
			'divisi'        => 'required|string',
			'kategori'      => 'required|string',
			'keperluan'     => 'required|string',
			'detail_kendala'=> 'required|string',
		]);

		// 2. Simpan ke Database
		// Menggunakan array_merge untuk menggabungkan input user + sistem generated data
		$ticket = Tickets::create(array_merge($validated, [
			'timestamp' => now(), 
			'status'    => 'Menunggu' // Memastikan status awal konsisten
		]));

		// 3. Format Pesan Telegram
		$message = "<b>🔥 NEW TICKET #{$ticket->id}</b>\n" .
				   "<b>User:</b> {$ticket->nama_karyawan} ({$ticket->divisi})\n" .
				   "<b>Kategori:</b> {$ticket->kategori}\n" .
				   "<b>Kendala:</b> {$ticket->detail_kendala}\n" .
				   "<b>Waktu:</b> {$ticket->timestamp}\n" .
				   "<b>Status:</b> Open";

		// 4. Kirim Notifikasi (Pastikan method ini ada di TelegramService)
		//$this->telegram->sendMessage($message);
		$this->telegram->sendTicketNotification($ticket);

		// 5. Response JSON
		return response()->json([
			'message' => 'Tiket berhasil dibuat', 
			'data' => $ticket
		], 201);
	}

    // 2. Update Tiket (Technical Support)
    public function update(Request $request, $id)
    {
        $ticket = Tickets::findOrFail($id);

        // Validasi input parsial
        $validated = $request->validate([
            'pic' => 'nullable|string',
            'penanganan' => 'nullable|string',
            'status' => 'required|in:Open,In Progress,Resolved,Closed',
            'tingkat_kesulitan' => 'nullable|in:Low,Medium,High',
            'keterangan' => 'nullable|string',
        ]);

        // Logika Pengisian Tanggal Otomatis
        if ($request->status == 'In Progress' && is_null($ticket->tanggal_response)) {
            $ticket->tanggal_response = Carbon::now()->toDateString();
            $ticket->jam_response = Carbon::now()->toTimeString();
        }

        if (($request->status == 'Resolved' || $request->status == 'Closed') && is_null($ticket->tanggal_selesai)) {
            $ticket->tanggal_selesai = Carbon::now()->toDateString();
            $ticket->jam_selesai = Carbon::now()->toTimeString();
        }

        $ticket->update($validated);

        // Notifikasi Update ke Telegram
        $message = "<b>✅ UPDATE TICKET #{$ticket->id}</b>\n" .
                   "<b>Status:</b> {$ticket->status}\n" .
                   "<b>PIC:</b> {$ticket->pic}\n" .
                   "<b>Penanganan:</b> {$ticket->penanganan}";

        $this->telegram->sendMessage($message);

        return response()->json(['message' => 'Tiket berhasil diperbarui', 'data' => $ticket], 200);
    }
}