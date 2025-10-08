<?php

namespace App\Models;

use App\Models\BeginCredit\Agency;
use App\Models\BeginCredit\Ministry;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Jenssegers\Agent\Agent;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Models\Activity;

class ProgramSub extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [

        'ministry_id',
        'program_id',
        'no',
        'decription'

    ];
    /**
     * Get the ministry this programSub belongs to.
     */
    public function ministry()
    {
        return $this->belongsTo(Ministry::class, 'ministry_id', 'id');
    }

    /**
     * Get the agency this programSub belongs to.
     */
    public function agency()
    {
        return $this->belongsTo(Agency::class, 'program_id', 'id');
    }

    /**
     * Get the program this programSub belongs to.
     */
    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id', 'id');
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
            ->useLogName(trans('menus.beginningcredit.programSub'))
            ->logOnly(['ministry_id', 'program_id', 'no', 'decription'])
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
        $activity->default_field    = "{$this->name} ";
        $activity->log_name         = trans('menus.beginningcredit.programsub');
        $platform = $agent->platform();
        $browser = $agent->browser();
        $activity->ip_address = request()->ip();
        $activity->platform = $platform;
        $activity->device = $agent->device();
        $activity->browser_version = $agent->version($browser);
        $activity->browser = $browser;
    }
}
