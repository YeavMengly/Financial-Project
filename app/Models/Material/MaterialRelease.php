<?php

namespace App\Models\Material;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialRelease extends Model
{
    use HasFactory;

    protected $fillable = [
        'ministry_id',
        'project_id',
        'project_sub_id',
        'agency_id',
        'material_entry_id',
        'p_name',
        'p_year',
        'title',
        'unit',
        'quantity_total',
        'quantity_request',
        'price',
        'total_price',
        'source',
        'refer',
        'date_release',
        'file',
    ];
}
