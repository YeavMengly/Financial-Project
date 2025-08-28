<?php

namespace App\Models\BeginCredit;

use App\Models\ProgramSub;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Agency extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'ministry_id',
        'program_id',
        'no',
        'name',
        'nick_name'
    ];

    /**
     * Configure activity log options
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'ministry_id',
                'program_id',
                'no',
                'name',
                'nick_name',
            ])
            ->useLogName('agency') // log group name
            ->logOnlyDirty()       // only log changed attributes
            ->dontSubmitEmptyLogs(); // ignore empty logs
    }

    // 🔹 Relationships
    public function ministry()
    {
        return $this->belongsTo(Ministry::class, 'ministry_id', 'id');
    }

    public function beginVoucher()
    {
        return $this->hasMany(BeginVoucher::class);
    }

    public function beginCreditMandate()
    {
        return $this->hasMany(BeginCreditMandate::class);
    }

    public function programSub()
    {
        return $this->hasMany(ProgramSub::class, 'no_id', 'id');
    }
}
