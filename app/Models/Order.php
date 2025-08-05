<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'shipping_address',
        'shipping_city',
        'shipping_postal_code',
        'shipping_phone',
        'subtotal',
        'tax',
        'total',
        'order_status',
        'payment_method',
        'payment_status',
        'payment_id',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Méthodes pour générer le numéro de commande
    public static function generateOrderNumber()
    {
        do {
            $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(6));
        } while (self::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    // Méthodes pour les statuts
    public function isPending()
    {
        return $this->order_status === 'pending';
    }

    public function isProcessing()
    {
        return $this->order_status === 'processing';
    }

    public function isShipped()
    {
        return $this->order_status === 'shipped';
    }

    public function isDelivered()
    {
        return $this->order_status === 'delivered';
    }

    public function isCancelled()
    {
        return $this->order_status === 'cancelled';
    }

    public function isPaid()
    {
        return $this->payment_status === 'paid';
    }

    public function isPaymentPending()
    {
        return $this->payment_status === 'pending';
    }

    // Méthodes pour les modes de paiement
    public function isOnlinePayment()
    {
        return $this->payment_method === 'online';
    }

    public function isCashOnDelivery()
    {
        return $this->payment_method === 'cash_on_delivery';
    }

    // Méthode pour calculer le total
    public function calculateTotal()
    {
        $this->total = $this->subtotal + $this->tax;
        $this->save();
        return $this->total;
    }

    // Méthode pour marquer comme payé
    public function markAsPaid($paymentId = null)
    {
        $this->payment_status = 'paid';
        $this->payment_id = $paymentId;
        $this->paid_at = now();
        $this->save();
    }
}
