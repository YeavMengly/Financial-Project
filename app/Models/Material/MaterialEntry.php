<?php

namespace App\Models\Material;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'ministry_id',
        'company_name',
        'stock_number',
        'stock_name',
        'user_entry',
        'p_code',
        'p_name',
        'p_year',
        'title',
        'unit',
        'quantity',
        'price',
        'total_price',
        'source',
        'note',
        'refer',
        'date_entry',
        'file',
    ];
}
