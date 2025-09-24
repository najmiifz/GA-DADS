<?php

namespace App\Notifications;

use App\Models\ApdRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class ApdRequestStatusNotification extends Notification
{
    use Queueable;

    protected ApdRequest $apdRequest;
    protected string $status;

    /**
     * Create a new notification instance.
     *
     * @param ApdRequest $apdRequest
     * @param string $status  // 'delivery', 'approved', 'rejected', 'received'
     */
    public function __construct(ApdRequest $apdRequest, string $status)
    {
        $this->apdRequest = $apdRequest;
        $this->status = $status;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification for database storage.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        // Define messages for each status
        $messages = [
            'delivery' => 'APD Anda telah dikirim: ',
            'approved' => 'APD Anda telah disetujui: ',
            'rejected' => 'APD Anda telah ditolak: ',
            'received' => 'APD Anda telah diterima: ',
        ];

        $prefix = $messages[$this->status] ?? 'Status APD Anda berubah: ';
        $message = $prefix . $this->apdRequest->nomor_pengajuan;

        return [
            'message' => $message,
            'url' => route('apd-requests.show', $this->apdRequest),
        ];
    }
}
