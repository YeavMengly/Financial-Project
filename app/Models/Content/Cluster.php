<?php

namespace App\Models\Content;

use App\Models\Content\Agency;
use App\Models\Content\Ministry;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Jenssegers\Agent\Agent;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Models\Activity;

class Cluster extends Model
{

    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'ministry_id',
        'program_id',
        'program_sub_id',
        'no',
        'decription'
    ];
    public function ministry()
    {
        return $this->belongsTo(Ministry::class, 'ministry_id', 'id');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id', 'id');
    }

    public function programSub()
    {
        return $this->belongsTo(ProgramSub::class, 'program_sub_id', 'id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->fillable)
            ->logOnlyDirty()
            ->useLogName('cluster');
    }
}
