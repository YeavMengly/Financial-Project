<?php

namespace App\Models\Loans;

use App\Models\BudgetPlan\BudgetVoucher;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanBudget extends Model
{
    use HasFactory;

    protected $fillable = [
        'subAccountNumber',
        'program',
        'internal_increase',
        'unexpected_increase',
        'additional_increase',
        'total_increase',
        'decrease',
        'editorial',
    ];

    public function budgetVoucher()
    {
        return $this->hasMany(BudgetVoucher::class, 'program');
    }
}
