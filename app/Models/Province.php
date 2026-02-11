<?php

namespace App\Models;

use App\Models\Electric\ElectricEntity;
use App\Models\Water\WaterEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    public function waterEntity()
    {
        return $this->hasMany(WaterEntity::class, 'province_id', 'id');
    }

    public function electricEntity()
    {
        return $this->hasMany(ElectricEntity::class, 'province_id', 'id');
    }
}
