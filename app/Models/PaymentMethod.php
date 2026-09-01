<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'account_number',
        'account_name',
        'qris_string', // Ditambahkan untuk payload QRIS statis
        'logo',
        'qris_image',  // Ditambahkan untuk file gambar QR Code QRIS
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Relasi ke Order
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Scope untuk ambil metode pembayaran yang aktif saja, urut sesuai sort_order
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order', 'asc');
    }
}
