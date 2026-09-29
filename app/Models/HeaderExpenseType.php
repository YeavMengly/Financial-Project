<?php

namespace App\Models;

use App\Models\BudgetAllocation;
use App\Models\BudgetPlan\BudgetMandate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Jenssegers\Agent\Agent;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class HeaderExpenseType extends Model
{
    use HasFactory,LogsActivity;
    protected $table = 'header_expenses_type';

    protected $fillable = [
        'name_kh',
        'status',
    ];
    protected $casts = [
        'status' => 'boolean',
    ];
    /**
     * Get the beginVoucher under this ministry.
     */
    // public function budgetMandate()
    // {
    //     return $this->hasMany(BudgetMandate::class, 'expense_type_id', 'id');
    // }

    /**
     * Get the budgetAllocation under this ministry.
     */
    public function budgetAllocation()
    {
        return $this->hasMany(BudgetAllocation::class, 'budget_header_expense_type_id', 'id');
    }


    /**
     * ✅ Spatie Activity Log Options
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('header_expenses_type')
            ->logOnly(['name_kh', 'status'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * ✅ Custom description: created/updated/deleted/restored
     */
    public function getDescriptionForEvent(string $eventName): string
    {
        return "HeaderExpenseType has been {$eventName}";
    }

    /**
     * ✅ Add extra info into activity (user agent, ip, etc.)
     */
    public function tapActivity($activity, string $eventName): void
    {
        $agent = new Agent();

        $activity->properties = $activity->properties->merge([
            'model' => [
                'id' => $this->id,
                'name_kh' => $this->name_kh,
                'status' => $this->status,
            ],
            'request' => [
                'ip' => request()->ip(),
                'url' => request()->fullUrl(),
                'method' => request()->method(),
            ],
            'device' => [
                'browser' => $agent->browser(),
                'browser_version' => $agent->version($agent->browser()),
                'platform' => $agent->platform(),
                'platform_version' => $agent->version($agent->platform()),
                'device' => $agent->device(),
                'is_desktop' => $agent->isDesktop(),
                'is_mobile' => $agent->isMobile(),
            ],
        ]);
    }
}
