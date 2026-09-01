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
            $table->string('order_code')->unique(); // Kode unik pesanan, contoh: INV-20260826-0001

            // Relasi ke Produk
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('product_name'); // Snapshot nama produk saat order
            $table->decimal('product_price', 12, 2); // Snapshot harga saat order
            $table->integer('qty')->default(1);
            $table->decimal('total_price', 12, 2); // qty x product_price

            // Data Pemesan
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_email')->nullable();
            $table->text('customer_address');
            $table->text('order_note')->nullable(); // Catatan Pesanan
            $table->text('product_note')->nullable(); // Keterangan opsional dari header modal

            // Metode Pembayaran
            $table->foreignId('payment_method_id')->nullable()->constrained('payment_methods')->onDelete('set null');
            $table->string('payment_method_name')->nullable(); // Snapshot nama metode bayar

            // Status
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');

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
