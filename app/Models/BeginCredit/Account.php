<?php

namespace App\Models\BeginCredit;

use App\Models\Chapter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Jenssegers\Agent\Agent;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class Account extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'ministry_id',
        'chapter_id',
        'no',
        'name',
    ];

    /* -----------------------------------------------------------------
     |  Relationships
     | -----------------------------------------------------------------
     */

    /**
     * Get the ministry this account belongs to.
     */
    public function ministry()
    {
        return $this->belongsTo(Ministry::class, 'ministry_id', 'id');
    }

    /**
     * Get the chapter this account belongs to.
     */
    public function chapter()
    {
        return $this->belongsTo(Chapter::class, 'chapter_id', 'id');
    }

    /**
     * Get the sub-accounts under this account.
     */
    public function subAccounts()
    {
        return $this->hasMany(AccountSub::class, 'account_id', 'id');
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
            ->useLogName(trans('menus.beginningcredit.accounts'))
            ->logOnly(['chapter_id', 'no', 'name'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $eventName) => __("Event: :event", ['event' => $eventName]));
    }

    /**
     * Customize the activity log fields.
     */
    public function tapActivity(Activity $activity): void
    {
        $agent = new Agent();

        $activity->default_field   = "{$this->no}";
        $activity->log_name        = trans('menus.beginningcredit.accounts');
        $activity->ip_address      = request()->ip();
        $activity->platform        = $agent->platform();
        $activity->device          = $agent->device();
        $browser                   = $agent->browser();
        $activity->browser         = $browser;
        $activity->browser_version = $agent->version($browser);
    }
}
