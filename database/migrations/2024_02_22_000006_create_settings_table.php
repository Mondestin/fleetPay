<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->string('name', 100);
            $table->text('value');
            $table->timestamps();
            
            // Make name unique per user
            $table->unique(['user_id', 'name']);
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
}; 