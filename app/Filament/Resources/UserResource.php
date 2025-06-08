<?php

namespace App\Filament\Resources;

use App\Enums\UserStatus;
use App\Filament\Resources\UserResource\Pages;
use App\Http\Responses\BaseResponse;
use App\Models\User\Role;
use App\Models\User\User;
use App\Traits\HasActionPermissions;
use App\Traits\Notifiable;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Exception;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Notifications\Auth\VerifyEmail;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource implements HasShieldPermissions
{
    use HasActionPermissions;

    protected static ?string $model = User::class;
    protected static int $globalSearchResultsLimit = 20;
    protected static ?int $navigationSort = 12;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'User Management';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $navigationLabel = 'User';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Name')
                            ->required(),

                        Select::make('role_id')
                            ->label('Role')
                            ->options(
                                Role::pluck('name', 'id')
                                    ->mapWithKeys(fn ($name, $id) => [$id => Str::headline($name)])
                            )
                            ->preload()
                            ->searchable()
                            ->required(),

                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->label('Email Address')
                            ->afterStateHydrated(function (Forms\Components\TextInput $component, $state) {
                                $component->state($state);
                            })
                            ->required(),

                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('index')
                    ->label('#')
                    ->rowIndex(),
                Tables\Columns\TextColumn::make('name')
                    ->label('User Name')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->label('Status')
                    ->formatStateUsing(fn($record) => $record->status)
                    ->color(fn($record) => isset($record->email_verified_at) ? 'success' : 'danger')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('roles.name')->label('Role')
                    ->formatStateUsing(fn($state): string => Str::headline($state))
                    ->colors(['gray'])
                    ->badge()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->copyable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('name')
                    ->label('User Name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->options(fn() => \Spatie\Permission\Models\Role::pluck('name', 'name')
                        ->mapWithKeys(fn($value) => [$value => Str::headline($value)])),

                Tables\Filters\SelectFilter::make('email')
                    ->options(fn() => User::pluck('email', 'email'))
                    ->searchable(),
            ])
            // ->filtersFormColumns(2)
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->color('dark')
                    ->hiddenLabel(),
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-s-pencil')
                    ->size('sm')
                    ->hiddenLabel(),
                Tables\Actions\Action::make('Delete User')
                    ->hiddenLabel()
                    ->icon('heroicon-s-trash')
                    ->action(function ($record) {
                        if ($record->employee && $record->employee->status == 1) {
                            BaseResponse::error('User cannot be deleted because they are still active');
                            return;
                        }

                        $record->syncPermissions([]);
                        $record->syncRoles([]);
                        $record->update([
                            'password_changed_at' => null,
                            'email_verified_at' => null,
                        ]);

                        BaseResponse::delete('User');
                    })
                    ->requiresConfirmation()
                    ->color('danger'),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('roles');
    }

    public static function getGlobalSearchResultTitle(Model $record): string|Htmlable
    {
        return $record->email;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['email', 'name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'name' => $record->name,
        ];
    }

    public static function doResendEmailVerification($settings = null, $user): void
    {
        if (!method_exists($user, 'notify')) {
            $userClass = $user::class;

            throw new Exception("Model [{$userClass}] does not have a [notify()] method.");
        }

        if ($settings->isMailSettingsConfigured()) {
            $notification = new VerifyEmail();
            $notification->url = Filament::getVerifyEmailUrl($user);

            $settings->loadMailSettingsToConfig();

            $user->notify($notification);


            Notification::make()
                ->title(__('resource.user.notifications.verify_sent.title'))
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title(__('resource.user.notifications.verify_warning.title'))
                ->body(__('resource.user.notifications.verify_warning.description'))
                ->warning()
                ->send();
        }
    }
}
