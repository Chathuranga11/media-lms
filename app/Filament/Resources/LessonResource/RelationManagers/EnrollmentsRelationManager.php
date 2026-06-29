<?php

namespace App\Filament\Resources\LessonResource\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EnrollmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'enrollments';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                // Grab the student's name from the user relationship
                TextColumn::make('user.first_name')
                    ->label('Student Name')
                    ->searchable(),

                TextColumn::make('user.mobile_number')
                    ->label('Phone'),

                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'warning' => 'requested',
                        'success' => fn($state) => in_array($state, ['paid', 'free', 'postpay']),
                    ]),

                TextColumn::make('created_at')
                    ->label('Enrolled On')
                    ->date(),
            ]);
    }
}
