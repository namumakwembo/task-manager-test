<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskResource\Pages;
use App\Filament\Resources\TaskResource\RelationManagers;
use App\Models\Task;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Tasks';


    public function getTitle():string{

        return __('Tasks');
    }

    public function getHeading(): string
    {
        return __('Tasks');
    }
    public static function getNavigationLabel(): string
    {
        return __('Tasks');
    }
    public function getSubheading(): ?string
    {
        return __('Tasks');
    }
    

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
       
            Forms\Components\Group::make()
            ->schema([

                Forms\Components\Section::make(__('Details'))->translateLabel()->schema([
                    TextInput::make('name')->translateLabel()->string()->maxLength(100)->required()->autofocus(),
                    MarkdownEditor::make('description')->translateLabel()->string()->maxLength(500)->nullable()
                    ->disableToolbarButtons([
                        'attachFiles',
                        'strike',
                    ])
                    ->columnSpan(2),
                ]),


            ]),

            Forms\Components\Group::make()
            ->schema([

                //Meta
                Forms\Components\Section::make("Meta")->translateLabel()
                    ->schema([
                        TagsInput::make('tags')->distinct()->label(__("Tags"))->placeholder(__("Add Tags"))->nullable(),
                         \Filament\Forms\Components\Select::make('status')->label(__("Status"))
                         ->required()
                            ->options([
                                'completed' => __('Completed'),
                                'pending' => __('Pending'),
                            ])
                    ]),

                ]),

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            Tables\Columns\TextColumn::make('name')->translateLabel()
                ->searchable(),
            Tables\Columns\TextColumn::make('description')->translateLabel()->markdown()->formatStateUsing(fn(string $state) =>  str()->limit($state, 10) )
                ->searchable(),
                TextColumn::make('tags')->translateLabel()->bulleted()->listWithLineBreaks()->formatStateUsing(fn(string $state) =>  str()->limit($state, 20) )->limitList(2),
                ToggleColumn::make('status')
                ->label("Completed")
                ->getStateUsing(fn ($record) => $record->status === 'completed') // Convert stored string to boolean
                ->updateStateUsing(function ($record, $state) {
                    $record->status = $state ? 'completed' : 'pending'; // Convert back to string
                    $record->save();
                })
                ->translateLabel(),
            

                TextColumn::make('created_at')->translateLabel()->since()->dateTimeTooltip()
      
        ])
        ->filters([
     
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\ViewAction::make(),
        ])
        ->bulkActions([
            // Tables\Actions\BulkActionGroup::make([
            Tables\Actions\DeleteBulkAction::make(),
            // ]),
        ])
        ->emptyStateActions([
            Tables\Actions\CreateAction::make()->label('Create Task'),
        ]);

    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
{
    return parent::getEloquentQuery()->where('user_id', auth()->id());
}

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'view' => Pages\ViewTask::route('/{record}'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }
}
