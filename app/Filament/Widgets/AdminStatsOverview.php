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

        $adminUsers = 0;
        $superAdmins = 0;
        try {
            if (\Spatie\Permission\Models\Role::where('name', 'admin')->exists()) {
                $adminUsers = User::role('admin')->count();
            }
            if (\Spatie\Permission\Models\Role::where('name', 'super_admin')->exists()) {
                $superAdmins = User::role('super_admin')->count();
            }
        } catch (\Throwable $e) {
            $adminUsers = 0;
            $superAdmins = 0;
        }

        $registeredToday = User::whereDate('created_at', today())->count();
        $loggedInToday = \App\Models\LoginActivity::whereDate('login_at', today())->distinct('user_id')->count('user_id');

        return [
            Stat::make('Total Users', $totalUsers)
                ->description("{$registeredToday} registered today")
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('primary')
                ->chart([3, 5, 8, 4, 7, 9, $totalUsers]),

            Stat::make('Active Accounts', $activeUsers)
                ->description(round(($totalUsers > 0 ? ($activeUsers / $totalUsers) * 100 : 100), 1) . '% active rate')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Logged-in Today', $loggedInToday)
                ->description('Active user sessions today')
                ->descriptionIcon('heroicon-m-arrow-right-on-rectangle')
                ->color('info'),

            Stat::make('Inactive / Suspended', $inactiveUsers)
                ->description('Deactivated user accounts')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color($inactiveUsers > 0 ? 'danger' : 'gray'),

            Stat::make('Administrators', $adminUsers + $superAdmins)
                ->description("{$adminUsers} Admins & {$superAdmins} Super Admins")
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('warning'),
        ];
    }
}