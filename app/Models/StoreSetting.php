<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreSetting extends Model
{
    protected $table = 'store_settings';

    protected $fillable = [
        'store_name',
        'whatsapp_number',
        'store_address',
        'logo',
        'description',
        'ketentuan_content',
        'cara_shopping_content',
        'faq_content',
    ];
}
