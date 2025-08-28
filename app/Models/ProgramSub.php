<?php

namespace App\Models;

use App\Models\BeginCredit\Agency;
use App\Models\BeginCredit\Ministry;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramSub extends Model
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

    public function agency()
    {
        return $this->belongsTo(Agency::class, 'program_id', 'id');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id', 'id');
    }
}
