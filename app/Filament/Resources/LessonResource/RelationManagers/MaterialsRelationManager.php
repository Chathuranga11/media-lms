<?php

namespace App\Filament\Resources\LessonResource\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

// The FIXED v5 Get Utility Import!
use Filament\Schemas\Components\Utilities\Get;

// Form Imports
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;

// Table Imports
use Filament\Tables\Columns\TextColumn;

// Unified v5 Action Imports
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class MaterialsRelationManager extends RelationManager
{
    protected static string $relationship = 'materials';

    protected static ?string $recordTitleAttribute = 'title';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Material Title')
                    ->placeholder('e.g., Join Live Class - Day 01')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Select::make('type')
                    ->label('Material Type')
                    ->options([
                        'live'      => '🔴 Live Zoom Class',
                        'recording' => '☁️ Zoom Cloud Recording',
                        'pdf'       => '📄 PDF Note / Tute',
                    ])
                    ->required()
                    ->live(),

                // <-- ADDED NEW AUDIENCE FIELD HERE -->
                Select::make('audience')
                    ->label('Access Audience')
                    ->options([
                        'all'       => 'All (Paid & Paid - Hall)',
                        'paid'      => 'Paid (Online Paid, Free, Postpay)',
                        'paid_hall' => 'Paid - Hall Only',
                    ])
                    ->default('all')
                    ->required()
                    ->helperText('Select which students are allowed to see this material.'),

                // Notice we now check if the type is 'live' OR 'recording' using in_array()
                TextInput::make('zoom_url')
                    ->label('Zoom Join URL')
                    ->url()
                    ->required(fn(Get $get) => in_array($get('type'), ['live', 'recording']))
                    ->visible(fn(Get $get) => in_array($get('type'), ['live', 'recording'])),

                TextInput::make('zoom_passcode')
                    ->label('Zoom Passcode')
                    ->required(fn(Get $get) => in_array($get('type'), ['live', 'recording']))
                    ->visible(fn(Get $get) => in_array($get('type'), ['live', 'recording'])),

                FileUpload::make('url')
                    ->label('Upload PDF')
                    ->directory('lesson-materials')
                    ->acceptedFileTypes(['application/pdf'])
                    ->required(fn(Get $get) => $get('type') === 'pdf')
                    ->visible(fn(Get $get) => $get('type') === 'pdf'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable(),

                TextColumn::make('type')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'live'      => 'danger',       // <-- Shows as a red badge for Live
                        'recording' => 'info',         // <-- Shows as a blue badge for Recording
                        'pdf'       => 'success',      // <-- Shows as a green badge for PDF
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'live'      => 'Live Class',
                        'recording' => 'Zoom Recording',
                        'pdf'       => 'PDF Note',
                        default     => $state,
                    }),

                // <-- ADDED AUDIENCE BADGE TO TABLE VISIBILITY -->
                TextColumn::make('audience')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'all'       => 'success',
                        'paid'      => 'info',
                        'paid_hall' => 'warning',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'all'       => 'All Students',
                        'paid'      => 'Online/Free/Postpay',
                        'paid_hall' => 'Paid - Hall Only',
                        default     => ucfirst($state),
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()->label('Add Material'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
