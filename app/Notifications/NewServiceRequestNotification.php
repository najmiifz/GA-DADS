<?php

namespace App\Notifications;

use App\Models\ServiceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class NewServiceRequestNotification extends Notification
{
    use Queueable;

    protected $serviceRequest;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\ServiceRequest  $serviceRequest
     * @return void
     */
    public function __construct(ServiceRequest $serviceRequest)
    {
        $this->serviceRequest = $serviceRequest;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification for database channel.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'message' => 'Pengajuan service baru masuk: ' . $this->serviceRequest->nomor_pengajuan,
            'url' => route('service-requests.show', $this->serviceRequest),
        ];
    }
}
