<?php

namespace App\Notifications;

use App\Models\ApdRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class NewApdRequestNotification extends Notification
{
    use Queueable;

    protected $apdRequest;

    public function __construct(ApdRequest $apdRequest)
    {
        $this->apdRequest = $apdRequest;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $userName = $this->apdRequest->user->name;
        $location = $this->apdRequest->nama_cluster;
        return [
            'message' => sprintf(
                'Pengajuan APD baru: %s oleh %s (%s)',
                $this->apdRequest->nomor_pengajuan,
                $userName,
                $location
            ),
            'url' => route('apd-requests.show', $this->apdRequest),
        ];
    }
}
