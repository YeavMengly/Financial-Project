<?php

namespace App\Models\Mission;

use App\Models\Content\Employee;
use App\Models\Content\Levels;
use App\Models\Content\Positions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class MissionEmployee extends Model
{
    use HasFactory;

    protected $fillable = [
        'ministry_id',
        'mission_id',
        'employee_id',
        'position_id',
        'level_name',
        'travel_allowance',
        'pocket_money',
        'total_pocket_money',
        'meal_money',
        'total_meal_money',
        'accommodation_money',
        'total_accommodation_money',
        'total',
        'assign_budget',
    ];

    protected $casts = [
        'travel_allowance' => 'decimal:0',
        'pocket_money' => 'decimal:0',
        'total_pocket_money' => 'decimal:0',
        'meal_money' => 'decimal:0',
        'total_meal_money' => 'decimal:0',
        'accommodation_money' => 'decimal:0',
        'total_accommodation_money' => 'decimal:0',
        'total' => 'decimal:0',
        'assign_budget' => 'boolean',
    ];

    public function mission()
    {
        return $this->belongsTo(Mission::class, 'mission_id', 'id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function position()
    {
        return $this->belongsTo(Positions::class);
    }

    public function level()
    {
        return $this->belongsTo(Levels::class);
    }
}
