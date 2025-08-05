<?php

echo "🧪 Test Complet du Système de Commandes\n";
echo "=======================================\n\n";

$baseUrl = 'http://127.0.0.1:8000/api';
$token = null;

function makeRequest($method, $url, $data = null, $headers = []) {
    global $token;
    
    $defaultHeaders = ['Accept: application/json'];
    if ($data) {
        $defaultHeaders[] = 'Content-Type: application/json';
    }
    if ($token) {
        $defaultHeaders[] = 'Authorization: Bearer ' . $token;
    }
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    
    if ($data) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge($defaultHeaders, $headers));
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'code' => $httpCode,
        'body' => json_decode($response, true) ?: []
    ];
}

// Test 1: Créer un utilisateur
echo "1. 🔐 Création d'un utilisateur...\n";
$userData = [
    'name' => 'Test User',
    'email' => 'test2@example.com',
    'password' => 'password123',
    'password_confirmation' => 'password123'
];

$response = makeRequest('POST', $baseUrl . '/register', $userData);
echo "Code: " . $response['code'] . "\n";
if ($response['code'] === 201 || $response['code'] === 200) {
    echo "✅ Utilisateur créé avec succès\n";
    if (isset($response['body']['access_token'])) {
        $token = $response['body']['access_token'];
        echo "Token obtenu: " . substr($token, 0, 20) . "...\n";
    }
} else {
    echo "❌ Erreur: " . json_encode($response['body']) . "\n";
}

echo "\n";

// Test 2: Se connecter
echo "2. 🔑 Connexion...\n";
$loginData = [
    'email' => 'test2@example.com',
    'password' => 'password123'
];

$response = makeRequest('POST', $baseUrl . '/login', $loginData);
echo "Code: " . $response['code'] . "\n";
if ($response['code'] === 200) {
    echo "✅ Connexion réussie\n";
    if (isset($response['body']['access_token'])) {
        $token = $response['body']['access_token'];
        echo "Token obtenu: " . substr($token, 0, 20) . "...\n";
    }
} else {
    echo "❌ Erreur: " . json_encode($response['body']) . "\n";
}

echo "\n";

// Test 3: Ajouter des produits au panier
if ($token) {
    echo "3. 🛒 Ajouter des produits au panier...\n";
    
    // Récupérer les produits disponibles
    $response = makeRequest('GET', $baseUrl . '/products');
    if ($response['code'] === 200 && !empty($response['body'])) {
        $product = $response['body'][0]; // Premier produit
        
        $cartData = [
            'product_id' => $product['id'],
            'quantity' => 2
        ];
        
        $response = makeRequest('POST', $baseUrl . '/carts', $cartData);
        echo "Code: " . $response['code'] . "\n";
        if ($response['code'] === 201 || $response['code'] === 200) {
            echo "✅ Produit ajouté au panier\n";
        } else {
            echo "❌ Erreur: " . json_encode($response['body']) . "\n";
        }
    } else {
        echo "❌ Aucun produit disponible\n";
    }
}

echo "\n";

// Test 4: Créer une commande
if ($token) {
    echo "4. 📋 Créer une commande...\n";
    $orderData = [
        'shipping_address' => '123 Rue de la Paix',
        'shipping_city' => 'Paris',
        'shipping_postal_code' => '75001',
        'shipping_phone' => '0123456789',
        'payment_method' => 'online',
        'notes' => 'Test de création de commande'
    ];
    
    $response = makeRequest('POST', $baseUrl . '/orders', $orderData);
    echo "Code: " . $response['code'] . "\n";
    if ($response['code'] === 201) {
        echo "✅ Commande créée avec succès\n";
        if (isset($response['body']['data']['order_number'])) {
            echo "Numéro de commande: " . $response['body']['data']['order_number'] . "\n";
            echo "Total: " . $response['body']['data']['total'] . "€\n";
        }
    } else {
        echo "❌ Erreur: " . json_encode($response['body']) . "\n";
    }
}

echo "\n";

// Test 5: Lister les commandes
echo "5. 📋 Lister les commandes...\n";
$response = makeRequest('GET', $baseUrl . '/orders');
echo "Code: " . $response['code'] . "\n";
if ($response['code'] === 200) {
    echo "✅ Commandes récupérées\n";
    if (isset($response['body']['data']['data'])) {
        echo "Nombre de commandes: " . count($response['body']['data']['data']) . "\n";
        foreach ($response['body']['data']['data'] as $order) {
            echo "- Commande #" . $order['order_number'] . " (Total: " . $order['total'] . "€)\n";
        }
    }
} else {
    echo "❌ Erreur: " . json_encode($response['body']) . "\n";
}

echo "\n";

// Test 6: Traiter un paiement
if ($token) {
    echo "6. 💳 Traiter un paiement...\n";
    $response = makeRequest('POST', $baseUrl . '/orders/1/payment');
    echo "Code: " . $response['code'] . "\n";
    if ($response['code'] === 200) {
        echo "✅ Paiement traité avec succès\n";
        if (isset($response['body']['data']['payment_id'])) {
            echo "ID de paiement: " . $response['body']['data']['payment_id'] . "\n";
        }
    } else {
        echo "❌ Erreur: " . json_encode($response['body']) . "\n";
    }
}

echo "\n";

// Test 7: Statistiques
echo "7. 📊 Statistiques...\n";
$response = makeRequest('GET', $baseUrl . '/orders/statistics');
echo "Code: " . $response['code'] . "\n";
if ($response['code'] === 200) {
    echo "✅ Statistiques récupérées\n";
    if (isset($response['body']['data'])) {
        $stats = $response['body']['data'];
        echo "Total commandes: " . $stats['total_orders'] . "\n";
        echo "Commandes en attente: " . $stats['pending_orders'] . "\n";
        echo "Chiffre d'affaires: " . $stats['total_revenue'] . "€\n";
        echo "Paiements en attente: " . $stats['pending_payments'] . "\n";
    }
} else {
    echo "❌ Erreur: " . json_encode($response['body']) . "\n";
}

echo "\n";
echo "🎉 Tests terminés !\n"; 