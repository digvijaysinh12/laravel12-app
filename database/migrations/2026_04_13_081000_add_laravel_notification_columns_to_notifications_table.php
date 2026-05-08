<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('notifications')) {
            return;
        }

        Schema::table('notifications', function (Blueprint $table) {
            // This guard keeps fresh migrations and upgraded databases compatible.
            if (! Schema::hasColumn('notifications', 'notifiable_id')) {
                $table->unsignedBigInteger('notifiable_id')->nullable()->after('id');
            }

            if (! Schema::hasColumn('notifications', 'notifiable_type')) {
                $table->string('notifiable_type')->nullable()->after('notifiable_id');
            }

            if (! Schema::hasColumn('notifications', 'data')) {
                $table->json('data')->nullable()->after('notifiable_type');
            }

            if (! Schema::hasColumn('notifications', 'read_at')) {
                $table->timestamp('read_at')->nullable()->after('data');
            }

        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('notifications')) {
            return;
        }

        Schema::table('notifications', function (Blueprint $table) {
            if (Schema::hasColumn('notifications', 'notifiable_type') && Schema::hasColumn('notifications', 'notifiable_id')) {
                $table->dropColumn(['notifiable_id', 'notifiable_type']);
            }

            if (Schema::hasColumn('notifications', 'data') && Schema::hasColumn('notifications', 'read_at')) {
                $table->dropColumn(['data', 'read_at']);
            }
        });
    }
};
