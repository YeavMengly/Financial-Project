<?php

namespace App\Models\Content;

use App\Models\Content\Agency;
use App\Models\BeginCredit\BeginVoucher;
use App\Models\Content\Chapter;
use App\Models\Content\Program;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Jenssegers\Agent\Agent;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Models\Activity;

class Ministry extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'no',
        'year',
        'title',
        'refer',
        'name',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /* -----------------------------------------------------------------
     |  Relationships
     | -----------------------------------------------------------------
     */

    /**
     * Get the beginVoucher under this ministry.
     */
    public function beginVoucher()
    {
        return $this->hasMany(BeginVoucher::class, 'ministry_id', 'id');
    }

    /**
     * Get the agency under this ministry.
     */
    public function agency()
    {
        return $this->hasMany(Agency::class, 'ministry_id', 'id');
    }

    /**
     * Get the chapters under this ministry.
     */
    public function chapters()
    {
        return $this->hasMany(Chapter::class, 'ministry_id', 'id');
    }

    /**
     * Get the accounts under this ministry.
     */
    public function accounts()
    {
        return $this->hasMany(Account::class, 'ministry_id', 'id');
    }

    /**
     * Get the program under this ministry.
     */
    public function program()
    {
        return $this->hasMany(Program::class, 'ministry_id', 'id');
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
            ->useLogName(trans('menus.content.ministries'))
            ->logOnly(['no', 'year', 'title', 'refer', 'name'])
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
        $activity->log_name         = trans('menus.content.ministries');
        $platform = $agent->platform();
        $browser = $agent->browser();
        $activity->ip_address = request()->ip();
        $activity->platform = $platform;
        $activity->device = $agent->device();
        $activity->browser_version = $agent->version($browser);
        $activity->browser = $browser;
    }
}
