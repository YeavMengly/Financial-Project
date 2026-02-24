<?php

namespace App\Models\Loans;

use App\Models\Content\AccountSub;
use App\Models\Content\Agency;
use App\Models\BeginCredit\BeginCredit;
use App\Models\BeginCredit\BeginVoucher;
use App\Models\BudgetPlan\BudgetVoucher;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function beginVoucher()
    {
        return $this->belongsTo(BeginVoucher::class, 'account_sub_id', 'id');
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class, 'agency_id', 'id');
    }
}
