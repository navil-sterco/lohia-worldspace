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
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->nullable();
            $table->enum('page_type', ['cms', 'modular','module_sub_page'])->default('modular');
            $table->json('display_location')->nullable();
            $table->boolean('is_published')->default(true);
            $table->boolean('target_blank')->default(false);
            $table->foreignId('tab_id')->nullable()->constrained('tabs')->onDelete('set null');
            $table->foreignId('parent_page_id')->nullable()->constrained('pages')->nullOnDelete();
            $table->integer('display_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
