<?php

namespace App\Models\BeginCredit;

use App\Models\Chapter;
use App\Models\Program;
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
    protected $fillable = [
        'no',
        'year',
        'title',
        'refer',
        'name'
    ];

    public function beginVoucher()
    {
        return $this->hasMany(BeginVoucher::class, 'ministry_id', 'id');
    }

    public function agency()
    {
        return $this->hasMany(Agency::class, 'ministry_id', 'id');
    }

    public function chapters()
    {
        return $this->hasMany(Chapter::class, 'ministry_id', 'id');
    }

    public function accounts()
    {
        return $this->hasMany(Account::class, 'ministry_id', 'id');
    }

    public function program()
    {
        return $this->hasMany(Program::class, 'ministry_id', 'id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(trans('menus.beginningcredit.ministries'))
            ->logOnly(['no', 'year', 'title', 'refer', 'name'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "{$eventName}");
    }

    public function tapActivity(Activity $activity)
    {
        $agent = new Agent();
        $activity->default_field    = "{$this->name} ";
        $activity->log_name         = trans('menus.beginningcredit.ministries');
        $platform = $agent->platform();
        $browser = $agent->browser();
        $activity->ip_address = request()->ip();
        $activity->platform = $platform;
        $activity->device = $agent->device();
        $activity->browser_version = $agent->version($browser);
        $activity->browser = $browser;
    }
}
