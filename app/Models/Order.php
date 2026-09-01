<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'notes', // <-- Sesuaikan dengan kolom database Anda ('notes')
        'order_code',
        'product_id',
        'product_name',
        'product_price',
        'qty',
        'total_price',
        'customer_name',
        'customer_phone',
        'customer_email',
        'customer_address',
        'order_note',
        'product_note',
        'payment_method_id',
        'payment_method_name',
        'status',
    ];

    protected $casts = [
        'product_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'qty' => 'integer',
    ];

    /**
     * Relasi ke Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relasi ke PaymentMethod
     */
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    /**
     * Format total harga ke Rupiah
     */
    public function getFormattedTotalPriceAttribute(): string
    {
        return 'Rp' . number_format((float) $this->total_price, 0, ',', '.');
    }

    /**
     * Generate kode order unik otomatis, contoh: INV-20260826-0001
     */
    public static function generateOrderCode(): string
    {
        $date = now()->format('Ymd');
        $prefix = "INV-{$date}-";

        $lastOrder = self::where('order_code', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        $lastNumber = 0;
        if ($lastOrder) {
            $lastNumber = (int) substr($lastOrder->order_code, -4);
        }

        $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

        return $prefix . $newNumber;
    }
}
