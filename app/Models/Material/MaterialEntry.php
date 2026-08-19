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
        'project_sub_id',
        'program_id',
        'program_sub_id',
        'cluster_id',
        'account_sub_id',
        'p_name',
        'p_year',
        'unit',
        'qty',
        'price',
        'total_price',
        'source',
    ];
}
