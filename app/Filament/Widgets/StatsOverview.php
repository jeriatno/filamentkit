<?php

namespace App\Filament\Widgets;

use App\Models\Master\Partner;
use App\Models\Rent\RentBill;
use App\Models\Rent\RentIn;
use App\Models\Rent\RentOut;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static bool $isLazy = false;

    public int $totalRentIn = 0;
    public int $totalRentOut = 0;
    public int $totalPartners = 0;
    public int $totalBillingUnpaid = 0;

    private function loadStatsData(): void
    {
        $rentIn = RentIn::with(['partner'])->count();
        $rentOut = RentOut::with(['rentIn'])->count();
        $partner = Partner::with(['user'])->count();
        $bills = RentBill::with(['rentIn'])->count();

        $this->totalRentIn = $rentIn ?? 0;
        $this->totalRentOut = $rentOut ?? 0;
        $this->totalPartners = $partner ?? 0;
        $this->totalBillingUnpaid = $bills ?? 0;
    }

    protected function getStats(): array
    {
        $this->loadStatsData();

        return [
            Stat::make('Total Rent In', $this->totalRentIn)
                ->color('primary'),

            Stat::make('Total Rent Out', $this->totalRentOut)
                ->color('success'),

            Stat::make('Total Partners', $this->totalPartners)
                ->color('warning'),

            Stat::make('Total Bill Unpaid', $this->totalBillingUnpaid)
                ->color('danger')
        ];
    }
}
