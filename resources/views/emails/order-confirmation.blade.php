<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de commande</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            background: #007bff;
            color: white;
            padding: 30px;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .order-details {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #007bff;
        }
        .product-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .product-item:last-child {
            border-bottom: none;
        }
        .total {
            font-weight: bold;
            font-size: 1.2em;
            text-align: right;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #007bff;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 0.9em;
        }
        .button {
            display: inline-block;
            background: #007bff;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
        }
        .status {
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🛍️ {{ $company['name'] }}</h1>
        <h2>Confirmation de commande</h2>
        <p>Merci pour votre commande !</p>
    </div>

    <div class="content">
        <p>Bonjour <strong>{{ $order->user->name }}</strong>,</p>
        
        <p>Nous avons bien reçu votre commande et nous vous en remercions !</p>

        <div class="order-details">
            <h3>📋 Détails de votre commande</h3>
            <p><strong>Numéro de commande :</strong> {{ $order->order_number }}</p>
            <p><strong>Date de commande :</strong> {{ $order->created_at->format('d/m/Y à H:i') }}</p>
            <p><strong>Statut :</strong> <span class="status">{{ ucfirst($order->order_status) }}</span></p>
            
            <h4>📦 Produits commandés :</h4>
            @foreach($order->items as $item)
            <div class="product-item">
                <span>{{ $item->product_name }} (x{{ $item->quantity }})</span>
                <span>{{ number_format($item->total, 2, ',', ' ') }} €</span>
            </div>
            @endforeach
            
            <div class="total">
                <div>Sous-total HT : {{ number_format($order->subtotal, 2, ',', ' ') }} €</div>
                <div>TVA (20%) : {{ number_format($order->tax, 2, ',', ' ') }} €</div>
                <div><strong>Total TTC : {{ number_format($order->total, 2, ',', ' ') }} €</strong></div>
            </div>
        </div>

        <div class="order-details">
            <h3>🚚 Informations de livraison</h3>
            <p><strong>Adresse :</strong><br>
            {{ $order->shipping_address }}<br>
            {{ $order->shipping_postal_code }} {{ $order->shipping_city }}<br>
            <strong>Téléphone :</strong> {{ $order->shipping_phone }}</p>
        </div>

        <div class="order-details">
            <h3>💳 Informations de paiement</h3>
            <p><strong>Mode de paiement :</strong> 
                @if($order->payment_method === 'online')
                    Paiement en ligne
                @else
                    Paiement à la livraison
                @endif
            </p>
            <p><strong>Statut du paiement :</strong> 
                @if($order->payment_status === 'paid')
                    ✅ Payé
                @else
                    ⏳ En attente
                @endif
            </p>
        </div>

        <p>Nous vous tiendrons informé(e) de l'avancement de votre commande par email.</p>

        <p>Pour toute question concernant votre commande, n'hésitez pas à nous contacter :</p>
        <ul>
            <li>📧 Email : {{ $company['email'] }}</li>
            <li>📞 Téléphone : {{ $company['phone'] }}</li>
        </ul>

        <p>Cordialement,<br>
        L'équipe {{ $company['name'] }}</p>
    </div>

    <div class="footer">
        <p>Cet email a été envoyé automatiquement suite à votre commande.</p>
        <p>© {{ date('Y') }} {{ $company['name'] }} - Tous droits réservés</p>
    </div>
</body>
</html> 