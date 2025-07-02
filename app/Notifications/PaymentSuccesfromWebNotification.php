<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;


class PaymentSuccesfromWebNotification extends Notification
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
                'tipe' => 'Pembayaran_from_web',
                'nama_lengkap' => $this->to,
                'tanggal' => $this->data['tanggal'],
                'status' => 'Sudah Dibayar',
                'nama' => $this->data['nama'],
                'email' => $this->data['email'],
                'total_harga' => $this->data['total_harga'],
                'paymentResult' => $this->data['paymentResult'],
                'cartItems' => $this->data['cartItems']
            ],
            'path' => 'https://inixindobdg.co.id',
            'status' => 'unread',
        ];
    }
}
