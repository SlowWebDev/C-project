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
        Schema::create('security_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('event_type'); // login, logout, password_change, email_change, failed_login, etc.
            $table->string('status'); // success, failed, blocked
            $table->string('ip_address', 45);
            $table->text('user_agent');
            $table->json('device_info')->nullable(); // browser, OS, device type
            $table->string('location')->nullable(); // city, country based on IP
            $table->text('description')->nullable();
            $table->json('metadata')->nullable(); // additional data
            $table->timestamp('occurred_at');
            $table->timestamps();
            
            $table->index(['user_id', 'occurred_at']);
            $table->index(['event_type', 'status']);
            $table->index('ip_address');
            $table->index('occurred_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_events');
    }
};
