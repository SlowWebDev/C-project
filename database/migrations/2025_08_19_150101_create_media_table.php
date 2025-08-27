<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image');
            $table->json('gallery')->nullable();
            $table->string('status')->default('published');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('media');
    }
};
