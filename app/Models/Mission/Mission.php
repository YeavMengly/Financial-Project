<?php

namespace App\Models\Mission;


use App\Models\Content\Levels;
use App\Models\Content\Ministry;
use App\Models\Content\NameList;
use App\Models\Content\Positions;
use App\Models\Province;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Jenssegers\Agent\Agent;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class Mission extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'ministry_id',
        'name_list_id',
        'position_id',
        'level_id',
        'legal_number',
        'legal_date',
        'description',
        'province_id',
        'start_date',
        'end_date',
        'days_count',
        'nights_count',
        'travel_allowance',
        'pocket_money',
        'total_pocket_money',
        'meal_money',
        'total_meal_money',
        'accommodation_money',
        'total_accommodation_money',
        'mission_type',
        'is_archived',
        'total',
    ];

    protected $casts = [
        'legal_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_archived' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Activity Log
    |--------------------------------------------------------------------------
    */

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(trans('menus.content.missions'))
            ->logOnly([
                'ministry_id',
                'name_list_id',
                'position_id',
                'level_id',
                'legal_number',
                'legal_date',
                'description',
                'province_id',
                'start_date',
                'end_date',
                'days_count',
                'nights_count',
                'travel_allowance',
                'pocket_money',
                'total_pocket_money',
                'meal_money',
                'total_meal_money',
                'accommodation_money',
                'total_accommodation_money',
                'mission_type',
                'is_archived',
                'total',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(
                fn (string $eventName) => $eventName
            );
    }

    public function tapActivity(Activity $activity): void
    {
        $agent = new Agent();

        $activity->default_field = "{$this->name}";

        $activity->log_name = trans('menus.content.missions');

        $browser = $agent->browser();

        $activity->ip_address = request()->ip();
        $activity->platform = $agent->platform();
        $activity->device = $agent->device();
        $activity->browser = $browser;
        $activity->browser_version = $agent->version($browser);
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function ministry()
    {
        return $this->belongsTo(Ministry::class);
    }

    public function nameList()
    {
        return $this->belongsTo(NameList::class);
    }

    public function position()
    {
        return $this->belongsTo(Positions::class);
    }

    public function level()
    {
        return $this->belongsTo(Levels::class);
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }
}