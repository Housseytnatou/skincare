<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdate extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $oldStatus;
    public $newStatus;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order, $oldStatus, $newStatus)
    {
        $this->order = $order;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $statusMessages = [
            'processing' => 'en cours de traitement',
            'shipped' => 'expédiée',
            'delivered' => 'livrée',
            'cancelled' => 'annulée'
        ];

        $statusMessage = $statusMessages[$this->newStatus] ?? $this->newStatus;

        return new Envelope(
            subject: 'Mise à jour de votre commande #' . $this->order->order_number . ' - ' . ucfirst($statusMessage),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.order-status-update',
            with: [
                'order' => $this->order,
                'oldStatus' => $this->oldStatus,
                'newStatus' => $this->newStatus,
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