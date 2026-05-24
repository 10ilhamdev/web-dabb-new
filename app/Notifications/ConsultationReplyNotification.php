<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\ArchivalConsultation;

class ConsultationReplyNotification extends Notification
{
    use Queueable;

    protected $consultation;
    protected $replyMessage;

    /**
     * Create a new notification instance.
     */
    public function __construct(ArchivalConsultation $consultation, $replyMessage)
    {
        $this->consultation = $consultation;
        $this->replyMessage = $replyMessage;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Balasan Konsultasi Kearsipan - Depot Arsip Berkelanjutan Bandung')
            ->view('vendor.mail.html.consultation-reply', [
                'name' => $this->consultation->name,
                'topic' => $this->consultation->detail ?? 'Konsultasi Kearsipan',
                'replyMessage' => $this->replyMessage,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
