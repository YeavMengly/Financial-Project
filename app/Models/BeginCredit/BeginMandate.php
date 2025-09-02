<?php

namespace App\Models\BeginCredit;

use App\Models\Loans\BudgetMandateLoan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\LogOptions;
use Jenssegers\Agent\Agent;
use Spatie\Activitylog\Traits\LogsActivity;

class BeginMandate extends Model
{
    use HasFactory;

    protected $fillable = [
        'ministry_id',
        'agency_id',
        'program_sub_id',
        'account_sub_id',
        'no',
        'txtDescription',
        'fin_law',
        'current_loan',
        'total_increase',
        'new_credit_status',
        'apply',
        'deadline_balance',
        'early_balance',
        'credit',
        'law_average',
        'law_correction',
    ];

    /**
     * Relationship to SubAccount
     */
    public function accountSub()
    {
        return $this->belongsTo(AccountSub::class, 'account_sub_id', 'id');
    }

    public function loans()
    {
        return $this->hasOne(BudgetMandateLoan::class, 'program', 'program');
    }

    public function initialBudgetMandate()
    {
        return $this->belongsTo(InitialBudgetMandate::class, 'year', 'year');
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class, 'agency_id', 'id');
    }

    /**
     * Spatie Log Options
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(trans('menus.initial.mandate'))
            ->logOnly([
                'subAccountNumber',
                'program',
                'txtDescription',
                'fin_law',
                'current_loan',
                'year',
                'new_credit_status',
                'apply',
                'deadline_balance',
                'credit',
                'law_average',
                'law_correction',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "{$eventName}");
    }

    /**
     * Additional metadata for activity logs
     */
    public function tapActivity(Activity $activity)
    {
        $agent = new Agent();
        $browser = $agent->browser();

        $activity->default_field = "{$this->subAccountNumber}";
        $activity->log_name = trans('menus.initial.mandate');
        $activity->ip_address = request()->ip();
        $activity->platform = $agent->platform();
        $activity->device = $agent->device();
        $activity->browser = $browser;
        $activity->browser_version = $agent->version($browser);
    }
}
