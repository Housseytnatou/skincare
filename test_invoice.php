<?php

echo "🧪 Test de Génération de Factures PDF\n";
echo "=====================================\n\n";

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

// Test 2: Voir les commandes existantes
echo "2. 📋 Lister les commandes...\n";
$response = makeRequest('GET', $baseUrl . '/orders');
echo "Code: " . $response['code'] . "\n";
if ($response['code'] === 200) {
    echo "✅ Commandes récupérées\n";
    if (isset($response['body']['data']['data']) && !empty($response['body']['data']['data'])) {
        $orders = $response['body']['data']['data'];
        echo "Nombre de commandes: " . count($orders) . "\n";
        foreach ($orders as $order) {
            echo "- Commande #" . $order['order_number'] . " (ID: " . $order['id'] . ")\n";
        }
        
        // Utiliser la première commande pour les tests
        $firstOrderId = $orders[0]['id'];
        
        // Test 3: Obtenir l'URL de la facture
        echo "\n3. 📄 Obtenir l'URL de la facture...\n";
        $response = makeRequest('GET', $baseUrl . '/orders/' . $firstOrderId . '/invoice/url');
        echo "Code: " . $response['code'] . "\n";
        if ($response['code'] === 200) {
            echo "✅ URL de facture obtenue\n";
            if (isset($response['body']['data']['invoice_url'])) {
                echo "URL: " . $response['body']['data']['invoice_url'] . "\n";
            }
        } else {
            echo "❌ Erreur: " . json_encode($response['body']) . "\n";
        }
        
        // Test 4: Télécharger la facture
        echo "\n4. 📥 Télécharger la facture...\n";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $baseUrl . '/orders/' . $firstOrderId . '/invoice/download');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $token]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        echo "Code: " . $httpCode . "\n";
        if ($httpCode === 200) {
            echo "✅ Facture téléchargée avec succès\n";
            echo "Taille du fichier: " . strlen($response) . " bytes\n";
            
            // Sauvegarder le fichier pour vérification
            file_put_contents('test_invoice.pdf', $response);
            echo "Fichier sauvegardé: test_invoice.pdf\n";
        } else {
            echo "❌ Erreur lors du téléchargement\n";
        }
        
    } else {
        echo "❌ Aucune commande trouvée\n";
    }
} else {
    echo "❌ Erreur: " . json_encode($response['body']) . "\n";
}

echo "\n";
echo "🎉 Tests de factures terminés !\n";
echo "📄 Vérifiez le fichier test_invoice.pdf pour voir la facture générée\n"; 