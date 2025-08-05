<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'image',
        'category_id',
        'stock_quantity',
        'min_stock_alert',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'min_stock_alert' => 'integer',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Vérifier si le produit est en stock
     */
    public function isInStock()
    {
        return $this->stock_quantity > 0;
    }

    /**
     * Vérifier si le stock est faible
     */
    public function isLowStock()
    {
        return $this->stock_quantity <= $this->min_stock_alert;
    }

    /**
     * Vérifier si le produit est disponible pour la vente
     */
    public function isAvailable()
    {
        return $this->is_active && $this->isInStock();
    }

    /**
     * Réduire le stock
     */
    public function reduceStock($quantity)
    {
        if ($this->stock_quantity >= $quantity) {
            $this->stock_quantity -= $quantity;
            $this->save();
            return true;
        }
        return false;
    }

    /**
     * Augmenter le stock
     */
    public function increaseStock($quantity)
    {
        $this->stock_quantity += $quantity;
        $this->save();
        return true;
    }

    /**
     * Obtenir le statut du stock
     */
    public function getStockStatus()
    {
        if (!$this->is_active) {
            return 'inactive';
        }
        
        if ($this->stock_quantity <= 0) {
            return 'out_of_stock';
        }
        
        if ($this->isLowStock()) {
            return 'low_stock';
        }
        
        return 'in_stock';
    }

    /**
     * Scope pour les produits en stock
     */
    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    /**
     * Scope pour les produits actifs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour les produits avec stock faible
     */
    public function scopeLowStock($query)
    {
        return $query->whereRaw('stock_quantity <= min_stock_alert');
    }
}
