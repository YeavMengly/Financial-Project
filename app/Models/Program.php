<?php

namespace App\Models;

use App\Models\BeginCredit\Ministry;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Jenssegers\Agent\Agent;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;

class Program extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;
    protected $fillable = [
        'ministry_id',
        'no',
        'title'
    ];
    public function ministries()
    {
        return $this->belongsTo(Ministry::class, 'ministry_id', 'id');
    }
    public function programSub()
    {
        return $this->hasMany(ProgramSub::class, 'program_id', 'id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(trans('menus.beginningcredit.program'))
            ->logOnly(['ministry_id', 'no', 'title'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "{$eventName}");
    }

    public function tapActivity(Activity $activity)
    {
        $agent = new Agent();
        $activity->default_field    = "{$this->name} ";
        $activity->log_name         = trans('menus.beginningcredit.program');
        $platform = $agent->platform();
        $browser = $agent->browser();
        $activity->ip_address = request()->ip();
        $activity->platform = $platform;
        $activity->device = $agent->device();
        $activity->browser_version = $agent->version($browser);
        $activity->browser = $browser;
    }
}
