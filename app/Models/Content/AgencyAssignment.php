<?php

namespace App\Models\Content;

use Illuminate\Database\Eloquent\Model;

class AgencyAssignment extends Model
{
    protected $fillable = [
        'agency_id',
        'program_id',
        'program_sub_id',
        'cluster_id',
    ];

    public function agency()
    {
        return $this->belongsTo(Agency::class, 'agency_id');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function programSub()
    {
        return $this->belongsTo(ProgramSub::class, 'program_sub_id');
    }

    public function cluster()
    {
        return $this->belongsTo(Cluster::class, 'cluster_id');
    }
}