<?php

namespace App\Notifications;

use App\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CourseCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Course $course
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Selamat! Anda Menyelesaikan Kursus')
            ->greeting('Halo ' . $notifiable->full_name . '!')
            ->line('Selamat! Anda telah berhasil menyelesaikan kursus: ' . $this->course->title)
            ->line('Anda dapat mengunduh sertifikat penyelesaian kursus Anda.')
            ->action('Lihat Sertifikat', route('certificates.show', $this->course->id))
            ->line('Terus semangat belajar!');
    }
}
