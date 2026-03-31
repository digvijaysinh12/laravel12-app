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
        Schema::table('orders', function (Blueprint $table) {
            // Rename wrong columns
            $table->renameColumn('payement_method', 'payment_method');
            $table->renameColumn('Shipping_address', 'shipping_address');
            $table->enum('status', [
                    'pending',
                    'confirmed',
                    'shipped',
                    'delivered', // fixed
                    'cancelled'
                ])->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            //
        });
    }
};
