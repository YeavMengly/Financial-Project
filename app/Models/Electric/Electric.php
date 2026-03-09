<?php

namespace App\Models\Electric;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Jenssegers\Agent\Agent;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class Electric extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'ministry_id',
        'title_entity',
        'location_number_use', // must match DB column exactly
        'date',
        'use_start',
        'use_end',
        'kilo',
        'reactive_energy',
        'cost_total'
    ];

    public function electricEntity()
    {
        return $this->belongsTo(ElectricEntity::class, 'title_entity', 'id');
    }

     /* -----------------------------------------------------------------
     |  Activity Log Configuration
     | -----------------------------------------------------------------
     */

    /**
     * Configure activity log options
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'ministry_id',
                'title_entity',
                'location_number_use', // must match DB column exactly
                'date',
                'use_start',
                'use_end',
                'kilo',
                'reactive_energy',
                'cost_total'
            ])
            ->useLogName('menus.electric')
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Customize the activity log fields.
     */
    public function tapActivity(Activity $activity)
    {
        $agent = new Agent();
        $activity->default_field    = "{$this->name} ";
        $activity->log_name         = trans('menus.electric');
        $platform = $agent->platform();
        $browser = $agent->browser();
        $activity->ip_address = request()->ip();
        $activity->platform = $platform;
        $activity->device = $agent->device();
        $activity->browser_version = $agent->version($browser);
        $activity->browser = $browser;
    }
}
