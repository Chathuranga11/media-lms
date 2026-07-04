<?php

namespace App\Filament\Student\Pages;

use Filament\Pages\Page;
use App\Models\Enrollment;
use App\Models\Material;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use BackedEnum;

class MyClasses extends Page
{
    protected static BackedEnum | string | null $navigationIcon = 'heroicon-o-book-open';
    protected static ?int $navigationSort = 2;
    protected string $view = 'filament.student.pages.my-classes';
    protected static ?string $title = 'My Classes';

    // This array will tell AlpineJS which videos the student is currently allowed to see
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

    /**
     * Gatekeeper Method: Called when they click "Load Recording"
     */
    public function unlockVideo($materialId)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $material = Material::findOrFail($materialId);

        if ($material->type === 'recording') {
            $pivot = $user->materials()->where('material_id', $materialId)->first();

            if (!$pivot) {
                // First view
                $user->materials()->attach($materialId, ['watch_count' => 1]);
            } else {
                $currentViews = $pivot->pivot->watch_count;

                if ($currentViews >= 3) {
                    // Block access
                    Notification::make()
                        ->danger()
                        ->title('View Limit Reached')
                        ->body('You have reached the maximum view limit (3/3) for this recording. Please contact administration.')
                        ->send();
                    return;
                }

                // Allow access, increment count
                $user->materials()->updateExistingPivot($materialId, [
                    'watch_count' => $currentViews + 1
                ]);
            }
        }

        // If they passed the check, tell the frontend this specific video is unlocked!
        $this->unlockedVideos[$materialId] = true;
    }
}
