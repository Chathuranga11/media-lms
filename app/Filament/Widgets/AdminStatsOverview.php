<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;
use App\Models\Lesson;
use App\Models\Enrollment; // Using your updated model import
use Carbon\Carbon;

class AdminStatsOverview extends BaseWidget
{
    // FIXED: Removed the 'static' keyword so it matches the parent class perfectly
    protected ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        // Calculate current month's income by finding paid enrollments and summing their lesson fees
        $monthlyIncome = Enrollment::with('lesson')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->where('status', 'paid')
            ->get()
            ->sum(function ($enrollment) {
                // Look for the 'fee' column from your database
                return $enrollment->lesson->fee ?? 0;
            });

        return [
            Stat::make('Total Registered Students', User::count())
                ->description('All time registrations')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Active Lessons', Lesson::count())
                ->description('Currently running')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('success'),

            Stat::make('Current Month Income', 'Rs. ' . number_format($monthlyIncome, 2))
                ->description(Carbon::now()->format('F') . ' Earnings')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning'),
        ];
    }
}
