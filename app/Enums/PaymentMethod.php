<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Cash = 'CASH';
    case Transfer = 'TRANSFER';
    case Qris = 'QRIS';
}
