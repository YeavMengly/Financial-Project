<?php

namespace App\Models\BeginCredit;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InitialBudgetMandate extends Model
{
    use HasFactory;

    protected $fillable = ['year', 'title', 'sub_title', 'description'];

    public function beginCreditMandates()
    {
        return $this->hasMany(BeginCreditMandate::class, 'year', 'year');
    }
}
