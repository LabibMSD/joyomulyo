<?php

namespace App\Filament\Resources\Vehicles\Schemas;

use App\Models\Vehicle;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class VehicleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('vehicle_code')
                    ->disabled()
                    ->hidden(fn (?Vehicle $record) => $record === null),
                TextInput::make('license_plate')
                    ->scopedUnique()
                    ->maxLength(15)
                    ->autocapitalize('characters')
                    ->afterStateUpdatedJs(<<<'JS'
                        $set('license_plate', ($state ?? '').toUpperCase())
                    JS),
                TextInput::make('brand')
                    ->maxLength(100)
                    ->autocapitalize('characters')
                    ->afterStateUpdatedJs(<<<'JS'
                        $set('brand', ($state ?? '').toUpperCase())
                    JS),
                TextInput::make('model')
                    ->maxLength(100)
                    ->autocapitalize('characters')
                    ->afterStateUpdatedJs(<<<'JS'
                        $set('model', ($state ?? '').toUpperCase())
                    JS),
            ]);
    }
}
