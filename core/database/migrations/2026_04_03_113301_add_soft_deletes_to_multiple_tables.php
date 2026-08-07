<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('page_sections', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('modules', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('module_entries', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('page_sections', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        
        Schema::table('modules', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('module_entries', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
