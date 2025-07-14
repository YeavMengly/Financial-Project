<?php

namespace App\Models\Loans;

use App\Models\BudgetPlan\BudgetMandate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetMandateLoan extends Model
{
    use HasFactory;
    protected $fillable = [
        'agencyNumber',
        'subDepart',
        'year',
        'subAccountNumber',
        'program',
        'internal_increase',
        'unexpected_increase',
        'additional_increase',
        'total_increase',
        'decrease',
        'editorial',
        'txtDescription'
    ];

    public function budgetMandate()
    {
        return $this->hasMany(BudgetMandate::class, 'program', 'program');
    }
}
