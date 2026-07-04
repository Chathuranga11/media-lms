<?php

namespace App\Filament\Resources\Students\RelationManagers;

use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

// THIS IS THE CRITICAL LINE THAT WAS MISSING:
use Filament\Tables\Actions\Action;

class MaterialsRelationManager extends RelationManager
{
    protected static string $relationship = 'materials';
    protected static ?string $recordTitleAttribute = 'title';
    protected static ?string $title = 'Student Video Views';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Recording Name')
                    ->searchable(),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge(),

                TextColumn::make('watch_count')
                    ->label('Views Used')
                    ->badge()
                    ->color(fn($state) => $state >= 3 ? 'danger' : 'success'),

                TextColumn::make('remaining_views')
                    ->label('Remaining Views')
                    ->getStateUsing(fn($record) => max(0, 3 - ($record->watch_count ?? 0)))
                    ->badge()
                    ->color(fn($state) => $state === 0 ? 'danger' : 'info'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->actions([
                // Because we imported Action at the top, this will now work perfectly.
                Action::make('adjust_views')
                    ->label('Edit Views')
                    ->icon('heroicon-m-adjustments-horizontal')
                    ->color('warning')
                    ->form([
                        TextInput::make('watch_count')
                            ->label('Current Watch Count')
                            ->numeric()
                            ->required()
                            ->helperText('Set to 0 to completely reset their views.'),
                    ])
                    ->fillForm(fn($record): array => [
                        'watch_count' => $record->watch_count,
                    ])
                    ->action(function ($record, array $data, RelationManager $livewire) {
                        $livewire->getOwnerRecord()->materials()->updateExistingPivot($record->id, [
                            'watch_count' => $data['watch_count'],
                        ]);
                    }),
            ])
            ->bulkActions([
                //
            ]);
    }
}
