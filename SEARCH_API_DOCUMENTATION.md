# 🔍 Documentation - Recherche et Filtrage Avancé

## 🎯 Vue d'ensemble

Le système de recherche et filtrage avancé permet aux utilisateurs de trouver rapidement les produits qu'ils recherchent grâce à des fonctionnalités de recherche intelligente, de filtrage multiple et de tri personnalisé.

## 📋 Fonctionnalités

### ✅ **Recherche Intelligente**
- Recherche par nom, description, catégorie
- Suggestions automatiques
- Recherche en temps réel
- Correspondance partielle

### ✅ **Filtrage Avancé**
- Par catégorie
- Par plage de prix
- Par disponibilité en stock
- Par statut de produit
- Par stock faible

### ✅ **Tri et Pagination**
- Tri par nom, prix, stock, date
- Pagination personnalisable
- Limitation des résultats
- Navigation entre pages

## 📋 Routes API - Recherche

### **Liste des Produits avec Filtres**
```http
GET /api/products
```

**Paramètres de requête :**
- `search` : Recherche par texte
- `category_id` : Filtrage par catégorie
- `min_price` / `max_price` : Plage de prix
- `in_stock` : Produits en stock (true/false)
- `active` : Produits actifs (true/false)
- `low_stock` : Produits avec stock faible (true)
- `sort_by` : Champ de tri (name, price, stock_quantity, created_at)
- `sort_order` : Ordre de tri (asc, desc)
- `per_page` : Nombre d'éléments par page (1-100)

**Exemple :**
```http
GET /api/products?search=crème&min_price=10&max_price=50&in_stock=true&sort_by=price&sort_order=asc&per_page=20
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
                "name": "Crème hydratante",
                "price": "25.50",
                "stock_quantity": 15,
                "stock_status": "in_stock",
                "is_in_stock": true,
                "is_low_stock": false,
                "is_available": true,
                "category": {
                    "id": 1,
                    "name": "Hydratation"
                }
            }
        ],
        "last_page": 3,
        "per_page": 20,
        "total": 45
    }
}
```

### **Recherche Rapide**
```http
GET /api/products/search?q={query}
```

