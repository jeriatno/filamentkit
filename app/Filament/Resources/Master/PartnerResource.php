<?php

namespace App\Filament\Resources\Master;

use App\Filament\Resources\Master\PartnerResource\Pages;
use App\Filament\Resources\Master\PartnerResource\RelationManagers;
use App\Http\Responses\BaseResponse;
use App\Models\Master\Partner;
use App\Models\Master\PartnerAddress;
use App\Models\Master\SMIPartner;
use App\Traits\HasActionPermissions;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PartnerResource extends Resource implements HasShieldPermissions
{
    use HasActionPermissions;

    protected static ?string $model = Partner::class;
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?int $navigationSort = 9;
    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Checkbox::make('is_smi_partner')
                            ->label('Is SMI Partner ?')
                            ->reactive(),

                        Select::make('smi_partner_id')
                            ->label('SMI Partner')
                            ->searchable()
                            ->getSearchResultsUsing(fn (string $search) =>
                            SMIPartner::searchDisplay($search)
                                ->pluck('display_name', 'id')
                            )
                            ->getOptionLabelUsing(fn ($value) =>
                            SMIPartner::withDisplayName()
                                ->where('id', $value)
                                ->value('display_name')
                            )
                            ->visible(fn (callable $get) => $get('is_smi_partner') === true)
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $partner = SMIPartner::find($state);
                                    if ($partner) {
                                        $set('code', $partner->code);
                                        $set('name', $partner->name);
                                    }
                                }
                            }),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                TextInput::make('code')
                                    ->label('Code')
                                    ->required()
                                    ->maxLength(50)
                                    ->columnSpan(1),

                                TextInput::make('name')
                                    ->label('Name')
                                    ->required()
                                    ->maxLength(100)
                                    ->columnSpan(2),

                                TextInput::make('email')
                                    ->label('Email')
                                    ->required()
                                    ->maxLength(100)
                                    ->columnSpan(2)
                            ])
                    ]),

                Forms\Components\Section::make('Addresses')
                    ->schema([
                        Forms\Components\Repeater::make('partnerAddress')
                            ->relationship('partnerAddress')
                            ->addActionLabel('Add Address')
                            ->defaultItems(0)
                            ->hiddenLabel()
                            ->itemLabel(fn(array $state): ?string => $state['address'] ?? null)
                            ->schema([
                                Forms\Components\TextInput::make('address')
                                    ->label('Address')
                                    ->placeholder('Ex: Jln. ABC')
                                    ->required(),
                            ])

                            ->deleteAction(function (Action $action) {
                                $action->requiresConfirmation();
                                $action->before(function ($state, array $arguments) use ($action) {
                                    BaseResponse::delete('Address');
                                });
                            })
                            ->reorderable()
                            ->reorderableWithButtons()
                            ->columns(1)
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->copyable(),

                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->toggleable()
                    ->sortable(),

                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->copyable(),

                TextColumn::make('partnerAddress.address')
                    ->label('Address')
                    ->formatStateUsing(function ($state, $record) {
                        return $record->partnerAddress
                            ->pluck('address')
                            ->filter()
                            ->map(fn($a) => "• $a")->join("<br>");
                    })
                    ->html()
                    ->wrap()
                    ->searchable()
                    ->toggleable()
            ])
            ->filters([
                Filter::make('code')
                    ->form([
                        TextInput::make('code')->label('Code'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['code'], fn ($q, $value) => $q->where('code', 'like', "%{$value}%"));
                    }),

                Filter::make('name')
                    ->form([
                        TextInput::make('name')->label('Name'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['name'], fn ($q, $value) => $q->where('name', 'like', "%{$value}%"));
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
            'index' => Pages\ListPartners::route('/'),
            'create' => Pages\CreatePartner::route('/create'),
            'edit' => Pages\EditPartner::route('/{record}/edit'),
        ];
    }
}
