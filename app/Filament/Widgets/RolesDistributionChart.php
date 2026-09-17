<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;
use Spatie\Permission\Models\Role;

class RolesDistributionChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected ?string $heading = 'User Roles Distribution';

    protected ?string $description = 'Breakdown of active user accounts across assigned roles.';

    protected ?string $pollingInterval = '60s';

    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $roles = Role::withCount('users')->get();

        $labels = [];
        $data = [];

        if ($roles->isNotEmpty()) {
            foreach ($roles as $role) {
                $labels[] = ucfirst(str_replace('_', ' ', $role->name));
                $data[] = $role->users_count;
            }
        } else {
            // Fallback to active vs inactive if no roles created yet
            $activeCount = User::where('is_active', true)->count();
            $inactiveCount = User::where('is_active', false)->count();

            $labels = ['Active Users', 'Inactive Users'];
            $data = [$activeCount, $inactiveCount];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Users',
                    'data' => $data,
                    'backgroundColor' => [
                        '#F59E0B', // Amber
                        '#10B981', // Emerald
                        '#6366F1', // Indigo
                        '#0EA5E9', // Sky
                        '#EC4899', // Pink
                        '#8B5CF6', // Purple
                    ],
                    'borderWidth' => 0,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
