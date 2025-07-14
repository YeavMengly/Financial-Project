<?php

namespace App\Models\BeginCredit;

use App\Models\Chapter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Jenssegers\Agent\Agent;

class Account extends Model
{
    use HasFactory;
    protected $fillable = [
        'chapterNumber',
        'accountNumber',
        'txtAccount'
    ];

    public function chapter()
    {
        return $this->belongsTo(Chapter::class, 'chapterNumber', 'chapterNumber'); // Ensure 'code' is used for both keys
    }

    public function subAccount()
    {
        return $this->hasMany(SubAccount::class, 'accountNumber', 'accountNumber');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(trans('menus.beginningcredit.accounts')) // Update key based on your lang file
            ->logOnly(['chapterNumber', 'accountNumber', 'txtAccount'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "{$eventName}");
    }

    public function tapActivity(Activity $activity)
    {
        $agent = new Agent();
        $activity->default_field = "{$this->accountNumber}";
        $activity->log_name = trans('menus.beginningcredit.accounts');
        $activity->ip_address = request()->ip();
        $activity->platform = $agent->platform();
        $activity->device = $agent->device();
        $browser = $agent->browser();
        $activity->browser = $browser;
        $activity->browser_version = $agent->version($browser);
    }
}
