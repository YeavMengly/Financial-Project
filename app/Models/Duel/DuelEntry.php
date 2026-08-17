<?php

namespace App\Models\Duel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DuelEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'ministry_id',
        'project_id',
        'item_name',
        'unit',
        'quantity',
        'price',
        'total_price',
        'date_entry',
        'pro_year',
        'source'
    ];
}
