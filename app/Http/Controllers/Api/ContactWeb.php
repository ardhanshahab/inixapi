<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\karyawan;
use App\Models\User;
use App\Notifications\ContactWeb as NotificationsContactWeb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContactWeb extends Controller
{

    public function store(Request $request)
    {
        try {
            Log::info('Request received', $request->all());

            // Validasi input
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'instansi' => 'required|string|max:255',
                'no_wa' => 'required|string|max:15',
                'pesan' => 'required|string',
            ]);

            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'instansi' => $request->instansi,
                'no_wa' => $request->no_wa,
                'pesan' => $request->pesan,
                'tipe' => 'Pesan Contact Us Website INIXINDO'
            ];

            Log::info('Fetching Karyawan data');
            $users = User::whereIn('jabatan', ['Tim Digital', 'Customer Care'])->get();
            Log::info('Users found', ['count' => $users->count(), 'users' => $users->toArray()]);

            if ($users->isEmpty()) {
                Log::warning('No users found with jabatan Tim Digital or Customer Care');
                return response()->json(['message' => 'Pesan diterima, tetapi tidak ada penerima notifikasi'], 200);
            }

            //Kirim Notifikasi ke setiap pengguna dengan kondisi
            foreach ($users as $user) {
                Log::info('Notifying user', ['user_id' => $user->id, 'username' => $user->username ?? 'N/A']);
                $user->notify(new NotificationsContactWeb($data));
            }

            return response()->json([
                'message' => 'Pesan berhasil dikirim!',
                'data' => $data
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error in ContactController@store', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Terjadi kesalahan server: ' . $e->getMessage()], 500);
        }
    }
}
