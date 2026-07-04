<?php

namespace App\Filament\Student\Widgets;

use Filament\Widgets\Widget;
use App\Models\Flyer;

class StudentFlyersWidget extends Widget
{
    // The view points to your student blade file
    protected string $view = 'filament.student.widgets.student-flyers-widget';

    // Make the widget take up the full width of the dashboard
    protected int | string | array $columnSpan = 'full';

    public function getFlyers()
    {
        return Flyer::where('is_active', true)->latest()->take(4)->get();
    }
}
