<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class OrderConfirmation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $tries = 3;

    public $backoff = [10, 30, 60];

    public Order $order;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order->loadMissing('items.product', 'user');
        $this->onQueue('emails');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('emails.order.subject', ['id' => $this->order->order_number]),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.orders.confirmation',
            with: [
                'order' => $this->order,
                'url' => route('user.orders.show', $this->order),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $path = $this->order->invoice_storage_path;

        if (! Storage::disk('local')->exists($path)) {
            Log::channel('mail')->warning('Order invoice attachment missing; sending mail without attachment.', [
                'order_id' => $this->order->id,
                'path' => $path,
            ]);

            return [];
        }

        return [
            Attachment::fromStorageDisk('local', $path)
                ->as('invoice-'.$this->order->order_number.'.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
