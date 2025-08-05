<?php

namespace App\Services;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class InvoiceService
{
    /**
     * Générer une facture PDF pour une commande
     */
    public function generateInvoice(Order $order)
    {
        // Charger les relations nécessaires
        $order->load(['items.product', 'user']);

        // Préparer les données pour la facture
        $data = [
            'order' => $order,
            'company' => [
                'name' => 'Skincare Shop',
                'address' => '123 Rue du Commerce',
                'city' => 'Paris',
                'postal_code' => '75001',
                'phone' => '01 23 45 67 89',
                'email' => 'contact@skincareshop.com',
                'siret' => '123 456 789 00012',
                'tva_number' => 'FR12345678900'
            ],
            'invoice_number' => 'FACT-' . $order->order_number,
            'invoice_date' => now()->format('d/m/Y'),
            'due_date' => now()->addDays(30)->format('d/m/Y'),
        ];

        // Générer le PDF
        $pdf = Pdf::loadView('pdfs.invoice', $data);
        
        // Configurer le PDF
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'Arial'
        ]);

        return $pdf;
    }

    /**
     * Sauvegarder la facture PDF sur le serveur
     */
    public function saveInvoice(Order $order)
    {
        $pdf = $this->generateInvoice($order);
        
        $filename = 'invoices/' . $order->order_number . '_' . date('Y-m-d') . '.pdf';
        
        // Sauvegarder le fichier
        Storage::put('public/' . $filename, $pdf->output());
        
        return $filename;
    }

    /**
     * Télécharger la facture PDF
     */
    public function downloadInvoice(Order $order)
    {
        $pdf = $this->generateInvoice($order);
        
        $filename = 'facture_' . $order->order_number . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Obtenir l'URL de la facture
     */
    public function getInvoiceUrl(Order $order)
    {
        $filename = 'invoices/' . $order->order_number . '_' . date('Y-m-d') . '.pdf';
        
        if (Storage::exists('public/' . $filename)) {
            return Storage::url($filename);
        }
        
        // Si le fichier n'existe pas, le créer
        $this->saveInvoice($order);
        
        return Storage::url($filename);
    }
} 