**Réponse :**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Crème hydratante",
            "price": "25.50",
            "category": "Hydratation",
            "stock_quantity": 15,
            "is_available": true
        }
    ]
}
```

### **Suggestions de Recherche**
```http
GET /api/products/suggestions?q={query}
```

**Réponse :**
```json
{
    "success": true,
    "data": [
        "Crème hydratante",
        "Crème anti-âge",
        "Hydratation"
    ]
}
```

### **Filtres Disponibles**
```http
GET /api/products/filters
```

**Réponse :**
```json
{
    "success": true,
    "data": {
        "categories": [
            {"id": 1, "name": "Hydratation"},
            {"id": 2, "name": "Anti-âge"}
        ],
        "price_ranges": [
            {"min": 0, "max": 10, "label": "0€ - 10€"},
            {"min": 10, "max": 25, "label": "10€ - 25€"},
            {"min": 25, "max": 50, "label": "25€ - 50€"},
            {"min": 50, "max": 100, "label": "50€ - 100€"},
            {"min": 100, "max": null, "label": "100€+"}
        ],
        "stock_statuses": [
            {"value": "in_stock", "label": "En stock"},
            {"value": "low_stock", "label": "Stock faible"},
            {"value": "out_of_stock", "label": "Rupture"},
            {"value": "inactive", "label": "Inactif"}
        ],
        "sort_options": [
            {"value": "name", "label": "Nom"},
            {"value": "price", "label": "Prix"},
            {"value": "stock_quantity", "label": "Stock"},
            {"value": "created_at", "label": "Date de création"}
        ],
        "price_stats": {
            "min_price": 5.00,
            "max_price": 150.00,
            "avg_price": 45.25
        }
    }
}
```

### **Produits Populaires**
```http
GET /api/products/popular
```

### **Produits Récents**
```http
GET /api/products/recent
```

## 🔧 Service SearchService

### **Méthodes Principales**
- `searchProducts()` : Recherche de base
- `getSuggestions()` : Suggestions de recherche
- `getPopularProducts()` : Produits populaires
- `getRecentProducts()` : Produits récents
- `getAvailableFilters()` : Filtres disponibles
- `searchWithPagination()` : Recherche avec pagination

### **Filtres Disponibles**
- **Catégorie** : `category_id`
- **Prix** : `min_price`, `max_price`
- **Stock** : `in_stock`, `low_stock`
- **Statut** : `active`, `stock_status`
- **Tri** : `sort_by`, `sort_order`
- **Pagination** : `per_page`

## 🎛️ Exemples d'Utilisation

### **Recherche Simple**
```bash
curl "http://127.0.0.1:8000/api/products?search=crème"
```

### **Filtrage par Prix**
```bash
curl "http://127.0.0.1:8000/api/products?min_price=10&max_price=50"
```

### **Produits en Stock**
```bash
curl "http://127.0.0.1:8000/api/products?in_stock=true"
```

### **Tri par Prix**
```bash
curl "http://127.0.0.1:8000/api/products?sort_by=price&sort_order=desc"
```

### **Recherche Combinée**
```bash
curl "http://127.0.0.1:8000/api/products?search=crème&min_price=10&in_stock=true&sort_by=price&per_page=10"
```

### **Suggestions**
```bash
curl "http://127.0.0.1:8000/api/products/suggestions?q=cr"
```

## 📊 Fonctionnalités Avancées

### **Recherche Intelligente**
- ✅ Recherche dans le nom et la description
- ✅ Recherche dans les catégories
- ✅ Correspondance partielle
- ✅ Suggestions automatiques

### **Filtrage Multiple**
- ✅ Combinaison de plusieurs filtres
- ✅ Filtres par plage de prix
- ✅ Filtres par disponibilité
- ✅ Filtres par statut de stock

### **Tri Personnalisé**
- ✅ Tri par nom (alphabétique)
- ✅ Tri par prix (croissant/décroissant)
- ✅ Tri par stock (quantité disponible)
- ✅ Tri par date de création

### **Pagination Optimisée**
- ✅ Limitation du nombre de résultats
- ✅ Navigation entre pages
- ✅ Métadonnées de pagination
- ✅ Performance optimisée

## 🧪 Test du Système

### **Via Script PHP**
```bash
php test_search.php
```

### **Via curl**
```bash
# Recherche simple
curl "http://127.0.0.1:8000/api/products?search=crème"

# Filtrage avancé
curl "http://127.0.0.1:8000/api/products?min_price=10&max_price=50&in_stock=true"

# Suggestions
curl "http://127.0.0.1:8000/api/products/suggestions?q=cr"
```

## 🚀 Fonctionnalités Avancées

### **Performance**
- ✅ Requêtes optimisées avec Eloquent
- ✅ Indexation des champs de recherche
- ✅ Cache des résultats fréquents
- ✅ Limitation des requêtes

### **Sécurité**
- ✅ Validation des paramètres
- ✅ Protection contre les injections SQL
- ✅ Limitation des résultats
- ✅ Sanitisation des entrées

### **Flexibilité**
- ✅ Filtres combinables
- ✅ Tri personnalisable
- ✅ Pagination configurable
- ✅ Suggestions intelligentes

## 📝 Notes Importantes

- Les recherches sont insensibles à la casse
- Les suggestions nécessitent au moins 2 caractères
- La pagination est limitée à 100 éléments par page
- Les filtres peuvent être combinés librement
- Les résultats incluent les informations de stock

## 🔍 Dépannage

### **Recherche Ne Fonctionne Pas**
1. Vérifier la syntaxe des paramètres
2. Consulter les logs Laravel
3. Tester avec des requêtes simples

### **Filtres Non Appliqués**
1. Vérifier les noms des paramètres
2. Consulter la documentation des filtres
3. Tester les filtres individuellement

### **Performance Lente**
1. Vérifier les index de base de données
2. Optimiser les requêtes Eloquent
3. Considérer l'utilisation du cache

## 🚀 Prochaines Améliorations

1. **Recherche full-text** avec Elasticsearch
2. **Filtres dynamiques** basés sur les données
3. **Recherche par tags** et attributs
4. **Historique de recherche** utilisateur
5. **Recherche par image** (reconnaissance visuelle)
6. **Suggestions contextuelles** basées sur l'historique 