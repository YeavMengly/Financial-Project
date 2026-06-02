<?php

namespace App\Models\Content;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'number_type',
        'name'
    ];
    public function chapter()
    {
         return $this->hasMany(Chapter::class, 'type_id', 'id');
    }
}
