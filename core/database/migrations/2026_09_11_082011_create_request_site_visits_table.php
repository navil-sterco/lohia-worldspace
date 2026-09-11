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
        Schema::create('request_site_visits', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('email');
            $table->string('mobile_phone', 20);
            $table->string('city_desc');
            $table->string('udf_16')->nullable();
            $table->decimal('budget_from', 15, 3)->nullable();
            $table->decimal('budget_to', 15, 3)->nullable();
            $table->string('udf_17')->nullable();
            $table->string('udf_18')->nullable();
            $table->date('udf_6')->nullable();
            $table->text('comments')->nullable();
            $table->string('origin_from')->default('WEBSITE L1');
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_site_visits');
    }
};
