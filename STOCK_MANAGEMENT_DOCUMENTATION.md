# 📦 Documentation - Gestion des Stocks

## 🎯 Vue d'ensemble

Le système de gestion des stocks permet de suivre les quantités disponibles, empêcher les commandes si stock insuffisant, et générer des alertes de stock faible. Il s'intègre parfaitement avec le système de commandes.

## 📊 Fonctionnalités

### ✅ **Gestion Automatique des Stocks**
- Vérification automatique lors de la création de commande
- Réservation de stock lors de la validation de commande
- Restauration de stock lors de l'annulation de commande
- Empêche les commandes si stock insuffisant

### ✅ **Alertes et Notifications**
- Alertes de stock faible
- Produits en rupture de stock
- Statistiques détaillées
- Historique des mouvements

### ✅ **Statuts de Stock**
- `in_stock` : Produit disponible
- `low_stock` : Stock faible (≤ seuil d'alerte)
- `out_of_stock` : Rupture de stock
- `inactive` : Produit désactivé

## 🏗️ Architecture

### **Modèle Product**
- `stock_quantity` : Quantité en stock
- `min_stock_alert` : Seuil d'alerte de stock faible
- `is_active` : Produit actif/inactif

### **Méthodes du Modèle**
- `isInStock()` : Vérifier si en stock
- `isLowStock()` : Vérifier si stock faible
- `isAvailable()` : Vérifier si disponible
- `reduceStock()` : Réduire le stock
- `increaseStock()` : Augmenter le stock
- `getStockStatus()` : Obtenir le statut

### **Scopes Eloquent**
- `inStock()` : Produits en stock
- `active()` : Produits actifs
- `lowStock()` : Produits avec stock faible

## 🔧 Service StockService

### **Vérification de Disponibilité**
```php
$availability = $stockService->checkOrderAvailability($cartItems);
```

### **Réservation de Stock**
```php
$results = $stockService->reserveStockForOrder($order);
```

### **Restauration de Stock**
```php
$results = $stockService->restoreStockForOrder($order);
```

### **Statistiques**
```php
$stats = $stockService->getStockStatistics();
```

## 📋 Routes API - Gestion des Stocks

### **Statistiques de Stock**
```http
GET /api/stock/statistics
```

**Réponse :**
```json
{
    "success": true,
    "data": {
        "total_products": 10,
        "active_products": 8,
        "in_stock_products": 6,
        "low_stock_products": 2,
        "out_of_stock_products": 2,
        "total_stock_value": 1250.50
    }
}
```

### **Produits avec Stock Faible**
```http
GET /api/stock/low-stock
```

### **Produits en Rupture**
```http
GET /api/stock/out-of-stock
```

### **Alertes de Stock**
```http
GET /api/stock/alerts
```

### **Vérifier la Disponibilité**
```http
POST /api/stock/check-availability
```

**Body :**
```json
{
    "items": [
        {"product_id": 1, "quantity": 2},
        {"product_id": 2, "quantity": 1}
    ]
}
```

### **Mettre à Jour le Stock**
```http
PUT /api/stock/products/{id}
```

**Body :**
```json
{
    "stock_quantity": 25,
    "min_stock_alert": 5,
    "is_active": true
}
```

### **Historique des Mouvements**
```http
GET /api/stock/history?product_id=1
```

### **Tous les Produits avec Stock**
```http
GET /api/stock/products
```

## 🔄 Intégration avec les Commandes

### **Création de Commande**
1. Vérification automatique de la disponibilité
2. Réservation du stock si disponible
3. Création de la commande
4. Réduction automatique des stocks

### **Annulation de Commande**
1. Annulation de la commande
2. Restauration automatique du stock
3. Notification des changements

### **Mise à Jour de Statut**
- Si commande annulée → Restauration du stock
- Si commande livrée → Stock définitivement réduit

## 📊 Statistiques et Rapports

### **Métriques Disponibles**
- Nombre total de produits
- Produits actifs/inactifs
- Produits en stock/rupture
- Valeur totale du stock
- Produits avec stock faible

### **Alertes Automatiques**
- Stock faible (≤ seuil d'alerte)
- Rupture de stock
- Produits inactifs

## 🧪 Test du Système

### **Via Script PHP**
```bash
php test_stock.php
```

### **Via API**
```bash
# Obtenir les statistiques
curl -X GET http://127.0.0.1:8000/api/stock/statistics \
  -H "Authorization: Bearer {token}"

# Vérifier la disponibilité
curl -X POST http://127.0.0.1:8000/api/stock/check-availability \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "items": [
      {"product_id": 1, "quantity": 2}
    ]
  }'
```

## 🚀 Fonctionnalités Avancées

### **Gestion Intelligente**
- ✅ Vérification automatique lors des commandes
- ✅ Réservation de stock en temps réel
- ✅ Restauration automatique lors d'annulation
- ✅ Alertes de stock faible
- ✅ Statistiques détaillées

### **Sécurité**
- ✅ Validation des quantités
- ✅ Empêche les commandes impossibles
- ✅ Gestion des erreurs de stock
- ✅ Logs des mouvements

### **Performance**
- ✅ Requêtes optimisées
- ✅ Scopes Eloquent
- ✅ Cache des statistiques
- ✅ Transactions de base de données

## 📝 Configuration

### **Migration**
```bash
php artisan migrate
```

### **Seeder**
```bash
php artisan db:seed --class=StockSeeder
```

### **Variables d'Environnement**
```env
# Seuil d'alerte par défaut
DEFAULT_MIN_STOCK_ALERT=5

# Activation automatique des produits
AUTO_ACTIVATE_PRODUCTS=true
```

## 🔍 Dépannage

### **Stocks Incorrects**
1. Vérifier les migrations
2. Exécuter le seeder
3. Vérifier les transactions

### **Commandes Bloquées**
1. Vérifier la disponibilité des produits
2. Consulter les logs de stock
3. Vérifier les seuils d'alerte

### **Alertes Non Générées**
1. Vérifier les seuils d'alerte
2. Consulter les produits inactifs
3. Vérifier les permissions

## 🚀 Prochaines Améliorations

1. **Notifications en temps réel** des alertes de stock
2. **Gestion des fournisseurs** et réapprovisionnement
3. **Prévisions de stock** basées sur l'historique
4. **Gestion des entrepôts** multiples
5. **Synchronisation** avec les systèmes externes
6. **Rapports automatisés** par email 