<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ClaimStatus: string implements HasColor, HasLabel
{
    case Pending = 'pending';
    case Treated = 'treated';
    case Rejected = 'rejected';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Pending => 'En cours de traitement',
            self::Treated => 'Traité',
            self::Rejected => 'En instance'
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Treated => 'success',
            self::Rejected => 'danger'
        };
    }
}
