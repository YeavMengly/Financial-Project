<?php

namespace App\Models\Content;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\LogOptions;
use Jenssegers\Agent\Agent;

class Employee extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $table = 'employees';

    protected $fillable = [
        'id_number',
        'account_number',
        'name_kh',
        'name_latin',
        'position_id'
    ];
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(trans('menus.content.employee'))
            ->logOnly([
                'id_number',
                'account_number',
                'name_kh',
                'name_latin',
                'position_id'
            ])
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
        $activity->default_field = "{$this->name}";
        $activity->log_name = trans('menus.content.employee');
        $activity->ip_address = request()->ip();
        $activity->platform = $agent->platform();
        $activity->device = $agent->device();
        $browser = $agent->browser();
        $activity->browser = $browser;
        $activity->browser_version = $agent->version($browser);
    }
}
