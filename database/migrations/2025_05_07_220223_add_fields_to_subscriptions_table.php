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
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->enum('plan_name', ['Free', 'Pro'])->default('Free');
            $table->timestamp('expires_at')->nullable();
            $table->enum('payment_method', ['Cash', 'Paypal', 'Card'])->default('Card');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn([
                'plan_name',
                'expires_at',
                'payment_method'
            ]);
        });
    }
};