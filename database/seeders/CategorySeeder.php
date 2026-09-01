<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Laravel Framework',
            'CodeIgniter PHP',
            'PHP Native / OOP',
            'Sistem Informasi Skripsi',
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(
                ['name' => $cat] // Mencari berdasarkan nama saja, tanpa kolom slug
            );
        }
    }
}
