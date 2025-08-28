<?php

namespace App\Models\BeginCredit;

use App\Models\Chapter;
use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ministry extends Model
{
    use HasFactory;

    // In app/Models/InitialBudget.php
    protected $fillable = [
        'no',
        'year',
        'title',
        'refer',
        'name'
    ];

    public function beginCredit()
    {
        return $this->hasMany(BeginVoucher::class, 'year', 'year');
    }

    public function agency()
    {
        return $this->hasMany(Agency::class, 'year', 'year');
    }

    public function chapters()
    {
        return $this->hasMany(Chapter::class, 'ministry_id', 'id');
    }

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    public function program()
    {
        return $this->hasMany(Program::class, 'ministry_id', 'id');
    }

    public function codes()
    {
        return $this->hasMany(Code::class, 'ministry_id', 'id');
    }
}
