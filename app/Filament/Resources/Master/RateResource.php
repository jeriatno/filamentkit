<?php

namespace App\Filament\Resources\Master;

use App\Filament\Resources\Master\RateResource\Pages;
use App\Filament\Resources\Master\RateResource\RelationManagers;
use App\Models\Master\Rate;
use App\Traits\HasActionPermissions;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RateResource extends Resource implements HasShieldPermissions
{
    use HasActionPermissions;

    protected static ?string $model = Rate::class;
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?int $navigationSort = 8;
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Select::make('warehouse_id')
                            ->label('Warehouse')
                            ->relationship('warehouse', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('value')
                            ->label('Value')
                            ->numeric()
                            ->required(),

                        DatePicker::make('start_at')
                            ->label('Start at')
                            ->required(),

                        DatePicker::make('end_at')
                            ->label('End at')
                            ->required(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('warehouse.name')
                    ->label('Warehouse')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('value')
                    ->label('Value')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_at')
                    ->label('Start at')
                    ->formatStateUsing(function ($state, $record) {
                        return \Carbon\Carbon::parse($state)->format('M d, Y');
                    })
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_at')
                    ->label('End at')
                    ->formatStateUsing(function ($state, $record) {
                        return \Carbon\Carbon::parse($state)->format('M d, Y');
                    })
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('warehouse_id')
                    ->label('Warehouse')
                    ->relationship('warehouse', 'name')
                    ->searchable(),

                Filter::make('start_at')
                    ->form([
                        DatePicker::make('start_from')->label('Start from'),
                        DatePicker::make('start_until')->label('Start until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['start_from'], fn ($q) => $q->whereDate('start_at', '>=', $data['start_from']))
                            ->when($data['start_until'], fn ($q) => $q->whereDate('start_at', '<=', $data['start_until']));
                    }),

                Filter::make('end_at')
                    ->form([
                        DatePicker::make('end_from')->label('End from'),
                        DatePicker::make('end_until')->label('End until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['end_from'], fn ($q) => $q->whereDate('end_at', '>=', $data['end_from']))
                            ->when($data['end_until'], fn ($q) => $q->whereDate('end_at', '<=', $data['end_until']));
                    }),
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
            'index' => Pages\ListRates::route('/'),
            'create' => Pages\CreateRate::route('/create'),
            'edit' => Pages\EditRate::route('/{record}/edit'),
        ];
    }
}
