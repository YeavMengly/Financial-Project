<?php

namespace App\Models\BudgetPlan;

use App\Models\Content\AccountSub;
use App\Models\Content\ExpenseType;
use App\Models\Content\Ministry;
use App\Models\Loans\BudgetMandateLoan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetMandate extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'ministry_id',
        'agency_id',
        'account_sub_id',
        'no',
        'budget',
        'expense_type_id',
        'legalNumber',
        'txtDescription',
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
     * Get the expenseType this budgetMandate belongs to.
     */
    public function expenseType()
    {
        return $this->belongsTo(ExpenseType::class, 'expense_type_id', 'id');
    }

    /**
     * Get the ministry this budgetMandate belongs to.
     */
    public function ministry()
    {
        return $this->belongsTo(Ministry::class, 'ministry_id', 'id');
    }

    /**
     * Get the accountSub this budgetMandate belongs to.
     */
    public function accountSub()
    {
        return $this->belongsTo(AccountSub::class, 'account_sub_id', 'id');
    }
}
