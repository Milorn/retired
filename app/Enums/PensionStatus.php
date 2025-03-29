<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PensionStatus: string implements HasColor, HasLabel
{
    case Suspended = 'suspendu';
    case Canceled = 'canceled';
    case Payment = 'payment';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Suspended => 'Suspendu',
            self::Canceled => 'Annulé',
            self::Payment => 'En paiment'
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::Suspended => 'warning',
            self::Canceled => 'danger',
            self::Payment => 'success'
        };
    }
}
