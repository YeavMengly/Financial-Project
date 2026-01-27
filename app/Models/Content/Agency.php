<?php

namespace App\Models\Content;

use App\Models\BeginCredit\BeginVoucher;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Jenssegers\Agent\Agent;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class Agency extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'ministry_id',
        'program_id',
        'no',
        'name',
        'nick_name'
    ];

    /* -----------------------------------------------------------------
     |  Relationships
     | -----------------------------------------------------------------
     */

    /**
     * Get the ministry this agency belongs to.
     */
    public function ministry()
    {
        return $this->belongsTo(Ministry::class, 'ministry_id', 'id');
    }

    /**
     * Get the beginVoucher this agency belongs to.
     */
    public function beginVoucher()
    {
        return $this->hasMany(BeginVoucher::class, 'agency_id', 'id');
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
                'program_id',
                'no',
                'name',
                'nick_name',
            ])
            ->useLogName('agency')
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
        $activity->log_name         = trans('agency');
        $platform = $agent->platform();
        $browser = $agent->browser();
        $activity->ip_address = request()->ip();
        $activity->platform = $platform;
        $activity->device = $agent->device();
        $activity->browser_version = $agent->version($browser);
        $activity->browser = $browser;
    }
}
