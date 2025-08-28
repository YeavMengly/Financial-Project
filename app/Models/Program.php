<?php

namespace App\Models;

use App\Models\BeginCredit\Agency;
use App\Models\BeginCredit\Ministry;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Program extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'ministry_id',
        'no',
        'title'
    ];
    public function ministries()
    {
        return $this->belongsTo(Ministry::class, 'ministry_id', 'id');
    }
    public function programSub()
    {
        return $this->hasMany(ProgramSub::class, 'program_id', 'id');
    }

    //  public function agency()
    // {
    //     return $this->belongsTo(Agency::class, 'program_id', 'id');
    // }

}
