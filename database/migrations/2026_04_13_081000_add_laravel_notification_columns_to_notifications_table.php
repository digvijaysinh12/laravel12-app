<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // FIXED: add the columns Laravel's database notifications need.
            $table->unsignedBigInteger('notifiable_id')->nullable()->after('id');
            $table->string('notifiable_type')->nullable()->after('notifiable_id');
            $table->json('data')->nullable()->after('notifiable_type');
            $table->timestamp('read_at')->nullable()->after('data');
            $table->index(['notifiable_type', 'notifiable_id']);
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex(['notifiable_type', 'notifiable_id']);
            $table->dropColumn(['notifiable_id', 'notifiable_type']);
            $table->dropColumn(['data', 'read_at']);
        });
    }
};
