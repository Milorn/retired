<?php

namespace App\Models;

use App\Enums\RenewalStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Renewal extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'status' => RenewalStatus::class,
        'documents' => 'array',
        'documents_names' => 'array',
    ];

    public function retiree()
    {
        return $this->belongsTo(Retiree::class);
    }
}
