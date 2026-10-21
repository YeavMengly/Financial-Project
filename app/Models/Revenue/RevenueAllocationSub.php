<?php

namespace App\Models\Revenue;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RevenueAllocationSub extends Model
{
    use HasFactory;
    protected $fillable = [
        'revenue_allocation_id',
        'code',
        'name',
        'description',
    ];
}
