<?php

namespace App\Models\Revenue;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentThroughPartnerBanks extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'name_kh',
        'name_en',
        'description',
    ];
}
