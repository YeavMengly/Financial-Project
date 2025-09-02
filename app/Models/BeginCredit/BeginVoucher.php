<?php

namespace App\Models\BeginCredit;

use App\Models\Loans\BudgetVoucherLoan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Jenssegers\Agent\Agent;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class BeginVoucher extends Model
{
    use HasFactory, LogsActivity;


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
    protected $dates = ['deleted_at'];

    /**
     * Relationship to SubAccount (if applicable)
     */
    public function accountSub()
    {
        return $this->belongsTo(AccountSub::class, 'account_sub_id', 'id');
    }

    /**
     * Relationship to BudgetVoucherLoan
     */
    public function loans()
    {
        return $this->hasOne(BudgetVoucherLoan::class, 'program', 'program');
    }

    public function ministries()
    {
        return $this->belongsTo(Ministry::class, 'ministry_id', 'id');
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
            ->useLogName(trans('menus.beginning.credit'))
            ->logOnly([
                'agencyNumber',
                'subDepart',
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
        $activity->log_name = trans('menus.beginning.credit');
        $activity->ip_address = request()->ip();
        $activity->platform = $agent->platform();
        $activity->device = $agent->device();
        $activity->browser = $browser;
        $activity->browser_version = $agent->version($browser);
    }
}
