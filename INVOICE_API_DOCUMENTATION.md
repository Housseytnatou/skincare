# 📄 Documentation API - Génération de Factures PDF

## 🎯 Vue d'ensemble

Le système de génération de factures PDF permet de créer, télécharger et prévisualiser des factures pour chaque commande. Les factures sont générées automatiquement avec toutes les informations nécessaires.

## 🔐 Authentification

Toutes les routes nécessitent une authentification via Sanctum. Incluez le token dans le header :
```
Authorization: Bearer {votre_token}
```

## 📋 Routes API - Factures

### 1. **Télécharger une facture PDF**
```http
GET /api/orders/{id}/invoice/download
```

**Réponse :** Fichier PDF à télécharger

### 2. **Obtenir l'URL de la facture**
```http
GET /api/orders/{id}/invoice/url
```

**Réponse :**
```json
{
    "success": true,
    "data": {
        "invoice_url": "http://127.0.0.1:8000/storage/invoices/ORD-20250127-ABC123_2025-01-27.pdf"
    }
}
```

### 3. **Prévisualiser une facture PDF**
```http
GET /api/orders/{id}/invoice/preview
```

**Réponse :** Flux PDF pour prévisualisation dans le navigateur

## 📄 Contenu de la Facture

La facture PDF contient :

### **En-tête**
- Logo et nom de l'entreprise
- Informations de l'entreprise (adresse, SIRET, TVA)
- Numéro de facture unique
- Date de facturation et échéance

### **Informations Client**
- Nom et email du client
- Adresse de facturation
- Adresse de livraison
- Téléphone de livraison

### **Détail de la Commande**
- Liste des produits avec quantités
- Prix unitaires et totaux
- Calcul des taxes (20% TVA)
- Total TTC

### **Informations de Paiement**
- Mode de paiement choisi
- Statut du paiement
- Référence de paiement (si applicable)

### **Pied de page**
- Remerciements
- Coordonnées de contact
- Date de génération

## 🧪 Test de l'API

### Via le Navigateur
Allez à : `http://127.0.0.1:8000/invoice_test.html`

### Via curl
```bash
# Se connecter
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test2@example.com",
    "password": "password123"
  }'

# Télécharger une facture
curl -X GET http://127.0.0.1:8000/api/orders/1/invoice/download \
  -H "Authorization: Bearer {token}" \
  --output facture.pdf
```

## 🔧 Configuration

### **Stockage des Fichiers**
Les factures sont stockées dans : `storage/app/public/invoices/`

### **Template HTML**
Le template de facture se trouve dans : `resources/views/pdfs/invoice.blade.php`

### **Service de Génération**
Le service de génération se trouve dans : `app/Services/InvoiceService.php`

## 📊 Fonctionnalités

✅ **Génération automatique** des factures
✅ **Template professionnel** avec logo et mise en page
✅ **Calcul automatique** des taxes
✅ **Informations complètes** client et commande
✅ **Téléchargement** direct en PDF
✅ **Prévisualisation** dans le navigateur
✅ **Stockage** des factures sur le serveur
✅ **URLs publiques** pour accès direct

## 🎨 Personnalisation

### **Modifier les Informations de l'Entreprise**
Éditez le fichier `app/Services/InvoiceService.php` :

```php
'company' => [
    'name' => 'Votre Nom d\'Entreprise',
    'address' => 'Votre Adresse',
    'city' => 'Votre Ville',
    'postal_code' => 'Votre Code Postal',
    'phone' => 'Votre Téléphone',
    'email' => 'votre@email.com',
    'siret' => 'Votre SIRET',
    'tva_number' => 'Votre Numéro TVA'
],
```

### **Modifier le Style de la Facture**
Éditez le fichier `resources/views/pdfs/invoice.blade.php` dans la section `<style>`.

## 🚀 Prochaines Améliorations

1. **Envoi automatique** des factures par email
2. **Archivage** des anciennes factures
3. **Génération de factures** en lot
4. **Templates multiples** selon le type de client
5. **Signature électronique** des factures

## 📝 Notes Importantes

- Les factures sont générées à la demande
- Le stockage est limité, pensez à nettoyer les anciennes factures
- Le template utilise des emojis pour le logo (🛍️)
- Les montants sont formatés en euros avec séparateurs
- La TVA est fixée à 20% (modifiable dans le service) 