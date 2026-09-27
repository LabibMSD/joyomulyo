<?php

namespace App\Filament\Resources\Vehicles\Tables;

use App\Models\Vehicle;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

class VehiclesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('vehicle_code')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('license_plate')
                    ->searchable(),
                TextColumn::make('brand')
                    ->searchable(),
                TextColumn::make('model')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('createdBy.name')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updatedBy.name')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deletedBy.name')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
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
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make()
                        ->successNotification(null)
                        ->action(function (Collection $records, ?string $model) {
                            $trashedRecords = $records->filter(fn (Vehicle $record) => $record->trashed());
                            $total = $trashedRecords->count();

                            $restored = collect();
                            $conflicting = collect();

                            foreach ($trashedRecords as $record) {
                                $isTaken = $model::where('license_plate', $record->license_plate)
                                    ->whereNull('deleted_at')
                                    ->exists();

                                if ($isTaken) {
                                    $conflicting->push($record);

                                    continue;
                                }

                                $record->restore();
                                $restored->push($record);
                            }

                            if ($trashedRecords->isEmpty()) {
                                Notification::make()
                                    ->warning()
                                    ->title('Nothing to restore')
                                    ->body('None of the selected records are in trash.')
                                    ->send();
                            } elseif ($restored->isEmpty()) {
                                Notification::make()
                                    ->danger()
                                    ->title('Failed to restore any records')
                                    ->body('License plate already in use: '.$conflicting->pluck('license_plate')->implode(', '))
                                    ->send();
                            } elseif ($conflicting->isNotEmpty()) {
                                Notification::make()
                                    ->warning()
                                    ->title("{$restored->count()} of {$total} restored")
                                    ->body('License plate already in use: '.$conflicting->pluck('license_plate')->implode(', '))
                                    ->send();
                            } else {
                                Notification::make()
                                    ->success()
                                    ->title('Restored')
                                    ->send();
                            }
                        }),
                ]),
            ]);
    }
}
