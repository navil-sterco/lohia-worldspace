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
        Schema::create('channel_partners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('pan');
            $table->string('country_code')->default('+91');
            $table->string('phone', 20);
            $table->string('email');
            $table->text('address')->nullable();
            $table->text('work_profile')->nullable();
            $table->text('other_organizations')->nullable();
            $table->string('region_of_operations')->nullable();
            $table->string('sales_team_member_name')->nullable();
            $table->string('company_name');
            $table->string('date_of_establishment');
            $table->string('organization_type')->nullable();
            $table->string('association_member')->nullable();
            $table->string('business_type')->nullable();
            $table->string('registration_certificate_path')->nullable();
            $table->string('rera_certificate_path')->nullable();
            $table->string('registered_address')->nullable();
            $table->string('company_pan_details')->nullable();
            $table->string('gst_certificate_path')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('channel_partners');
    }
};
