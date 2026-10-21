<?php

namespace App\Models\Duel;

use App\Models\Content\Ministry;
use App\Models\Content\ExecutiveUnit; // Adjust path as needed
use App\Models\Material\Projects;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DuelRelease extends Model
{
    use HasFactory;

    protected $fillable = [
        'ministry_id',
        'project_id',
        'duel_entries_id',
        'item_name',
        'receipt_number',
        'agency',
        'executive_unit_id',
        'user_request',
        'receiver',
        'unit',
        'title',
        'quantity_total',
        'quantity_request',
        'quantity_remain',
        'note',
        'refer',
        'date_release',
        'file',
    ];

    protected $casts = [
        'date_release' => 'date',
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

    public function duelEntry()
    {
        return $this->belongsTo(DuelEntry::class, 'duel_entries_id');
    }

    public function executiveUnit()
    {
        return $this->belongsTo(ExecutiveUnit::class, 'executive_unit_id');
    }
}
