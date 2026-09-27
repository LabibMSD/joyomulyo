<?php

namespace App\Filament\Resources\Vehicles\Schemas;

use App\Models\Vehicle;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class VehicleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('vehicle_code'),
                TextEntry::make('license_plate')
                    ->placeholder('-'),
                TextEntry::make('brand')
                    ->placeholder('-'),
                TextEntry::make('model')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('createdBy.name')
                    ->label('Created by')
                    ->placeholder('-'),
                TextEntry::make('updatedBy.name')
                    ->label('Updated by')
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Vehicle $record): bool => $record->trashed()),
                TextEntry::make('deletedBy.name')
                    ->label('Deleted by')
                    ->placeholder('-')
                    ->visible(fn (Vehicle $record): bool => $record->trashed()),
            ]);
    }
}
