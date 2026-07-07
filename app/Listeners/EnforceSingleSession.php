<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Filament\Notifications\Notification; // <-- Added Notification Facade

class EnforceSingleSession
{
    public function handle(Login $event): void
    {
        $userId = $event->user->getAuthIdentifier();
        $currentSessionId = request()->session()->getId();

        // FIX 1: Use Laravel's exact timestamp generator to prevent timezone mismatches
        $activeThreshold = now()->subMinutes(15)->getTimestamp();

        // Look for OTHER sessions for this user that were active recently
        $existingActiveSession = DB::table('sessions')
            ->where('user_id', $userId)
            ->where('id', '!=', $currentSessionId)
            ->where('last_activity', '>=', $activeThreshold)
            ->first();

        if ($existingActiveSession) {
            // 1. Log this NEW attempt out immediately
            Auth::logout();

            // FIX 2: Trigger a global popup notification as a fail-safe
            Notification::make()
                ->danger()
                ->title('Login Blocked')
                ->body('This account is active on another device. Please wait 15 minutes.')
                ->send();

            // FIX 3: Target EVERY possible field name to ensure the red text appears
            throw ValidationException::withMessages([
                'data.mobile_number' => 'Account active on another device. Please wait 15 mins.',
                'mobile_number'      => 'Account active on another device. Please wait 15 mins.',
                'data.email'         => 'Account active on another device. Please wait 15 mins.',
                'email'              => 'Account active on another device. Please wait 15 mins.',
            ]);
        } else {
            // If the old session is dead, delete it and allow this login to succeed.
            DB::table('sessions')
                ->where('user_id', $userId)
                ->where('id', '!=', $currentSessionId)
                ->delete();
        }
    }
}
