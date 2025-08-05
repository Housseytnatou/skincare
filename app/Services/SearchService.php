<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class SearchService
{
    /**
     * Recherche avancée de produits
     */
    public function searchProducts($query, $filters = [])
    {
        $searchQuery = Product::with('category');

        // Recherche par texte
        if (!empty($query)) {
            $searchQuery->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('description', 'LIKE', "%{$query}%")
                  ->orWhereHas('category', function ($categoryQuery) use ($query) {
                      $categoryQuery->where('name', 'LIKE', "%{$query}%");
                  });
            });
        }

        // Appliquer les filtres
        $searchQuery = $this->applyFilters($searchQuery, $filters);

        return $searchQuery;
    }

    /**
     * Appliquer les filtres à la requête
     */
    private function applyFilters($query, $filters)
    {
        // Filtrage par catégorie
        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        // Filtrage par prix
        if (isset($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }
        if (isset($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        // Filtrage par disponibilité
        if (isset($filters['in_stock'])) {
            if ($filters['in_stock']) {
                $query->inStock();
            } else {
                $query->where('stock_quantity', 0);
            }
        }

        // Filtrage par statut actif
        if (isset($filters['active'])) {
            if ($filters['active']) {
                $query->active();
            } else {
                $query->where('is_active', false);
            }
        }

        // Filtrage par stock faible
        if (isset($filters['low_stock'])) {
            if ($filters['low_stock']) {
                $query->lowStock();
            }
        }

        // Filtrage par statut de stock
        if (isset($filters['stock_status'])) {
            switch ($filters['stock_status']) {
                case 'in_stock':
                    $query->inStock();
                    break;
                case 'low_stock':
                    $query->lowStock();
                    break;
                case 'out_of_stock':
                    $query->where('stock_quantity', 0);
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
            }
        }

        return $query;
    }

    /**
     * Obtenir les suggestions de recherche
     */
    public function getSuggestions($query, $limit = 8)
    {
        if (strlen($query) < 2) {
            return [];
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
        $categoryNames = Category::where('name', 'LIKE', "%{$query}%")
            ->limit(3)
            ->pluck('name')
            ->toArray();
        
        $suggestions = array_merge($suggestions, $categoryNames);

        // Supprimer les doublons et limiter
        $suggestions = array_unique($suggestions);
        $suggestions = array_slice($suggestions, 0, $limit);

        return $suggestions;
    }

    /**
     * Obtenir les produits populaires
     */
    public function getPopularProducts($limit = 8)
    {
        return Product::with('category')
            ->active()
            ->inStock()
            ->orderBy('stock_quantity', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($product) {
                $product->stock_status = $product->getStockStatus();
                $product->is_in_stock = $product->isInStock();
                $product->is_low_stock = $product->isLowStock();
                $product->is_available = $product->isAvailable();
                return $product;
            });
    }

    /**
     * Obtenir les produits récents
     */
    public function getRecentProducts($limit = 8)
    {
        return Product::with('category')
            ->active()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($product) {
                $product->stock_status = $product->getStockStatus();
                $product->is_in_stock = $product->isInStock();
                $product->is_low_stock = $product->isLowStock();
                $product->is_available = $product->isAvailable();
                return $product;
            });
    }

    /**
     * Obtenir les statistiques de recherche
     */
    public function getSearchStatistics($query)
    {
        $totalProducts = Product::count();
        $activeProducts = Product::active()->count();
        $inStockProducts = Product::inStock()->count();
        
        $searchResults = 0;
        if (!empty($query)) {
            $searchResults = Product::where('name', 'LIKE', "%{$query}%")
                ->orWhere('description', 'LIKE', "%{$query}%")
                ->orWhereHas('category', function ($categoryQuery) use ($query) {
                    $categoryQuery->where('name', 'LIKE', "%{$query}%");
                })
                ->count();
        }

        return [
            'total_products' => $totalProducts,
            'active_products' => $activeProducts,
            'in_stock_products' => $inStockProducts,
            'search_results' => $searchResults,
            'query' => $query
        ];
    }

    /**
     * Obtenir les filtres disponibles
     */
    public function getAvailableFilters()
    {
        $filters = [
            'categories' => Category::all(['id', 'name']),
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

        // Ajouter les statistiques de prix
        $priceStats = Product::selectRaw('
            MIN(price) as min_price,
            MAX(price) as max_price,
            AVG(price) as avg_price
        ')->first();

        $filters['price_stats'] = [
            'min_price' => $priceStats->min_price ?? 0,
            'max_price' => $priceStats->max_price ?? 0,
            'avg_price' => round($priceStats->avg_price ?? 0, 2)
        ];

        return $filters;
    }

    /**
     * Recherche avec pagination et tri
     */
    public function searchWithPagination($query, $filters = [], $sortBy = 'name', $sortOrder = 'asc', $perPage = 15)
    {
        $searchQuery = $this->searchProducts($query, $filters);

        // Tri
        $allowedSortFields = ['name', 'price', 'stock_quantity', 'created_at'];
        if (in_array($sortBy, $allowedSortFields)) {
            $searchQuery->orderBy($sortBy, $sortOrder);
        }

        // Pagination
        $perPage = min($perPage, 100); // Limiter à 100 par page

        $products = $searchQuery->paginate($perPage);

        // Ajouter les informations de stock à chaque produit
        $products->getCollection()->transform(function ($product) {
            $product->stock_status = $product->getStockStatus();
            $product->is_in_stock = $product->isInStock();
            $product->is_low_stock = $product->isLowStock();
            $product->is_available = $product->isAvailable();
            return $product;
        });

        return $products;
    }
} 