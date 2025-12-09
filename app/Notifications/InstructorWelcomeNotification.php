<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InstructorWelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Selamat! Akun Instruktur Anda Telah Dibuat')
            ->greeting('Halo ' . $notifiable->full_name . '!')
            ->line('Selamat! Akun instruktur Anda telah berhasil dibuat di LMS DC Tech.')
            ->line('Sebagai instruktur, Anda sekarang dapat:')
            ->line('• Membuat dan mengelola kursus')
            ->line('• Menambahkan materi pembelajaran')
            ->line('• Melihat statistik kursus Anda')
            ->line('• Berinteraksi dengan siswa melalui komentar')
            ->action('Masuk ke Dashboard Instruktur', route('instructor.dashboard'))
            ->line('Jika Anda memiliki pertanyaan, jangan ragu untuk menghubungi tim admin.')
            ->line('Selamat bergabung dan semoga sukses!');
    }
}

