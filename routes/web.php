<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TicketController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


// Halaman Utama
Route::get('/', [TicketController::class, 'index'])->name('tickets.index');

// Endpoint AJAX (Untuk JSON)
Route::get('/tickets/data', [TicketController::class, 'getData'])->name('tickets.data'); // Ambil semua data
Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store'); // Simpan baru
Route::get('/tickets/{id}', [TicketController::class, 'show'])->name('tickets.show'); // Ambil 1 data untuk edit
Route::put('/tickets/{id}', [TicketController::class, 'update'])->name('tickets.update'); // Update data

Route::get('/test-telegram', function () {
    $token = env('TELEGRAM_BOT_TOKEN');
    $chatId = env('TELEGRAM_GROUP_ID');
    
    $response = Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
        'chat_id' => $chatId,
        'text' => 'Halo, ini tes pesan manual.',
    ]);

    return $response->json();
});