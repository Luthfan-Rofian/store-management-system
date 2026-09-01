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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama Bank, E-Wallet, atau QRIS (Contoh: BCA, DANA, QRIS Utama)
            $table->string('account_number')->nullable(); // Nomor Rekening / No HP (dibuat nullable untuk QRIS)
            $table->string('account_name')->nullable(); // Atas Nama pemilik rekening/merchant (nullable untuk QRIS)
            $table->text('qris_string')->nullable(); // Opsional: Menyimpan teks/payload QRIS statis
            $table->string('logo')->nullable(); // Path logo/icon bank atau e-wallet
            $table->string('qris_image')->nullable(); // Path khusus gambar QR Code QRIS
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
