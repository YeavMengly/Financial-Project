<?php

namespace App\Models\BeginCredit;

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

    protected $fillable = [
        'ministry_id',
        'account_id',
        'no',
        'name'
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }

    public function beginMandate()
    {
        return $this->hasMany(BeginMandate::class, 'account_sub_id', 'id');
    }

    public function budgetMandate()
    {
        return $this->hasMany(BudgetMandate::class, 'account_sub_id', 'id');
    }

    public function mandateLoan()
    {
        return $this->hasMany(BudgetMandateLoan::class, 'account_sub_id', 'id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(trans('menus.beginningcredit.subaccounts'))
            ->logOnly(['accountNumber', 'subAccountNumber', 'txtSubAccount'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "{$eventName}");
    }

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
