<?php

namespace App\Models\Duel;

use App\Models\Content\Ministry;
use App\Models\Material\Projects;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use Jenssegers\Agent\Agent;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class DuelEntry extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'ministry_id',
        'project_id',
        'item_name',
        'unit',
        'quantity',
        'price',
        'total_price',
        'date_entry',
        'pro_year',
        'source'
    ];

    protected $casts = [
        'date_entry'  => 'date',
        'quantity'    => 'integer',
        'price'       => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    // ==========================================
    // Relationships
    // ==========================================

    public function ministry()
    {
        return $this->belongsTo(Ministry::class);
    }

    public function project()
    {
        return $this->belongsTo(Projects::class, 'project_id');
    }

    public function duelReleases()
    {
        return $this->hasMany(DuelRelease::class, 'duel_entries_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(trans('menus.duel.entry'))
            ->logOnly([
                'status',
                'is_archived',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "{$eventName}");
    }
    public function tapActivity(Activity $activity)
    {
        $agent = new Agent();
        $activity->default_field = "{$this->title} ";
        $activity->log_name = trans('menus.duel.entry');
        $platform = $agent->platform();
        $browser = $agent->browser();
        $activity->ip_address = request()->ip();
        $activity->platform = $platform;
        $activity->device = $agent->device();
        $activity->browser_version = $agent->version($browser);
        $activity->browser = $browser;
    }
}
