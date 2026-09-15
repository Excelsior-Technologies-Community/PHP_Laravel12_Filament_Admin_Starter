<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make(
                'Total Users',
                User::count()
            )
                ->description('All registered users')
                ->descriptionIcon('heroicon-m-users'),

            Stat::make(
                'Active Users',
                User::where('is_active', true)->count()
            )
                ->description('Currently active')
                ->descriptionIcon('heroicon-m-check-circle'),

            Stat::make(
                'Inactive Users',
                User::where('is_active', false)->count()
            )
                ->description('Currently inactive')
                ->descriptionIcon('heroicon-m-x-circle'),

            Stat::make(
                'New Today',
                User::whereDate('created_at', today())->count()
            )
                ->description('Users registered today')
                ->descriptionIcon('heroicon-m-user-plus'),
        ];
    }
}