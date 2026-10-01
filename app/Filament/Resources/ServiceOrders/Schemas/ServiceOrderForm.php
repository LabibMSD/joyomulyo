<?php

namespace App\Filament\Resources\ServiceOrders\Schemas;

use App\Enums\ServiceOrderStatus;
use App\Filament\Resources\Vehicles\Schemas\VehicleForm;
use App\Models\Vehicle;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Operation;
use Illuminate\Database\Eloquent\Builder;

class ServiceOrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('service_number')
                    ->disabled()
                    ->hiddenOn(Operation::Create),
                Select::make('vehicle_id')
                    ->relationship(name: 'vehicle', modifyQueryUsing: fn (Builder $query) => $query->whereNull('deleted_at'))
                    ->createOptionForm(fn (Schema $schema) => VehicleForm::configure($schema))
                    ->editOptionForm(fn (Schema $schema) => VehicleForm::configure($schema))
                    ->editOptionAction(
                        fn (Action $action) => $action->hidden(function (Get $get) {
                            $vehicle = Vehicle::withTrashed()->find($get('vehicle_id'));

                            return ! $vehicle || $vehicle->trashed();
                        })
                    )
                    ->getOptionLabelFromRecordUsing(fn (Vehicle $record) => $record->display_name)
                    ->getOptionLabelUsing(function ($value) {
                        $record = Vehicle::withTrashed()->find($value);

                        return $record?->display_name ?? '(FORCE DELETED)';
                    })
                    ->searchable(['vehicle_code', 'license_plate', 'brand', 'model'])
                    ->preload()
                    ->required(),
                Select::make('status')
                    ->options(ServiceOrderStatus::class)
                    ->default(ServiceOrderStatus::Checking)
                    ->required(),
                Textarea::make('complaint')
                    ->columnSpanFull(),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
