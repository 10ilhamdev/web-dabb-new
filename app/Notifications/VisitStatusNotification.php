<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\VisitRegistration;

class VisitStatusNotification extends Notification
{
    use Queueable;

    protected VisitRegistration $registration;
    protected string $status;
    protected ?string $keterangan;

    /**
     * Create a new notification instance.
     */
    public function __construct(VisitRegistration $registration, string $status, ?string $keterangan)
    {
        $this->registration = $registration;
        $this->status = $status;
        $this->keterangan = $keterangan;
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
        $statusText = $this->status === 'approved' ? 'Disetujui' : 'Ditolak';
        $visitDate = $this->registration->visit_date ? $this->registration->visit_date->format('d M Y') : '-';
        
        // Translate visit_time to readable string
        $timeTranslation = $this->registration->visit_time;
        if ($this->registration->visit_time) {
            $lowerTime = strtolower($this->registration->visit_time);
            if ($lowerTime === 'siang') {
                $timeTranslation = __('home.layanan_publik.form_time_siang') !== 'home.layanan_publik.form_time_siang' 
                    ? __('home.layanan_publik.form_time_siang') 
                    : 'Siang (13:00 - 16:00)';
            } else {
                $timeTranslation = __('home.layanan_publik.form_time_pagi') !== 'home.layanan_publik.form_time_pagi' 
                    ? __('home.layanan_publik.form_time_pagi') 
                    : 'Pagi (07:30 - 12:00)';
            }
        } else {
            $timeTranslation = 'Pagi (07:30 - 12:00)';
        }

        return (new MailMessage)
            ->subject('Update Status Pendaftaran Kunjungan - Depot Arsip Berkelanjutan Bandung')
            ->view('vendor.mail.html.visit-status', [
                'name' => $this->registration->name,
                'status' => $this->status,
                'statusText' => $statusText,
                'visitDate' => $visitDate,
                'visitTime' => $timeTranslation,
                'visitorCount' => $this->registration->visitor_count,
                'keterangan' => $this->keterangan,
            ]);
    }
}
