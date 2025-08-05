# 📋 Documentation API - Système de Commandes

## 🎯 Vue d'ensemble

Le système de commandes permet aux utilisateurs de passer des commandes depuis leur panier et aux administrateurs de gérer ces commandes. Il inclut la gestion des statuts, des paiements et des statistiques.

## 🔐 Authentification

Toutes les routes nécessitent une authentification via Sanctum. Incluez le token dans le header :
```
Authorization: Bearer {votre_token}
```

## 📋 Routes API

### 1. **Lister les commandes de l'utilisateur**
```http
GET /api/orders
```

**Réponse :**
```json
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "order_number": "ORD-20250127-ABC123",
                "shipping_address": "123 Rue de la Paix",
                "shipping_city": "Paris",
                "subtotal": 100.00,
                "tax": 20.00,
                "total": 120.00,
                "order_status": "pending",
                "payment_method": "online",
                "payment_status": "pending",
                "created_at": "2025-01-27T10:00:00.000000Z",
                "items": [...]
            }
        ]
    }
}
```

### 2. **Afficher une commande spécifique**
```http
GET /api/orders/{id}
```

### 3. **Créer une nouvelle commande**
```http
POST /api/orders
```

**Body :**
```json
{
    "shipping_address": "123 Rue de la Paix",
    "shipping_city": "Paris",
    "shipping_postal_code": "75001",
    "shipping_phone": "0123456789",
    "payment_method": "online",
    "notes": "Livraison en matinée"
}
```

**Modes de paiement disponibles :**
- `online` : Paiement en ligne (simulé)
- `cash_on_delivery` : Paiement à la livraison

### 4. **Mettre à jour le statut d'une commande**
```http
PUT /api/orders/{id}
```

**Body :**
```json
{
    "order_status": "shipped",
    "payment_status": "paid",
    "notes": "Commande expédiée"
}
```

**Statuts de commande :**
- `pending` : En attente
- `processing` : En cours de traitement
- `shipped` : Expédiée
- `delivered` : Livrée
- `cancelled` : Annulée

**Statuts de paiement :**
- `pending` : En attente
- `paid` : Payé
- `failed` : Échoué

### 5. **Annuler une commande**
```http
POST /api/orders/{id}/cancel
```

### 6. **Traiter un paiement en ligne**
```http
POST /api/orders/{id}/payment
```

### 7. **Statistiques (Admin uniquement)**
```http
GET /api/orders/statistics
```

**Réponse :**
```json
{
    "success": true,
    "data": {
        "total_orders": 25,
        "pending_orders": 5,
        "total_revenue": 1500.00,
        "pending_payments": 3
    }
}
```

## 🗄️ Structure de la Base de Données

### Table `orders`
- `id` : Identifiant unique
- `user_id` : ID de l'utilisateur
- `order_number` : Numéro de commande unique
- `shipping_address` : Adresse de livraison
- `shipping_city` : Ville de livraison
- `shipping_postal_code` : Code postal
- `shipping_phone` : Téléphone
- `subtotal` : Sous-total HT
- `tax` : Taxes (20% TVA)
- `total` : Total TTC
- `order_status` : Statut de la commande
- `payment_method` : Mode de paiement
- `payment_status` : Statut du paiement
- `payment_id` : ID du paiement
- `paid_at` : Date de paiement
- `notes` : Notes
- `created_at` / `updated_at` : Timestamps

### Table `order_items`
- `id` : Identifiant unique
- `order_id` : ID de la commande
- `product_id` : ID du produit
- `product_name` : Nom du produit (snapshot)
- `price` : Prix unitaire
- `quantity` : Quantité
- `total` : Total pour cet article

## 🔧 Fonctionnalités Implémentées

✅ **Création de commandes** depuis le panier
✅ **Gestion des statuts** de commande et paiement
✅ **Calcul automatique** des taxes (20% TVA)
✅ **Numérotation unique** des commandes
✅ **Simulation de paiement** en ligne
✅ **Gestion des paiements** après livraison
✅ **Statistiques** pour les administrateurs
✅ **Validation** des données
✅ **Transactions** pour garantir l'intégrité

## 🚀 Prochaines Étapes

1. **Génération de factures PDF**
2. **Système d'emails automatiques**
3. **Interface d'administration**
4. **Gestion des stocks**
5. **Historique des modifications**

## 🧪 Test de l'API

Pour tester l'API, vous pouvez utiliser Postman ou curl :

```bash
# Créer une commande
curl -X POST http://localhost:8000/api/orders \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "shipping_address": "123 Rue de la Paix",
    "shipping_city": "Paris",
    "shipping_postal_code": "75001",
    "shipping_phone": "0123456789",
    "payment_method": "online"
  }'
```

## 📝 Notes Importantes

- Les commandes sont créées depuis le panier de l'utilisateur
- Le panier est automatiquement vidé après création de la commande
- Les taxes sont calculées automatiquement (20% TVA)
- Seuls les administrateurs peuvent voir les statistiques
- Les utilisateurs ne peuvent voir que leurs propres commandes 