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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->decimal('price');
            $table->json('images')->nullable(); // Or $table->text('images')->nullable();
            $table->json('specifications')->nullable(); // Or $table->text('specifications')->nullable();
            $table->json('variations')->nullable(); // Or $table->text('variations')->nullable();
            $table->string('stock_status')->default('in stock');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
