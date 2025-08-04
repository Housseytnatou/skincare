<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    // Lister tous les produits
    public function index()
    {
        return Product::all();
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
}
