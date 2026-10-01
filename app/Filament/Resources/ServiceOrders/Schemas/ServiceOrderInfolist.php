<?php

namespace App\Filament\Resources\ServiceOrders\Schemas;

use App\Models\ServiceOrder;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ServiceOrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('service_number'),
                TextEntry::make('vehicle.display_name')
                    ->label('Vehicle'),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('complaint')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('notes')
                    ->placeholder('-')
                    ->columnSpanFull(),
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
                    ->visible(fn (ServiceOrder $record): bool => $record->trashed()),
                TextEntry::make('deletedBy.name')
                    ->label('Deleted by')
                    ->placeholder('-')
                    ->visible(fn (ServiceOrder $record): bool => $record->trashed()),
            ]);
    }
}
