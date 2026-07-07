<?php

namespace App\Filament\Student\Widgets;

use Filament\Widgets\Widget;

class QuickLinksWidget extends Widget
{
    // CRITICAL FIX: Removed the 'static' keyword from $view
    protected string $view = 'filament.student.widgets.quick-links-widget';

    // Makes the widget take up the full width of the screen
    protected int | string | array $columnSpan = 'full';

    // Set to -1 to force it to the very top of the dashboard
    protected static ?int $sort = -1;
}
