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
use Filament\Tables\Filters\SelectFilter;
class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
       
            Forms\Components\Group::make()
            ->schema([

                Forms\Components\Section::make(__('Details'))->translateLabel()->schema([
                    TextInput::make('name')->string()->maxLength(100)->required()->autofocus(),
                    MarkdownEditor::make('description')->string()->maxLength(500)->nullable()->columnSpan(2),
                ])->columns(2),

                //Meta
                Forms\Components\Section::make("Meta")
                    ->schema([
                        TagsInput::make('tags')->distinct()->label("Tags")->placeholder(__("Add Tags")),
                         \Filament\Forms\Components\Select::make('status')
                            ->options([
                                'Completed' => 'completed',
                                'pending' => 'pending',
                            ])
                    ]),


            ])->columnSpan(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            Tables\Columns\TextColumn::make('name')
                ->description(fn (Task $record): string => $record->description)
                ->searchable(),
            Tables\Columns\TextColumn::make('description')
                ->description(fn (Task $record): string => $record->description)
                ->searchable(),
                TextColumn::make('created_at')->since()->dateTimeTooltip()
      
        ])
        ->filters([
     
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
        ])
        ->bulkActions([
            // Tables\Actions\BulkActionGroup::make([
            Tables\Actions\DeleteBulkAction::make(),
            // ]),
        ])
        ->emptyStateActions([
            Tables\Actions\CreateAction::make(),
        ]);

    }

    public static function getRelations(): array
    {
        return [
            //
        ];
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
