<?php

namespace App\Models;

use App\Models\BudgetPlan\BudgetVoucher;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'task'
    ];

    /* -----------------------------------------------------------------
     |  Relationships
     | -----------------------------------------------------------------
     */

    /**
     * Get the budgetVoucher under this taskType.
     */
    public function budgetVoucher()
    {
        return $this->hasMany(BudgetVoucher::class, 'task_type', 'task');
    }
}
