<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;

class OrderShippedMail extends Mailable
{
    use SerializesModels;
    
    public $deleteWhenMissingModels = true;
    public function __construct(
        public Order $order
    ) {}

    public function build()
    {
        return $this->subject('Your order has been shipped')
            ->view('emails.orders.shipped');
    }
}