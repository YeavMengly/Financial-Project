<?php

namespace App\Models\Content;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicService extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_code',
        'service_prefix_name',
        'service_name',
        'categories_id',
        'service_price',
        'max_service_time',
        'expired',
        'percentage_national',
        'percentage_mlvt',
        'percentage_interior',
        'percentage_mef'
    ];
}
