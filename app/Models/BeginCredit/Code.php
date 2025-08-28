<?php

namespace App\Models\BeginCredit;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Code extends Model
{
    use HasFactory;


    protected $fillable = [
        'ministry_id',
        'no',
        'title'
    ];
    public function ministries()
    {
        return $this->belongsTo(Ministry::class, 'ministry_id', 'id');
    }
}
