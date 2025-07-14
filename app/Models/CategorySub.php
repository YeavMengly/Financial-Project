<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Jenssegers\Agent\Agent;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class CategorySub extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = ['cate_id', 'name', 'order'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(trans('menus.setting.category.sub'))
            ->logOnly(['cate_id', 'name', 'order'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "{$eventName}");
    }

    public function tapActivity(Activity $activity)
    {
        $agent = new Agent();
        $activity->default_field    = "{$this->name} ";
        $activity->log_name         = trans('menus.setting.category.sub');
        $platform = $agent->platform();
        $browser = $agent->browser();
        $activity->ip_address = request()->ip();
        $activity->platform = $platform;
        $activity->device = $agent->device();
        $activity->browser_version = $agent->version($browser);
        $activity->browser = $browser;
    }
}
