<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\User;

class DataFromWebinixController extends Controller
{
    public function fetchArticles()
    {
        $apiUrl = 'http://127.0.0.1:8001/api/articles';
        $response = Http::get($apiUrl);

        $articles = collect();

        if ($response->successful()) {
            $apiData = $response->json()['data'] ?? [];

            foreach ($apiData as $item) {
                $user = User::with('karyawan')
                    ->whereHas('karyawan', function ($query) use ($item) {
                        $query->where('nama_lengkap', $item['pembuat'])
                              ->where('divisi', 'Education');
                    })
                    ->first();

                // Validasi eksistensi User dan memastikan id_instruktur tidak null
                if ($user && !is_null($user->id_instruktur)) {
                    $item['nama_lengkap_pembuat'] = $user->karyawan->nama_lengkap ?? null;
                    $articles->push($item);
                }
            }
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Data artikel berhasil diproses',
            'data'    => $articles
        ], 200);
    }
}
