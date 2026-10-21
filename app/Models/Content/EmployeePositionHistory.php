<?php

namespace App\Models\Content;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeePositionHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'position_id',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    public function position()
    {
        return $this->belongsTo(Positions::class, 'position_id', 'id');
    }
}
