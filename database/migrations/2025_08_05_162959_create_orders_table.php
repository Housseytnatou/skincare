<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('order_number')->unique();
            
            // Informations de livraison
            $table->string('shipping_address');
            $table->string('shipping_city');
            $table->string('shipping_postal_code');
            $table->string('shipping_phone');
            
            // Informations de commande
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            
            // Statuts
            $table->enum('order_status', ['pending', 'processing', 'shipped', 'delivered', 'cancelled'])->default('pending');
            $table->enum('payment_method', ['online', 'cash_on_delivery'])->default('online');
            $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');
            
            // Informations de paiement
            $table->string('payment_id')->nullable();
            $table->timestamp('paid_at')->nullable();
            
            // Notes et commentaires
            $table->text('notes')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
