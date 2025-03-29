<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum RenewalStatus: string implements HasColor, HasLabel
{
    case Pending = 'pending';
    case Done = 'done';
    case Rejected = 'rejected';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Pending => 'En attente',
            self::Done => 'Validée',
            self::Rejected => 'Rejetée'
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Done => 'success',
            self::Rejected => 'danger'
        };
    }
}
