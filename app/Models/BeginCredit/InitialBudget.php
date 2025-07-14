<?php

namespace App\Models\BeginCredit;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InitialBudget extends Model
{
    use HasFactory;

    // In app/Models/InitialBudget.php
    protected $fillable = ['year', 'title', 'sub_title', 'description'];

    public function beginCredit()
    {
        return $this->hasMany(BeginCredit::class, 'year', 'year');
    }

    public function agency()
    {
        return $this->hasMany(Agency::class, 'year', 'year');
    }
}
