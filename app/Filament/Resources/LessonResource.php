<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LessonResource\Pages;
use App\Filament\Resources\LessonResource\RelationManagers;
use App\Models\Lesson;
use Filament\Resources\Resource;

// 1. The Core Schema v5 Imports
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

// 2. The Form Input Imports
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;

// 3. The Table Imports
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

// 4. The Action Imports
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

use Filament\Forms\Components\FileUpload;

class LessonResource extends Resource
{
    protected static ?string $model = Lesson::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Notice how clean this is without the 'Forms\Components\' prefix!
                Section::make('Class Details')
                    ->schema([
                        TextInput::make('name')
                            ->label('Lesson Name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('fee')
                            ->label('Class Fee (LKR)')
                            ->numeric()
                            ->default(0)
                            ->required()
                            ->prefix('Rs.'),

                        DateTimePicker::make('next_class_at')
                            ->label('Next Live Class Date & Time')
                            ->nullable(),
                        TextInput::make('zoom_link')
                            ->label('Live Class Zoom Link')
                            ->url() // Validates that it's an actual web address
                            ->columnSpanFull(),

                        FileUpload::make('pdf_material_path')
                            ->label('Class Materials (PDF)')
                            ->directory('lesson-materials') // Saves to storage/app/public/lesson-materials
                            ->acceptedFileTypes(['application/pdf'])
                            ->columnSpanFull(),

                        Textarea::make('description')
                            ->label('Description / Notes')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('fee')->money('LKR')->sortable(),
                TextColumn::make('next_class_at')->dateTime('d M Y, h:i A')->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\MaterialsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLessons::route('/'),
            'create' => Pages\CreateLesson::route('/create'),
            'edit' => Pages\EditLesson::route('/{record}/edit'),
        ];
    }
}
