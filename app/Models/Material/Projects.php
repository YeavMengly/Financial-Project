<?php

namespace App\Models\Material;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Jenssegers\Agent\Agent;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Models\Content\Program;
use App\Models\Content\ProgramSub;
class Projects extends Model
{
    use HasFactory;
    use LogsActivity;
    use SoftDeletes;

    protected $table = 'projects';

    protected $fillable = [
        'ministry_id',
        'sub_project',
        'program_id',
        'program_sub_id',
        'cluster_id',
        'account_sub_id',
        'stock_number',
        'stock_name',
        'company_name',
        'warehouse_voucher',
        'warehouse_owner',
        'user_entry',
        'user_receiver',
        'date',
        'title',
        'file',
        'note',
        'refer',
    ];

    protected $casts = [
        'date' => 'date',
    ];
    
    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function programSub()
    {
        return $this->belongsTo(ProgramSub::class, 'program_sub_id');
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(trans('menus.projects'))
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

        $activity->default_field = $this->stock_name;
        $activity->log_name = trans('menus.projects');

        $platform = $agent->platform();
        $browser = $agent->browser();

        $activity->ip_address = request()->ip();
        $activity->platform = $platform;
        $activity->device = $agent->device();
        $activity->browser_version = $agent->version($browser);
        $activity->browser = $browser;
    }
}
