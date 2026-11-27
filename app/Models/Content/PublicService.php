<?php

namespace App\Models\Content;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Jenssegers\Agent\Agent;
use Spatie\Activitylog\Traits\LogsActivity;

class PublicService extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $table = 'public_services';

    protected $fillable = [
        'service_code',
        'service_prefix_name',
        'service_name',
        'categories_id',
        'service_price',
        'max_service_time',
        'expired',
        'percentage_national',
        'percentage_mlvt',
        'percentage_interior',
        'percentage_mef'
    ];

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
            ->useLogName(trans('content.public.service'))
            ->logOnly(['service_code', 'service_name'])
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
        $activity->default_field    = "{$this->service_name} ";
        $activity->log_name         = trans('content.public.service');
        $platform = $agent->platform();
        $browser = $agent->browser();
        $activity->ip_address = request()->ip();
        $activity->platform = $platform;
        $activity->device = $agent->device();
        $activity->browser_version = $agent->version($browser);
        $activity->browser = $browser;
    }
}
