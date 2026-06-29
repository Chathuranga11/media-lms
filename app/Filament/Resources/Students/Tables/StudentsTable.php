<?php

namespace App\Filament\Resources\Students\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Filters\SelectFilter;

class StudentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('first_name')->label('First Name')->searchable()->sortable(),
                TextColumn::make('last_name')->label('Last Name')->searchable()->sortable(),
                TextColumn::make('mobile_number')->label('Mobile Number')->searchable(),
                TextColumn::make('district')->label('District')->sortable()->searchable()->placeholder('Not Set'),

                // This toggle switch lets you disable/enable users instantly without needing Action buttons
                ToggleColumn::make('is_active')
                    ->label('Active Access')
                    ->onColor('success')
                    ->offColor('danger')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('district')
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
                Group::make('district')
                    ->label('Enrollment by District')
                    ->collapsible(),
            ]);
    }
}
