<?php

namespace App\Models\BudgetPlan;

use App\Models\BeginCredit\AccountSub;
use App\Models\BeginCredit\Ministry;
use App\Models\TaskType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetVoucher extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'ministry_id',
        'program_id',
        'program_sub_id',
        'agency_id',
        'account_sub_id',
        'cluster_id',
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

    /* -----------------------------------------------------------------
     |  Relationships
     | -----------------------------------------------------------------
     */

    /**
     * Get the taskType this budgetVoucher belongs to.
     */
    public function taskType()
    {
        return $this->belongsTo(TaskType::class, 'task_type', 'id');
    }

    /**
     * Get the ministry this budgetVoucher belongs to.
     */
    public function ministry()
    {
        return $this->belongsTo(Ministry::class, 'ministry_id', 'id');
    }

    /**
     * Get the accountSub this budgetVoucher belongs to.
     */
    public function accountSub()
    {
        return $this->belongsTo(AccountSub::class, 'account_sub_id', 'id');
    }
}
