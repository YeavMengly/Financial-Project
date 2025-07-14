<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubDepart extends Model
{
    use HasFactory;

    protected $fillable = [
        'depart_id',
        'subDepart',
        'txtSubDepart'
    ];
}
