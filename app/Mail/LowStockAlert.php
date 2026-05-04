<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class LowStockAlert extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Collection $products;

    public function __construct(Collection $products)
    {
        $this->products = $products;
        $this->onQueue('emails');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('emails.admin.low_stock_subject'),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.admin.low-stock',
            with: [
                'products' => $this->products,
                'threshold' => config('mail.low_stock_threshold'),
            ],
        );
    }
}
