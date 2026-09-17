<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class UserRegistrationsChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Monthly User Registrations';

    protected ?string $description = 'Number of new users registered each month over the past year.';

    protected ?string $pollingInterval = '60s';

    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $months = collect(range(11, 0))->map(function ($monthsAgo) {
            return Carbon::now()->subMonths($monthsAgo);
        });

        $labels = $months->map(fn (Carbon $date) => $date->format('M Y'))->toArray();

        $data = $months->map(function (Carbon $date) {
            return User::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        })->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'New Users Registered',
                    'data' => $data,
                    'fill' => 'start',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.15)',
                    'borderColor' => 'rgb(245, 158, 11)',
                    'tension' => 0.35,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
