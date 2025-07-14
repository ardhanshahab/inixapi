<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RequestPenawaranNotification extends Notification
{
    use Queueable;

    protected $data;
    protected $to;

    public function __construct($data, $to)
    {
        $this->data = $data;
        $this->to = $to;
    }

    public function via($notifiable)
    {
        return ['database']; // Menggunakan 'database' dan 'broadcast'
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        // Determine the type based on approval statuses

    return [
            'user' => '-',
            'message' => [
                'tipe' => 'Request Penawaran',
                'nama_lengkap' => $this->to,
                'tanggal' => $this->data['tanggal'],
                'status' => 'Request Penawaran',
                'nama' => $this->data['nama_lengkap'],
                'email' => $this->data['email'],
                'no_telepon' => $this->data['no_telepon'],
                'instansi' => $this->data['instansi'],
                'pax' => $this->data['pax'],
                'nama_materi' => $this->data['nama_materi'],
                'kelas' => $this->data['kelas']
            ],
            'path' => 'https://inixindobdg.co.id',
            'status' => 'unread',
        ];

    }
}
