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
        'agency_id',
        'program_sub_id',
        'ministry_id',
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
}
