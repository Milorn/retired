<?php

namespace App\Filament\Widgets;

use App\Enums\UserType;
use App\Models\Renewal;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class RetireeOverview extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    protected static bool $isLazy = false;

    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return Auth::user()->type == UserType::Retiree;
    }

    protected function getStats(): array
    {
        $pensionLetter = substr(Auth::user()->retiree->pension_status->getLabel(), 0, 1);
        $renewal = Renewal::query()
            ->where('year', today()->year)
            ->where('retiree_id', Auth::user()->retiree->id)
            ->latest()
            ->first();

        return [
            Stat::make('Net mensuel', Auth::user()->retiree->net_monthly.' DZD'),
            Stat::make('État pension', Auth::user()->retiree->pension_status->getLabel()),
            Stat::make('État renouvellement '.today()->year, $renewal ? $renewal->status->getLabel() : 'Non renouvelé'),
        ];
    }
}
