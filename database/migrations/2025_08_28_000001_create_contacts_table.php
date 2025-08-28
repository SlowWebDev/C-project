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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone');
            $table->text('message')->nullable();
            $table->enum('type', ['general', 'project_inquiry'])->default('general');
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('status', ['new', 'read', 'replied'])->default('new');
            $table->timestamps();
            
            // Indexes
            $table->index(['type', 'status']);
            $table->index(['created_at']);
            $table->index(['email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
