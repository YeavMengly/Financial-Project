<?php

namespace App\Models;

use App\Models\BeginCredit\Account;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Jenssegers\Agent\Agent;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;


class Chapter extends Model
{
    use HasFactory;
    protected $fillable = [
        'chapterNumber',
        'txtChapter'
    ];

    public function account()
    {
        return $this->hasMany(Account::class, 'chapterNumber', 'chapterNumber'); // Ensure 'code' is used for both keys
    }

   public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(trans('menus.beginningcredit.chapters')) // Update as needed
            ->logOnly(['chapterNumber', 'txtChapter'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "{$eventName}");
    }

    public function tapActivity(Activity $activity)
    {
        $agent = new Agent();
        $activity->default_field = "{$this->chapterNumber}";
        $activity->log_name = trans('menus.beginningcredit.chapters');
        $activity->ip_address = request()->ip();
        $activity->platform = $agent->platform();
        $activity->device = $agent->device();
        $browser = $agent->browser();
        $activity->browser = $browser;
        $activity->browser_version = $agent->version($browser);
    }
}
