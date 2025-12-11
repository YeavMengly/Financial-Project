<?php

namespace App\Models\Duel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DuelRelease extends Model
{
    use HasFactory;
    protected $fillable = [
        'ministry_id',
        'item_name',
        'receipt_number',
        'stock_number',
        'agency',
        'user_request',
        'unit',
        'quantity_total',
        'quantity_request',
        'duel_total',
        'note',
        'refer',
        'date_release',
        'file',
    ];
}
