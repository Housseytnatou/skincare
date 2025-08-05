<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StockController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// 🔐 Route pour récupérer les infos de l'utilisateur connecté
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ✅ Routes d'authentification
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// ✅ Routes publiques (consultation libre)
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::get('/products/search', [ProductController::class, 'search']);
Route::get('/products/suggestions', [ProductController::class, 'suggestions']);
Route::get('/products/filters', [ProductController::class, 'filters']);
Route::get('/products/popular', [ProductController::class, 'popular']);
Route::get('/products/recent', [ProductController::class, 'recent']);

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);

// 🔐 Routes protégées par authentification Sanctum (admin + utilisateur)
Route::middleware('auth:sanctum')->group(function () {
    // 🔐 Déconnexion
    Route::post('/logout', [AuthController::class, 'logout']);

    // 🔐 Gestion produits (admin uniquement)
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);

    // 🔐 Gestion catégories (admin uniquement)
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

    // 🔐 Gestion panier (pour tous les utilisateurs authentifiés)
    Route::get('/carts', [CartController::class, 'index']);
    Route::post('/carts', [CartController::class, 'store']);
    Route::put('/carts/{id}', [CartController::class, 'update']);
    Route::delete('/carts/{id}', [CartController::class, 'destroy']);

    // 🚀 Nouvelles routes panier
    Route::get('/carts/total', [CartController::class, 'total']);
    Route::delete('/carts/clear', [CartController::class, 'clear']);
    Route::get('/carts/count', [CartController::class, 'count']);

    // 🛒 Routes de gestion des commandes
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::put('/orders/{id}', [OrderController::class, 'update']);
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel']);
    Route::post('/orders/{id}/payment', [OrderController::class, 'processPayment']);
    
    // 📄 Routes de gestion des factures
    Route::get('/orders/{id}/invoice/download', [OrderController::class, 'downloadInvoice']);
    Route::get('/orders/{id}/invoice/url', [OrderController::class, 'getInvoiceUrl']);
    Route::get('/orders/{id}/invoice/preview', [OrderController::class, 'previewInvoice']);
    
    // 📊 Statistiques (admin uniquement)
    Route::get('/orders/statistics', [OrderController::class, 'statistics']);
});

// 📦 Routes de gestion des stocks
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/stock/statistics', [StockController::class, 'statistics']);
    Route::get('/stock/low-stock', [StockController::class, 'lowStock']);
    Route::get('/stock/out-of-stock', [StockController::class, 'outOfStock']);
    Route::get('/stock/alerts', [StockController::class, 'alerts']);
    Route::get('/stock/history', [StockController::class, 'history']);
    Route::get('/stock/products', [StockController::class, 'products']);
    Route::post('/stock/check-availability', [StockController::class, 'checkAvailability']);
    Route::put('/stock/products/{id}', [StockController::class, 'updateStock']);
});
