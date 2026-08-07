<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('module_entries', function (Blueprint $table) {
            $table->boolean('mapped_to_homepage')->default(false)->after('is_published');
        });
    }

    public function down(): void
    {
        Schema::table('module_entries', function (Blueprint $table) {
            $table->dropColumn('mapped_to_homepage');
        });
    }
};
