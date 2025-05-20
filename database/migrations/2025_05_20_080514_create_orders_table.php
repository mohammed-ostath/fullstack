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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('status')->default('pending');
            $table->string('shopping_name');
            $table->string('shopping_address');
            $table->string('shopping_city');
            $table->string('shopping_state')->nullable();
            $table->string('shopping_zipcode');
            $table->string('shopping_country');
            $table->string('shopping_phone');
            $table->decimal('subtotal',10,2);
            $table->decimal('tax',10.2)->default(0);
            $table->decimal('shopping_cost',10,2)->default(0);
            $table->decimal('total',10,2);
            $table->string('payment_method')->default('cod');
            $table->string('payment_status')->default('pending');
            $table->string('order_number')->unique();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
