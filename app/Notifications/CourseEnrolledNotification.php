<?php

namespace App\Notifications;

use App\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CourseEnrolledNotification extends Notification implements ShouldQueue
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
            ->subject('Selamat! Anda Berhasil Terdaftar di Kursus')
            ->greeting('Halo ' . $notifiable->full_name . '!')
            ->line('Selamat! Anda telah berhasil terdaftar dalam kursus: ' . $this->course->title)
            ->action('Mulai Belajar', route('courses.content', $this->course->id))
            ->line('Terima kasih telah menggunakan platform kami!');
    }
}
