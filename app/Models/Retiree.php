<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Retiree extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function claims()
    {
        return $this->hasMany(Claim::class);
    }

    public function renewals()
    {
        return $this->hasMany(Renewal::class);
    }
}
