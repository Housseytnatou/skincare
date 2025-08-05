<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Product;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Créer quelques commandes de test
        $users = User::all();
        $products = Product::all();

        if ($users->isEmpty() || $products->isEmpty()) {
            $this->command->info('Aucun utilisateur ou produit trouvé. Créez d\'abord des utilisateurs et des produits.');
            return;
        }

        // Créer 5 commandes de test
        for ($i = 0; $i < 5; $i++) {
            $user = $users->random();
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => Order::generateOrderNumber(),
                'shipping_address' => '123 Rue de la Paix',
                'shipping_city' => 'Paris',
                'shipping_postal_code' => '75001',
                'shipping_phone' => '0123456789',
                'payment_method' => rand(0, 1) ? 'online' : 'cash_on_delivery',
                'order_status' => ['pending', 'processing', 'shipped', 'delivered'][rand(0, 3)],
                'payment_status' => ['pending', 'paid'][rand(0, 1)],
                'subtotal' => 0,
                'tax' => 0,
                'total' => 0,
                'notes' => 'Commande de test ' . ($i + 1),
            ]);

            // Ajouter 1 à 3 produits par commande
            $numProducts = rand(1, 3);
            $subtotal = 0;

            for ($j = 0; $j < $numProducts; $j++) {
                $product = $products->random();
                $quantity = rand(1, 3);
                $price = $product->price;
                $total = $price * $quantity;
                $subtotal += $total;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $price,
                    'quantity' => $quantity,
                    'total' => $total,
                ]);
            }

            // Calculer les taxes et le total
            $tax = $subtotal * 0.20;
            $total = $subtotal + $tax;

            $order->update([
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
            ]);

            // Si le paiement est marqué comme payé, ajouter une date de paiement
            if ($order->payment_status === 'paid') {
                $order->markAsPaid('PAY-TEST-' . strtoupper(uniqid()));
            }
        }

        $this->command->info('5 commandes de test ont été créées avec succès !');
    }
}
