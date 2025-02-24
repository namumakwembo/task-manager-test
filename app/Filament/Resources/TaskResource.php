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
                    MarkdownEditor::make('description')->string()->maxLength(500)->nullable()
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
                Forms\Components\Section::make("Meta")
                    ->schema([
                        TagsInput::make('tags')->distinct()->label(__("Tags"))->placeholder(__("Add Tags")),
                         \Filament\Forms\Components\Select::make('status')
                            ->options([
                                'Completed' => 'completed',
                                'pending' => 'pending',
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
                TextColumn::make('tags')->translateLabel()->bulleted()->listWithLineBreaks()->limitList(2),

                TextColumn::make('created_at')->since()->dateTimeTooltip()
      
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
            Tables\Actions\CreateAction::make(),
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
