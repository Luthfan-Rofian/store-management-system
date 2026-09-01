<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->text('description')->nullable()->after('store_address');
            $table->text('ketentuan_content')->nullable()->after('description');
            $table->text('cara_shopping_content')->nullable()->after('ketentuan_content');
            $table->text('faq_content')->nullable()->after('cara_shopping_content');
        });
    }

    public function down(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->dropColumn(['description', 'ketentuan_content', 'cara_shopping_content', 'faq_content']);
        });
    }
};
