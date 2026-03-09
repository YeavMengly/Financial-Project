<?php

namespace App\Models\Duel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DuelEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'ministry_id',
        'item_name',
        'company_name',
        'stock_number',
        'stock_name',
        'user_entry',
        'unit',
        'title',
        'quantity',
        'price',
        'duel_total',
        'note',
        'refer',
        'date_entry',
        'file',
        'source'
    ];
}
