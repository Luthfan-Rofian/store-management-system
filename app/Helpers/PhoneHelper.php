<?php

namespace App\Helpers;

class PhoneHelper
{
    /**
     * Membersihkan karakter non-angka dan normalisasi awalan 08 -> 628.
     */
    public static function normalize(string $phone): string
    {
        // Hapus semua karakter selain angka
        $clean = preg_replace('/[^0-9]/', '', $phone);

        // Ubah awalan 0 menjadi 62
        if (str_starts_with($clean, '0')) {
            $clean = '62' . substr($clean, 1);
        }

        // Jika belum ada awalan 62, tambahkan
        if (!str_starts_with($clean, '62')) {
            $clean = '62' . $clean;
        }

        return $clean;
    }
}
