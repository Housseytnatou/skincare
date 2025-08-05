<?php

echo "📦 Test du Système de Gestion des Stocks\n";
echo "========================================\n\n";

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

// Test 1: Se connecter
echo "1. 🔑 Connexion...\n";
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

// Test 2: Obtenir les statistiques de stock
if ($token) {
    echo "2. 📊 Statistiques de stock...\n";
    $response = makeRequest('GET', $baseUrl . '/stock/statistics');
    echo "Code: " . $response['code'] . "\n";
    if ($response['code'] === 200) {
        echo "✅ Statistiques récupérées\n";
        if (isset($response['body']['data'])) {
            $stats = $response['body']['data'];
            echo "  - Total produits: " . $stats['total_products'] . "\n";
            echo "  - Produits actifs: " . $stats['active_products'] . "\n";
            echo "  - En stock: " . $stats['in_stock_products'] . "\n";
            echo "  - Stock faible: " . $stats['low_stock_products'] . "\n";
            echo "  - Rupture: " . $stats['out_of_stock_products'] . "\n";
            echo "  - Valeur totale: " . number_format($stats['total_stock_value'], 2, ',', ' ') . " €\n";
        }
    } else {
        echo "❌ Erreur: " . json_encode($response['body']) . "\n";
    }
}

echo "\n";

// Test 3: Obtenir les produits avec stock faible
if ($token) {
    echo "3. ⚠️ Produits avec stock faible...\n";
    $response = makeRequest('GET', $baseUrl . '/stock/low-stock');
    echo "Code: " . $response['code'] . "\n";
    if ($response['code'] === 200) {
        echo "✅ Produits avec stock faible récupérés\n";
        if (isset($response['body']['data'])) {
            $products = $response['body']['data'];
            echo "Nombre de produits avec stock faible: " . count($products) . "\n";
            foreach ($products as $product) {
                echo "  - " . $product['name'] . " (Stock: " . $product['stock_quantity'] . ")\n";
            }
        }
    } else {
        echo "❌ Erreur: " . json_encode($response['body']) . "\n";
    }
}

echo "\n";

// Test 4: Obtenir les alertes de stock
if ($token) {
    echo "4. 🚨 Alertes de stock...\n";
    $response = makeRequest('GET', $baseUrl . '/stock/alerts');
    echo "Code: " . $response['code'] . "\n";
    if ($response['code'] === 200) {
        echo "✅ Alertes récupérées\n";
        if (isset($response['body']['data'])) {
            $alerts = $response['body']['data'];
            echo "Nombre d'alertes: " . count($alerts) . "\n";
            foreach ($alerts as $alert) {
                echo "  - " . $alert['product_name'] . " (Stock: " . $alert['current_stock'] . "/" . $alert['min_alert'] . ")\n";
            }
        }
    } else {
        echo "❌ Erreur: " . json_encode($response['body']) . "\n";
    }
}

echo "\n";

// Test 5: Vérifier la disponibilité des produits
if ($token) {
    echo "5. 🔍 Vérification de disponibilité...\n";
    $availabilityData = [
        'items' => [
            ['product_id' => 1, 'quantity' => 2],
            ['product_id' => 2, 'quantity' => 1]
        ]
    ];
    
    $response = makeRequest('POST', $baseUrl . '/stock/check-availability', $availabilityData);
    echo "Code: " . $response['code'] . "\n";
    if ($response['code'] === 200) {
        echo "✅ Disponibilité vérifiée\n";
        if (isset($response['body']['data'])) {
            $availability = $response['body']['data'];
            echo "Disponible: " . ($availability['available'] ? 'Oui' : 'Non') . "\n";
            if (!empty($availability['insufficient_stock'])) {
                echo "Produits avec stock insuffisant:\n";
                foreach ($availability['insufficient_stock'] as $item) {
                    echo "  - " . $item['product_name'] . " (Demandé: " . $item['requested_quantity'] . ", Disponible: " . $item['available_quantity'] . ")\n";
                }
            }
        }
    } else {
        echo "❌ Erreur: " . json_encode($response['body']) . "\n";
    }
}

echo "\n";

// Test 6: Créer une commande avec vérification de stock
if ($token) {
    echo "6. 🛒 Créer une commande avec vérification de stock...\n";
    
    // D'abord ajouter des produits au panier
    echo "  Ajout de produits au panier...\n";
    $cartData = [
        'product_id' => 1,
        'quantity' => 1
    ];
    
    $response = makeRequest('POST', $baseUrl . '/carts', $cartData);
    if ($response['code'] === 201) {
        echo "  ✅ Produit ajouté au panier\n";
        
        // Créer la commande
        $orderData = [
            'shipping_address' => '789 Rue des Tests',
            'shipping_city' => 'Marseille',
            'shipping_postal_code' => '13001',
            'shipping_phone' => '0123456789',
            'payment_method' => 'online',
            'notes' => 'Test de gestion des stocks'
        ];
        
        $response = makeRequest('POST', $baseUrl . '/orders', $orderData);
        echo "Code: " . $response['code'] . "\n";
        if ($response['code'] === 201) {
            echo "✅ Commande créée avec succès\n";
            if (isset($response['body']['stock_reserved'])) {
                echo "Stock réservé pour la commande\n";
            }
        } else {
            echo "❌ Erreur: " . json_encode($response['body']) . "\n";
        }
    } else {
        echo "❌ Erreur lors de l'ajout au panier: " . json_encode($response['body']) . "\n";
    }
}

echo "\n";
echo "🎉 Tests de gestion des stocks terminés !\n"; 