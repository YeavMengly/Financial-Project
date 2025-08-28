<?php

namespace App\Models\BeginCredit;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CodeSub extends Model
{
    use HasFactory;

    protected $fillable = [
        'ministry_id',
        'program_id',
        'no',
        'decription'
    ];

    public function ministries()
    {
        return $this->belongsTo(Ministry::class, 'ministry_id', 'id');
    }

    public function program()
    {
        return $this->belongsTo(Code::class, 'program_id', 'id');
    }
}
