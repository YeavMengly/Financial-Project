<?php

namespace App\Models\Mission;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MissionPaymentDraft extends Model
{
    use HasFactory;
    protected $table = 'mission_payment_drafts';

    protected $fillable = [
        'mission_ids',
        'total_amount',
        'created_by',
        'status',
        'paid_at',
    ];

    protected $casts = [
        'mission_ids' => 'array',
        'total_amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];
}
