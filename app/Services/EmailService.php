<?php

namespace App\Services;

use App\Models\Order;
use App\Mail\OrderConfirmation;
use App\Mail\OrderStatusUpdate;
use App\Mail\PaymentConfirmation;
use Illuminate\Support\Facades\Mail;

class EmailService
{
    /**
     * Envoyer l'email de confirmation de commande
     */
    public function sendOrderConfirmation(Order $order)
    {
        try {
            Mail::to($order->user->email)->send(new OrderConfirmation($order));
            
            return [
                'success' => true,
                'message' => 'Email de confirmation envoyé avec succès'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'envoi de l\'email',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Envoyer l'email de mise à jour de statut
     */
    public function sendOrderStatusUpdate(Order $order, $oldStatus, $newStatus)
    {
        try {
            Mail::to($order->user->email)->send(new OrderStatusUpdate($order, $oldStatus, $newStatus));
            
            return [
                'success' => true,
                'message' => 'Email de mise à jour envoyé avec succès'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'envoi de l\'email',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Envoyer l'email de confirmation de paiement
     */
    public function sendPaymentConfirmation(Order $order, $paymentId = null)
    {
        try {
            Mail::to($order->user->email)->send(new PaymentConfirmation($order, $paymentId));
            
            return [
                'success' => true,
                'message' => 'Email de confirmation de paiement envoyé avec succès'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'envoi de l\'email',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Envoyer tous les emails automatiques pour une nouvelle commande
     */
    public function sendOrderEmails(Order $order)
    {
        $results = [];

        // Email de confirmation de commande
        $results['confirmation'] = $this->sendOrderConfirmation($order);

        // Si le paiement est en ligne et déjà payé, envoyer la confirmation de paiement
        if ($order->payment_method === 'online' && $order->payment_status === 'paid') {
            $results['payment'] = $this->sendPaymentConfirmation($order, $order->payment_id);
        }

        return $results;
    }

    /**
     * Envoyer l'email de mise à jour de statut si nécessaire
     */
    public function sendStatusUpdateEmail(Order $order, $oldStatus, $newStatus)
    {
        // Ne pas envoyer d'email si le statut n'a pas changé
        if ($oldStatus === $newStatus) {
            return [
                'success' => true,
                'message' => 'Aucun changement de statut, email non envoyé'
            ];
        }

        return $this->sendOrderStatusUpdate($order, $oldStatus, $newStatus);
    }

    /**
     * Envoyer l'email de confirmation de paiement quand le paiement est traité
     */
    public function sendPaymentEmail(Order $order, $paymentId = null)
    {
        return $this->sendPaymentConfirmation($order, $paymentId);
    }
} 