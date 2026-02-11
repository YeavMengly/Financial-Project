<?php

namespace App\Models\Material;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialRelease extends Model
{
    use HasFactory;

    protected $fillable = [
        'ministry_id',
        'p_code',
        'p_name',
        'p_year',
        'title',
        'unit',
        'quantity_total',
        'quantity_request',
        'total',
        'source',
        'refer',
        'date_release',
        'file',
    ];
}
