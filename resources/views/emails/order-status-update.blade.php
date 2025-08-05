<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mise à jour de commande</title>
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
        .status-update {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #007bff;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            color: white;
            margin: 10px 0;
        }
        .status-processing { background: #ffc107; }
        .status-shipped { background: #17a2b8; }
        .status-delivered { background: #28a745; }
        .status-cancelled { background: #dc3545; }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 0.9em;
        }
        .order-info {
            background: #e9ecef;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🛍️ {{ $company['name'] }}</h1>
        <h2>Mise à jour de votre commande</h2>
        <p>Votre commande a été mise à jour</p>
    </div>

    <div class="content">
        <p>Bonjour <strong>{{ $order->user->name }}</strong>,</p>
        
        <p>Nous vous informons que le statut de votre commande a été mis à jour.</p>

        <div class="status-update">
            <h3>📋 Détails de la mise à jour</h3>
            
            <div class="order-info">
                <p><strong>Numéro de commande :</strong> {{ $order->order_number }}</p>
                <p><strong>Date de commande :</strong> {{ $order->created_at->format('d/m/Y à H:i') }}</p>
            </div>

            <h4>🔄 Changement de statut :</h4>
            <p>
                <strong>Ancien statut :</strong> 
                <span class="status-badge status-{{ $oldStatus }}">{{ ucfirst($oldStatus) }}</span>
            </p>
            <p>
                <strong>Nouveau statut :</strong> 
                <span class="status-badge status-{{ $newStatus }}">{{ ucfirst($newStatus) }}</span>
            </p>

            @if($newStatus === 'processing')
            <div class="status-update">
                <h4>⚙️ Votre commande est en cours de traitement</h4>
                <p>Notre équipe prépare votre commande avec soin. Elle sera expédiée dans les plus brefs délais.</p>
            </div>
            @endif

            @if($newStatus === 'shipped')
            <div class="status-update">
                <h4>📦 Votre commande a été expédiée !</h4>
                <p>Votre commande est maintenant en route vers vous. Vous devriez la recevoir dans les prochains jours.</p>
                <p><strong>Adresse de livraison :</strong><br>
                {{ $order->shipping_address }}<br>
                {{ $order->shipping_postal_code }} {{ $order->shipping_city }}</p>
            </div>
            @endif

            @if($newStatus === 'delivered')
            <div class="status-update">
                <h4>✅ Votre commande a été livrée !</h4>
                <p>Votre commande a été livrée avec succès. Nous espérons que vous êtes satisfait(e) de votre achat !</p>
                <p>N'hésitez pas à nous faire part de votre avis sur nos produits.</p>
            </div>
            @endif

            @if($newStatus === 'cancelled')
            <div class="status-update">
                <h4>❌ Votre commande a été annulée</h4>
                <p>Votre commande a été annulée. Si vous avez des questions, n'hésitez pas à nous contacter.</p>
                <p>Si vous souhaitez passer une nouvelle commande, nous serons ravis de vous aider.</p>
            </div>
            @endif
        </div>

        <div class="status-update">
            <h3>📦 Récapitulatif de votre commande</h3>
            <p><strong>Total de la commande :</strong> {{ number_format($order->total, 2, ',', ' ') }} €</p>
            <p><strong>Mode de paiement :</strong> 
                @if($order->payment_method === 'online')
                    Paiement en ligne
                @else
                    Paiement à la livraison
                @endif
            </p>
        </div>

        <p>Pour toute question concernant votre commande, n'hésitez pas à nous contacter :</p>
        <ul>
            <li>📧 Email : {{ $company['email'] }}</li>
            <li>📞 Téléphone : {{ $company['phone'] }}</li>
        </ul>

        <p>Cordialement,<br>
        L'équipe {{ $company['name'] }}</p>
    </div>

    <div class="footer">
        <p>Cet email a été envoyé automatiquement suite à la mise à jour de votre commande.</p>
        <p>© {{ date('Y') }} {{ $company['name'] }} - Tous droits réservés</p>
    </div>
</body>
</html> 