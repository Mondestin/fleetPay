<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('platform_earnings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('driver_id')->constrained('drivers')->onDelete('cascade');
            $table->enum('platform', ['bolt', 'uber', 'heetch']);
            $table->date('week_start_date');
            $table->decimal('earnings', 10, 2);
            $table->foreignUuid('created_by')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['pending', 'paid'])->default('pending');
            $table->boolean('validated')->default(false);
            $table->timestamps();
            
            $table->unique(['driver_id', 'platform', 'week_start_date'], 'unique_weekly_platform');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_earnings');
    }
}; 