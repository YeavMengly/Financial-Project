<?php

namespace App\Models\BeginCredit;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agency extends Model
{
    use HasFactory;
    protected $fillable = [
        'agencyNumber',
        'agencyTitle',
    ];

    public function initialBudget()
    {
        return $this->belongsTo(InitialBudget::class, 'year', 'year');
    }

    public function beginCredit()
    {
        return $this->hasMany(BeginCredit::class);
    }

    public function beginCreditMandate()
    {
        return $this->hasMany(BeginCreditMandate::class);
    }
}
