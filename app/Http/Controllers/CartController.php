<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    // Afficher tous les items du panier de l'utilisateur connecté
    public function index(Request $request)
    {
        $user = $request->user();
        $cartItems = Cart::where('user_id', $user->id)->with('product')->get();
        return response()->json($cartItems);
    }

    // Ajouter un produit au panier
    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        // Vérifier si le produit est déjà dans le panier
        $cartItem = Cart::where('user_id', $user->id)
            ->where('product_id', $validated['product_id'])
            ->first();

        if ($cartItem) {
            // Si déjà dans le panier, augmenter la quantité
            $cartItem->quantity += $validated['quantity'];
            $cartItem->save();
        } else {
            // Sinon, créer un nouvel item panier
            $cartItem = Cart::create([
                'user_id' => $user->id,
                'product_id' => $validated['product_id'],
                'quantity' => $validated['quantity'],
            ]);
        }

        return response()->json($cartItem, 201);
    }

    // Mettre à jour la quantité d'un produit dans le panier
    public function update(Request $request, $id)
    {
        $user = $request->user();

        $cartItem = Cart::where('id', $id)->where('user_id', $user->id)->first();

        if (!$cartItem) {
            return response()->json(['message' => 'Article du panier non trouvé'], 404);
        }

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem->quantity = $validated['quantity'];
        $cartItem->save();

        return response()->json($cartItem);
    }

    // Supprimer un produit du panier
    public function destroy(Request $request, $id)
    {
        $user = $request->user();

        $cartItem = Cart::where('id', $id)->where('user_id', $user->id)->first();

        if (!$cartItem) {
            return response()->json(['message' => 'Article du panier non trouvé'], 404);
        }

        $cartItem->delete();

        return response()->json(['message' => 'Article supprimé du panier']);
    }

    // Calculer le total du panier (prix * quantité)
    public function total(Request $request)
    {
        $user = $request->user();

        $total = Cart::where('user_id', $user->id)
            ->with('product')
            ->get()
            ->sum(function ($item) {
                return $item->product->price * $item->quantity;
            });

        return response()->json(['total' => $total]);
    }

    // Vider complètement le panier
    public function clear(Request $request)
    {
        $user = $request->user();
        Cart::where('user_id', $user->id)->delete();

        return response()->json(['message' => 'Panier vidé avec succès']);
    }

    // Obtenir le nombre total d'articles dans le panier
    public function count(Request $request)
    {
        $user = $request->user();

        $count = Cart::where('user_id', $user->id)->sum('quantity');

        return response()->json(['count' => $count]);
    }
}
