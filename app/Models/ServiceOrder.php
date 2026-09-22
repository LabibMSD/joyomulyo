<?php

namespace App\Models;

use App\Enums\ServiceOrderStatus;
use Auth;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Override;

#[Fillable(['vehicle_id', 'status', 'complaint', 'notes'])]
class ServiceOrder extends Model
{
    use SoftDeletes;

    #[Override]
    protected function casts(): array
    {
        return [
            'status' => ServiceOrderStatus::class,
            'invoiced_at' => 'datetime',
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id')->withTrashed();
    }

    public function invoicedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invoiced_by')->withTrashed();
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by')->withTrashed();
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by')->withTrashed();
    }

    public function items(): HasMany
    {
        return $this->hasMany(ServiceOrderItem::class, 'service_order_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'service_order_id');
    }

    #[Override]
    protected static function booted(): void
    {
        static::creating(function (self $serviceOrder) {
            if ($id = Auth::id()) {
                $serviceOrder->created_by = $id;
            }
        });

        static::updating(function (self $serviceOrder) {
            if ($id = Auth::id()) {
                $serviceOrder->updated_by = $id;
            }
        });

        static::deleting(function (self $serviceOrder) {
            if (! $serviceOrder->isForceDeleting() && ($id = Auth::id())) {
                $serviceOrder->deleted_by = $id;
                $serviceOrder->saveQuietly();
            }
        });

        static::restoring(function (self $serviceOrder) {
            $serviceOrder->deleted_by = null;
        });
    }
}
