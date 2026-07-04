<?php

namespace App\Filament\Student\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use App\Models\Lesson;
use App\Models\Enrollment;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth; // <-- Explicit Facade imported
use BackedEnum;

class UpcomingCatalog extends Page
{
    protected static BackedEnum | string | null $navigationIcon = 'heroicon-o-sparkles';
    protected static ?string $navigationLabel = 'Live Classes';
    protected static ?int $navigationSort = 3;
    protected static ?string $title = 'Available Upcoming Lessons';
    protected string $view = 'filament.student.pages.lesson-catalog';

    public function getLessonsProperty()
    {
        // Using Auth::id() makes Intelephense 100% happy
        $myLessonIds = Enrollment::where('user_id', Auth::id())->pluck('lesson_id');

        return Lesson::whereNotIn('id', $myLessonIds)
            ->whereNotNull('next_class_at')
            ->where('next_class_at', '>=', now())
            ->orderBy('next_class_at', 'asc')
            ->get();
    }

    public function requestLessonAction(): Action
    {
        return Action::make('requestLesson')
            ->modalHeading('Submit Bank Payment Slip')
            ->modalDescription('Upload a screenshot or photo of your bank transfer receipt to unlock this class.')
            ->form([
                FileUpload::make('payment_slip')
                    ->label('Bank Receipt Image')
                    ->disk('public')
                    ->directory('bank-slips')
                    ->image()
                    ->required(),
            ])
            ->action(function (array $data, array $arguments) {
                Enrollment::create([
                    'user_id' => Auth::id(), // <-- Zero squigglies here
                    'lesson_id' => $arguments['lesson'],
                    'status' => 'pending_payment', // The database accepts this silently
                    'payment_slip' => $data['payment_slip'],
                ]);

                Notification::make()
                    ->title('Receipt Uploaded Successfully')
                    ->body('Your enrollment is pending admin verification.')
                    ->success()
                    ->send();
            });
    }
}
