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
        Schema::create('device_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('device_id'); // device identifier
            $table->string('device_name')->nullable(); // friendly device name
            $table->string('device_type'); // desktop, mobile, tablet
            $table->string('browser');
            $table->string('operating_system');
            $table->string('ip_address', 45);
            $table->string('location')->nullable();
            $table->boolean('is_trusted')->default(false);
            $table->boolean('is_blocked')->default(false);
            $table->timestamp('last_activity');
            $table->timestamp('first_seen');
            $table->text('user_agent');
            $table->json('metadata')->nullable();
            $table->timestamps();
            

            $table->unique(['user_id', 'device_id']);
            
            $table->index(['user_id', 'last_activity']);
            $table->index(['device_id', 'user_id']);
            $table->index('is_blocked');
            $table->index('is_trusted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_sessions');
    }
};
