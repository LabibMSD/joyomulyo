<?php

namespace App\Filament\Resources\Vehicles\Pages;

use App\Filament\Resources\Vehicles\VehicleResource;
use App\Models\Vehicle;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewVehicle extends ViewRecord
{
    protected static string $resource = VehicleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->hidden(fn (Vehicle $record) => $record->trashed()),
            DeleteAction::make(),
            RestoreAction::make()
                ->before(function (?string $model, Vehicle $record, RestoreAction $action) {
                    $isConflict = $model::where('license_plate', $record->license_plate)
                        ->whereNull('deleted_at')
                        ->exists();

                    if ($isConflict) {
                        Notification::make()
                            ->danger()
                            ->title('Restore failed')
                            ->body("License plate {$record->license_plate} is already used by another active record.")
                            ->send();

                        $action->halt();
                    }
                }),
            ForceDeleteAction::make(),
        ];
    }
}
