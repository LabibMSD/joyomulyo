<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Auth;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Override;

#[Fillable(['name', 'username', 'phone_number', 'email', 'is_active', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    #[Override]
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(self::class, 'updated_by')->withTrashed();
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(self::class, 'deleted_by')->withTrashed();
    }

    public function createdVehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'created_by')->withTrashed();
    }

    public function deletedVehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'deleted_by')->withTrashed();
    }

    public function createdServiceOrders(): HasMany
    {
        return $this->hasMany(ServiceOrder::class, 'created_by')->withTrashed();
    }

    public function deletedServiceOrders(): HasMany
    {
        return $this->hasMany(ServiceOrder::class, 'deleted_by')->withTrashed();
    }

    public function createPayments(): HasMany
    {
        return $this->hasMany(Payment::class, 'created_by');
    }

    public function voidedPayments(): HasMany
    {
        return $this->hasMany(Payment::class, 'voided_by');
    }

    #[Override]
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active;
    }

    #[Override]
    protected static function booted(): void
    {
        static::updating(function (self $user) {
            if ($id = Auth::id()) {
                $user->updated_by = $id;
            }
        });

        static::deleting(function (self $user) {
            if (! $user->isForceDeleting() && ($id = Auth::id())) {
                $user->deleted_by = $id;
                $user->saveQuietly();
            }
        });

        static::restoring(function (self $user) {
            $user->deleted_by = null;
        });
    }
}
