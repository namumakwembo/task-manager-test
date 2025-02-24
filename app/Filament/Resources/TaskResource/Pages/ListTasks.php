<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTasks extends ListRecords
{
    protected static string $resource = TaskResource::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    
    public static function shouldRegisterNavigation(array $parameters = []): bool
    {
        return true;
    }

    public function getTitle():string{

        return __('Tasks');
    }

    public function getHeading(): string
    {
        return __('Tasks');
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label(__('Create Task')),
        ];
    }
}
