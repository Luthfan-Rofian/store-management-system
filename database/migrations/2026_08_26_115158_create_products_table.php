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
            $table->string('slug')->unique();
            $table->string('image')->nullable();
            $table->decimal('original_price', 12, 2)->nullable(); // Harga Coret
            $table->decimal('price', 12, 2); // Harga Diskon / Harga Jual
            $table->integer('stock')->default(0);
            $table->longText('description')->nullable(); // Deskripsi HTML
            $table->longText('specification')->nullable(); // Spesifikasi/Fitur Produk
            $table->boolean('is_active')->default(true);
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
