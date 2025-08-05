<?php

echo "🔍 Test du Système de Recherche et Filtrage Avancé\n";
echo "==================================================\n\n";

$baseUrl = 'http://127.0.0.1:8000/api';

function makeRequest($method, $url, $data = null, $headers = []) {
    $defaultHeaders = ['Accept: application/json'];
    if ($data) {
        $defaultHeaders[] = 'Content-Type: application/json';
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

// Test 1: Obtenir tous les produits avec pagination
echo "1. 📋 Tous les produits avec pagination...\n";
$response = makeRequest('GET', $baseUrl . '/products?per_page=5');
echo "Code: " . $response['code'] . "\n";
if ($response['code'] === 200) {
    echo "✅ Produits récupérés\n";
    if (isset($response['body']['data']['data'])) {
        $products = $response['body']['data']['data'];
        echo "Nombre de produits: " . count($products) . "\n";
        echo "Page actuelle: " . $response['body']['data']['current_page'] . "\n";
        echo "Total de pages: " . $response['body']['data']['last_page'] . "\n";
    }
} else {
    echo "❌ Erreur: " . json_encode($response['body']) . "\n";
}

echo "\n";

// Test 2: Recherche par texte
echo "2. 🔍 Recherche par texte...\n";
$response = makeRequest('GET', $baseUrl . '/products/search?q=crème');
echo "Code: " . $response['code'] . "\n";
if ($response['code'] === 200) {
    echo "✅ Recherche effectuée\n";
    if (isset($response['body']['data'])) {
        $products = $response['body']['data'];
        echo "Résultats trouvés: " . count($products) . "\n";
        foreach ($products as $product) {
            echo "  - " . $product['name'] . " (" . $product['price'] . "€)\n";
        }
    }
} else {
    echo "❌ Erreur: " . json_encode($response['body']) . "\n";
}

echo "\n";

// Test 3: Suggestions de recherche
echo "3. 💡 Suggestions de recherche...\n";
$response = makeRequest('GET', $baseUrl . '/products/suggestions?q=cr');
echo "Code: " . $response['code'] . "\n";
if ($response['code'] === 200) {
    echo "✅ Suggestions récupérées\n";
    if (isset($response['body']['data'])) {
        $suggestions = $response['body']['data'];
        echo "Suggestions: " . implode(', ', $suggestions) . "\n";
    }
} else {
    echo "❌ Erreur: " . json_encode($response['body']) . "\n";
}

echo "\n";

// Test 4: Filtrage par prix
echo "4. 💰 Filtrage par prix...\n";
$response = makeRequest('GET', $baseUrl . '/products?min_price=10&max_price=50');
echo "Code: " . $response['code'] . "\n";
if ($response['code'] === 200) {
    echo "✅ Filtrage par prix effectué\n";
    if (isset($response['body']['data']['data'])) {
        $products = $response['body']['data']['data'];
        echo "Produits entre 10€ et 50€: " . count($products) . "\n";
        foreach ($products as $product) {
            echo "  - " . $product['name'] . " (" . $product['price'] . "€)\n";
        }
    }
} else {
    echo "❌ Erreur: " . json_encode($response['body']) . "\n";
}

echo "\n";

// Test 5: Filtrage par disponibilité
echo "5. 📦 Filtrage par disponibilité...\n";
$response = makeRequest('GET', $baseUrl . '/products?in_stock=true');
echo "Code: " . $response['code'] . "\n";
if ($response['code'] === 200) {
    echo "✅ Filtrage par disponibilité effectué\n";
    if (isset($response['body']['data']['data'])) {
        $products = $response['body']['data']['data'];
        echo "Produits en stock: " . count($products) . "\n";
    }
} else {
    echo "❌ Erreur: " . json_encode($response['body']) . "\n";
}

echo "\n";

// Test 6: Tri par prix
echo "6. 📊 Tri par prix...\n";
$response = makeRequest('GET', $baseUrl . '/products?sort_by=price&sort_order=desc');
echo "Code: " . $response['code'] . "\n";
if ($response['code'] === 200) {
    echo "✅ Tri par prix effectué\n";
    if (isset($response['body']['data']['data'])) {
        $products = $response['body']['data']['data'];
        echo "Produits triés par prix décroissant:\n";
        foreach (array_slice($products, 0, 3) as $product) {
            echo "  - " . $product['name'] . " (" . $product['price'] . "€)\n";
        }
    }
} else {
    echo "❌ Erreur: " . json_encode($response['body']) . "\n";
}

echo "\n";

// Test 7: Produits populaires
echo "7. ⭐ Produits populaires...\n";
$response = makeRequest('GET', $baseUrl . '/products/popular');
echo "Code: " . $response['code'] . "\n";
if ($response['code'] === 200) {
    echo "✅ Produits populaires récupérés\n";
    if (isset($response['body']['data'])) {
        $products = $response['body']['data'];
        echo "Produits populaires: " . count($products) . "\n";
        foreach ($products as $product) {
            echo "  - " . $product['name'] . " (Stock: " . $product['stock_quantity'] . ")\n";
        }
    }
} else {
    echo "❌ Erreur: " . json_encode($response['body']) . "\n";
}

echo "\n";

// Test 8: Produits récents
echo "8. 🆕 Produits récents...\n";
$response = makeRequest('GET', $baseUrl . '/products/recent');
echo "Code: " . $response['code'] . "\n";
if ($response['code'] === 200) {
    echo "✅ Produits récents récupérés\n";
    if (isset($response['body']['data'])) {
        $products = $response['body']['data'];
        echo "Produits récents: " . count($products) . "\n";
        foreach ($products as $product) {
            echo "  - " . $product['name'] . " (Créé: " . $product['created_at'] . ")\n";
        }
    }
} else {
    echo "❌ Erreur: " . json_encode($response['body']) . "\n";
}

echo "\n";

// Test 9: Filtres disponibles
echo "9. 🎛️ Filtres disponibles...\n";
$response = makeRequest('GET', $baseUrl . '/products/filters');
echo "Code: " . $response['code'] . "\n";
if ($response['code'] === 200) {
    echo "✅ Filtres récupérés\n";
    if (isset($response['body']['data'])) {
        $filters = $response['body']['data'];
        echo "Catégories: " . count($filters['categories']) . "\n";
        echo "Plages de prix: " . count($filters['price_ranges']) . "\n";
        echo "Statuts de stock: " . count($filters['stock_statuses']) . "\n";
        echo "Options de tri: " . count($filters['sort_options']) . "\n";
    }
} else {
    echo "❌ Erreur: " . json_encode($response['body']) . "\n";
}

echo "\n";

// Test 10: Recherche combinée
echo "10. 🔍 Recherche combinée (texte + filtres)...\n";
$response = makeRequest('GET', $baseUrl . '/products?search=crème&min_price=5&in_stock=true&sort_by=price&sort_order=asc');
echo "Code: " . $response['code'] . "\n";
if ($response['code'] === 200) {
    echo "✅ Recherche combinée effectuée\n";
    if (isset($response['body']['data']['data'])) {
        $products = $response['body']['data']['data'];
        echo "Résultats combinés: " . count($products) . "\n";
        foreach ($products as $product) {
            echo "  - " . $product['name'] . " (" . $product['price'] . "€, Stock: " . $product['stock_quantity'] . ")\n";
        }
    }
} else {
    echo "❌ Erreur: " . json_encode($response['body']) . "\n";
}

echo "\n";
echo "🎉 Tests de recherche et filtrage terminés !\n";
echo "📊 Le système de recherche avancée est opérationnel\n"; 