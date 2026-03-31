<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            //relation
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // order identity
            $table->string('order_number')->unique();

            // Pricing
            $table->decimal('total_amount',10,2);

            // Order status
            $table->enum('status',[
                'pending',
                'confirmed',
                'shipped',
                'delevered',
                'cancelled'
            ])->default('pending');

            //Payment
            $table->string('payement_method')->nullable();
            $table->string('payment_status')->default('pending');

            // Shipping
            $table->text('Shipping_address');
            $table->string('phone');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
