<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RentOutResource\Pages;
use App\Filament\Resources\RentOutResource\RelationManagers;
use App\Models\Rent\RentOut;
use App\Traits\HasActionPermissions;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Date;

class RentOutResource extends Resource implements HasShieldPermissions
{
    use HasActionPermissions;

    protected static ?string $model = RentOut::class;
    protected static ?string $navigationLabel = 'Rent Out';
    protected static ?string $navigationGroup = 'Rents';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationIcon = 'heroicon-o-archive-box-x-mark';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('doc_no')
                    ->label('Doc No')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('doc_date')
                    ->label('Doc Date')
                    ->formatStateUsing(function ($state, $record) {
                        return \Carbon\Carbon::parse($state)->format('M d, Y H:i');
                    })
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('partner.name')
                    ->label('Partner')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('city_name')
                    ->label('City Name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('warehouse.name')
                    ->label('Warehouse')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('est_date_in')
                    ->label('Estimate Date In')
                    ->formatStateUsing(function ($state, $record) {
                        return \Carbon\Carbon::parse($state)->format('M d, Y');
                    })
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('est_return_date_in')
                    ->label('Estimate Return Date')
                    ->formatStateUsing(function ($state, $record) {
                        return \Carbon\Carbon::parse($state)->format('M d, Y');
                    })
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('address')
                    ->label('Address')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('dn_number')
                    ->label('Delivery Note')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('handling_fee')
                    ->label('Handling Fee')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('est_value')
                    ->label('Estimate Value / CCM')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRentOuts::route('/'),
            'create' => Pages\CreateRentOut::route('/create'),
            'edit' => Pages\EditRentOut::route('/{record}/edit'),
        ];
    }
}
