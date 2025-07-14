<?php

namespace App\Models\BudgetPlan;

use App\Models\BeginCredit\InitialBudget;
use App\Models\Loans\BudgetVoucherLoan;
use App\Models\TaskType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetVoucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'agencyNumber',
        'subDepart',
        'year',
        'subAccountNumber',
        'program',
        'txtDescription',
        'budget',
        'task_type',
        'attachments',
        'date'
    ];

    public function taskType()
    {
        return $this->belongsTo(TaskType::class, 'task_type', 'task');
    }

    public function loanVoucher()
    {
        return $this->belongsTo(BudgetVoucherLoan::class, 'program', 'program');
    }

    public function initialBudget()
    {
        return $this->belongsTo(InitialBudget::class, 'year', 'year');
    }
}
