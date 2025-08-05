<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StockController extends Controller
{
    protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * Obtenir les statistiques de stock
     */
    public function statistics()
    {
        $stats = $this->stockService->getStockStatistics();

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Obtenir les produits avec stock faible
     */
    public function lowStock()
    {
        $products = $this->stockService->getLowStockProducts();

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    /**
     * Obtenir les produits en rupture de stock
     */
    public function outOfStock()
    {
        $products = $this->stockService->getOutOfStockProducts();

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    /**
     * Mettre à jour le stock d'un produit
     */
    public function updateStock(Request $request, $productId)
    {
        $validator = Validator::make($request->all(), [
            'stock_quantity' => 'required|integer|min:0',
            'min_stock_alert' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->stockService->updateProductStock(
            $productId,
            $request->stock_quantity,
            $request->min_stock_alert
        );

        if ($request->has('is_active')) {
            $product = Product::find($productId);
            if ($product) {
                $product->is_active = $request->is_active;
                $product->save();
            }
        }

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Stock updated successfully',
                'data' => $result['product']
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 404);
        }
    }

    /**
     * Obtenir les alertes de stock faible
     */
    public function alerts()
    {
        $alerts = $this->stockService->checkLowStockAlerts();

        return response()->json([
            'success' => true,
            'data' => $alerts
        ]);
    }

    /**
     * Obtenir l'historique des mouvements de stock
     */
    public function history(Request $request)
    {
        $productId = $request->get('product_id');
        $history = $this->stockService->getStockHistory($productId);

        return response()->json([
            'success' => true,
            'data' => $history
        ]);
    }

    /**
     * Vérifier la disponibilité des produits
     */
    public function checkAvailability(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'items' => 'required|array',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $availability = $this->stockService->checkOrderAvailability($request->items);

        return response()->json([
            'success' => true,
            'data' => $availability
        ]);
    }

    /**
     * Obtenir tous les produits avec leurs informations de stock
     */
    public function products()
    {
        $products = Product::with('category')->get()->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'stock_quantity' => $product->stock_quantity,
                'min_stock_alert' => $product->min_stock_alert,
                'is_active' => $product->is_active,
                'stock_status' => $product->getStockStatus(),
                'is_in_stock' => $product->isInStock(),
                'is_low_stock' => $product->isLowStock(),
                'is_available' => $product->isAvailable(),
                'category' => $product->category ? $product->category->name : null
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }
}
