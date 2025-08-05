<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class StockService
{
    /**
     * Vérifier la disponibilité des produits pour une commande
     */
    public function checkOrderAvailability($cartItems)
    {
        $availability = [
            'available' => true,
            'unavailable_items' => [],
            'insufficient_stock' => []
        ];

        foreach ($cartItems as $item) {
            $product = Product::find($item['product_id']);
            
            if (!$product) {
                $availability['available'] = false;
                $availability['unavailable_items'][] = [
                    'product_id' => $item['product_id'],
                    'reason' => 'Product not found'
                ];
                continue;
            }

            // Vérifier si le produit est actif
            if (!$product->is_active) {
                $availability['available'] = false;
                $availability['unavailable_items'][] = [
                    'product_id' => $item['product_id'],
                    'product_name' => $product->name,
                    'reason' => 'Product is inactive'
                ];
                continue;
            }

            // Vérifier le stock
            if ($product->stock_quantity < $item['quantity']) {
                $availability['available'] = false;
                $availability['insufficient_stock'][] = [
                    'product_id' => $item['product_id'],
                    'product_name' => $product->name,
                    'requested_quantity' => $item['quantity'],
                    'available_quantity' => $product->stock_quantity
                ];
            }
        }

        return $availability;
    }

    /**
     * Réserver le stock pour une commande
     */
    public function reserveStockForOrder(Order $order)
    {
        $results = [];
        
        foreach ($order->items as $item) {
            $product = Product::find($item->product_id);
            
            if ($product) {
                $success = $product->reduceStock($item->quantity);
                $results[] = [
                    'product_id' => $item->product_id,
                    'product_name' => $product->name,
                    'quantity' => $item->quantity,
                    'success' => $success,
                    'new_stock' => $product->stock_quantity
                ];
            }
        }

        return $results;
    }

    /**
     * Restaurer le stock (en cas d'annulation de commande)
     */
    public function restoreStockForOrder(Order $order)
    {
        $results = [];
        
        foreach ($order->items as $item) {
            $product = Product::find($item->product_id);
            
            if ($product) {
                $product->increaseStock($item->quantity);
                $results[] = [
                    'product_id' => $item->product_id,
                    'product_name' => $product->name,
                    'quantity' => $item->quantity,
                    'new_stock' => $product->stock_quantity
                ];
            }
        }

        return $results;
    }

    /**
     * Obtenir les produits avec stock faible
     */
    public function getLowStockProducts()
    {
        return Product::lowStock()->active()->get();
    }

    /**
     * Obtenir les produits en rupture de stock
     */
    public function getOutOfStockProducts()
    {
        return Product::where('stock_quantity', 0)->active()->get();
    }

    /**
     * Mettre à jour le stock d'un produit
     */
    public function updateProductStock($productId, $newQuantity, $minAlert = null)
    {
        $product = Product::find($productId);
        
        if (!$product) {
            return [
                'success' => false,
                'message' => 'Product not found'
            ];
        }

        $product->stock_quantity = $newQuantity;
        
        if ($minAlert !== null) {
            $product->min_stock_alert = $minAlert;
        }

        $product->save();

        return [
            'success' => true,
            'message' => 'Stock updated successfully',
            'product' => $product
        ];
    }

    /**
     * Obtenir les statistiques de stock
     */
    public function getStockStatistics()
    {
        $totalProducts = Product::count();
        $activeProducts = Product::active()->count();
        $inStockProducts = Product::inStock()->count();
        $lowStockProducts = Product::lowStock()->count();
        $outOfStockProducts = Product::where('stock_quantity', 0)->count();

        $totalStockValue = Product::sum(DB::raw('stock_quantity * price'));

        return [
            'total_products' => $totalProducts,
            'active_products' => $activeProducts,
            'in_stock_products' => $inStockProducts,
            'low_stock_products' => $lowStockProducts,
            'out_of_stock_products' => $outOfStockProducts,
            'total_stock_value' => $totalStockValue
        ];
    }

    /**
     * Vérifier et envoyer des alertes de stock faible
     */
    public function checkLowStockAlerts()
    {
        $lowStockProducts = $this->getLowStockProducts();
        
        $alerts = [];
        foreach ($lowStockProducts as $product) {
            $alerts[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'current_stock' => $product->stock_quantity,
                'min_alert' => $product->min_stock_alert,
                'category' => $product->category->name ?? 'N/A'
            ];
        }

        return $alerts;
    }

    /**
     * Obtenir l'historique des mouvements de stock
     */
    public function getStockHistory($productId = null)
    {
        $query = OrderItem::with(['order', 'product'])
            ->whereHas('order', function ($q) {
                $q->whereIn('order_status', ['pending', 'processing', 'shipped', 'delivered']);
            });

        if ($productId) {
            $query->where('product_id', $productId);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }
} 