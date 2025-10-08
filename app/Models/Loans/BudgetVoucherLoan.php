<?php

namespace App\Models\Loans;

use App\Models\BeginCredit\AccountSub;
use App\Models\BeginCredit\Agency;
use App\Models\BeginCredit\BeginCredit;
use App\Models\BudgetPlan\BudgetVoucher;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PDO;

class BudgetVoucherLoan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    // protected $table = 'budget_voucher_loans';
    protected $fillable = [
        'ministry_id',
        'agency_id',
        'account_sub_id',
        'no',
        'internal_increase',
        'unexpected_increase',
        'additional_increase',
        'total_increase',
        'decrease',
        'editorial',
        'txtDescription'
    ];

    /* -----------------------------------------------------------------
     |  Relationships
     | -----------------------------------------------------------------
     */

    /**
     * Get the accountSub under this budgetVoucherLoan.
     */
    public function accountSub()
    {
        return $this->belongsTo(AccountSub::class, 'account_sub_id', 'id');
    }

    /**
     * Get the agency under this budgetVoucherLoan.
     */
    public function agency()
    {
        return $this->belongsTo(Agency::class, 'agency_id', 'id');
    }
}
