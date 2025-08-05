<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $paymentId;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order, $paymentId = null)
    {
        $this->order = $order;
        $this->paymentId = $paymentId;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmation de paiement - Commande #' . $this->order->order_number,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-confirmation',
            with: [
                'order' => $this->order,
                'paymentId' => $this->paymentId,
                'company' => [
                    'name' => 'Skincare Shop',
                    'email' => 'contact@skincareshop.com',
                    'phone' => '01 23 45 67 89',
                ]
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
} 