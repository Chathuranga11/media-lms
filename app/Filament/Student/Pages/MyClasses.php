<?php

namespace App\Filament\Student\Pages;

use Filament\Pages\Page;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Auth;
use BackedEnum;

class MyClasses extends Page
{
    // Strict Type match for PHP 8.1+
    protected static BackedEnum | string | null $navigationIcon = 'heroicon-o-book-open';

    // ADDED: Locks this tab to Position #2 in your sidebar
    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.student.pages.my-classes';

    protected static ?string $title = 'My Enrolled Classes';

    protected function getViewData(): array
    {
        return [
            // 'lesson.materials' eager-loads the entire vault in 2 lightning-fast SQL queries
            'enrollments' => Enrollment::with(['lesson.materials'])
                ->where('user_id', Auth::id())
                ->latest()
                ->get(),
        ];
    }
}
