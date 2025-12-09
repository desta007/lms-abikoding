<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Invoice $invoice
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Pembayaran Berhasil Diproses')
            ->greeting('Halo ' . $notifiable->full_name . '!')
            ->line('Pembayaran Anda untuk invoice #' . $this->invoice->invoice_number . ' telah berhasil diproses.')
            ->line('Total Pembayaran: Rp ' . number_format($this->invoice->total_amount, 0, ',', '.'))
            ->action('Lihat Invoice', route('payments.success', $this->invoice->id))
            ->line('Terima kasih atas pembayaran Anda!');
    }
}
