<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum UserType: string implements HasColor, HasLabel
{
    case Admin = 'admin';
    case Agent = 'agent';
    case Retiree = 'retiree';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Admin => 'Administrateur',
            self::Agent => 'Agent',
            self::Retiree => 'Retraité'
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::Admin => 'primary',
            self::Agent => 'success',
            self::Retiree => 'warning'
        };
    }
}
