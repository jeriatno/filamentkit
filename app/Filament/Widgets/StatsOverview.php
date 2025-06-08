<?php

namespace App\Filament\Widgets;

use App\Models\Master\Partner;
use App\Models\Rent\RentBill;
use App\Models\Rent\RentIn;
use App\Models\Rent\RentOut;
use App\Models\User\Role;
use App\Models\User\User;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static bool $isLazy = false;

    public int $totalRole = 0;
    public int $totalUsers = 0;
    public int $totalUserActive = 0;
    public int $totalUserInactive = 0;

    private function loadStatsData(): void
    {
        $totalRole = Role::count();
        $totalUsers = User::count();
        $totalUserActive = User::userActive()->count();
        $totalUserInactive = User::userInactive()->count();

        $this->totalRole = $totalRole ?? 0;
        $this->totalUsers = $totalUsers ?? 0;
        $this->totalUserActive = $totalUserActive ?? 0;
        $this->totalUserInactive = $totalUserInactive ?? 0;
    }

    protected function getStats(): array
    {
        $this->loadStatsData();

        return [
            Stat::make('Total Role', $this->totalRole)
                ->color('primary'),

            Stat::make('Total Users', $this->totalUsers)
                ->color('success'),

            Stat::make('Total User Active', $this->totalUserActive)
                ->color('warning'),

            Stat::make('Total User Inactive', $this->totalUserInactive)
                ->color('danger')
        ];
    }
}
