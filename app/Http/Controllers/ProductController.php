<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    // Lister tous les produits avec recherche et filtrage
    public function index(Request $request)
    {
        $query = Product::with('category');

        // Recherche par texte
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhereHas('category', function ($categoryQuery) use ($search) {
                      $categoryQuery->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Filtrage par catégorie
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filtrage par prix
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Filtrage par disponibilité
        if ($request->has('in_stock')) {
            if ($request->boolean('in_stock')) {
                $query->inStock();
            } else {
                $query->where('stock_quantity', 0);
            }
        }

        // Filtrage par statut actif
        if ($request->has('active')) {
            if ($request->boolean('active')) {
                $query->active();
            } else {
                $query->where('is_active', false);
            }
        }

        // Filtrage par stock faible
        if ($request->has('low_stock')) {
            if ($request->boolean('low_stock')) {
                $query->lowStock();
            }
        }

        // Tri
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        
        $allowedSortFields = ['name', 'price', 'stock_quantity', 'created_at'];
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $perPage = min($perPage, 100); // Limiter à 100 par page

        $products = $query->paginate($perPage);

        // Ajouter les informations de stock à chaque produit
        $products->getCollection()->transform(function ($product) {
            $product->stock_status = $product->getStockStatus();
            $product->is_in_stock = $product->isInStock();
            $product->is_low_stock = $product->isLowStock();
            $product->is_available = $product->isAvailable();
            return $product;
        });

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    // Créer un nouveau produit
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'price' => 'required|numeric',
                'category_id' => 'required|exists:categories,id',
            ]);

            $product = Product::create($validated);
            return response()->json($product, 201);

        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    // Afficher un produit spécifique
    public function show($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Produit non trouvé'], 404);
        }
        return response()->json($product);
    }

    // Mettre à jour un produit
    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Produit non trouvé'], 404);
        }

        try {
            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'price' => 'sometimes|required|numeric',
                'category_id' => 'sometimes|required|exists:categories,id',
            ]);

            $product->update($validated);
            return response()->json($product, 200);

        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    // Supprimer un produit
    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Produit non trouvé'], 404);
        }

        $product->delete();
        return response()->json(['message' => 'Produit supprimé'], 200);
    }

    /**
     * Recherche avancée avec suggestions
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([
                'success' => true,
                'data' => []
            ]);
        }

        $products = Product::with('category')
            ->where('name', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->orWhereHas('category', function ($categoryQuery) use ($query) {
                $categoryQuery->where('name', 'LIKE', "%{$query}%");
            })
            ->active()
            ->inStock()
            ->limit(10)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'category' => $product->category ? $product->category->name : null,
                    'stock_quantity' => $product->stock_quantity,
                    'is_available' => $product->isAvailable()
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    /**
     * Obtenir les suggestions de recherche
     */
    public function suggestions(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([
                'success' => true,
                'data' => []
            ]);
        }

        $suggestions = [];

        // Suggestions de noms de produits
        $productNames = Product::where('name', 'LIKE', "%{$query}%")
            ->active()
            ->limit(5)
            ->pluck('name')
            ->toArray();
        
        $suggestions = array_merge($suggestions, $productNames);

        // Suggestions de catégories
        $categoryNames = \App\Models\Category::where('name', 'LIKE', "%{$query}%")
            ->limit(3)
            ->pluck('name')
            ->toArray();
        
        $suggestions = array_merge($suggestions, $categoryNames);

        // Supprimer les doublons et limiter
        $suggestions = array_unique($suggestions);
        $suggestions = array_slice($suggestions, 0, 8);

        return response()->json([
            'success' => true,
            'data' => $suggestions
        ]);
    }

    /**
     * Obtenir les filtres disponibles
     */
    public function filters()
    {
        $filters = [
            'categories' => \App\Models\Category::all(['id', 'name']),
            'price_ranges' => [
                ['min' => 0, 'max' => 10, 'label' => '0€ - 10€'],
                ['min' => 10, 'max' => 25, 'label' => '10€ - 25€'],
                ['min' => 25, 'max' => 50, 'label' => '25€ - 50€'],
                ['min' => 50, 'max' => 100, 'label' => '50€ - 100€'],
                ['min' => 100, 'max' => null, 'label' => '100€+']
            ],
            'stock_statuses' => [
                ['value' => 'in_stock', 'label' => 'En stock'],
                ['value' => 'low_stock', 'label' => 'Stock faible'],
                ['value' => 'out_of_stock', 'label' => 'Rupture'],
                ['value' => 'inactive', 'label' => 'Inactif']
            ],
            'sort_options' => [
                ['value' => 'name', 'label' => 'Nom'],
                ['value' => 'price', 'label' => 'Prix'],
                ['value' => 'stock_quantity', 'label' => 'Stock'],
                ['value' => 'created_at', 'label' => 'Date de création']
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $filters
        ]);
    }

    /**
     * Obtenir les produits populaires
     */
    public function popular()
    {
        $products = Product::with('category')
            ->active()
            ->inStock()
            ->orderBy('stock_quantity', 'desc')
            ->limit(8)
            ->get()
            ->map(function ($product) {
                $product->stock_status = $product->getStockStatus();
                $product->is_in_stock = $product->isInStock();
                $product->is_low_stock = $product->isLowStock();
                $product->is_available = $product->isAvailable();
                return $product;
            });

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    /**
     * Obtenir les produits récents
     */
    public function recent()
    {
        $products = Product::with('category')
            ->active()
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get()
            ->map(function ($product) {
                $product->stock_status = $product->getStockStatus();
                $product->is_in_stock = $product->isInStock();
                $product->is_low_stock = $product->isLowStock();
                $product->is_available = $product->isAvailable();
                return $product;
            });

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }
}
