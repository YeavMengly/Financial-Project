<?php

namespace App\Models\Revenue;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicServiceCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'service_cate_name',
        'service_is_ministry',
        'parent'
    ];
}
