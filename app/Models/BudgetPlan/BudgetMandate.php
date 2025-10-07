<?php

namespace App\Models\BudgetPlan;

use App\Models\BeginCredit\AccountSub;
use App\Models\BeginCredit\Ministry;
use App\Models\Loans\BudgetMandateLoan;
use App\Models\TaskType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetMandate extends Model
{
    use HasFactory;

    protected $fillable = [
        'ministry_id',
        'agency_id',
        'account_sub_id',
        'no',
        'txtDescription',
        'budget',
        'task_type',
        'attachments',
        'date'
    ];

    protected $casts = [
        'attachments' => 'array',
        'date'        => 'date',
    ];

    public function taskType()
    {
        return $this->belongsTo(TaskType::class, 'task_type', 'task');
    }

    public function ministries()
    {
        return $this->belongsTo(Ministry::class, 'ministry_id', 'id');
    }
    public function accountSub()
    {
        return $this->belongsTo(AccountSub::class, 'account_sub_id', 'id');
    }
}
