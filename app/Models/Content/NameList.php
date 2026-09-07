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

class NameList extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'id_number',
        'account_number',
        'name_kh',
        'name_latin'
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


    /**
     * Get the account under this name.
     */
    public function account()
    {
        return $this->hasMany(Account::class, 'account_number', 'id');
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
            ->useLogName(trans('menus.conten.name.list'))
            ->logOnly(['id_number', 'account_number', 'name_kh','name_latin'])
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
        $activity->log_name         = trans('menus.content.name.list');
        $platform = $agent->platform();
        $browser = $agent->browser();
        $activity->ip_address = request()->ip();
        $activity->platform = $platform;
        $activity->device = $agent->device();
        $activity->browser_version = $agent->version($browser);
        $activity->browser = $browser;
    }
}
