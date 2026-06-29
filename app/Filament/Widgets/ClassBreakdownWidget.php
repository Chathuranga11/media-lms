<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Lesson;

class ClassBreakdownWidget extends BaseWidget
{
    // Makes the table take up the full width of the dashboard
    protected int | string | array $columnSpan = 'full';

    // The title displayed at the top of the widget
    protected static ?string $heading = 'Class Enrollment & Income Breakdown';

    public function table(Table $table): Table
    {
        return $table
            ->query(Lesson::query())
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Class Name')
                    ->searchable()
                    ->weight('bold'),

                // INCOME: Calculated by multiplying Paid Students * Lesson Fee
                Tables\Columns\TextColumn::make('income')
                    ->label('Total Income')
                    ->money('LKR')
                    ->state(function (Lesson $record) {
                        $paidStudentsCount = $record->enrollments()->where('status', 'paid')->count();

                        // Look for the 'fee' column from your database
                        $lessonFee = $record->fee ?? 0;

                        return $paidStudentsCount * $lessonFee;
                    }),

                // TOTAL STUDENTS: Uses the Filament 'counts' helper
                // TOTAL STUDENTS: Manually counts all enrollments tied to this lesson
                Tables\Columns\TextColumn::make('total_students')
                    ->label('Registered')
                    ->badge()
                    ->color('gray')
                    ->state(function (Lesson $record) {
                        return $record->enrollments()->count();
                    }),

                // PAID STUDENTS
                Tables\Columns\TextColumn::make('paid_students')
                    ->label('Paid')
                    ->badge()
                    ->color('success')
                    ->state(function (Lesson $record) {
                        return $record->enrollments()->where('status', 'paid')->count();
                    }),

                // FREE STUDENTS
                Tables\Columns\TextColumn::make('free_students')
                    ->label('Free')
                    ->badge()
                    ->color('info')
                    ->state(function (Lesson $record) {
                        return $record->enrollments()->where('status', 'free')->count();
                    }),

                // POSTPAY STUDENTS
                Tables\Columns\TextColumn::make('postpay_students')
                    ->label('Postpay')
                    ->badge()
                    ->color('warning')
                    ->state(function (Lesson $record) {
                        return $record->enrollments()->where('status', 'postpay')->count();
                    }),
            ])
            ->defaultSort('created_at', 'desc'); // Sorts the newest classes to the top
    }
}
