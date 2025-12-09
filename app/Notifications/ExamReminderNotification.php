<?php

namespace App\Notifications;

use App\Models\Exam;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExamReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Exam $exam
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Pengingat: Ujian Akan Dimulai')
            ->greeting('Halo ' . $notifiable->full_name . '!')
            ->line('Ini adalah pengingat bahwa ujian "' . $this->exam->title . '" akan dimulai.')
            ->line('Tanggal Mulai: ' . $this->exam->start_date->format('d F Y H:i'))
            ->line('Durasi: ' . $this->exam->duration_minutes . ' menit')
            ->action('Mulai Ujian', route('exams.show', $this->exam->id))
            ->line('Selamat mengerjakan!');
    }
}
