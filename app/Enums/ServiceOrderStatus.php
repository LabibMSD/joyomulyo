<?php

namespace App\Enums;

enum ServiceOrderStatus: string
{
    case Checking = 'CHECKING';
    case WaitingDecision = 'WAITING_DECISION';
    case Working = 'WORKING';
    case WaitingPart = 'WAITING_PART';
    case Completed = 'COMPLETED';
    case Declined = 'DECLINED';
}
