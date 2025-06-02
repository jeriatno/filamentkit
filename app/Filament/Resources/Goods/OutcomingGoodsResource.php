<?php

namespace App\Filament\Resources\Goods;

use App\Filament\Resources\Goods\OutcomingGoodsResource\Pages;
use App\Filament\Resources\Goods\OutcomingGoodsResource\RelationManagers;
use App\Models\Goods\OutcomingGoods;
use App\Traits\HasActionPermissions;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OutcomingGoodsResource extends Resource implements HasShieldPermissions
{
    use HasActionPermissions;

    protected static ?string $model = OutcomingGoods::class;
    protected static ?string $navigationGroup = 'Inventories';
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationIcon = 'heroicon-o-folder-minus';

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
                //
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
            'index' => Pages\ListOutcomingGoods::route('/'),
            'create' => Pages\CreateOutcomingGoods::route('/create'),
            'edit' => Pages\EditOutcomingGoods::route('/{record}/edit'),
        ];
    }
}
