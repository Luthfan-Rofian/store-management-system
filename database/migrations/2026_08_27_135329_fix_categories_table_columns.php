<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Jika tabel categories belum ada sama sekali, buat baru dengan kolom id dan name
        if (!Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->timestamps();
            });
        } else {
            // Jika tabel sudah ada tapi belum punya kolom 'name', tambahkan kolom 'name'
            Schema::table('categories', function (Blueprint $table) {
                if (!Schema::hasColumn('categories', 'name')) {
                    $table->string('name')->after('id');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'name')) {
                $table->dropColumn('name');
            }
        });
    }
};
