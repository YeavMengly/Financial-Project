<?php

namespace App\Models;

use App\Models\BeginCredit\BeginMandate;
use App\Models\BeginCredit\BeginVoucher;
use App\Models\Content\ExpenseType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Jenssegers\Agent\Agent;

class BudgetAllocation extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'ministry_id',
        'budget_begin_voucher_id',
        'budget_expense_type_id',
        'amount',
        'rounds'
    ];
    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function beginVoucher()
    {
        return $this->belongsTo(
            BeginVoucher::class,
            'budget_begin_voucher_id',
            'id'
        );
    }

    public function expenseType()
    {
        return $this->belongsTo(
            ExpenseType::class,
            'budget_expense_type_id',
            'id'
        );
    }

    /**
     * Spatie Log Options
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(trans('menus.budget.allocation'))
            ->logOnly([
                'ministry_id',
                'budget_begin_voucher_id',
                'budget_expense_type_id',
                'amount',
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
        $activity->default_field = "{$this->name}";
        $activity->log_name = trans('menus.budget.allocation');
        $activity->platform = $agent->platform();
        $browser = $agent->browser();
        $activity->ip_address = request()->ip();
        $activity->device = $agent->device();
        $activity->browser_version = $agent->version($browser);
        $activity->browser = $browser;
    }
}
