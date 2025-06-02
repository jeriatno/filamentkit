<?php

namespace App\Livewire;

use Exception;
use Filament\Facades\Filament;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Jeffgreco13\FilamentBreezy\Livewire\MyProfileComponent;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Str;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Support\Facades\FilamentView;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;

use function Filament\Support\is_app_url;

class MyProfileExtended extends MyProfileComponent
{
    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public $user;

    public function mount(): void
    {
        $this->fillForm();
    }

    protected function fillForm(): void
    {
        $data = $this->getUser()->attributesToArray();

        $this->form->fill($data);
    }

    public function getUser(): Authenticatable & Model
    {
        $user = Filament::auth()->user();

        if (! $user instanceof Model) {
            throw new Exception('The authenticated user object must be an Eloquent model to allow the profile page to update it.');
        }

        return $user;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([

                Grid::make()->schema([
                    TextInput::make('name')
                        ->label('Full Name')
                        ->disabled()
                        ->required(),

                    Placeholder::make('status')
                        ->label('Status')
                        ->content(fn(?Model $record) => new HtmlString(
                            $record
                                ? ($record->is_active == 1
                                    ? "Your account has been activated <br>" . $record->created_at->format('M d, Y h:i A')
                                    : "Your account is inactive")
                                : ''
                        )),

                    Select::make('roles')
                        ->label('Role')
                        ->relationship('roles', 'name')
                        ->getOptionLabelFromRecordUsing(fn(Model $record) => Str::headline($record->name))
                        ->disabled(),

                    TextInput::make('email')
                        ->disabled()
                        ->required(),

                ]),
            ])
            ->operation('edit')
            ->model($this->getUser())
            ->statePath('data');
    }

    public function submit()
    {
        try {
            $data = $this->form->getState();

            $this->handleRecordUpdate($this->getUser(), $data);

            Notification::make()
                ->title('ProfileResource updated')
                ->success()
                ->send();

            $this->redirect('my-profile', navigate: FilamentView::hasSpaMode() && is_app_url('my-profile'));
        } catch (\Throwable $th) {
            Notification::make()
                ->title('Failed to update.')
                ->danger()
                ->send();
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);

        return $record;
    }

    public function cancel()
    {
        $this->redirect('dashboard');
    }



    public function render(): View
    {
        return view("livewire.my-profile-extended");
    }
}
