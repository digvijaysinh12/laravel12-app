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
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();

            $table->string('subject');
            $table->text('message');

            $table->string('customer_name');
            $table->string('customer_email');

            $table->enum('priority', [
                'low',
                'medium',
                'high',
            ])->default('medium');

            $table->enum('status', [
                'open',
                'assigned',
                'in_progress',
                'closed',
            ])->default('open');

            $table->string('assigned_to')->nullable();

            // Slack thread timestamp
            $table->string('slack_thread_ts')->nullable();

            Schema::dropIfExists('support_tickets');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};
