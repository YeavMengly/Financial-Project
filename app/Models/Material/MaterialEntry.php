<?php

namespace App\Models\Material;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaterialEntry extends Model
{
    use HasFactory;
    use SoftDeletes;

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

    public function materialReleases()
{
    return $this->hasMany(MaterialRelease::class, 'material_entry_id', 'id');
}
}
