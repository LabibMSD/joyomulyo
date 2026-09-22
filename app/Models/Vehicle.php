<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Override;

#[Fillable(['license_plate', 'brand', 'model'])]
class Vehicle extends Model
{
    use SoftDeletes;

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

    public function serviceOrders(): HasMany
    {
        return $this->hasMany(ServiceOrder::class, 'vehicle_id');
    }

    protected function licensePlate(): Attribute
    {
        return Attribute::make(
            set: fn (?string $state): ?string => $state ? strtoupper($state) : null
        );
    }

    protected function brand(): Attribute
    {
        return Attribute::make(
            set: fn (?string $state): ?string => $state ? strtoupper($state) : null
        );
    }

    protected function model(): Attribute
    {
        return Attribute::make(
            set: fn (?string $state): ?string => $state ? strtoupper($state) : null
        );
    }

    #[Override]
    protected static function booted(): void
    {
        static::creating(function (self $vehicle) {
            if ($id = Auth::id()) {
                $vehicle->created_by = $id;
            }
        });

        static::updating(function (self $vehicle) {
            if ($id = Auth::id()) {
                $vehicle->updated_by = $id;
            }
        });

        static::deleting(function (self $vehicle) {
            if (! $vehicle->isForceDeleting() && ($id = Auth::id())) {
                $vehicle->deleted_by = $id;
                $vehicle->saveQuietly();
            }
        });

        static::restoring(function (self $vehicle) {
            $vehicle->deleted_by = null;
        });
    }
}
