<?php

namespace App\Filament\Widgets;

use App\Enums\UserType;
use App\Models\Agent;
use App\Models\Claim;
use App\Models\Renewal;
use App\Models\Retiree;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    protected static bool $isLazy = false;

    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return match (Auth::user()->type) {
            UserType::Retiree => $this->getStatsForRetiree(),
            UserType::Agent => $this->getStatsForAgent(),
            UserType::Admin => $this->getStatsForAdmin(),
            default => []
        };
    }

    private function getStatsForRetiree()
    {
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

    private function getStatsForAgent()
    {
        return [
            Stat::make('Nombre de retraités', Retiree::count()),
            Stat::make('Nombre d\'agents', Agent::count()),
            Stat::make('Nombre de réclamations', Claim::count()),
        ];
    }

    private function getStatsForAdmin()
    {
        return [
            Stat::make('Nombre de retraités', Retiree::count()),
            Stat::make('Nombre d\'agents', Agent::count()),
            Stat::make('Nombre de réclamations', Claim::count()),
        ];
    }
}
