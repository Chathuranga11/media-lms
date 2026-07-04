<?php

namespace App\Filament\Resources\Students;

use App\Filament\Resources\Students\Pages\CreateStudent;
use App\Filament\Resources\Students\Pages\EditStudent;
use App\Filament\Resources\Students\Pages\ListStudents;
use App\Filament\Resources\Students\Schemas\StudentForm;

use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StudentResource extends Resource
{
    protected static ?string $model = User::class;

    // --- THESE 3 LINES FIX THE SIDEBAR & HEADERS ---
    // They force Filament to proudly display "Students" instead of "Users"
    protected static ?string $modelLabel = 'Student';
    protected static ?string $pluralModelLabel = 'Students';
    protected static ?string $navigationLabel = 'Students';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'first_name';

    public static function form(Schema $schema): Schema
    {
        return StudentForm::configure($schema);
    }

    // --- NATIVE INLINE TABLE DEFINITION ---
    // Bypasses the external StudentsTable file entirely so data cannot render blank
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('first_name')->label('First Name')->searchable()->sortable(),
                \Filament\Tables\Columns\TextColumn::make('last_name')->label('Last Name')->searchable()->sortable(),
                \Filament\Tables\Columns\TextColumn::make('mobile_number')->label('Mobile Number')->searchable(),
                \Filament\Tables\Columns\TextColumn::make('district')->label('District')->sortable()->searchable()->placeholder('Not Set'),

                // Active access toggle switch right on the grid
                \Filament\Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Active Access')
                    ->onColor('success')
                    ->offColor('danger')
                    ->sortable(),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('district')
                    ->options([
                        'Ampara' => 'Ampara',
                        'Anuradhapura' => 'Anuradhapura',
                        'Badulla' => 'Badulla',
                        'Batticaloa' => 'Batticaloa',
                        'Colombo' => 'Colombo',
                        'Galle' => 'Galle',
                        'Gampaha' => 'Gampaha',
                        'Hambantota' => 'Hambantota',
                        'Jaffna' => 'Jaffna',
                        'Kalutara' => 'Kalutara',
                        'Kandy' => 'Kandy',
                        'Kegalle' => 'Kegalle',
                        'Kilinochchi' => 'Kilinochchi',
                        'Kurunegala' => 'Kurunegala',
                        'Mannar' => 'Mannar',
                        'Matale' => 'Matale',
                        'Matara' => 'Matara',
                        'Monaragala' => 'Monaragala',
                        'Mullaitivu' => 'Mullaitivu',
                        'Nuwara Eliya' => 'Nuwara Eliya',
                        'Polonnaruwa' => 'Polonnaruwa',
                        'Puttalam' => 'Puttalam',
                        'Ratnapura' => 'Ratnapura',
                        'Trincomalee' => 'Trincomalee',
                        'Vavuniya' => 'Vavuniya',
                    ]),
            ])
            ->groups([
                \Filament\Tables\Grouping\Group::make('district')
                    ->label('Enrollment by District')
                    ->collapsible(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\Students\RelationManagers\MaterialsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStudents::route('/'),
            'create' => CreateStudent::route('/create'),
            'edit' => EditStudent::route('/{record}/edit'),
        ];
    }

    // Locks the query so Admin accounts never show up in the student management list
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('role', 'student');
    }
}
