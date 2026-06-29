<?php

namespace App\Filament\Resources\LessonResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;

class LessonEnrollmentStats extends BaseWidget
{
    // Grabs the specific Lesson we are currently looking at
    public ?Model $record = null;

    protected function getStats(): array
    {
        // 1. Calculate the counts
        $requested = $this->record->enrollments()->where('status', 'requested')->count();
        $paid      = $this->record->enrollments()->where('status', 'paid')->count();
        $postpay   = $this->record->enrollments()->where('status', 'postpay')->count();
        $free      = $this->record->enrollments()->where('status', 'free')->count();

        // 2. Calculate the Income
        // IMPORTANT: Change 'fee' to whatever your column is named in the lessons table!
        $fee = $this->record->fee ?? 0;
        $income = $paid * $fee;

        return [
            Stat::make('Requested', $requested)
                ->color('warning'),

            Stat::make('Paid', $paid)
                ->color('success'),

            Stat::make('Postpay', $postpay)
                ->color('info'),

            Stat::make('Free', $free)
                ->color('gray'),

            Stat::make('Income', 'Rs. ' . number_format($income, 2))
                ->color('success')
                ->description('Paid Students * Lesson Fee')
                ->descriptionIcon('heroicon-m-banknotes'),
        ];
    }
}
