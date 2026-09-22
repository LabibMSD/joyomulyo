<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Active = 'ACTIVE';
    case Void = 'VOID';
}
