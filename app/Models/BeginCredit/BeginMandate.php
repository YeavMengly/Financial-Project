<?php

namespace App\Models\BeginCredit;

use App\Models\Content\AccountSub;
use App\Models\Content\Agency;
use App\Models\Content\Ministry;
use App\Models\Loans\BudgetMandateLoan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\LogOptions;
use Jenssegers\Agent\Agent;
use Spatie\Activitylog\Traits\LogsActivity;

class BeginMandate extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'ministry_id',
        'agency_id',
        'program_id',
        'program_sub_id',
        'chapter_id',
        'account_id',
        'account_sub_id',
        'cluster_id',
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
        'expense_type_id'
    ];
    /* -----------------------------------------------------------------
     |  Relationships
     | -----------------------------------------------------------------
     */

    /**
     * Get the accountSub this beginMandate belongs to.
     */
    public function accountSub()
    {
        return $this->belongsTo(AccountSub::class, 'account_sub_id', 'id');
    }

    /**
     * Get the ministry this beginMandate belongs to.
     */
    public function ministry()
    {
        return $this->belongsTo(Ministry::class, 'ministry_id', 'id');
    }

    /**
     * Get the agency this beginMandate belongs to.
     */
    public function agency()
    {
        return $this->belongsTo(Agency::class, 'agency_id', 'id');
    }

    /**
     * Get the loans this beginMandate belongs to.
     */
    public function loans()
    {
        return $this->hasOne(BudgetMandateLoan::class, 'account_sub_id', 'id');
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
            ->useLogName(trans('menus.beginning.credit'))
            ->logOnly([
                'ministry_id',
                'agency_id',
                'program_id',
                'program_sub_id',
                'account_sub_id',
                'no',
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
     * Customize the activity log fields.
     */
    public function tapActivity(Activity $activity)
    {
        $agent = new Agent();
        $browser = $agent->browser();
        $activity->default_field = "{$this->name}";
        $activity->log_name = trans('menus.beginning.credit');
        $activity->ip_address = request()->ip();
        $activity->platform = $agent->platform();
        $activity->device = $agent->device();
        $activity->browser = $browser;
        $activity->browser_version = $agent->version($browser);
    }
}
