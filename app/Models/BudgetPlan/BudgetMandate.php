<?php

namespace App\Models\BudgetPlan;

use App\Models\Loans\BudgetMandateLoan;
use App\Models\TaskType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetMandate extends Model
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

    public function loanMandate()
    {
        return $this->belongsTo(BudgetMandateLoan::class, 'program', 'program');
    }

    public function initialMandate()
    {
        return $this->belongsTo(InitialMandate::class, 'year', 'year');
    }
}
