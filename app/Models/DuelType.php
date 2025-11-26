<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DuelType extends Model
{
    use HasFactory;
    protected $fillable = [
        'name_km',
        'name_latn',
    ];
}
