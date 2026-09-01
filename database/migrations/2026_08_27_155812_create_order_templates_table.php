<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_templates', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Judul/Nama pilihan di dropdown
            $table->text('content'); // Isi teks catatan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_templates');
    }
};
