<?php

namespace App\Filament\Pages;

use App\Enums\Access;
use App\Models\Employee\Employee;
use App\Models\Store\Area;
use App\Models\Store\Store;
use App\Traits\HandlesFilter;
use Coolsam\Flatpickr\Forms\Components\Flatpickr;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    use HasPageShield, HasFiltersForm, InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $title = null;

    protected array $data = [];
    public array $stores = [];
    public array $employees = [];
}
