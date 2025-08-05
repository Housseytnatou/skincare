<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture {{ $invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            font-size: 12px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
        }
        .company-info {
            flex: 1;
        }
        .invoice-info {
            flex: 1;
            text-align: right;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
        }
        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        .invoice-number {
            font-size: 16px;
            color: #666;
            margin-bottom: 5px;
        }
        .invoice-date {
            font-size: 14px;
            color: #666;
        }
        .client-info {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .client-details {
            display: flex;
            justify-content: space-between;
        }
        .client-address {
            flex: 1;
        }
        .shipping-address {
            flex: 1;
            text-align: right;
        }
        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .products-table th {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }
        .products-table td {
            border: 1px solid #ddd;
            padding: 10px;
        }
        .products-table .quantity {
            text-align: center;
        }
        .products-table .price {
            text-align: right;
        }
        .products-table .total {
            text-align: right;
        }
        .totals {
            width: 100%;
            margin-bottom: 30px;
        }
        .totals table {
            width: 100%;
            border-collapse: collapse;
        }
        .totals td {
            padding: 5px;
            border: none;
        }
        .totals .label {
            text-align: right;
            font-weight: bold;
        }
        .totals .amount {
            text-align: right;
        }
        .totals .total-row {
            font-size: 16px;
            font-weight: bold;
            border-top: 2px solid #333;
        }
        .payment-info {
            margin-bottom: 30px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        .logo {
            font-size: 32px;
            color: #007bff;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-info">
            <div class="logo">🛍️</div>
            <div class="company-name">{{ $company['name'] }}</div>
            <div>{{ $company['address'] }}</div>
            <div>{{ $company['postal_code'] }} {{ $company['city'] }}</div>
            <div>Tél: {{ $company['phone'] }}</div>
            <div>Email: {{ $company['email'] }}</div>
            <div>SIRET: {{ $company['siret'] }}</div>
            <div>TVA: {{ $company['tva_number'] }}</div>
        </div>
        <div class="invoice-info">
            <div class="invoice-title">FACTURE</div>
            <div class="invoice-number">N° {{ $invoice_number }}</div>
            <div class="invoice-date">Date: {{ $invoice_date }}</div>
            <div class="invoice-date">Échéance: {{ $due_date }}</div>
        </div>
    </div>

    <div class="client-info">
        <div class="section-title">Informations Client</div>
        <div class="client-details">
            <div class="client-address">
                <strong>Facturation:</strong><br>
                {{ $order->user->name }}<br>
                {{ $order->user->email }}
            </div>
            <div class="shipping-address">
                <strong>Livraison:</strong><br>
                {{ $order->shipping_address }}<br>
                {{ $order->shipping_postal_code }} {{ $order->shipping_city }}<br>
                Tél: {{ $order->shipping_phone }}
            </div>
        </div>
    </div>

    <div class="products-section">
        <div class="section-title">Détail de la Commande</div>
        <table class="products-table">
            <thead>
                <tr>
                    <th>Produit</th>
                    <th class="quantity">Quantité</th>
                    <th class="price">Prix unitaire</th>
                    <th class="total">Total HT</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td class="quantity">{{ $item->quantity }}</td>
                    <td class="price">{{ number_format($item->price, 2, ',', ' ') }} €</td>
                    <td class="total">{{ number_format($item->total, 2, ',', ' ') }} €</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="totals">
        <table>
            <tr>
                <td class="label">Sous-total HT:</td>
                <td class="amount">{{ number_format($order->subtotal, 2, ',', ' ') }} €</td>
            </tr>
            <tr>
                <td class="label">TVA (20%):</td>
                <td class="amount">{{ number_format($order->tax, 2, ',', ' ') }} €</td>
            </tr>
            <tr class="total-row">
                <td class="label">Total TTC:</td>
                <td class="amount">{{ number_format($order->total, 2, ',', ' ') }} €</td>
            </tr>
        </table>
    </div>

    <div class="payment-info">
        <div class="section-title">Informations de Paiement</div>
        <p><strong>Mode de paiement:</strong> 
            @if($order->payment_method === 'online')
                Paiement en ligne
            @else
                Paiement à la livraison
            @endif
        </p>
        <p><strong>Statut du paiement:</strong> 
            @if($order->payment_status === 'paid')
                ✅ Payé
            @else
                ⏳ En attente
            @endif
        </p>
        @if($order->payment_id)
        <p><strong>Référence de paiement:</strong> {{ $order->payment_id }}</p>
        @endif
    </div>

    <div class="footer">
        <p>Merci de votre confiance !</p>
        <p>Pour toute question, contactez-nous au {{ $company['phone'] }} ou par email à {{ $company['email'] }}</p>
        <p>Facture générée automatiquement le {{ now()->format('d/m/Y à H:i') }}</p>
    </div>
</body>
</html> 