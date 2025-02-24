<?php
 
namespace App\Filament\Widgets;

use App\Models\Task;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
 
class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {


        $tasks = auth()->user()->tasks;
        $totalTasks = $tasks->count();

        return [
            Stat::make(__('Total'), $totalTasks)  ->description(__('All Tasks'))   ,

            Stat::make(__('Pending'), $tasks->where('status', 'pending')->count())  ->description(__('Tasks not completed'))          ->descriptionIcon('heroicon-m-clock')
            ->color('warning'),

            Stat::make(__('Completed'), $tasks->where('status', 'completed')->count())->description(__('Completed Tasks')) ->color('success') ->descriptionIcon('heroicon-m-check'),
        ];
    }
}