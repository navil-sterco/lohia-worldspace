<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('module_entry_sub_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_entry_id')->constrained('module_entries')->cascadeOnDelete();
            $table->foreignId('page_id')->constrained('pages')->cascadeOnDelete();
            $table->timestamps();

            $table->unique('page_id', 'mesp_page_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('module_entry_sub_pages');
    }
};
