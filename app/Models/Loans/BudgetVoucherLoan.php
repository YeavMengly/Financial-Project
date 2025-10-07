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

    protected $table = 'budget_voucher_loans';
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


    public function accountSub()
    {
        return $this->belongsTo(AccountSub::class, 'account_sub_id', 'id');
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class, 'agency_id', 'id');
    }
}
