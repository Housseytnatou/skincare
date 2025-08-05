<?php

echo "📧 Test du Système d'Emails Automatiques\n";
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

// Test 2: Créer une commande (pour déclencher les emails)
if ($token) {
    echo "2. 📋 Créer une commande avec emails automatiques...\n";
    $orderData = [
        'shipping_address' => '456 Avenue des Tests',
        'shipping_city' => 'Lyon',
        'shipping_postal_code' => '69002',
        'shipping_phone' => '0987654321',
        'payment_method' => 'online',
        'notes' => 'Test d\'emails automatiques'
    ];
    
    $response = makeRequest('POST', $baseUrl . '/orders', $orderData);
    echo "Code: " . $response['code'] . "\n";
    if ($response['code'] === 201) {
        echo "✅ Commande créée avec succès\n";
        if (isset($response['body']['emails_sent'])) {
            echo "📧 Emails envoyés :\n";
            foreach ($response['body']['emails_sent'] as $type => $result) {
                echo "  - $type : " . ($result['success'] ? '✅' : '❌') . " " . $result['message'] . "\n";
            }
        }
    } else {
        echo "❌ Erreur: " . json_encode($response['body']) . "\n";
    }
}

echo "\n";

// Test 3: Traiter un paiement (pour déclencher l'email de paiement)
if ($token) {
    echo "3. 💳 Traiter un paiement avec email de confirmation...\n";
    
    // D'abord, récupérer les commandes
    $response = makeRequest('GET', $baseUrl . '/orders');
    if ($response['code'] === 200 && isset($response['body']['data']['data']) && !empty($response['body']['data']['data'])) {
        $orders = $response['body']['data']['data'];
        $firstOrderId = $orders[0]['id'];
        
        $response = makeRequest('POST', $baseUrl . '/orders/' . $firstOrderId . '/payment');
        echo "Code: " . $response['code'] . "\n";
        if ($response['code'] === 200) {
            echo "✅ Paiement traité avec succès\n";
            if (isset($response['body']['email_sent'])) {
                $emailResult = $response['body']['email_sent'];
                echo "📧 Email de paiement : " . ($emailResult['success'] ? '✅' : '❌') . " " . $emailResult['message'] . "\n";
            }
        } else {
            echo "❌ Erreur: " . json_encode($response['body']) . "\n";
        }
    }
}

echo "\n";

// Test 4: Mettre à jour le statut d'une commande (pour déclencher l'email de mise à jour)
if ($token) {
    echo "4. 🔄 Mettre à jour le statut avec email de notification...\n";
    
    $response = makeRequest('GET', $baseUrl . '/orders');
    if ($response['code'] === 200 && isset($response['body']['data']['data']) && !empty($response['body']['data']['data'])) {
        $orders = $response['body']['data']['data'];
        $firstOrderId = $orders[0]['id'];
        
        $updateData = [
            'order_status' => 'processing'
        ];
        
        $response = makeRequest('PUT', $baseUrl . '/orders/' . $firstOrderId, $updateData);
        echo "Code: " . $response['code'] . "\n";
        if ($response['code'] === 200) {
            echo "✅ Statut mis à jour avec succès\n";
            if (isset($response['body']['emails_sent'])) {
                echo "📧 Emails envoyés :\n";
                foreach ($response['body']['emails_sent'] as $type => $result) {
                    echo "  - $type : " . ($result['success'] ? '✅' : '❌') . " " . $result['message'] . "\n";
                }
            }
        } else {
            echo "❌ Erreur: " . json_encode($response['body']) . "\n";
        }
    }
}

echo "\n";
echo "🎉 Tests d'emails terminés !\n";
echo "📧 Vérifiez les logs Laravel pour voir les emails envoyés\n";
echo "💡 Pour voir les emails en développement, configurez MAIL_DRIVER=log dans .env\n"; 