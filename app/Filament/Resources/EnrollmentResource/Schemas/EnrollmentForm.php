<?php

namespace App\Filament\Resources\Enrollments\Schemas;

use Filament\Schemas\Schema;

class EnrollmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Payment Verification')
                    ->schema([

                        // 1. STUDENT SELECTION
                        \Filament\Forms\Components\Select::make('user_id')
                            ->label('Student')
                            ->relationship('user', 'first_name', function ($query) {
                                return $query->where('role', 'student');
                            })
                            ->getOptionLabelFromRecordUsing(fn($record) => "{$record->first_name} {$record->last_name} ({$record->mobile_number})")
                            ->searchable()
                            ->preload()
                            ->required(),

                        // 2. LESSON SELECTION
                        \Filament\Forms\Components\Select::make('lesson_id')
                            ->label('Lesson / Class')
                            ->relationship('lesson', 'title') // Note: Change 'title' to 'name' if your DB requires it
                            ->searchable()
                            ->preload()
                            ->required(),

                        // 3. STATUS (Keys strictly matched to your Capitalized MySQL ENUM definition)
                        \Filament\Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'Requested' => 'Requested',
                                'Paid'      => 'Paid',
                                'Free'      => 'Free',
                                'Postpay'   => 'Postpay',
                            ])
                            ->required(),

                    ])->columns(3),
            ]);
    }
}
