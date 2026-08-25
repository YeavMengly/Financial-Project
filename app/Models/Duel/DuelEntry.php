<?php

namespace App\Models\Duel;

use App\Models\Content\Ministry;
use App\Models\Material\Projects;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DuelEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'ministry_id',
        'project_id',
        // 'stock_number',
        // 'stock_name',
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
}
