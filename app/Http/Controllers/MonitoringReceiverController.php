<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Monitoring;
class MonitoringReceiverController extends Controller
{
    private $token = 'PHAREIO12345';

    public function receive(Request $request)
    {
        try {

            // ======================
            // VALIDASI INTERNAL TOKEN
            // ======================

            if ($request->header('X-INTERNAL-TOKEN') !== $this->token) {

                Log::warning('Token internal salah');

                return response()->json([
                    'message' => 'Unauthorized'
                ], 401);
            }

            Log::info('Monitoring diterima', [
                'payload' => $request->all()
            ]);

            // ======================
            // AMBIL DATA PHARE
            // ======================

            $data = $request->all();

            /*
             Contoh payload:
            [
                "event" => "monitor.down",
                "monitor" => [
                    "name" => "Website A"
                ]
            ]
            */

            $event = $data['event'] ?? null;
            $monitor = $data['monitor']['name'] ?? '-';

            Monitoring::create([

                'event' => $data['event'] ?? null,

                'monitor_id' => $data['monitor']['id'] ?? null,
                'monitor_name' => $data['monitor']['name'] ?? null,
                'status' => $data['monitor']['status'] ?? null,
                'protocol' => $data['monitor']['protocol'] ?? null,

                'request' => $data['monitor']['request'] ?? null,
                'regions' => $data['monitor']['regions'] ?? null,

                'interval' => $data['monitor']['interval'] ?? null,

                'incident_confirmations' =>
                    $data['monitor']['incident_confirmations'] ?? null,

                'recovery_confirmations' =>
                    $data['monitor']['recovery_confirmations'] ?? null,

                'project_id' => $data['project']['id'] ?? null,
                'project_name' => $data['project']['name'] ?? null,
                'project_slug' => $data['project']['slug'] ?? null,

                'payload' => $data,
            ]);

            return response()->json([
                'success' => true
            ]);

        } catch (\Throwable $e) {

            Log::error('Receiver Error', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false
            ], 500);
        }
    }
}