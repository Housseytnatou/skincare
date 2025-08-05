<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class StockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();

        foreach ($products as $product) {
            // Générer un stock aléatoire entre 0 et 50
            $stockQuantity = rand(0, 50);
            
            // Définir une alerte de stock faible (entre 3 et 10)
            $minStockAlert = rand(3, 10);
            
            // Activer tous les produits par défaut
            $isActive = true;
            
            // Si le stock est 0, désactiver le produit
            if ($stockQuantity === 0) {
                $isActive = false;
            }

            $product->update([
                'stock_quantity' => $stockQuantity,
                'min_stock_alert' => $minStockAlert,
                'is_active' => $isActive,
            ]);
        }

        $this->command->info('Stocks mis à jour pour ' . $products->count() . ' produits');
    }
} 