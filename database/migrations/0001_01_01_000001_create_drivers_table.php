<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('driver_uber_id', 100)->nullable();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('full_name', 200)->nullable();
            $table->string('phone_number', 20)->nullable()->unique();
            $table->string('email')->nullable()->unique();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
}; 