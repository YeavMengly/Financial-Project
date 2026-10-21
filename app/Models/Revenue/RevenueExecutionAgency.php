<?php

namespace App\Models\Revenue;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RevenueExecutionAgency extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'name',
        'nick_name',
    ];
}
