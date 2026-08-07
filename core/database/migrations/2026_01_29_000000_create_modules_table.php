<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->json('page_section_ids')->nullable();
            $table->boolean('auto_generate_slug')->default(true);
            $table->json('fields_config')->nullable();
            $table->json('mapping_config')->nullable();
            $table->json('map_to_module_ids')->nullable();
            $table->boolean('mapping_enabled')->default(false);
            $table->boolean('types_enabled')->default(false);
            $table->json('types')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};

