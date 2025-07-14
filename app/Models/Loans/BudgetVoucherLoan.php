<?php

namespace App\Models\Loans;

use App\Models\BeginCredit\BeginCredit;
use App\Models\BudgetPlan\BudgetVoucher;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetVoucherLoan extends Model
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

    /**
     * One Loan has many related BudgetVoucher entries
     */
    public function budgetVoucher()
    {
        return $this->hasMany(BudgetVoucher::class, 'program', 'program');
    }

    /**
     * Each BudgetVoucherLoan is linked to a BeginCredit record
     */
    public function beginCredit()
    {
        return $this->belongsTo(BeginCredit::class, 'program', 'program');
    }
}
