<?php

namespace App\Models\Material;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Jenssegers\Agent\Agent;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class MaterialRelease extends Model
{
    use HasFactory;
    use LogsActivity;
    // use SoftDeletes;
    protected $fillable = [
        'ministry_id',
        'project_id',
        'project_sub_id',
        'agency_id',
        'material_entry_id',
        'p_name',
        'p_year',
        'title',
        'unit',
        'quantity_total',
        'quantity_request',
        'price',
        'total_price',
        'source',
        'refer',
        'date_release',
        'file',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(trans('menus.material.release'))
            ->logOnly([
                'user_receiver',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(
                fn(string $eventName) => $eventName
            );
    }

    public function tapActivity(Activity $activity): void
    {
        $agent = new Agent();

        $user = auth()->user();

        // Default field
        $activity->default_field = $this->stock_name;

        // Log name
        $activity->log_name = trans('menus.material.release');

        // Authenticated user information
        $activity->causer_id = $user?->id;
        $activity->causer_type = $user ? get_class($user) : null;

        // Store the user's name as a custom activity property
        $activity->properties = $activity->properties->put(
            'fullname',
            $user?->fullname
        );

        // Request information
        $activity->ip_address = request()->ip();
        $activity->platform = $agent->platform();
        $activity->device = $agent->device();

        $browser = $agent->browser();

        $activity->browser = $browser;
        $activity->browser_version = $agent->version($browser);
    }
}
