<?php

namespace App\Notifications;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OverdueBookNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Transaction $transaction) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $daysOverdue = $this->transaction->getDaysOverdue();
        $penalty = $this->transaction->getPenalty();

        return (new MailMessage)
            ->subject('Pengingat Pengembalian Buku - '.$this->transaction->book->title)
            ->greeting('Halo, '.$notifiable->name)
            ->line('Buku yang Anda pinjam telah melewati tanggal pengembalian.')
            ->line('**Judul Buku:** '.$this->transaction->book->title)
            ->line('**Tanggal Tenggat:** '.$this->transaction->due_date->translatedFormat('l, d F Y'))
            ->line('**Keterlambatan:** '.$daysOverdue.' hari')
            ->line('**Denda:** Rp '.number_format($penalty))
            ->line('Mohon segera mengembalikan buku untuk menghindari penambahan denda.')
            ->action('Lihat Buku', url('/books/'.$this->transaction->book->id))
            ->line('Terima kasih telah menggunakan perpustakaan kami!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $daysOverdue = $this->transaction->getDaysOverdue();
        $penalty = $this->transaction->getPenalty();

        return [
            'transaction_id' => $this->transaction->id,
            'book_title' => $this->transaction->book->title,
            'due_date' => $this->transaction->due_date->toDateString(),
            'days_overdue' => $daysOverdue,
            'penalty' => $penalty,
            'message' => "Buku '{$this->transaction->book->title}' terlambat {$daysOverdue} hari. Denda: Rp ".number_format($penalty),
        ];
    }
}
