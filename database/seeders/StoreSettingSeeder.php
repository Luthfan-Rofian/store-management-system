<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StoreSettingSeeder extends Seeder
{
    public function run(): void
    {
        // Hapus data lama
        DB::table('store_settings')->truncate();

        // Insert data baru
        DB::table('store_settings')->insert([
            [
                'store_name' => 'Toko Online Saya',
                'whatsapp_number' => '628123456789',
                'store_address' => 'Jl. Contoh No. 123, Kota Bandung, Jawa Barat 40123',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
