<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use Auth;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Override;

#[Fillable(['service_order_id', 'amount', 'payment_method', 'paid_at', 'notes'])]
class Payment extends Model
{
    #[Override]
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payment_method' => PaymentMethod::class,
            'paid_at' => 'datetime',
            'status' => PaymentStatus::class,
            'voided_at' => 'datetime',
        ];
    }

    public function serviceOrder(): BelongsTo
    {
        return $this->belongsTo(ServiceOrder::class, 'service_order_id')->withTrashed();
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by')->withTrashed();
    }

    public function voidedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'voided_by')->withTrashed();
    }

    #[Override]
    protected static function booted(): void
    {
        static::creating(function (self $payment) {
            if ($id = Auth::id()) {
                $payment->created_by = $id;
            }
        });

        static::updating(function (self $payment) {
            if ($id = Auth::id()) {
                $payment->updated_by = $id;
            }
        });
    }
}
