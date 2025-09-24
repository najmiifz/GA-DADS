<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Asset;

class PajakJatuhTempoNotification extends Notification
{
    use Queueable;

    public $asset;

    /**
     * Create a new notification instance.
     */
    public function __construct(Asset $asset)
    {
        $this->asset = $asset;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('Pajak kendaraan akan jatuh tempo.')
                    ->line('Pajak untuk kendaraan ' . $this->asset->merk . ' ' . $this->asset->model . ' dengan nomor polisi ' . $this->asset->serial_number . ' akan jatuh tempo dalam 7 hari.')
                    ->action('Lihat Detail Aset', url('/assets/' . $this->asset->id))
                    ->line('Terima kasih telah menggunakan aplikasi kami!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'asset_id' => $this->asset->id,
            'message' => 'Pajak untuk kendaraan ' . $this->asset->merk . ' ' . $this->asset->model . ' (' . $this->asset->serial_number . ') akan jatuh tempo dalam 7 hari.',
            'url' => url('/assets/' . $this->asset->id),
        ];
    }
}
