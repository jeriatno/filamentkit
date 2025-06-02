<?php

namespace App\Filament\Resources\Master;

use App\Filament\Resources\Master\WarahouseResource\Pages;
use App\Filament\Resources\Master\WarahouseResource\RelationManagers;
use App\Models\Master\Warehouse;
use App\Traits\HasActionPermissions;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WarehouseResource extends Resource implements HasShieldPermissions
{
    use HasActionPermissions;

    protected static ?string $model = Warehouse::class;
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?int $navigationSort = 6;
    protected static ?string $navigationIcon = 'heroicon-o-home-modern';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('city')
                            ->label('City')
                            ->required()
                            ->maxLength(100),

                        TextInput::make('address')
                            ->label('Address')
                            ->maxLength(255),
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

                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('city')
                    ->label('City')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('address')
                    ->label('Address')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                Filter::make('city')
                    ->form([
                        TextInput::make('city')
                            ->label('City'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query->when($data['city'], fn ($q) =>
                        $q->where('city', 'like', '%' . $data['city'] . '%')
                        );
                    }),

                Filter::make('name')
                    ->form([
                        TextInput::make('name')
                            ->label('Name'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query->when($data['name'], fn ($q) =>
                        $q->where('name', 'like', '%' . $data['name'] . '%')
                        );
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
            'index' => Pages\ListWarehouses::route('/'),
            'create' => Pages\CreateWarehouse::route('/create'),
            'edit' => Pages\EditWarehouse::route('/{record}/edit'),
        ];
    }
}
