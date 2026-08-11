<?php

namespace App\Models\Material;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'ministry_id',
        'project_id',
        'p_name',
        'p_year',
        'unit',
        'qty',
        'price',
        'total_price',
        'source',
    ];
}
