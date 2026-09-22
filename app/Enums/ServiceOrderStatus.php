<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ServiceOrderStatus: string implements HasColor, HasLabel
{
    case Checking = 'CHECKING';
    case WaitingDecision = 'WAITING_DECISION';
    case Working = 'WORKING';
    case WaitingPart = 'WAITING_PART';
    case Completed = 'COMPLETED';
    case Declined = 'DECLINED';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Checking => 'Checking',
            self::WaitingDecision => 'Waiting Decision',
            self::Working => 'Working',
            self::WaitingPart => 'Waiting Part',
            self::Completed => 'Completed',
            self::Declined => 'Declined',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Checking => 'warning',
            self::WaitingDecision => 'gray',
            self::Working => 'info',
            self::WaitingPart => 'warning',
            self::Completed => 'success',
            self::Declined => 'danger',
        };
    }
}
