<?php

namespace App\Models\BudgetPlan;

use App\Models\Content\AccountSub;
use App\Models\Content\Agency;
use App\Models\Content\ExpenseType;
use App\Models\Content\Ministry;
use App\Models\Loans\BudgetMandateLoan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Jenssegers\Agent\Agent;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class BudgetMandate extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'ministry_id',
        'agency_id',
        'program_id',
        'program_sub_id',
        'cluster_id',
        'account_sub_id',
        'no',
        'fin_law',
        'budget',
        'expense_type_id',
        'legal_id',
        'payment_voucher_number',
        'legal_number',
        'legal_name',
        'status',
        'is_archived',
        'description',
        'attachments',
        'transaction_date',
        'request_date',
        'legal_date',
    ];

    protected $casts = [
        'attachments' => 'array',
        'transaction_date' => 'date',
        'request_date' => 'date',
        'legal_date' => 'date',
        'expense_type_id' => 'array',
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

    /**
     * Get the agency this budgetMandate belongs to.
     */
    public function agency()
    {
        return $this->belongsTo(Agency::class, 'agency_id', 'id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(trans('menus.budget.control.mandate'))
            ->logOnly([
                'legal_number',
                'description',
                'status',
                'is_archived',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "{$eventName}");
    }
    public function tapActivity(Activity $activity)
    {
        $agent = new Agent();
        $activity->default_field = "{$this->title} ";
        $activity->log_name = trans('menus.budget.control.mandate');
        $platform = $agent->platform();
        $browser = $agent->browser();
        $activity->ip_address = request()->ip();
        $activity->platform = $platform;
        $activity->device = $agent->device();
        $activity->browser_version = $agent->version($browser);
        $activity->browser = $browser;
    }
}
