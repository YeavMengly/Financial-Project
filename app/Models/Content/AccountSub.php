<?php

namespace App\Models\Content;

use App\Models\BeginCredit\BeginMandate;
use App\Models\BudgetPlan\BudgetMandate;
use App\Models\BudgetPlan\BudgetVoucher;
use App\Models\Loans\BudgetMandateLoan;
use App\Models\Loans\BudgetVoucherLoan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Jenssegers\Agent\Agent;

class AccountSub extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;


    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'ministry_id',
        'account_id',
        'no',
        'name'
    ];

    /* -----------------------------------------------------------------
     |  Relationships
     | -----------------------------------------------------------------
     */

    /**
     * Get the account this accountSub belongs to.
     */

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }

    /**
     * Get the beginMandate this accountSub belongs to.
     */
    public function beginMandate()
    {
        return $this->hasMany(BeginMandate::class, 'account_sub_id', 'id');
    }

    /**
     * Get the budgetMandate this accountSub belongs to.
     */
    public function budgetMandate()
    {
        return $this->hasMany(BudgetMandate::class, 'account_sub_id', 'id');
    }

    /**
     * Get the mandateLoan this accountSub belongs to.
     */
    public function mandateLoan()
    {
        return $this->hasMany(BudgetMandateLoan::class, 'account_sub_id', 'id');
    }

    /* -----------------------------------------------------------------
     |  Activity Log Configuration
     | -----------------------------------------------------------------
     */

    /**
     * Configure the activity log options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(trans('menus.beginningcredit.subaccounts'))
            ->logOnly(['ministry_id', 'account_id', 'no', 'name'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "{$eventName}");
    }

    /**
     * Customize the activity log fields.
     */
    public function tapActivity(Activity $activity)
    {
        $agent = new Agent();
        $activity->default_field = "{$this->subAccountNumber}";
        $activity->log_name = trans('menus.beginningcredit.subaccounts');
        $activity->ip_address = request()->ip();
        $activity->platform = $agent->platform();
        $activity->device = $agent->device();
        $browser = $agent->browser();
        $activity->browser = $browser;
        $activity->browser_version = $agent->version($browser);
    }
}
