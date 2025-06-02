<?php

namespace App\Filament\Resources\Goods;

use App\Filament\Resources\Goods\IncomingGoodsResource\Pages;
use App\Filament\Resources\Goods\IncomingGoodsResource\RelationManagers;
use App\Models\Goods\IncomingGoods;
use App\Traits\HasActionPermissions;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class IncomingGoodsResource extends Resource implements HasShieldPermissions
{
    use HasActionPermissions;

    protected static ?string $model = IncomingGoods::class;
    protected static ?string $navigationGroup = 'Inventories';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationIcon = 'heroicon-o-folder-plus';

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
                TextColumn::make('rentIn.partner.display_name')
                    ->label('Partner')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('rentIn.doc_no')
                    ->label('Doc No')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('pn')
                    ->label('Part Number')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('sn')
                    ->label('Serial Number')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('vol_std')
                    ->label('Vol Std')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('rent_qty')
                    ->label('Quantity')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('vol_ccm')
                    ->label('Vol CCM')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('ccm_per_qty')
                    ->label('CCM Per Qty')
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
            'index' => Pages\ListIncomingGoods::route('/'),
            'create' => Pages\CreateIncomingGoods::route('/create'),
            'edit' => Pages\EditIncomingGoods::route('/{record}/edit'),
        ];
    }
}
