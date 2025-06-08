<?php

namespace App\Filament\Pages\Setting;

use App\Mail\TestMail;
use App\Services\FileService;
use App\Settings\GeneralSettings;
use App\Settings\MailSettings;
use App\Settings\NotificationSettings;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\SettingsPage;
use Filament\Support\Facades\FilamentView;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Mail;

use function Filament\Support\is_app_url;

class ManageGeneral extends SettingsPage
{
    use HasPageShield;
    protected static string $settings = GeneralSettings::class;

    protected static ?int $navigationSort = 97;
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public string $themePath = '';

    public string $twConfigPath = '';

    public function mount(): void
    {
        $this->themePath = resource_path('css/filament/admin/theme.css');
        $this->twConfigPath = resource_path('css/filament/admin/tailwind.config.js');

        $this->fillForm();
    }

    protected function fillForm(): void
    {
        $settings = app(static::getSettings());

        $data = $this->mutateFormDataBeforeFill($settings->toArray());

        // Mail settings data
        $mailSettings = app(MailSettings::class);
        $mailData = $mailSettings->toArray();
        $data = array_merge($data, $mailData);

        $this->form->fill($data);
    }

    public function form(Form $form): Form
    {
        $settings = app(GeneralSettings::class);

        return $form
            ->schema([
                Forms\Components\Section::make('Site')
                    ->label(fn() => __('page.general_settings.sections.site'))
                    ->description(fn() => __('page.general_settings.sections.site.description'))
                    ->icon('heroicon-o-globe-alt')
                    ->schema([
                        Forms\Components\Grid::make()->schema([
                            Forms\Components\TextInput::make('brand_name')
                                ->label(fn() => __('page.general_settings.fields.brand_name'))
                                ->required(),
//                            Forms\Components\Select::make('site_active')
//                                ->label(fn() => __('page.general_settings.fields.site_active'))
//                                ->options([
//                                    0 => "Not Active",
//                                    1 => "Active",
//                                ])
//                                ->native(false)
//                                ->required(),
                            Forms\Components\TextInput::make('brand_logoHeight')
                                ->label(fn() => __('page.general_settings.fields.brand_logoHeight'))
                                ->required()
                                ->maxWidth('w-1/2'),
                        ]),
                        Forms\Components\Grid::make()->schema([

                            Forms\Components\Grid::make()->schema([
                                Forms\Components\FileUpload::make('brand_logo')
                                    ->label(fn() => __('page.general_settings.fields.brand_logo'))
                                    ->image()
                                    ->directory('sites')
                                    ->visibility('public')
                                    ->moveFiles(),

                                Forms\Components\FileUpload::make('site_favicon')
                                    ->label(fn() => __('page.general_settings.fields.site_favicon'))
                                    ->image()
                                    ->directory('sites')
                                    ->visibility('public')
                                    ->moveFiles()
                                    ->acceptedFileTypes(['image/x-icon', 'image/vnd.microsoft.icon']),
                            ]),
                        ])->columns(4),
                    ]),
                Forms\Components\Tabs::make('Tabs')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Mail Settings')
                            ->schema([
                                Forms\Components\Grid::make()
                                    ->schema([
                                        Forms\Components\Section::make('Configuration')
                                            ->label(fn() => __('page.mail_settings.sections.config.title'))
                                            ->icon('heroicon-o-calendar')
                                            ->schema([
                                                Forms\Components\Grid::make()
                                                    ->schema([
                                                        Forms\Components\Select::make('driver')->label(fn() => __('page.mail_settings.fields.driver'))
                                                            ->options([
                                                                "smtp" => "SMTP (Recommended)",
                                                                "mailgun" => "Mailgun",
                                                                "ses" => "Amazon SES",
                                                                "postmark" => "Postmark",
                                                            ])
                                                            ->native(false)
                                                            ->required()
                                                            ->columnSpan(2),
                                                        Forms\Components\TextInput::make('host')->label(fn() => __('page.mail_settings.fields.host'))
                                                            ->required(),
                                                        Forms\Components\TextInput::make('port')->label(fn() => __('page.mail_settings.fields.port')),
                                                        Forms\Components\Select::make('encryption')->label(fn() => __('page.mail_settings.fields.encryption'))
                                                            ->options([
                                                                "ssl" => "SSL",
                                                                "tls" => "TLS",
                                                            ])
                                                            ->native(false),
                                                        Forms\Components\TextInput::make('timeout')->label(fn() => __('page.mail_settings.fields.timeout')),
                                                        Forms\Components\TextInput::make('username')->label(fn() => __('page.mail_settings.fields.username')),
                                                        Forms\Components\TextInput::make('password')->label(fn() => __('page.mail_settings.fields.password'))
                                                            ->password()
                                                            ->revealable(),
                                                    ])
                                                    ->columns(3),
                                            ])
                                            ->columnSpan(2),
                                        Forms\Components\Group::make()
                                            ->schema([
                                                Forms\Components\Section::make('From (Sender)')
                                                    ->label(fn() => __('page.mail_settings.section.sender.title'))
                                                    ->icon('heroicon-o-user')
                                                    ->schema([
                                                        Forms\Components\TextInput::make('from_address')->label(fn() => __('page.mail_settings.fields.email'))
                                                            ->required(),
                                                        Forms\Components\TextInput::make('from_name')->label(fn() => __('page.mail_settings.fields.name'))
                                                            ->required(),
                                                    ]),

                                                Forms\Components\Section::make('Mail to')
                                                    ->label(fn() => __('page.mail_settings.section.mail_to.title'))
                                                    ->schema([
                                                        Forms\Components\TextInput::make('mail_to')
                                                            ->label(fn() => __('page.mail_settings.fields.mail_to'))
                                                            ->hiddenLabel()
                                                            ->placeholder(fn() => __('page.mail_settings.fields.placeholder.receiver_email')),
                                                        // ->required()
                                                        Forms\Components\Actions::make([
                                                            Forms\Components\Actions\Action::make('Send Test Mail')
                                                                ->label(fn() => __('page.mail_settings.actions.send_test_mail'))
                                                                ->action('sendTestMail')
                                                                ->color('primary')
                                                                ->icon('heroicon-o-envelope')
                                                        ])->fullWidth(),
                                                    ])
                                            ])
                                            ->columnSpan(1),
                                    ])
                                    ->columns(3),
                            ]),
                    ])
                    ->persistTabInQueryString()
                    ->columnSpanFull(),
            ])
            ->columns(3)
            ->statePath('data');
    }

    public function save(): void
    {
        try {
            $data = $this->mutateFormDataBeforeSave($this->form->getState());

            // Save General Settings
            $settings = app(static::getSettings());
            $generalData = array_intersect_key($data, array_flip([
                'brand_name',
                'site_active',
                'brand_logoHeight',
                'brand_logo',
                'site_favicon',
            ]));
            $settings->fill($generalData);
            $settings->save();

            // Save Mail Settings
            $mailSettings = app(MailSettings::class);
            $mailData = array_intersect_key($data, array_flip([
                'driver',
                'host',
                'port',
                'encryption',
                'timeout',
                'username',
                'password',
                'from_address',
                'from_name',
                'mail_to'
            ]));
            $mailSettings->fill($mailData);
            $mailSettings->save();

            Notification::make()
                ->title('Settings updated.')
                ->success()
                ->send();

            $this->redirect(static::getUrl(), navigate: FilamentView::hasSpaMode() && is_app_url(static::getUrl()));
        } catch (\Throwable $th) {
            Notification::make()
                ->title('Failed to update settings. ' . $th->getMessage())
                ->danger()
                ->send();

            throw $th;
        }
    }

    public function sendTestMail()
    {
        $data = $this->form->getState();

        $mailSettings = app(MailSettings::class);
        $mailSettings->loadMailSettingsToConfig($data);

        try {
            $mailTo = $data['mail_to'];
            $mailData = [
                'title' => 'This is a test email to verify SMTP settings',
                'body' => 'This is for testing email using smtp.'
            ];

            Mail::to($mailTo)->send(new TestMail($mailData));

            Notification::make()
                ->title('Mail Sent to: ' . $mailTo)
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public static function getNavigationGroup(): ?string
    {
        return __("menu.nav_group.settings");
    }

    public static function getNavigationLabel(): string
    {
        return __("page.general_settings.navigationLabel");
    }

    public function getTitle(): string|Htmlable
    {
        return __("page.general_settings.title");
    }

    public function getHeading(): string|Htmlable
    {
        return __("page.general_settings.heading");
    }

    public function getSubheading(): string|Htmlable|null
    {
        return __("page.general_settings.subheading");
    }
}
