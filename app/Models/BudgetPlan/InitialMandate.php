<?php

namespace App\Models\BudgetPlan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InitialMandate extends Model
{
    use HasFactory;

    protected $fillable = ['year', 'title', 'sub_title', 'description'];

    public function budgetMandate()
    {
        return $this->hasMany(BudgetMandate::class, 'year', 'year');
    }
}
