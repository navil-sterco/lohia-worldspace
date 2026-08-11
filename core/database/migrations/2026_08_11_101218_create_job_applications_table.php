<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->string('job_slug')->nullable()->index();
            $table->string('job_title')->nullable();

            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->string('best_time_to_call')->nullable();

            $table->string('cv_path');
            $table->string('cv_original_name')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};
