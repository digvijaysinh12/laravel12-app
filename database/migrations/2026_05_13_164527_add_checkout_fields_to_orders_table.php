<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            $table->string('full_name')->after('user_id');

            $table->string('city')->nullable()->after('phone');

            $table->string('pincode', 6)->nullable()->after('city');

            $table->text('notes')->nullable()->after('pincode');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            $table->dropColumn([
                'full_name',
                'city',
                'pincode',
                'notes',
            ]);
        });
    }
};