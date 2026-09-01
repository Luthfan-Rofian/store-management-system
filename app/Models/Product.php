<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'image',
        'original_price',
        'price',
        'stock',
        'description',
        'specification',
        'is_active',
        'category_id', // <-- Ditambahkan agar bisa diisi dan disimpan
    ];

    protected $casts = [
        'original_price' => 'decimal:2',
        'price' => 'decimal:2',
        'stock' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Relasi ke Category
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relasi ke Order
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Cek apakah produk masih ada stok
     */
    public function isInStock(): bool
    {
        return $this->stock > 0;
    }

    /**
     * Hitung persentase diskon
     */
    public function getDiscountPercentageAttribute(): ?int
    {
        if (!$this->original_price || $this->original_price <= $this->price) {
            return null;
        }

        return (int) round((($this->original_price - $this->price) / $this->original_price) * 100);
    }

    /**
     * Format harga ke Rupiah
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp' . number_format((float) $this->price, 0, ',', '.');
    }

    public function getFormattedOriginalPriceAttribute(): string
    {
        return $this->original_price ? 'Rp' . number_format((float) $this->original_price, 0, ',', '.') : '';
    }

    /**
     * Route model binding pakai slug, bukan id
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
