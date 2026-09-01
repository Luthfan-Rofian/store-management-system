<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $methods = [
            [
                'name' => 'Bank BCA',
                'account_number' => '1234567890',
                'account_name' => 'Toko Online Saya',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Bank CIMB Niaga',
                'account_number' => '7001234567',
                'account_name' => 'Toko Online Saya',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Bank Jago',
                'account_number' => '108234567890',
                'account_name' => 'Toko Online Saya',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Bank SeaBank',
                'account_number' => '901234567890',
                'account_name' => 'Toko Online Saya',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'QRIS',
                'account_number' => null,
                'account_name' => 'Scan untuk membayar',
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($methods as $method) {
            PaymentMethod::updateOrCreate(
                ['name' => $method['name']],
                $method
            );
        }
    }
}
