<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Notifications\PaymentSuccesfromWebNotification;
use Illuminate\Support\Facades\Notification as NotificationFacade;
// use App\Models\karyawan;
use App\Models\User;

class PaymentSuccesController extends Controller
{
    public function receivePayment(Request $request)
    {
        // Validasi input (bisa dikembangkan sesuai kebutuhan)
        $recive = $request->validate([
            'nama' => 'required|string',
            'instansi' => 'nullable|string',
            'email' => 'required|email',
            'total_harga' => 'required|numeric',
            'cartItems' => 'required|array',
            'paymentResult' => 'required|array',
            'transaksi_uuid' => 'required|string'
        ]);
        $recive['tanggal'] = now();


        $data = $recive;

        $users = User::whereIn('jabatan', ['Customer Care', 'Tim Digital'])->get();

        $to = User::whereIn('jabatan', ['Customer Care', 'Tim Digital'])->get();


        foreach ($users as $user) {
            NotificationFacade::send($user, new PaymentSuccesfromWebNotification($data, $to) );
        }

        // NotificationFacade::send(new PaymentSuccesfromWebNotification($data, $to->pluck('nama_lengkap')->toArray()));

        // Response sukses
        return response()->json([
            'message' => 'Data pembayaran diterima dengan sukses dan notifikasi telah dikirim',
            'data' => $recive
        ], 200);
    }

}
