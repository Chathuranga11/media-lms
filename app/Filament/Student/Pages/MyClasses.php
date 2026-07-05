<?php

namespace App\Filament\Student\Pages;

use Filament\Pages\Page;
use App\Models\Enrollment;
use App\Models\Material;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // <-- Added DB Facade
use Filament\Notifications\Notification;
use BackedEnum;

class MyClasses extends Page
{
    protected static BackedEnum | string | null $navigationIcon = 'heroicon-o-book-open';
    protected static ?int $navigationSort = 2;
    protected string $view = 'filament.student.pages.my-classes';
    protected static ?string $title = 'My Classes';

    public array $unlockedVideos = [];

    protected function getViewData(): array
    {
        return [
            'enrollments' => Enrollment::with(['lesson.materials'])
                ->where('user_id', Auth::id())
                ->latest()
                ->get(),
        ];
    }

    public function unlockVideo($materialId)
    {
        try {
            $userId = Auth::id();

            // Safely query the pivot table directly to bypass any Model relationship errors
            $pivot = DB::table('material_user')
                ->where('user_id', $userId)
                ->where('material_id', $materialId)
                ->first();

            if (!$pivot) {
                // First view
                DB::table('material_user')->insert([
                    'user_id' => $userId,
                    'material_id' => $materialId,
                    'watch_count' => 1
                ]);
            } else {
                if ($pivot->watch_count >= 3) {
                    Notification::make()
                        ->danger()
                        ->title('View Limit Reached')
                        ->body('You have used all 3 views for this recording.')
                        ->send();

                    return; // Stop execution
                }

                // Increment view count
                DB::table('material_user')
                    ->where('user_id', $userId)
                    ->where('material_id', $materialId)
                    ->update([
                        'watch_count' => $pivot->watch_count + 1
                    ]);
            }

            // Unlock the video in the UI
            $this->unlockedVideos[$materialId] = true;
        } catch (\Exception $e) {
            // If the database fails, catch it and show a popup instead of freezing the button
            Notification::make()
                ->danger()
                ->title('System Error')
                ->body('Could not load video. Error: ' . $e->getMessage())
                ->send();
        }
    }
}
