<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de paiement</title>
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
            background: #28a745;
            color: white;
            padding: 30px;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .payment-details {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #28a745;
        }
        .success-icon {
            font-size: 3em;
            text-align: center;
            margin: 20px 0;
        }
        .order-info {
            background: #e9ecef;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 0.9em;
        }
        .amount {
            font-size: 1.5em;
            font-weight: bold;
            color: #28a745;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🛍️ {{ $company['name'] }}</h1>
        <h2>Confirmation de paiement</h2>
        <p>Paiement traité avec succès !</p>
    </div>

    <div class="content">
        <div class="success-icon">✅</div>
        
        <p>Bonjour <strong>{{ $order->user->name }}</strong>,</p>
        
        <p>Nous vous confirmons que votre paiement a été traité avec succès !</p>

        <div class="payment-details">
            <h3>💳 Détails du paiement</h3>
            
            <div class="order-info">
                <p><strong>Numéro de commande :</strong> {{ $order->order_number }}</p>
                <p><strong>Date de paiement :</strong> {{ $order->paid_at->format('d/m/Y à H:i') }}</p>
                @if($paymentId)
                <p><strong>Référence de paiement :</strong> {{ $paymentId }}</p>
                @endif
            </div>

            <h4>💰 Montant payé</h4>
            <p class="amount">{{ number_format($order->total, 2, ',', ' ') }} €</p>
            
            <p><strong>Mode de paiement :</strong> 
                @if($order->payment_method === 'online')
                    Paiement en ligne
                @else
                    Paiement à la livraison
                @endif
            </p>
        </div>

        <div class="payment-details">
            <h3>📦 Récapitulatif de votre commande</h3>
            <p><strong>Produits commandés :</strong></p>
            <ul>
                @foreach($order->items as $item)
                <li>{{ $item->product_name }} (x{{ $item->quantity }}) - {{ number_format($item->total, 2, ',', ' ') }} €</li>
                @endforeach
            </ul>
            
            <p><strong>Adresse de livraison :</strong><br>
            {{ $order->shipping_address }}<br>
            {{ $order->shipping_postal_code }} {{ $order->shipping_city }}</p>
        </div>

        <div class="payment-details">
            <h3>📋 Prochaines étapes</h3>
            <p>Votre commande va maintenant être traitée par notre équipe :</p>
            <ol>
                <li>✅ <strong>Paiement confirmé</strong> (terminé)</li>
                <li>⚙️ <strong>Préparation de la commande</strong> (en cours)</li>
                <li>📦 <strong>Expédition</strong> (à venir)</li>
                <li>🚚 <strong>Livraison</strong> (à venir)</li>
            </ol>
            
            <p>Nous vous tiendrons informé(e) de chaque étape par email.</p>
        </div>

        <p>Merci de votre confiance !</p>

        <p>Pour toute question concernant votre commande, n'hésitez pas à nous contacter :</p>
        <ul>
            <li>📧 Email : {{ $company['email'] }}</li>
            <li>📞 Téléphone : {{ $company['phone'] }}</li>
        </ul>

        <p>Cordialement,<br>
        L'équipe {{ $company['name'] }}</p>
    </div>

    <div class="footer">
        <p>Cet email a été envoyé automatiquement suite à la confirmation de votre paiement.</p>
        <p>© {{ date('Y') }} {{ $company['name'] }} - Tous droits réservés</p>
    </div>
</body>
</html> 