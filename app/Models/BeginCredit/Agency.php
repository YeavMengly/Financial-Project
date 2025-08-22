<?php

namespace App\Models\BeginCredit;

use App\Models\Program;
use App\Models\ProgramSub;
use App\Models\SubDepart;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agency extends Model
{
    use HasFactory;
    protected $fillable = [
        'ministry_id',
        'program_id',
        'no',
        'name',
        'nick_name'
    ];

    public function ministry()
    {
        return $this->belongsTo(Ministry::class, 'ministry_id', 'id');
    }

    public function beginVoucher()
    {
        return $this->hasMany(BeginVoucher::class);
    }

    public function beginCreditMandate()
    {
        return $this->hasMany(BeginCreditMandate::class);
    }

    // public function subDepart()
    // {
    //     return $this->belongsTo(SubDepart::class);
    // }

    public function programSub()
    {
        return $this->hasMany(ProgramSub::class, 'no_id', 'id');
    }

    // public function program(){
    //     return $this->hasMany(Program::class, 'program_id', 'id');
    // }
}
