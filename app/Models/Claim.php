<?php

namespace App\Models;

use App\Enums\ClaimStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Claim extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'status' => ClaimStatus::class,
        'date' => 'date',
    ];

    public function retiree()
    {
        return $this->belongsTo(Retiree::class);
    }
}
