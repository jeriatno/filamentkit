<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RentBillResource\Pages;
use App\Filament\Resources\RentBillResource\RelationManagers;
use App\Models\Rent\RentBill;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RentBillResource extends Resource
{
    protected static ?string $model = RentBill::class;
    protected static ?string $navigationGroup = 'Billing';
    protected static ?int $navigationSort = 5;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
            'index' => Pages\ListRentBills::route('/'),
            'create' => Pages\CreateRentBill::route('/create'),
            'edit' => Pages\EditRentBill::route('/{record}/edit'),
        ];
    }
}
