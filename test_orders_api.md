# 🧪 Guide de Test - API Commandes

## 📋 Prérequis

1. Serveur Laravel démarré : `php artisan serve`
2. Base de données configurée
3. Utilisateur connecté avec token Sanctum

## 🔧 Configuration Initiale

### 1. Exécuter les migrations
```bash
php artisan migrate
```

### 2. Créer des données de test
```bash
php artisan db:seed --class=OrderSeeder
```

### 3. Obtenir un token d'authentification
```bash
# Créer un utilisateur (si pas déjà fait)
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'

# Se connecter
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }'
```

## 🧪 Tests des Endpoints

### Test 1 : Lister les commandes
```bash
curl -X GET http://localhost:8000/api/orders \
  -H "Authorization: Bearer {votre_token}"
```

### Test 2 : Créer une commande
```bash
curl -X POST http://localhost:8000/api/orders \
  -H "Authorization: Bearer {votre_token}" \
  -H "Content-Type: application/json" \
  -d '{
    "shipping_address": "456 Avenue des Champs",
    "shipping_city": "Lyon",
    "shipping_postal_code": "69001",
    "shipping_phone": "0987654321",
    "payment_method": "online",
    "notes": "Livraison en matinée"
  }'
```

### Test 3 : Voir une commande spécifique
```bash
curl -X GET http://localhost:8000/api/orders/1 \
  -H "Authorization: Bearer {votre_token}"
```

### Test 4 : Traiter un paiement
```bash
curl -X POST http://localhost:8000/api/orders/1/payment \
  -H "Authorization: Bearer {votre_token}"
```

### Test 5 : Mettre à jour le statut
```bash
curl -X PUT http://localhost:8000/api/orders/1 \
  -H "Authorization: Bearer {votre_token}" \
  -H "Content-Type: application/json" \
  -d '{
    "order_status": "shipped",
    "payment_status": "paid"
  }'
```

### Test 6 : Annuler une commande
```bash
curl -X POST http://localhost:8000/api/orders/1/cancel \
  -H "Authorization: Bearer {votre_token}"
```

### Test 7 : Statistiques (Admin)
```bash
curl -X GET http://localhost:8000/api/orders/statistics \
  -H "Authorization: Bearer {token_admin}"
```

## ✅ Résultats Attendus

### Création de commande réussie
```json
{
  "success": true,
  "message": "Commande créée avec succès",
  "data": {
    "id": 1,
    "order_number": "ORD-20250127-ABC123",
    "shipping_address": "456 Avenue des Champs",
    "subtotal": 100.00,
    "tax": 20.00,
    "total": 120.00,
    "order_status": "pending",
    "payment_method": "online",
    "payment_status": "pending"
  }
}
```

### Paiement traité
```json
{
  "success": true,
  "message": "Paiement traité avec succès",
  "data": {
    "order": {...},
    "payment_id": "PAY-ABC123DEF456"
  }
}
```

## 🚨 Cas d'Erreur

### Panier vide
```json
{
  "success": false,
  "message": "Le panier est vide"
}
```

### Validation échouée
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "shipping_address": ["Le champ adresse de livraison est obligatoire."]
  }
}
```

### Accès non autorisé
```json
{
  "success": false,
  "message": "Accès non autorisé"
}
```

## 📊 Vérifications

1. ✅ Les commandes sont créées avec un numéro unique
2. ✅ Le panier est vidé après création de commande
3. ✅ Les taxes sont calculées correctement (20%)
4. ✅ Les statuts sont mis à jour correctement
5. ✅ Les paiements sont simulés avec succès
6. ✅ Les statistiques sont accessibles aux admins
7. ✅ La validation des données fonctionne
8. ✅ Les transactions garantissent l'intégrité

## 🔄 Workflow Complet

1. **Ajouter des produits au panier**
2. **Créer une commande** depuis le panier
3. **Traiter le paiement** (simulation)
4. **Mettre à jour le statut** (admin)
5. **Vérifier les statistiques** (admin)

Le système de commandes est maintenant fonctionnel et prêt pour la suite du développement ! 