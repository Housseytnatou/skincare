# 📧 Documentation - Système d'Emails Automatiques

## 🎯 Vue d'ensemble

Le système d'emails automatiques envoie des notifications aux clients à chaque étape importante de leur commande. Il est entièrement automatisé et s'intègre parfaitement avec le système de commandes.

## 📧 Types d'Emails

### 1. **Confirmation de Commande**
- **Déclencheur** : Création d'une nouvelle commande
- **Contenu** : Détails de la commande, produits, adresse de livraison
- **Template** : `resources/views/emails/order-confirmation.blade.php`

### 2. **Mise à Jour de Statut**
- **Déclencheur** : Changement du statut de commande
- **Contenu** : Ancien vs nouveau statut, informations de livraison
- **Template** : `resources/views/emails/order-status-update.blade.php`

### 3. **Confirmation de Paiement**
- **Déclencheur** : Paiement traité avec succès
- **Contenu** : Détails du paiement, montant, référence
- **Template** : `resources/views/emails/payment-confirmation.blade.php`

## 🔧 Configuration

### **Configuration Email (fichier .env)**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=votre@email.com
MAIL_PASSWORD=votre_mot_de_passe
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=votre@email.com
MAIL_FROM_NAME="${APP_NAME}"
```

### **Pour les Tests (Développement)**
```env
MAIL_MAILER=log
```
Les emails seront sauvegardés dans `storage/logs/laravel.log`

## 📋 Statuts de Commande et Emails

| Statut | Email Envoyé | Contenu |
|--------|--------------|---------|
| `pending` | Confirmation de commande | Détails de la commande |
| `processing` | Mise à jour de statut | Commande en cours de traitement |
| `shipped` | Mise à jour de statut | Commande expédiée |
| `delivered` | Mise à jour de statut | Commande livrée |
| `cancelled` | Mise à jour de statut | Commande annulée |

## 💳 Paiements et Emails

| Action | Email Envoyé | Contenu |
|--------|--------------|---------|
| Paiement en ligne traité | Confirmation de paiement | Détails du paiement |
| Paiement à la livraison marqué comme payé | Confirmation de paiement | Confirmation manuelle |

## 🏗️ Architecture

### **Classes Mail**
- `OrderConfirmation` : Email de confirmation de commande
- `OrderStatusUpdate` : Email de mise à jour de statut
- `PaymentConfirmation` : Email de confirmation de paiement

### **Service EmailService**
- `sendOrderConfirmation()` : Envoi confirmation commande
- `sendOrderStatusUpdate()` : Envoi mise à jour statut
- `sendPaymentConfirmation()` : Envoi confirmation paiement
- `sendOrderEmails()` : Envoi tous les emails d'une commande
- `sendStatusUpdateEmail()` : Envoi conditionnel mise à jour
- `sendPaymentEmail()` : Envoi confirmation paiement

### **Intégration dans OrderController**
- **Création de commande** : Envoi automatique confirmation
- **Mise à jour de statut** : Envoi automatique notification
- **Traitement de paiement** : Envoi automatique confirmation

## 📧 Templates d'Emails

### **Design Responsive**
- Compatible mobile et desktop
- Design moderne avec couleurs de la marque
- Emojis pour une meilleure expérience utilisateur

### **Contenu Personnalisé**
- Nom du client
- Numéro de commande unique
- Détails des produits
- Adresse de livraison
- Informations de paiement

### **Informations de Contact**
- Email de support
- Téléphone de contact
- Coordonnées de l'entreprise

## 🧪 Test du Système

### **Via Script PHP**
```bash
php test_emails.php
```

### **Via API**
```bash
# Créer une commande (déclenche emails)
curl -X POST http://127.0.0.1:8000/api/orders \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "shipping_address": "123 Test",
    "shipping_city": "Paris",
    "shipping_postal_code": "75001",
    "shipping_phone": "0123456789",
    "payment_method": "online"
  }'

# Mettre à jour le statut (déclenche email)
curl -X PUT http://127.0.0.1:8000/api/orders/1 \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "order_status": "processing"
  }'
```

## 📊 Fonctionnalités

✅ **Envoi automatique** lors de la création de commande
✅ **Notifications de statut** lors des mises à jour
✅ **Confirmation de paiement** automatique
✅ **Templates HTML** responsives et modernes
✅ **Gestion d'erreurs** avec logs détaillés
✅ **Personnalisation** des contenus
✅ **Intégration complète** avec l'API

## 🚀 Prochaines Améliorations

1. **Emails en lot** pour les notifications groupées
2. **Templates multiples** selon le type de client
3. **Notifications push** en plus des emails
4. **SMS automatiques** pour les notifications urgentes
5. **Historique des emails** envoyés
6. **Gestion des bounces** et emails invalides

## 📝 Notes Importantes

- Les emails sont envoyés de manière asynchrone
- En cas d'échec, les erreurs sont loggées
- Les templates utilisent Blade avec des variables dynamiques
- Le système est extensible pour de nouveaux types d'emails
- Compatible avec tous les providers SMTP (Gmail, SendGrid, etc.)

## 🔍 Dépannage

### **Emails non envoyés**
1. Vérifier la configuration SMTP dans `.env`
2. Consulter les logs dans `storage/logs/laravel.log`
3. Tester avec `MAIL_MAILER=log` pour le développement

### **Templates non trouvés**
1. Vérifier que les fichiers existent dans `resources/views/emails/`
2. Vider le cache : `php artisan view:clear`
3. Vérifier les permissions des fichiers

### **Erreurs SMTP**
1. Vérifier les identifiants SMTP
2. Tester la connexion SMTP
3. Vérifier les paramètres de sécurité (TLS, port) 