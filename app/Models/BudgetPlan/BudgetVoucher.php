<?php

namespace App\Models\BudgetPlan;

use App\Models\Content\AccountSub;
use App\Models\Content\Agency;
use App\Models\Content\ExpenseType;
use App\Models\Content\Ministry;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Jenssegers\Agent\Agent;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class BudgetVoucher extends Model
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
        'budget',
        'expense_type_id',
        'legal_number',
        'legal_name',
        'status',
        'is_archived',
        'description',
        'attachments',
        'transaction_date',
        'request_date'
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
     * Get the expense_type_id this budgetVoucher belongs to.
     */
    public function expenseType()
    {
        return $this->belongsTo(ExpenseType::class, 'expense_type_id', 'id');
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
            ->useLogName(trans('menus.budget.control.voucher'))
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
        $activity->log_name = trans('menus.budget.control.voucher');
        $platform = $agent->platform();
        $browser = $agent->browser();
        $activity->ip_address = request()->ip();
        $activity->platform = $platform;
        $activity->device = $agent->device();
        $activity->browser_version = $agent->version($browser);
        $activity->browser = $browser;
    }
}
