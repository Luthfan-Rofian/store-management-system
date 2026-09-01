<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            StoreSettingSeeder::class,
            AdminUserSeeder::class,
            PaymentMethodSeeder::class,
            CategorySeeder::class, // <-- Kita tambahkan di sini agar otomatis terisi
            ProductSeeder::class,
        ]);
    }
}
