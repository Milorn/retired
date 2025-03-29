<?php

namespace App\Filament\Widgets;

use App\Enums\UserType;
use App\Models\Claim;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;

class ClaimsChart extends ChartWidget
{
    protected static ?string $heading = 'Mes Réclamations';

    public function getHeading(): string|Htmlable|null
    {
        return Auth::user()->type == UserType::Retiree ? 'Mes Réclamations' : 'Réclamations';
    }

    protected static ?string $pollingInterval = null;

    protected static bool $isLazy = false;

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $trend = Auth::user()->type == UserType::Retiree ? Trend::query(Claim::query()->where('retiree_id', Auth::user()->retiree->id)) : Trend::model(Claim::class);

        $data = $trend
            ->dateColumn('date')
            ->between(
                start: today()->subYear(),
                end: today(),
            )
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => Auth::user()->type == UserType::Retiree ? 'Mes Réclamations' : 'Réclamations',
                    'data' => $data->map(fn ($value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn ($value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
