<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Override;

#[Fillable(['service_order_id', 'name', 'amount'])]
class ServiceOrderItem extends Model
{
    #[Override]
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
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

    #[Override]
    protected static function booted(): void
    {
        static::creating(function (self $serviceOrderItem) {
            if ($id = Auth::id()) {
                $serviceOrderItem->created_by = $id;
            }
        });

        static::updating(function (self $serviceOrderItem) {
            if ($id = Auth::id()) {
                $serviceOrderItem->updated_by = $id;
            }
        });
    }
}
