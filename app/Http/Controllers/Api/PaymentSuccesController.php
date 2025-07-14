<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Notifications\PaymentSuccesfromWebNotification;
use Illuminate\Support\Facades\Notification as NotificationFacade;
// use App\Models\karyawan;
use App\Models\User;
use App\Notifications\RequestPenawaranNotification;

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

    public function requestPenawaran(Request $request)
    {

        // dd($request->all());
        // Validasi input (bisa dikembangkan sesuai kebutuhan)
       $validated = $request->validate([
            'nama_materi' => 'required|string',
            'nama_lengkap' => 'nullable|string',
            'tipe' => 'nullable|string',
            'kelas' => 'nullable|string',
            'email' => 'required|email',
            'no_telepon' => 'required|numeric',
            'instansi' => 'required|string',
            'pax' => 'required|string',
            'id' => 'required|string',
        ]);
        $validated['tanggal'] = now();

        $data = $validated;

        $users = User::whereIn('jabatan', ['Customer Care', 'Tim Digital'])->get();

        $to = User::whereIn('jabatan', ['Customer Care', 'Tim Digital'])->get();


        foreach ($users as $user) {
            NotificationFacade::send($user, new RequestPenawaranNotification($data, $to) );
        }

        // NotificationFacade::send(new PaymentSuccesfromWebNotification($data, $to->pluck('nama_lengkap')->toArray()));

        // Response sukses
        return response()->json([
            'message' => 'Data pembayaran diterima dengan sukses dan notifikasi telah dikirim',
            'data' => $validated
        ], 200);
    }

}
