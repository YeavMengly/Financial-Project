<?php

namespace App\Models\Revenue;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RevenueSource extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'name',
        'description',
    ];
}
