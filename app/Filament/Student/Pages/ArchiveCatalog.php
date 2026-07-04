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

class ArchiveCatalog extends Page
{
    protected static BackedEnum | string | null $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationLabel = 'Old Classes';
    protected static ?int $navigationSort = 4;
    protected static ?string $title = 'Available Archived Lessons';
    protected string $view = 'filament.student.pages.lesson-catalog';

    public function getLessonsProperty()
    {
        $myLessonIds = Enrollment::where('user_id', Auth::id())->pluck('lesson_id');

        return Lesson::whereNotIn('id', $myLessonIds)
            ->where(function ($q) {
                $q->whereNull('next_class_at')->orWhere('next_class_at', '<', now());
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function requestLessonAction(): Action
    {
        return Action::make('requestLesson')
            ->modalHeading('Unlock Archived Lesson')
            ->modalDescription('Upload your bank transfer slip to gain instant access to these recorded archives.')
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
                    'status' => 'Pending Verification',
                    'payment_slip' => $data['payment_slip'],
                ]);

                Notification::make()->title('Receipt Uploaded')->success()->send();
            });
    }
}
