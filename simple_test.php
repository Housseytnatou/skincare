<?php

echo "🧪 Test Simple de l'API\n";
echo "=======================\n\n";

// Test de connexion
echo "1. Test de connexion...\n";
$loginData = json_encode([
    'email' => 'test@example.com',
    'password' => 'password123'
]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/api/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $loginData);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Accept: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Code HTTP: $httpCode\n";
echo "Réponse: " . substr($response, 0, 200) . "...\n\n";

// Test des commandes (sans token pour voir l'erreur)
echo "2. Test des commandes (sans token)...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/api/orders');
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Code HTTP: $httpCode\n";
echo "Réponse: " . substr($response, 0, 200) . "...\n\n";

// Test des produits (route publique)
echo "3. Test des produits (route publique)...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/api/products');
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Code HTTP: $httpCode\n";
echo "Réponse: " . substr($response, 0, 200) . "...\n\n";

echo "✅ Tests terminés !\n"; 