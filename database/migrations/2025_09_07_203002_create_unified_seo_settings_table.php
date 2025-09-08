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
        Schema::create('unified_seo_settings', function (Blueprint $table) {
            $table->id();
            
            // Home Page Settings
            $table->string('home_title')->nullable();
            $table->string('home_meta_title')->nullable();
            $table->text('home_meta_description')->nullable();
            $table->text('home_meta_keywords')->nullable();
            $table->string('home_og_title')->nullable();
            $table->text('home_og_description')->nullable();
            $table->string('home_og_image')->nullable();
            $table->string('home_canonical')->nullable();
            $table->integer('home_priority')->default(10);
            $table->string('home_frequency')->default('weekly');
            $table->boolean('home_active')->default(false);
            $table->boolean('home_indexable')->default(false);
            
            // About Page Settings
            $table->string('about_title')->nullable();
            $table->string('about_meta_title')->nullable();
            $table->text('about_meta_description')->nullable();
            $table->text('about_meta_keywords')->nullable();
            $table->string('about_og_title')->nullable();
            $table->text('about_og_description')->nullable();
            $table->string('about_og_image')->nullable();
            $table->string('about_canonical')->nullable();
            $table->integer('about_priority')->default(8);
            $table->string('about_frequency')->default('monthly');
            $table->boolean('about_active')->default(false);
            $table->boolean('about_indexable')->default(false);
            
            // Contact Page Settings
            $table->string('contact_title')->nullable();
            $table->string('contact_meta_title')->nullable();
            $table->text('contact_meta_description')->nullable();
            $table->text('contact_meta_keywords')->nullable();
            $table->string('contact_og_title')->nullable();
            $table->text('contact_og_description')->nullable();
            $table->string('contact_og_image')->nullable();
            $table->string('contact_canonical')->nullable();
            $table->integer('contact_priority')->default(7);
            $table->string('contact_frequency')->default('monthly');
            $table->boolean('contact_active')->default(false);
            $table->boolean('contact_indexable')->default(false);
            
            // Projects Page Settings
            $table->string('projects_title')->nullable();
            $table->string('projects_meta_title')->nullable();
            $table->text('projects_meta_description')->nullable();
            $table->text('projects_meta_keywords')->nullable();
            $table->string('projects_og_title')->nullable();
            $table->text('projects_og_description')->nullable();
            $table->string('projects_og_image')->nullable();
            $table->string('projects_canonical')->nullable();
            $table->integer('projects_priority')->default(9);
            $table->string('projects_frequency')->default('weekly');
            $table->boolean('projects_active')->default(false);
            $table->boolean('projects_indexable')->default(false);
            
            // Media Page Settings
            $table->string('media_title')->nullable();
            $table->string('media_meta_title')->nullable();
            $table->text('media_meta_description')->nullable();
            $table->text('media_meta_keywords')->nullable();
            $table->string('media_og_title')->nullable();
            $table->text('media_og_description')->nullable();
            $table->string('media_og_image')->nullable();
            $table->string('media_canonical')->nullable();
            $table->integer('media_priority')->default(6);
            $table->string('media_frequency')->default('weekly');
            $table->boolean('media_active')->default(false);
            $table->boolean('media_indexable')->default(false);
            
            // Careers Page Settings
            $table->string('careers_title')->nullable();
            $table->string('careers_meta_title')->nullable();
            $table->text('careers_meta_description')->nullable();
            $table->text('careers_meta_keywords')->nullable();
            $table->string('careers_og_title')->nullable();
            $table->text('careers_og_description')->nullable();
            $table->string('careers_og_image')->nullable();
            $table->string('careers_canonical')->nullable();
            $table->integer('careers_priority')->default(5);
            $table->string('careers_frequency')->default('monthly');
            $table->boolean('careers_active')->default(false);
            $table->boolean('careers_indexable')->default(false);
            
            // Global Settings
            $table->string('site_name')->default('C-Project');
            $table->string('default_og_image')->nullable();
            $table->string('google_analytics')->nullable();
            $table->string('google_console')->nullable();
            $table->string('facebook_app_id')->nullable();
            $table->json('schema_data')->nullable();
            $table->json('custom_meta')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unified_seo_settings');
    }
};
