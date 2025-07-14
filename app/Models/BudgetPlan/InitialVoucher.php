<?php

namespace App\Models\BudgetPlan;

use App\Models\BeginCredit\BeginCredit;
use App\Models\BeginCredit\InitialBudget;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InitialVoucher extends Model
{
    use HasFactory;

    protected $fillable = ['year', 'title', 'sub_title', 'description'];

    public function budgetVoucher()
    {
        return $this->hasMany(BudgetVoucher::class, 'year', 'year');
    }

    public function initialBudget()
    {
        return $this->belongsTo(InitialBudget::class, 'year', 'year');
    }

    //  BelongTo InitialVoucher
    public function beginCredit()
    {
        return $this->belongsTo(BeginCredit::class);
    }
}
