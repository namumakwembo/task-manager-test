<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTask extends CreateRecord
{
    protected static string $resource = TaskResource::class;

    public function getTitle():string{

        return __('Create Task');
    }

   


    public static function shouldRegisterNavigation(array $parameters = []): bool
    {
        return true;
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
       $data['user_id'] = auth()->id();


        return $data;
    }


}
