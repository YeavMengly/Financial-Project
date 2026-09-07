<?php

namespace App\Models\Content;

use App\Models\Content\Ministry;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Jenssegers\Agent\Agent;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;

class Positions extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'level_id',
        'name_position',
    ];

    /* -----------------------------------------------------------------
     |  Relationships
     | -----------------------------------------------------------------
     */

    /**
     * Get the ministry this name belongs to.
     */
    public function ministry()
    {
        return $this->belongsTo(Ministry::class, 'ministry_id', 'id');
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
            ->useLogName(trans('menus.conten.position'))
            ->logOnly(['level_id', 'name_position'])
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
        $activity->default_field    = "{$this->name_position} ";
        $activity->log_name         = trans('menus.content.position');
        $platform = $agent->platform();
        $browser = $agent->browser();
        $activity->ip_address = request()->ip();
        $activity->platform = $platform;
        $activity->device = $agent->device();
        $activity->browser_version = $agent->version($browser);
        $activity->browser = $browser;
    }
}
