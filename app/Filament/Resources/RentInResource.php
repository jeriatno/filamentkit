<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RentInResource\Pages;
use App\Filament\Resources\RentInResource\RelationManagers;
use App\Models\Master\Partner;
use App\Models\Master\PartnerAddress;
use App\Models\Master\Warehouse;
use App\Models\Rent\RentIn;
use App\Traits\HasActionPermissions;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Date;

class RentInResource extends Resource implements HasShieldPermissions
{
    use HasActionPermissions;

    protected static ?string $model = RentIn::class;
    protected static ?string $pluralLabel = 'Rent In';
    protected static ?string $navigationLabel = 'Rent In';
    protected static ?string $navigationGroup = 'Rents';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationIcon = 'heroicon-o-archive-box-arrow-down';

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
                SelectFilter::make('partner_id')
                    ->label('Partner')
                    ->searchable()
                    ->relationship('partner', 'name'),

                SelectFilter::make('warehouse_id')
                    ->label('Warehouse')
                    ->searchable()
                    ->relationship('warehouse', 'name'),

                Filter::make('city_name')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('city_name')->label('Kota'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query->when($data['city_name'],
                            fn($q) => $q->where('city_name', 'like', '%'.$data['city_name'].'%')
                        );
                    }),

                Filter::make('est_date_in')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from')->label('Dari'),
                        \Filament\Forms\Components\DatePicker::make('until')->label('Sampai'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['from'], fn($q) => $q->whereDate('est_date_in', '>=', $data['from']))
                            ->when($data['until'], fn($q) => $q->whereDate('est_date_in', '<=', $data['until']));
                    })
                    ->label('Estimate Date In'),
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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('doc_no')
                                    ->label('Doc No')
                                    ->disabled()
                                    ->placeholder('Auto Generated')
                                    ->dehydrated(false),

                                Forms\Components\DatePicker::make('doc_date')
                                    ->label('Doc Date')
                                    ->default(Carbon::now())
                                    ->required(),
                            ]),
                    ]),

                Forms\Components\Section::make('Partner Information')
                    ->schema([

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('partner_id')
                                    ->label('Partner')
                                    ->searchable()
                                    ->required()
                                    ->getSearchResultsUsing(function (string $search) {
                                        return Partner::query()
                                            ->where('name', 'like', "%{$search}%")
                                            ->orWhere('code', 'like', "%{$search}%")
                                            ->limit(50)
                                            ->get()
                                            ->mapWithKeys(fn($partner) => [$partner->id => $partner->display_name]);
                                    })
                                    ->getOptionLabelUsing(function ($value): ?string {
                                        $partner = Partner::find($value);
                                        return $partner?->display_name;
                                    })
                                    ->prefixIcon('heroicon-o-user')
                                    ->reactive(),

                                Forms\Components\Select::make('partner_address_id')
                                    ->label('Address')
                                    ->searchable()
                                    ->required()
                                    ->prefixIcon('heroicon-o-map')
                                    ->options(function (callable $get) {
                                        $partnerId = $get('partner_id');
                                        if (!$partnerId) {
                                            return [];
                                        }
                                        return PartnerAddress::where('partner_id', $partnerId)
                                            ->pluck('address', 'id')
                                            ->toArray();
                                    }),
                            ]),
                    ]),

                Forms\Components\Section::make('Warehouse Information')
                    ->schema([

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('city_id')
                                    ->label('City Name')
                                    ->relationship('city', 'name')
                                    ->searchable()
                                    ->required()
                                    ->reactive()
                                    ->prefixIcon('heroicon-o-map-pin'),

                                Forms\Components\Select::make('warehouse_id')
                                    ->label('Warehouse')
                                    ->relationship('warehouse', 'name')
                                    ->searchable()
                                    ->required()
                                    ->prefixIcon('heroicon-o-home-modern')
                                    ->options(function (callable $get) {
                                        $cityId = $get('city_id');
                                        if (!$cityId) {
                                            return [];
                                        }
                                        return Warehouse::where('city_id', $cityId)
                                            ->pluck('name', 'id')
                                            ->toArray();
                                    }),
                            ]),
                    ]),

                Forms\Components\Section::make('Other Information')
                    ->schema([

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('est_date_in')
                                    ->label('Estimate Date In')
                                    ->required(),

                                Forms\Components\DatePicker::make('est_return_date_in')
                                    ->label('Estimate Return Date')
                                    ->required(),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('handling_fee')
                                    ->label('Handling Fee')
                                    ->numeric()
                                    ->prefix('Rp'),

                                Forms\Components\TextInput::make('return_fee')
                                    ->label('Estimate Return Fee')
                                    ->numeric()
                                    ->prefix('Rp'),

                                Forms\Components\TextInput::make('est_value')
                                    ->label('Estimate Value / CCM')
                                    ->numeric()
                                    ->prefix('Rp'),
                            ]),
                    ]),

                Forms\Components\Section::make('Detail Items')
                    ->schema([

                        Forms\Components\Repeater::make('items')
                            ->hiddenLabel()
                            ->relationship('details')
                            ->addActionLabel('Add Item')
                            ->schema([
                                Forms\Components\TextInput::make('pn')
                                    ->label('Part Number')
                                    ->required()
                                    ->maxLength(100),

                                Forms\Components\TextInput::make('pn_desc')
                                    ->label('PN Description')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('qty')
                                    ->label('Qty')
                                    ->numeric()
                                    ->required()
                                    ->minValue(1),

                                Forms\Components\TextInput::make('volume')
                                    ->label('Volume')
                                    ->numeric()
                                    ->required(),

                                Forms\Components\FileUpload::make('picture')
                                    ->label('Picture')
                                    ->image()
                                    ->directory('item-pictures')
                                    ->maxSize(2048),
                            ])
                            ->columns(5)
                            ->collapsible()
                            ->defaultItems(1)
                            ->reorderable()
                            ->cloneable()
                            ->columnSpanFull(),
                    ])
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
            'index'  => Pages\ListRentIns::route('/'),
            'create' => Pages\CreateRentIn::route('/create'),
            'edit'   => Pages\EditRentIn::route('/{record}/edit'),
        ];
    }
}
