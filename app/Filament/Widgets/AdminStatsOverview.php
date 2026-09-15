<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsOverview extends BaseWidget
{
    protected ?string $pollingInterval = '30s';

    protected ?string $heading = 'User Management Overview';

    protected ?string $description = 'Current statistics for registered users and administrators.';

    protected function getStats(): array
    {
        $totalUsers = User::count();

        $activeUsers = User::where('is_active', true)->count();

        $inactiveUsers = User::where('is_active', false)->count();

        $adminUsers = User::role('admin')->count();

        $superAdmins = User::role('super_admin')->count();

        $registeredToday = User::whereDate(
            'created_at',
            today()
        )->count();

        return [
            Stat::make('Total Users', $totalUsers)
                ->description('All registered users')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Active Users', $activeUsers)
                ->description('Currently active accounts')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Inactive Users', $inactiveUsers)
                ->description('Disabled accounts')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),

            Stat::make('Administrators', $adminUsers)
                ->description('Users with Admin role')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('warning'),

            Stat::make('Super Admins', $superAdmins)
                ->description('Users with Super Admin role')
                ->descriptionIcon('heroicon-m-star')
                ->color('warning'),

            Stat::make('Registered Today', $registeredToday)
                ->description('New users today')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('info'),
        ];
    }
}