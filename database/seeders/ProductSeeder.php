<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Smartwatch Fitness Tracker',
                'slug' => 'smartwatch-fitness-tracker-' . time(),
                'price' => 299000,
                'original_price' => 599000,
                'stock' => 50,
                'description' => '<p>Smartwatch terbaru dengan fitur monitoring kesehatan lengkap:</p>
                    <ul>
                        <li>Heart Rate Monitor</li>
                        <li>Sleep Tracking</li>
                        <li>GPS Built-in</li>
                        <li>Water Resistant 5ATM</li>
                        <li>Battery 14 Hari</li>
                    </ul>',
                'specification' => '<p><strong>Spesifikasi:</strong></p>
                    <ul>
                        <li>Layar: AMOLED 1.4"</li>
                        <li>Prosesor: Snapdragon 4100+</li>
                        <li>RAM: 1GB</li>
                        <li>Storage: 8GB</li>
                        <li>Konektivitas: Bluetooth 5.0, NFC</li>
                        <li>Warna: Black, Silver, Gold</li>
                    </ul>',
                'is_active' => true,
                'views' => 0,
            ],
            [
                'name' => 'Wireless Earbuds Pro',
                'slug' => 'wireless-earbuds-pro-' . time(),
                'price' => 149000,
                'original_price' => 299000,
                'stock' => 75,
                'description' => '<p>Earbuds nirkabel dengan kualitas audio premium:</p>
                    <ul>
                        <li>Active Noise Cancellation</li>
                        <li>40 Jam Battery Life</li>
                        <li>Comfort Fit Ergonomis</li>
                        <li>IPX5 Water Resistant</li>
                    </ul>',
                'specification' => '<p><strong>Spesifikasi:</strong></p>
                    <ul>
                        <li>Driver: 10mm Dynamic</li>
                        <li>Freq Response: 20Hz-20kHz</li>
                        <li>Codec: AAC, SBC</li>
                        <li>Weight: 4.2g per earbud</li>
                        <li>Case Weight: 50g</li>
                    </ul>',
                'is_active' => true,
                'views' => 0,
            ],
            [
                'name' => 'Portable Speaker Bluetooth',
                'slug' => 'portable-speaker-bluetooth-' . time(),
                'price' => 199000,
                'original_price' => 399000,
                'stock' => 30,
                'description' => '<p>Speaker portabel dengan bass yang dalam:</p>
                    <ul>
                        <li>360° Sound</li>
                        <li>20 Jam Playtime</li>
                        <li>Waterproof IPX7</li>
                        <li>Microphone Built-in</li>
                    </ul>',
                'specification' => '<p><strong>Spesifikasi:</strong></p>
                    <ul>
                        <li>Power: 20W</li>
                        <li>Frequency: 50Hz-20kHz</li>
                        <li>Bluetooth: v5.0</li>
                        <li>Baterai: 3600mAh</li>
                    </ul>',
                'is_active' => true,
                'views' => 0,
            ],
            [
                'name' => 'USB-C Fast Charger 65W',
                'slug' => 'usb-c-fast-charger-65w-' . time(),
                'price' => 89000,
                'original_price' => 179000,
                'stock' => 100,
                'description' => '<p>Charger cepat untuk semua device:</p>
                    <ul>
                        <li>65W Fast Charging</li>
                        <li>Dual Port USB-C & USB-A</li>
                        <li>Compact Design</li>
                        <li>Safe Protection</li>
                    </ul>',
                'specification' => '<p><strong>Spesifikasi:</strong></p>
                    <ul>
                        <li>Input: 100-240V 50/60Hz</li>
                        <li>Output: USB-C 65W, USB-A 18W</li>
                        <li>Material: Aluminum Alloy</li>
                        <li>Weight: 145g</li>
                    </ul>',
                'is_active' => true,
                'views' => 0,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
