<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Jenssegers\Agent\Agent;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class Document extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = ['user_id', 'cate_id', 'sub_id', 'year', 'title', 'description', 'fileName'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(trans('menus.document'))
            ->logOnly(['user_id', 'cate_id', 'sub_id', 'year', 'title', 'description', 'fileName'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $eventName) => "{$eventName}");
    }

    public function tapActivity(Activity $activity)
    {
        $agent = new Agent();
        $activity->default_field = "{$this->title} ";
        $activity->log_name = trans('menus.document');
        $platform = $agent->platform();
        $browser = $agent->browser();
        $activity->ip_address = request()->ip();
        $activity->platform = $platform;
        $activity->device = $agent->device();
        $activity->browser_version = $agent->version($browser);
        $activity->browser = $browser;
    }
}
