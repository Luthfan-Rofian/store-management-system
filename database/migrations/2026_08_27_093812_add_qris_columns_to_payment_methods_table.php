<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_methods', function (Blueprint $table) {
            if (!Schema::hasColumn('payment_methods', 'qris_string')) {
                $table->text('qris_string')->nullable()->after('account_name');
            }
            if (!Schema::hasColumn('payment_methods', 'qris_image')) {
                $table->string('qris_image')->nullable()->after('logo');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payment_methods', function (Blueprint $table) {
            $table->dropColumn(['qris_string', 'qris_image']);
        });
    }
};
