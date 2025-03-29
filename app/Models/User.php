<?php

namespace App\Models;

use App\Enums\UserType;
use Filament\Models\Contracts\HasName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements HasName
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getFilamentName(): string
    {
        return ucfirst($this->identifier);
    }

    protected function casts(): array
    {
        return [
            'type' => UserType::class,
            'password' => 'hashed',
        ];
    }

    public function retiree()
    {
        return $this->hasOne(Retiree::class);
    }
}
